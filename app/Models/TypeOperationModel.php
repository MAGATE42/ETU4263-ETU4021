<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * TypeOperationModel : Gestion des types d'opérations (dépôt, retrait, transfert)
 */
class TypeOperationModel extends Model
{
    protected $table            = 'types_operations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['code', 'libelle', 'description', 'actif'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'code'    => 'required|max_length[20]',
        'libelle' => 'required|max_length[100]',
        'actif'   => 'permit_empty|in_list[0,1]',
    ];

    /**
     * Récupère tous les types d'opérations actifs
     */
    public function getTypesActifs(): array
    {
        return $this->where('actif', 1)->findAll();
    }

    /**
     * Récupère un type d'opération par son code
     */
    public function getParCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }
}
