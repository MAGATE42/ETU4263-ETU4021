<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\PrefixeModel;
use App\Models\ConfigurationModel;

class OperateurController extends BaseController
{
    private const OPERATEUR_IDENTIFIANT = 'operateur';
    private const OPERATEUR_MOT_DE_PASSE = '4021';

    protected CompteModel        $compteModel;
    protected TransactionModel   $transactionModel;
    protected TypeOperationModel $typeModel;
    protected BaremeFraisModel   $baremeModel;
    protected PrefixeModel       $prefixeModel;
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

    private function operateurEstConnecte(): bool
    {
        return (bool) session()->get('operateur_connecte');
    }

    private function redirigerSiNonConnecte(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->operateurEstConnecte()) {
            return redirect()->to('/operateur');
        }

        return null;
    }

    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (! $this->operateurEstConnecte()) {
            return view('operateur/login', [
                'titre' => 'Orange Money - Connexion opérateur',
            ]);
        }

        $gains         = $this->transactionModel->getGainOperateur();
        $totalGain     = $this->transactionModel->getTotalGainOperateur();
        $comptes       = $this->compteModel->getComptesAvecStats();
        $transactions  = $this->transactionModel->getToutesTransactionsAvecDetails(10);
        $nbComptes     = $this->compteModel->countAll();
        $nbTransactions= $this->transactionModel->countAll();

        return view('operateur/dashboard', [
            'titre'          => 'Orange Money - Tableau de Bord Opérateur',
            'gains'          => $gains,
            'total_gain'     => $totalGain,
            'comptes'        => $comptes,
            'transactions'   => $transactions,
            'nb_comptes'     => $nbComptes,
            'nb_transactions'=> $nbTransactions,
        ]);
    }

    public function login(): \CodeIgniter\HTTP\RedirectResponse
    {
        $identifiant = trim((string) $this->request->getPost('identifiant'));
        $motDePasse   = (string) $this->request->getPost('mot_de_passe');

        if ($identifiant === '' || $motDePasse === '') {
            return redirect()->to('/operateur')->with('erreur', 'Veuillez saisir l’identifiant et le mot de passe.');
        }

        if ($identifiant !== self::OPERATEUR_IDENTIFIANT || $motDePasse !== self::OPERATEUR_MOT_DE_PASSE) {
            return redirect()->to('/operateur')->with('erreur', 'Identifiants opérateur invalides.');
        }

        session()->set([
            'operateur_connecte' => true,
            'operateur_identifiant' => $identifiant,
        ]);

        return redirect()->to('/operateur')->with('success', 'Connexion opérateur réussie.');
    }

    public function deconnexion(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->remove(['operateur_connecte', 'operateur_identifiant']);

        return redirect()->to('/operateur')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    public function gererPrefixes(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $prefixes = $this->prefixeModel->findAll();

        return view('operateur/prefixes', [
            'titre'   => 'Gestion des Préfixes - Opérateur',
            'prefixes'=> $prefixes,
        ]);
    }

    public function ajouterPrefixe(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $prefixe           = trim($this->request->getPost('prefixe'));
        $description       = trim($this->request->getPost('description'));
        $estAutreOperateur = $this->request->getPost('est_autre_operateur') ? 1 : 0;

        if (empty($prefixe)) {
            return redirect()->back()->with('erreur', 'Le préfixe est obligatoire.');
        }

        $existe = $this->prefixeModel->where('prefixe', $prefixe)->first();
        if ($existe) {
            return redirect()->back()->with('erreur', 'Ce préfixe existe déjà.');
        }

        $this->prefixeModel->insert([
            'prefixe'             => $prefixe,
            'description'         => $description,
            'actif'               => 1,
            'est_autre_operateur' => $estAutreOperateur,
        ]);

        return redirect()->back()->with('success', 'Préfixe ajouté avec succès.');
    }

    public function supprimerPrefixe(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $this->prefixeModel->delete($id);
        return redirect()->back()->with('success', 'Préfixe supprimé.');
    }

    public function togglePrefixe(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $prefixe = $this->prefixeModel->find($id);
        if (!$prefixe) {
            return redirect()->back()->with('erreur', 'Préfixe introuvable.');
        }
        $nouvelEtat = $prefixe['actif'] == 1 ? 0 : 1;
        $this->prefixeModel->update($id, ['actif' => $nouvelEtat]);
        $message = $nouvelEtat ? 'Préfixe activé.' : 'Préfixe désactivé.';
        return redirect()->back()->with('success', $message);
    }

    public function gererTypesOperations(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $types = $this->typeModel->findAll();

        return view('operateur/types_operations', [
            'titre'=> 'Gestion des Types d\'Opérations',
            'types'=> $types,
        ]);
    }

    public function gererBaremes(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $types   = $this->typeModel->findAll();
        $baremes = $this->baremeModel->getBaremesAvecType();

        return view('operateur/baremes', [
            'titre'  => 'Gestion des Barèmes de Frais',
            'types'  => $types,
            'baremes'=> $baremes,
        ]);
    }

    public function ajouterBareme(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $typeId    = (int) $this->request->getPost('type_operation_id');
        $montMin   = (float) $this->request->getPost('montant_min');
        $montMax   = (float) $this->request->getPost('montant_max');
        $frais     = (float) $this->request->getPost('frais');

        if ($typeId <= 0 || $montMin < 0 || $montMax <= $montMin || $frais < 0) {
            return redirect()->back()->with('erreur', 'Données invalides. Vérifiez les montants.');
        }

        $this->baremeModel->insert([
            'type_operation_id' => $typeId,
            'montant_min'       => $montMin,
            'montant_max'       => $montMax,
            'frais'             => $frais,
        ]);

        return redirect()->back()->with('success', 'Barème ajouté avec succès.');
    }

    public function supprimerBareme(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $this->baremeModel->delete($id);
        return redirect()->back()->with('success', 'Barème supprimé.');
    }

    public function gererConfigurations(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $commissionExterne = $this->configModel->getValeur('commission_transfert_externe', 0);

        return view('operateur/configurations', [
            'titre'              => 'Configurations Globales',
            'commission_externe' => $commissionExterne,
        ]);
    }

    public function sauvegarderConfigurations(): \CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $commission = (float) $this->request->getPost('commission_externe');
        
        if ($commission < 0) {
            return redirect()->back()->with('erreur', 'La commission ne peut pas être négative.');
        }

        $this->configModel->setValeur('commission_transfert_externe', (string) $commission);

        return redirect()->back()->with('success', 'Configurations sauvegardées.');
    }

    public function situationGains(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $gains             = $this->transactionModel->getGainOperateur();
        $totalGain         = $this->transactionModel->getTotalGainOperateur();
        $totalCommissionExt= $this->transactionModel->getTotalCommissionAutresOperateurs();
        $dusAutresOps      = $this->transactionModel->getMontantsDusAutresOperateurs();
        $transactions      = $this->transactionModel->getToutesTransactionsAvecDetails(200);

        return view('operateur/gains', [
            'titre'                => 'Situation des Gains - Opérateur',
            'gains'                => $gains,
            'total_gain'           => $totalGain,
            'total_commission_ext' => $totalCommissionExt,
            'dus_autres_ops'       => $dusAutresOps,
            'transactions'         => $transactions,
        ]);
    }

    public function situationComptes(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($redirect = $this->redirigerSiNonConnecte()) {
            return $redirect;
        }

        $comptes = $this->compteModel->getComptesAvecStats();

        return view('operateur/comptes', [
            'titre'  => 'Situation des Comptes Clients',
            'comptes'=> $comptes,
        ]);
    }
}
