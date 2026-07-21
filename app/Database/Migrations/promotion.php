<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
class AuthentificationPromotion extends Migration
{
    public function up(){
        $this->db->table('configuration')->inert([
            'clé' => 'commission_transfert',
            'valeur' => '0',
            'created_at' => date('Y-m-d'),
        ]);
        $this->forge ->addColum('baremes-frais,[
        'type _frais=> [
            'type'=>'VARCHAR',
            'constraint'=20,
            'default => 'fixe',
            'after' =>'frais',
        ];
        ]');
    }
    public function down ()
    {
        $this->db->table('configuration')-> where('cle','commission_transfert')-delete();
        $this->forge->dropDown('baremes-frais','type_frais');
    }
}   