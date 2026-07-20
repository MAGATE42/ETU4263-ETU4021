<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\PrefixeModel;

/**
 * UtilisateurController : Gère le côté CLIENT du système mobile money Orange
 */
class UtilisateurController extends BaseController
{
    protected CompteModel       $compteModel;
    protected TransactionModel  $transactionModel;
    protected TypeOperationModel $typeModel;
    protected BaremeFraisModel  $baremeModel;
    protected PrefixeModel      $prefixeModel;

    public function __construct()
    {
        $this->compteModel      = new CompteModel();
        $this->transactionModel = new TransactionModel();
        $this->typeModel        = new TypeOperationModel();
        $this->baremeModel      = new BaremeFraisModel();
        $this->prefixeModel     = new PrefixeModel();
    }

    public function index(): string
    {
        return view('client/login', [
            'titre' => 'Orange Money - Connexion',
        ]);
    }

    public function verifierUtilisateur(): \CodeIgniter\HTTP\RedirectResponse
    {
        $telephone = trim($this->request->getPost('telephone'));

        if (empty($telephone)) {
            return redirect()->to('/client')->with('erreur', 'Veuillez saisir votre numéro de téléphone.');
        }

        if (!$this->prefixeModel->estPrefixeValide($telephone)) {
            return redirect()->to('/client')->with('erreur', 'Ce numéro ne correspond pas à un préfixe Orange valide (032, 033, 037).');
        }

        $compte = $this->compteModel->getParTelephone($telephone);

        if (!$compte) {
            $id = $this->compteModel->insert([
                'telephone' => $telephone,
                'nom'       => 'Client',
                'prenom'    => '',
                'solde'     => 0.00,
                'statut'    => 'actif',
            ]);
            $compte = $this->compteModel->find($id);
        }

        if ($compte['statut'] !== 'actif') {
            return redirect()->to('/client')->with('erreur', 'Votre compte est suspendu. Contactez le service client.');
        }

        session()->set([
            'client_id'        => $compte['id'],
            'client_telephone' => $compte['telephone'],
            'client_nom'       => $compte['nom'] . ' ' . $compte['prenom'],
            'client_connecte'  => true,
        ]);

        return redirect()->to('/client/dashboard');
    }

    public function dashboard(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        $compteId = session()->get('client_id');
        $compte   = $this->compteModel->find($compteId);
        $historique = $this->transactionModel->getHistoriqueCompte($compteId, 5);

        return view('client/dashboard', [
            'titre'     => 'Orange Money - Mon Compte',
            'compte'    => $compte,
            'historique'=> $historique,
        ]);
    }

    public function faireDepot(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        $montant   = (float) $this->request->getPost('montant');
        $compteId  = session()->get('client_id');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Le montant doit être supérieur à 0.');
        }

        $typeDepot = $this->typeModel->getParCode('DEPOT');
        if (!$typeDepot) {
            return redirect()->back()->with('erreur', 'Type d\'opération introuvable.');
        }

        $this->compteModel->crediter($compteId, $montant);

        $this->transactionModel->insert([
            'reference'         => $this->transactionModel->genererReference(),
            'compte_id'         => $compteId,
            'type_operation_id' => $typeDepot['id'],
            'montant'           => $montant,
            'frais'             => 0,
            'statut'            => 'success',
            'note'              => 'Dépôt automatique',
        ]);

