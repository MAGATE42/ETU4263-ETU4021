<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Création de la table des préfixes valables de l'opérateur Orange
 */
class CreatePrefixesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'prefixe' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'actif' => [
                'type'    => 'INTEGER',
                'default' => 1,
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
        $this->forge->createTable('prefixes');
    }

    public function down(): void
    {
        $this->forge->dropTable('prefixes');
    }
}
