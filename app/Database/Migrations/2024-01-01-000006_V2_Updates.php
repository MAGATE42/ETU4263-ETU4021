<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Mises à jour pour la Version 2 (multi-opérateurs)
 */
class V2Updates extends Migration
{
    public function up(): void
    {
        // 1. Ajouter est_autre_operateur à prefixes
        $this->forge->addColumn('prefixes', [
            'est_autre_operateur' => [
                'type'    => 'INTEGER',
                'default' => 0,
            ],
        ]);

        // 2. Ajouter commission_autre_operateur à transactions
        $this->forge->addColumn('transactions', [
            'commission_autre_operateur' => [
                'type'    => 'REAL',
                'default' => 0,
            ],
        ]);

        // 3. Créer la table configurations
        $this->forge->addField([
            'cle' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'valeur' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addKey('cle', true);
        $this->forge->createTable('configurations');
    }

    public function down(): void
    {
        $this->forge->dropColumn('prefixes', 'est_autre_operateur');
        $this->forge->dropColumn('transactions', 'commission_autre_operateur');
        $this->forge->dropTable('configurations');
    }
}
