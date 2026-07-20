<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CompteModel : Gestion des comptes clients (mobile money)
 */
class CompteModel extends Model
{
    protected $table            = 'comptes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['telephone', 'nom', 'prenom', 'solde', 'statut'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'telephone' => 'required|min_length[7]|max_length[20]',
        'solde'     => 'permit_empty|numeric',
        'statut'    => 'permit_empty|in_list[actif,suspendu,fermé]',
    ];

    /**
     * Recherche un compte par numéro de téléphone
     */
    public function getParTelephone(string $telephone): ?array
    {
        return $this->where('telephone', $telephone)->first();
    }

    /**
     * Vérifie si un numéro de téléphone existe déjà
     */
    public function telephoneExiste(string $telephone): bool
    {
        return $this->where('telephone', $telephone)->countAllResults() > 0;
    }

    /**
     * Met à jour le solde d'un compte
     */
    public function mettreAJourSolde(int $compteId, float $nouveauSolde): bool
    {
        return $this->update($compteId, ['solde' => $nouveauSolde]);
    }

    /**
     * Crédite un montant sur un compte
     */
    public function crediter(int $compteId, float $montant): bool
    {
        $compte = $this->find($compteId);
        if (!$compte) return false;
        return $this->mettreAJourSolde($compteId, $compte['solde'] + $montant);
    }

    /**
     * Débite un montant d'un compte (avec vérification de solde)
     */
    public function debiter(int $compteId, float $montant): bool
    {
        $compte = $this->find($compteId);
        if (!$compte) return false;
        if ($compte['solde'] < $montant) return false;
        return $this->mettreAJourSolde($compteId, $compte['solde'] - $montant);
    }

    /**
     * Récupère tous les comptes avec statistiques
     */
    public function getComptesAvecStats(): array
    {
        return $this->db->table('comptes c')
            ->select('c.*, COUNT(t.id) as nb_transactions, IFNULL(SUM(CASE WHEN to2.code = "RETRAIT" THEN t.frais WHEN to2.code = "TRANSFERT" THEN t.frais ELSE 0 END), 0) as total_frais_generes')
            ->join('transactions t', 't.compte_id = c.id', 'left')
            ->join('types_operations to2', 'to2.id = t.type_operation_id', 'left')
            ->groupBy('c.id')
            ->get()
            ->getResultArray();
    }
}
