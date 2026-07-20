<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ConfigurationModel : Gestion des configurations globales
 */
class ConfigurationModel extends Model
{
    protected $table            = 'configurations';
    protected $primaryKey       = 'cle';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['cle', 'valeur'];
    protected $useTimestamps    = true;

    /**
     * Récupère la valeur d'une configuration par sa clé
     */
    public function getValeur(string $cle, $defaut = null)
    {
        $config = $this->find($cle);
        return $config ? $config['valeur'] : $defaut;
    }

    /**
     * Définit ou met à jour la valeur d'une configuration
     */
    public function setValeur(string $cle, string $valeur)
    {
        $existe = $this->find($cle);
        if ($existe) {
            return $this->update($cle, ['valeur' => $valeur]);
        } else {
            return $this->insert(['cle' => $cle, 'valeur' => $valeur]);
        }
    }
}
