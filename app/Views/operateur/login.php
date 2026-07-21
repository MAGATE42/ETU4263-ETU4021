<?= view('layouts/header', ['titre' => $titre ?? 'Orange Money - Connexion opérateur']) ?>
<nav class="navbar navbar-expand-lg navbar-light navbar-orange">
<div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand navbar-brand-text" href="/client">
        <img src="<?= base_url('img/logo.png') ?>" alt="Orange Money" class="brand-logo">
        <span>Orange Money</span>
    </a>
    <span class="badge bg-orange-light text-orange border">
        Espace Operateur
    </span>
</div>
</nav>
<main class="operateur-login-page py-4">
    <div class="container py-3">
        <div class="row justify-content-center align-items-start pt-3 pb-4">
            <div class="col-lg-10 col-xl-9">
                <div class="row g-0 operateur-login-card overflow-hidden">
                    <div class="col-lg-6 operateur-login-image">
                        <img src="<?= base_url('img/orange.png') ?>" alt="Orange" class="operateur-login-illustration">
                    </div>
                    <div class="col-lg-6 bg-white p-5">
                        <div class="mb-4">
                            <h2 class="mt-3 mb-2">Se connecter</h2>
                            <p class="text-muted mb-0">Utilisez vos identifiants opérateur pour continuer.</p>
                        </div>

                        <?php if (session()->getFlashdata('erreur')): ?>
                            <div class="alert alert-danger">
                                <?= esc(session()->getFlashdata('erreur')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= esc(session()->getFlashdata('success')) ?>
                            </div>
                        <?php endif; ?>

                        <form action="/operateur/login" method="POST" class="mt-4">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="identifiant" class="form-label fw-bold">Identifiant</label>
                                <input type="text" name="identifiant" id="identifiant" class="form-control form-control-lg" placeholder="operateur" required>
                            </div>
                            <div class="mb-4">
                                <label for="mot_de_passe" class="form-label fw-bold">Mot de passe</label>
                                <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control form-control-lg" placeholder="••••••" required>
                            </div>
                            <button type="submit" class="btn btn-orange btn-lg w-100">Accéder au dashboard</button>
                        </form>

                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <a href="/client" class="btn btn-outline-secondary btn-sm">Espace client</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>