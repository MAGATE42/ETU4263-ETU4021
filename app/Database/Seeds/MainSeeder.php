<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder principal : peuple toutes les tables de données initiales
 * Simule deux personnes : Personne 1 (4263) et Personne 2 (4021)
 */
class MainSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Préfixes Orange ────────────────────────────────────────
        $prefixes = [
            ['prefixe' => '032', 'description' => 'Orange Madagascar - 032', 'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['prefixe' => '033', 'description' => 'Orange Madagascar - 033', 'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['prefixe' => '037', 'description' => 'Orange Madagascar - 037', 'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('prefixes')->insertBatch($prefixes);

        // ─── 2. Types d'opérations ────────────────────────────────────
        $types = [
            ['code' => 'DEPOT',     'libelle' => 'Dépôt',     'description' => 'Dépôt d\'argent sur le compte',       'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['code' => 'RETRAIT',   'libelle' => 'Retrait',   'description' => 'Retrait d\'argent du compte',           'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['code' => 'TRANSFERT', 'libelle' => 'Transfert', 'description' => 'Transfert d\'argent vers un autre compte', 'actif' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('types_operations')->insertBatch($types);

        // ─── 3. Barèmes de frais ───────────────────────────────────────
        // Récupération des IDs
        $retrait   = $this->db->table('types_operations')->where('code', 'RETRAIT')->get()->getRow();
        $transfert = $this->db->table('types_operations')->where('code', 'TRANSFERT')->get()->getRow();

        $tranchesRetrait = [
            ['montant_min' => 100,       'montant_max' => 1000,      'frais' => 50],
            ['montant_min' => 1001,      'montant_max' => 5000,      'frais' => 50],
            ['montant_min' => 5001,      'montant_max' => 10000,     'frais' => 100],
            ['montant_min' => 10001,     'montant_max' => 25000,     'frais' => 200],
            ['montant_min' => 25001,     'montant_max' => 50000,     'frais' => 400],
            ['montant_min' => 50001,     'montant_max' => 100000,    'frais' => 800],
            ['montant_min' => 100001,    'montant_max' => 250000,    'frais' => 1500],
            ['montant_min' => 250001,    'montant_max' => 500000,    'frais' => 1500],
            ['montant_min' => 500001,    'montant_max' => 1000000,   'frais' => 2500],
            ['montant_min' => 1000001,   'montant_max' => 2000000,   'frais' => 3000],
        ];

        $tranchesTransfert = [
            ['montant_min' => 100,       'montant_max' => 1000,      'frais' => 50],
            ['montant_min' => 1001,      'montant_max' => 5000,      'frais' => 50],
            ['montant_min' => 5001,      'montant_max' => 10000,     'frais' => 100],
            ['montant_min' => 10001,     'montant_max' => 25000,     'frais' => 200],
            ['montant_min' => 25001,     'montant_max' => 50000,     'frais' => 400],
            ['montant_min' => 50001,     'montant_max' => 100000,    'frais' => 800],
            ['montant_min' => 100001,    'montant_max' => 250000,    'frais' => 1500],
            ['montant_min' => 250001,    'montant_max' => 500000,    'frais' => 1500],
            ['montant_min' => 500001,    'montant_max' => 1000000,   'frais' => 2500],
            ['montant_min' => 1000001,   'montant_max' => 2000000,   'frais' => 3000],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($tranchesRetrait as $t) {
            $this->db->table('baremes_frais')->insert(array_merge($t, ['type_operation_id' => $retrait->id, 'created_at' => $now]));
        }
        foreach ($tranchesTransfert as $t) {
            $this->db->table('baremes_frais')->insert(array_merge($t, ['type_operation_id' => $transfert->id, 'created_at' => $now]));
        }

        // ─── 4. Comptes simulés : Personne 1 (4263) et Personne 2 (4021) ─
        $comptes = [
            [
                'telephone'  => '0334263',
                'nom'        => 'RAKOTO',
                'prenom'     => 'Jean',
                'solde'      => 150000.00,
                'statut'     => 'actif',
                'created_at' => $now,
            ],
            [
                'telephone'  => '0334021',
                'nom'        => 'RABE',
                'prenom'     => 'Marie',
                'solde'      => 75000.00,
                'statut'     => 'actif',
                'created_at' => $now,
            ],
        ];
        $this->db->table('comptes')->insertBatch($comptes);
    }
}
