<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\PrefixeModel;
use App\Models\ConfigurationModel;

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
    protected ConfigurationModel $configModel;

    public function __construct()
    {
        $this->compteModel      = new CompteModel();
        $this->transactionModel = new TransactionModel();
        $this->typeModel        = new TypeOperationModel();
        $this->baremeModel      = new BaremeFraisModel();
        $this->prefixeModel     = new PrefixeModel();
        $this->configModel      = new ConfigurationModel();
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
            return redirect()->to('/client')->with('erreur', 'Ce numéro ne correspond à aucun préfixe valide.');
        }

        // Bloquer les numéros appartenant à un autre opérateur
        $prefixeDetails = $this->prefixeModel->getDetailsPrefixe($telephone);
        if ($prefixeDetails && $prefixeDetails['est_autre_operateur'] == 1) {
            return redirect()->to('/client')->with('erreur', 'Les numéros d\'autres opérateurs ne peuvent pas se connecter sur cette plateforme.');
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
        $prefixes = $this->prefixeModel->findAll();

        return view('client/dashboard', [
            'titre'     => 'Orange Money - Mon Compte',
            'compte'    => $compte,
            'historique'=> $historique,
            'prefixes'  => $prefixes,
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

        $montantTotal = (float) $this->request->getPost('montant');
        $telDestInput = $this->request->getPost('telephone_destinataire');
        $inclureRetrait= $this->request->getPost('inclure_frais_retrait') ? true : false;
        
        $compteId     = session()->get('client_id');
        $telExpediteur= session()->get('client_telephone');

        if ($montantTotal <= 0) {
            return redirect()->back()->with('erreur', 'Le montant doit être supérieur à 0.');
        }

        if (empty($telDestInput)) {
            return redirect()->back()->with('erreur', 'Veuillez saisir au moins un numéro de destinataire.');
        }

        // Nettoyage et séparation des numéros
        if (is_array($telDestInput)) {
            $destinataires = array_filter(array_map('trim', $telDestInput));
        } else {
            $destinataires = array_filter(array_map('trim', explode(',', trim($telDestInput))));
        }

        if (empty($destinataires)) {
            return redirect()->back()->with('erreur', 'Format de destinataires invalide.');
        }

        $isMultiple = count($destinataires) > 1;

        $typeTransfert = $this->typeModel->getParCode('TRANSFERT');
        $typeDepot     = $this->typeModel->getParCode('DEPOT');
        $typeRetrait   = $this->typeModel->getParCode('RETRAIT');

        if (!$typeTransfert || !$typeDepot || !$typeRetrait) {
            return redirect()->back()->with('erreur', 'Configuration des types d\'opération manquante.');
        }

        // Validation des destinataires
        $comptesDests = [];
        foreach ($destinataires as $telDest) {
            if ($telDest === $telExpediteur) {
                return redirect()->back()->with('erreur', 'Vous ne pouvez pas vous envoyer un transfert à vous-même.');
            }

            $prefixeDetails = $this->prefixeModel->getDetailsPrefixe($telDest);
            if (!$prefixeDetails) {
                return redirect()->back()->with('erreur', "Le numéro $telDest n'est pas valide.");
            }

            if ($isMultiple && $prefixeDetails['est_autre_operateur'] == 1) {
                return redirect()->back()->with('erreur', "L'envoi multiple n'est possible que vers des numéros du même opérateur (interne).");
            }

            $comptesDests[] = [
                'telephone' => $telDest,
                'prefixe'   => $prefixeDetails
            ];
        }

        $montantUnitaire = round($montantTotal / count($comptesDests), 2);
        $totalDebitGlobal = 0;
        $operationsLog = [];
        
        $commissionPourcentage = (float) $this->configModel->getValeur('commission_transfert_externe', 0);

        // Simulation pour calculer le débit global
        foreach ($comptesDests as &$dest) {
            $montantAEnvoyer = $montantUnitaire;
            $fraisRetrait    = 0;
            $commissionExt   = 0;

            if ($dest['prefixe']['est_autre_operateur'] == 1) {
                // Commission externe (pourcentage)
                $commissionExt = round(($montantAEnvoyer * $commissionPourcentage) / 100, 2);
            } else {
                if ($inclureRetrait) {
                    $fraisRetrait = $this->baremeModel->calculerFrais($typeRetrait['id'], $montantAEnvoyer);
                    $montantAEnvoyer += $fraisRetrait; // On envoie le montant + les frais pour que le retrait soit "gratuit" pour le destinataire
                }
            }

            $fraisTransfert = $this->baremeModel->calculerFrais($typeTransfert['id'], $montantAEnvoyer);
            $totalDebit = $montantAEnvoyer + $fraisTransfert + $commissionExt;
            $totalDebitGlobal += $totalDebit;

            $dest['montant_a_envoyer'] = $montantAEnvoyer;
            $dest['frais_transfert']   = $fraisTransfert;
            $dest['commission_ext']    = $commissionExt;
            $dest['total_debit']       = $totalDebit;
        }

        // Vérification du solde global
        $compteEmetteur = $this->compteModel->find($compteId);
        if ($compteEmetteur['solde'] < $totalDebitGlobal) {
            return redirect()->back()->with('erreur', 'Solde insuffisant pour le total de l\'opération. Solde : ' . number_format($compteEmetteur['solde'], 0, ',', ' ') . ' Ar | Requis : ' . number_format($totalDebitGlobal, 0, ',', ' ') . ' Ar');
        }

        // Exécution des transactions
        $db = \Config\Database::connect();
        $db->transStart();

        $this->compteModel->debiter($compteId, $totalDebitGlobal);

        foreach ($comptesDests as $dest) {
            $compteDestRow = $this->compteModel->getParTelephone($dest['telephone']);
            if (!$compteDestRow) {
                $idDest = $this->compteModel->insert([
                    'telephone' => $dest['telephone'],
                    'nom'       => 'Client',
                    'prenom'    => '',
                    'solde'     => 0.00,
                    'statut'    => 'actif',
                ]);
                $compteDestRow = $this->compteModel->find($idDest);
            }

            $this->compteModel->crediter($compteDestRow['id'], $dest['montant_a_envoyer']);

            $ref = $this->transactionModel->genererReference();
            $noteAdd = $inclureRetrait && !$dest['prefixe']['est_autre_operateur'] ? " (Frais de retrait inclus)" : "";
            
            // Transaction Emetteur
            $this->transactionModel->insert([
                'reference'              => $ref,
                'compte_id'              => $compteId,
                'type_operation_id'      => $typeTransfert['id'],
                'montant'                => $dest['montant_a_envoyer'], // Montant réellement transféré
                'frais'                  => $dest['frais_transfert'],
                'commission_autre_operateur' => $dest['commission_ext'],
                'compte_destinataire_id' => $compteDestRow['id'],
                'statut'                 => 'success',
                'note'                   => 'Transfert vers ' . $dest['telephone'] . $noteAdd,
            ]);

            // Transaction Destinataire
            $this->transactionModel->insert([
                'reference'              => 'REC-' . substr($ref, 4),
                'compte_id'              => $compteDestRow['id'],
                'type_operation_id'      => $typeDepot['id'],
                'montant'                => $dest['montant_a_envoyer'],
                'frais'                  => 0,
                'commission_autre_operateur' => 0,
                'compte_destinataire_id' => null, // Optionnel, on pourrait mettre l'émetteur
                'statut'                 => 'success',
                'note'                   => 'Reçu de ' . $telExpediteur,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erreur', 'Erreur lors de l\'exécution du transfert. Veuillez réessayer.');
        }

        if ($isMultiple) {
            return redirect()->back()->with('success', 'Transferts groupés effectués avec succès pour un total de ' . number_format($totalDebitGlobal, 0, ',', ' ') . ' Ar débités.');
        }

        return redirect()->back()->with('success', 'Transfert effectué avec succès vers ' . $comptesDests[0]['telephone'] . ' (Débité : ' . number_format($totalDebitGlobal, 0, ',', ' ') . ' Ar).');
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
    
    public function sauvegarderConfigurations(): \CodeIgniter\HTTP\RedirectResponse
    {
        
        $pourcentage = (float) $this->request->getPost('pourcentage_epargne');
        
        if ($pourcentage < 0) {
            return redirect()->back()->with('erreur', 'La pourcentage ne peut pas être négative.');
        }

        $this->configModel->setValeur('pourcentage_depargne', (string) $pourcentage);

        return redirect()->back()->with('success', 'Configurations sauvegardées.');
    }

    public function voirConfiguration(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->get('client_connecte')) {
            return redirect()->to('/client');
        }

        return view('client/configuration');
    }
}
