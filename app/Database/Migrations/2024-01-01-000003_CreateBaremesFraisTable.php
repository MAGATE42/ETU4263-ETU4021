<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Création de la table des barèmes de frais par tranche
 */
class CreateBaremesFraisTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'type_operation_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'montant_min' => [
                'type'    => 'REAL',
                'null'    => false,
                'default' => 0,
            ],
            'montant_max' => [
                'type' => 'REAL',
                'null' => false,
            ],
            'frais' => [
                'type'    => 'REAL',
                'null'    => false,
                'default' => 0,
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
        $this->forge->createTable('baremes_frais');
    }

    public function down(): void
    {
        $this->forge->dropTable('baremes_frais');
    }
}