        return redirect()->back()->with('success', 'Dépôt de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès.');
    }

    public function faireRetrait(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        $montant  = (float) $this->request->getPost('montant');
        $compteId = session()->get('client_id');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Le montant doit être supérieur à 0.');
        }

        $typeRetrait = $this->typeModel->getParCode('RETRAIT');
        if (!$typeRetrait) {
            return redirect()->back()->with('erreur', 'Type d\'opération introuvable.');
        }

        $frais = $this->baremeModel->calculerFrais($typeRetrait['id'], $montant);

        $compte    = $this->compteModel->find($compteId);
        $totalDebit = $montant + $frais;

        if ($compte['solde'] < $totalDebit) {
            return redirect()->back()->with('erreur', 'Solde insuffisant. Solde : ' . number_format($compte['solde'], 0, ',', ' ') . ' Ar | Requis : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar)');
        }

        $this->compteModel->debiter($compteId, $totalDebit);

        $this->transactionModel->insert([
            'reference'         => $this->transactionModel->genererReference(),
            'compte_id'         => $compteId,
            'type_operation_id' => $typeRetrait['id'],
            'montant'           => $montant,
            'frais'             => $frais,
            'statut'            => 'success',
            'note'              => 'Retrait automatique',
        ]);

        return redirect()->back()->with('success', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectué avec succès. Frais prélevés : ' . number_format($frais, 0, ',', ' ') . ' Ar.');
    }

    public function faireTransfert(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        $montant      = (float) $this->request->getPost('montant');
        $telDest      = trim($this->request->getPost('telephone_destinataire'));
        $compteId     = session()->get('client_id');
        $telExpéditeur = session()->get('client_telephone');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Le montant doit être supérieur à 0.');
        }

        if (empty($telDest)) {
            return redirect()->back()->with('erreur', 'Veuillez saisir le numéro du destinataire.');
        }

        if ($telDest === $telExpéditeur) {
            return redirect()->back()->with('erreur', 'Vous ne pouvez pas vous envoyer un transfert à vous-même.');
        }

        if (!$this->prefixeModel->estPrefixeValide($telDest)) {
            return redirect()->back()->with('erreur', 'Le numéro du destinataire n\'est pas un numéro Orange valide.');
        }

        $compteDest = $this->compteModel->getParTelephone($telDest);
        if (!$compteDest) {
            $idDest     = $this->compteModel->insert([
                'telephone' => $telDest,
                'nom'       => 'Client',
                'prenom'    => '',
                'solde'     => 0.00,
                'statut'    => 'actif',
            ]);
            $compteDest = $this->compteModel->find($idDest);
        }

        $typeTransfert = $this->typeModel->getParCode('TRANSFERT');
        if (!$typeTransfert) {
            return redirect()->back()->with('erreur', 'Type d\'opération introuvable.');
        }

        $frais      = $this->baremeModel->calculerFrais($typeTransfert['id'], $montant);
        $compteEmetteur = $this->compteModel->find($compteId);
        $totalDebit = $montant + $frais;

        if ($compteEmetteur['solde'] < $totalDebit) {
            return redirect()->back()->with('erreur', 'Solde insuffisant. Solde : ' . number_format($compteEmetteur['solde'], 0, ',', ' ') . ' Ar | Requis : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar)');
        }

        $this->compteModel->debiter($compteId, $totalDebit);
        $this->compteModel->crediter($compteDest['id'], $montant);

        $ref = $this->transactionModel->genererReference();
        $this->transactionModel->insert([
            'reference'              => $ref,
            'compte_id'              => $compteId,
            'type_operation_id'      => $typeTransfert['id'],
            'montant'                => $montant,
            'frais'                  => $frais,
            'compte_destinataire_id' => $compteDest['id'],
            'statut'                 => 'success',
            'note'                   => 'Transfert vers ' . $telDest,
        ]);

        $typeDepot = $this->typeModel->getParCode('DEPOT');
        $this->transactionModel->insert([
            'reference'              => 'REC-' . substr($ref, 4),
            'compte_id'              => $compteDest['id'],
            'type_operation_id'      => $typeDepot['id'],
            'montant'                => $montant,
            'frais'                  => 0,
            'compte_destinataire_id' => null,
            'statut'                 => 'success',
            'note'                   => 'Reçu de ' . $telExpéditeur,
        ]);

        return redirect()->back()->with('success', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $telDest . ' effectué avec succès.');
    }

    public function voirHistoriques(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        $compteId   = session()->get('client_id');
        $compte     = $this->compteModel->find($compteId);
        $historique = $this->transactionModel->getHistoriqueCompte($compteId, 100);

        return view('client/historiques', [
            'titre'     => 'Mes Historiques - Orange Money',
            'compte'    => $compte,
            'historique'=> $historique,
        ]);
    }

    public function deconnexion(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/client')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
