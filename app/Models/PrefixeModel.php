<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PrefixeModel : Gestion des préfixes valables de l'opérateur Orange
 */
class PrefixeModel extends Model
{
    protected $table            = 'prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['prefixe', 'description', 'actif'];
    protected $useTimestamps    = true;

    // ─── Validation ──────────────────────────────────────────────────
    protected $validationRules = [
        'prefixe'     => 'required|min_length[2]|max_length[10]',
        'description' => 'permit_empty|max_length[100]',
        'actif'       => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'required'   => 'Le préfixe est obligatoire.',
            'min_length' => 'Le préfixe doit avoir au moins 2 caractères.',
        ],
    ];

    /**
     * Récupère tous les préfixes actifs
     */
    public function getPrefixesActifs(): array
    {
        return $this->where('actif', 1)->findAll();
    }

    /**
     * Vérifie si un numéro de téléphone commence par un préfixe valide
     */
    public function estPrefixeValide(string $telephone): bool
    {
        $prefixes = $this->getPrefixesActifs();
        foreach ($prefixes as $p) {
            if (str_starts_with($telephone, $p['prefixe'])) {
                return true;
            }
        }
        return false;
    }
}
