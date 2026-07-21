<?php
$currentUrl = current_url();
function isActive(string $path): string {
    return str_contains(current_url(), $path) ? 'active' : '';
}
?>
<?= view('layouts/header', ['titre' => $titre ?? 'Orange Money - Opérateur']) ?>

<!-- Navbar Opérateur -->
<nav class="navbar navbar-expand-lg navbar-light navbar-orange">
    <div class="container-fluid">
        <a class="navbar-brand navbar-brand-text" href="/client">
            <img src="<?= base_url('img/logo.png') ?>" alt="Orange Money" class="brand-logo">
            <span>Orange Money</span>
        </a>
        <div class="d-flex align-items-center">
            <a href="/operateur/deconnexion" class="btn btn-danger btn-sm me-2">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </a>
            <a href="/client" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-person-circle me-1"></i>Espace client
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky">
                <div class="sidebar-section">Navigation</div>
                <a href="/operateur" class="sidebar-link <?= rtrim(parse_url(current_url(), PHP_URL_PATH), '/') === '/operateur' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                </a>

                <div class="sidebar-section mt-3">Configuration</div>
                <a href="/operateur/prefixes" class="sidebar-link <?= isActive('prefixes') ? 'active' : '' ?>">
                    <i class="bi bi-telephone me-2"></i> Préfixes
                </a>
                <a href="/operateur/types" class="sidebar-link <?= isActive('types') ? 'active' : '' ?>">
                    <i class="bi bi-grid-3x3-gap me-2"></i> Opérations
                </a>
                <a href="/operateur/baremes" class="sidebar-link <?= isActive('baremes') ? 'active' : '' ?>">
                    <i class="bi bi-table me-2"></i> Barèmes de frais
                </a>
                <a href="/operateur/configurations" class="sidebar-link <?= isActive('configurations') ? 'active' : '' ?>">
                    <i class="bi bi-sliders me-2"></i> Configurations
                </a>

                <div class="sidebar-section mt-3">Rapports</div>
                <a href="/operateur/gains" class="sidebar-link <?= isActive('gains') ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow me-2"></i> Situation gains
                </a>
                <a href="/operateur/comptes" class="sidebar-link <?= isActive('comptes') ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i> Comptes clients
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('erreur')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
