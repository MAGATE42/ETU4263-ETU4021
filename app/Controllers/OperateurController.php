<?php

namespace App\Controllers;

use App\Models\CompteModel;
use App\Models\TransactionModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\PrefixeModel;

/**
 * OperateurController : Gère le côté OPÉRATEUR du système mobile money Orange
 */
class OperateurController extends BaseController
{
    protected CompteModel        $compteModel;
    protected TransactionModel   $transactionModel;
    protected TypeOperationModel $typeModel;
    protected BaremeFraisModel   $baremeModel;
    protected PrefixeModel       $prefixeModel;

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

    public function gererPrefixes(): string
    {
        $prefixes = $this->prefixeModel->findAll();

        return view('operateur/prefixes', [
            'titre'   => 'Gestion des Préfixes - Opérateur',
            'prefixes'=> $prefixes,
        ]);
    }

    public function ajouterPrefixe(): \CodeIgniter\HTTP\RedirectResponse
    {
        $prefixe     = trim($this->request->getPost('prefixe'));
        $description = trim($this->request->getPost('description'));

        if (empty($prefixe)) {
            return redirect()->back()->with('erreur', 'Le préfixe est obligatoire.');
        }

        $existe = $this->prefixeModel->where('prefixe', $prefixe)->first();
        if ($existe) {
            return redirect()->back()->with('erreur', 'Ce préfixe existe déjà.');
        }

        $this->prefixeModel->insert([
            'prefixe'     => $prefixe,
            'description' => $description,
            'actif'       => 1,
        ]);

        return redirect()->back()->with('success', 'Préfixe ajouté avec succès.');
    }

    public function supprimerPrefixe(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->prefixeModel->delete($id);
        return redirect()->back()->with('success', 'Préfixe supprimé.');
    }

    public function togglePrefixe(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $prefixe = $this->prefixeModel->find($id);
        if (!$prefixe) {
            return redirect()->back()->with('erreur', 'Préfixe introuvable.');
        }
        $nouvelEtat = $prefixe['actif'] == 1 ? 0 : 1;
        $this->prefixeModel->update($id, ['actif' => $nouvelEtat]);
        $message = $nouvelEtat ? 'Préfixe activé.' : 'Préfixe désactivé.';
        return redirect()->back()->with('success', $message);
    }

    public function gererTypesOperations(): string
    {
        $types = $this->typeModel->findAll();

        return view('operateur/types_operations', [
            'titre'=> 'Gestion des Types d\'Opérations',
            'types'=> $types,
        ]);
    }

    public function gererBaremes(): string
    {
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
        $this->baremeModel->delete($id);
        return redirect()->back()->with('success', 'Barème supprimé.');
    }

    public function situationGains(): string
    {
        $gains         = $this->transactionModel->getGainOperateur();
        $totalGain     = $this->transactionModel->getTotalGainOperateur();
        $transactions  = $this->transactionModel->getToutesTransactionsAvecDetails(200);

        return view('operateur/gains', [
            'titre'      => 'Situation des Gains - Opérateur Orange',
            'gains'      => $gains,
            'total_gain' => $totalGain,
            'transactions'=> $transactions,
        ]);
    }

    public function situationComptes(): string
    {
        $comptes = $this->compteModel->getComptesAvecStats();

        return view('operateur/comptes', [
            'titre'  => 'Situation des Comptes Clients',
            'comptes'=> $comptes,
        ]);
    }
}
