<?= view('layouts/header', ['titre' => $titre ?? 'Orange Money - Connexion']) ?>

<nav class="navbar navbar-expand-lg navbar-light navbar-orange">
    <div class="container">
        <a class="navbar-brand navbar-brand-text" href="/client">
            <img src="/img/logo.png" alt="Orange Money" class="brand-logo">
            Orange Money
        </a>
        <span class="badge bg-orange-light text-orange border">Espace Client</span>
    </div>
</nav>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <h1 class="page-title text-orange">Bienvenue</h1>
                <p class="text-muted">Connectez-vous avec votre numéro de téléphone Orange</p>
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

            <div class="card p-4">
                <form action="/client/login" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="telephone" class="form-label fw-bold">Numéro de téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control form-control-lg" placeholder="ex: 0331402571" required>
                        <div class="form-text">Préfixes acceptés : 032, 033, 037</div>
                    </div>
                    <button type="submit" class="btn btn-orange btn-lg w-100">Accéder à mon compte</button>
                </form>
            </div>

            <div class="text-center mt-4">
                <a href="/operateur" class="text-muted text-decoration-none">
                    <i class="bi bi-shield-lock me-1"></i> Accès espace opérateur
                </a>
            </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>
