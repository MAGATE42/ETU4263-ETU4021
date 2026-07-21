<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Création de la table des comptes clients (utilisateurs)
 */
class CreateComptesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'telephone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'nom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'prenom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'solde' => [
                'type'    => 'REAL',
                'null'    => false,
                'default' => 0,
            ],
            'pourcentage_epargne' => [
                'type'    => 'REAL',
                'null'    => false,
                'default' => 0,
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'actif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('telephone');
        $this->forge->createTable('comptes');
    }

    public function down(): void
    {
        $this->forge->dropTable('comptes');
    }
}
