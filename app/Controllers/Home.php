<?php

namespace App\Controllers;

/**
 * Home : Redirige vers l'espace opérateur par défaut
 */
class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        return redirect()->to('/operateur');
    }
}
