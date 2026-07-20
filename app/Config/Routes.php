<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ── Page d'accueil ──────────────────────────────────────────────────
$routes->get('/', 'Home::index');

// ════════════════════════════════════════════════════════════════════
// CÔTÉ CLIENT – UtilisateurController
// ════════════════════════════════════════════════════════════════════
$routes->group('client', function ($routes) {
    $routes->get('/',                     'UtilisateurController::index');
    $routes->post('login',                'UtilisateurController::verifierUtilisateur');
    $routes->get('dashboard',             'UtilisateurController::dashboard');
    $routes->post('depot',                'UtilisateurController::faireDepot');
    $routes->post('retrait',              'UtilisateurController::faireRetrait');
    $routes->post('transfert',            'UtilisateurController::faireTransfert');
    $routes->get('historiques',           'UtilisateurController::voirHistoriques');
    $routes->get('deconnexion',           'UtilisateurController::deconnexion');
});

// ════════════════════════════════════════════════════════════════════
// CÔTÉ OPÉRATEUR – OperateurController
// ════════════════════════════════════════════════════════════════════
$routes->group('operateur', function ($routes) {
    $routes->get('/',                     'OperateurController::index');
    $routes->get('prefixes',              'OperateurController::gererPrefixes');
    $routes->post('prefixes/ajouter',     'OperateurController::ajouterPrefixe');
    $routes->post('prefixes/supprimer/(:num)', 'OperateurController::supprimerPrefixe/$1');
    $routes->post('prefixes/toggle/(:num)',    'OperateurController::togglePrefixe/$1');

    $routes->get('types',                 'OperateurController::gererTypesOperations');

    $routes->get('baremes',               'OperateurController::gererBaremes');
    $routes->post('baremes/ajouter',      'OperateurController::ajouterBareme');
    $routes->post('baremes/supprimer/(:num)', 'OperateurController::supprimerBareme/$1');

    $routes->get('gains',                 'OperateurController::situationGains');
    $routes->get('comptes',               'OperateurController::situationComptes');
});
