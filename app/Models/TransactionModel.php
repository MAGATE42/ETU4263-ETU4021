<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * TransactionModel : Gestion des transactions (dépôts, retraits, transferts)
 */
class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'reference',
        'compte_id',
        'type_operation_id',
        'montant',
        'frais',
        'commission_autre_operateur',
        'compte_destinataire_id',
        'statut',
        'note',
    ];
    protected $useTimestamps = true;

    /**
     * Génère une référence unique pour la transaction
     */
    public function genererReference(): string
    {
        return 'ORG-' . strtoupper(uniqid()) . '-' . date('ymd');
    }

    /**
     * Récupère l'historique des transactions d'un compte avec les détails
     */
    public function getHistoriqueCompte(int $compteId, int $limite = 50): array
    {
        return $this->db->table('transactions t')
            ->select('t.*, to2.libelle as type_libelle, to2.code as type_code,
                      c.telephone as compte_telephone, c.nom as compte_nom,
                      cd.telephone as dest_telephone, cd.nom as dest_nom')
            ->join('types_operations to2', 'to2.id = t.type_operation_id')
            ->join('comptes c', 'c.id = t.compte_id')
            ->join('comptes cd', 'cd.id = t.compte_destinataire_id', 'left')
            ->where('t.compte_id', $compteId)
            ->orderBy('t.created_at', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    /**
     * Récupère toutes les transactions avec détails (côté opérateur)
     */
    public function getToutesTransactionsAvecDetails(int $limite = 100): array
    {
        return $this->db->table('transactions t')
            ->select('t.*, to2.libelle as type_libelle, to2.code as type_code,
                      c.telephone as compte_telephone, c.nom as compte_nom, c.prenom as compte_prenom,
                      cd.telephone as dest_telephone, cd.nom as dest_nom, cd.prenom as dest_prenom')
            ->join('types_operations to2', 'to2.id = t.type_operation_id')
            ->join('comptes c', 'c.id = t.compte_id')
            ->join('comptes cd', 'cd.id = t.compte_destinataire_id', 'left')
            ->orderBy('t.created_at', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    /**
     * Calcule le gain total de l'opérateur (somme des frais retrait + transfert)
     */
    public function getGainOperateur(): array
    {
        $result = $this->db->table('transactions t')
            ->select('to2.code as type_code, to2.libelle as type_libelle, 
                      COUNT(t.id) as nb_transactions, 
                      SUM(t.montant) as total_montants,
                      SUM(t.frais) as total_frais')
            ->join('types_operations to2', 'to2.id = t.type_operation_id')
            ->where('t.statut', 'success')
            ->whereIn('to2.code', ['RETRAIT', 'TRANSFERT'])
            ->groupBy('to2.code')
            ->get()
            ->getResultArray();

        return $result;
    }

    /**
     * Calcule le gain total global
     */
    public function getTotalGainOperateur(): float
    {
        $row = $this->db->table('transactions t')
            ->select('SUM(t.frais) as total')
            ->join('types_operations to2', 'to2.id = t.type_operation_id')
            ->where('t.statut', 'success')
            ->whereIn('to2.code', ['RETRAIT', 'TRANSFERT'])
            ->get()
            ->getRow();

        return $row ? (float) $row->total : 0.0;
    }

    /**
     * Calcule la commission totale perçue pour le compte des autres opérateurs
     */
    public function getTotalCommissionAutresOperateurs(): float
    {
        $row = $this->db->table('transactions t')
            ->select('SUM(t.commission_autre_operateur) as total')
            ->where('t.statut', 'success')
            ->get()
            ->getRow();

        return $row ? (float) $row->total : 0.0;
    }

    /**
     * Récupère les montants dus à chaque opérateur externe
     * basé sur les transferts envoyés vers leurs préfixes
     */
    public function getMontantsDusAutresOperateurs(): array
    {
        // On récupère les transferts vers les comptes qui ont un préfixe d'autre opérateur
        $result = $this->db->table('transactions t')
            ->select('p.description as operateur_nom, p.prefixe, SUM(t.montant) as total_du')
            ->join('types_operations to2', 'to2.id = t.type_operation_id')
            ->join('comptes cd', 'cd.id = t.compte_destinataire_id')
            ->join('prefixes p', 'cd.telephone LIKE p.prefixe || "%"')
            ->where('t.statut', 'success')
            ->where('to2.code', 'TRANSFERT')
            ->where('p.est_autre_operateur', 1)
            ->groupBy('p.id')
            ->get()
            ->getResultArray();

        return $result;
    }
}
