<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * BaremeFraisModel : Gestion des barèmes de frais par tranche de montant
 */
class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'type_operation_id' => 'required|integer',
        'montant_min'       => 'required|numeric',
        'montant_max'       => 'required|numeric',
        'frais'             => 'required|numeric',
    ];

    /**
     * Récupère tous les barèmes d'un type d'opération, triés par montant_min
     */
    public function getBaremesParType(int $typeOperationId): array
    {
        return $this->where('type_operation_id', $typeOperationId)
                    ->orderBy('montant_min', 'ASC')
                    ->findAll();
    }

    /**
     * Calcule les frais applicables pour un montant et un type d'opération donnés
     * Retourne les frais ou 0 si pas de barème correspondant
     */
    public function calculerFrais(int $typeOperationId, float $montant): float
    {
        $bareme = $this->where('type_operation_id', $typeOperationId)
                       ->where('montant_min <=', $montant)
                       ->where('montant_max >=', $montant)
                       ->first();

        return $bareme ? (float) $bareme['frais'] : 0.0;
    }

    /**
     * Récupère tous les barèmes avec le libellé du type d'opération
     */
    public function getBaremesAvecType(): array
    {
        return $this->db->table('baremes_frais bf')
                        ->select('bf.*, to.libelle as type_libelle, to.code as type_code')
                        ->join('types_operations to', 'to.id = bf.type_operation_id')
                        ->orderBy('to.code, bf.montant_min', 'ASC')
                        ->get()
                        ->getResultArray();
    }
}
