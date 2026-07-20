<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Création de la table des transactions/opérations
 */
class CreateTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'reference' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'compte_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'type_operation_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'montant' => [
                'type' => 'REAL',
                'null' => false,
            ],
            'frais' => [
                'type'    => 'REAL',
                'null'    => false,
                'default' => 0,
            ],
            // Pour les transferts : compte destinataire
            'compte_destinataire_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'statut' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'success',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('reference');
        $this->forge->createTable('transactions');
    }

    public function down(): void
    {
        $this->forge->dropTable('transactions');
    }
}
