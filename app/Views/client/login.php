<?= view('layouts/header', ['titre' => $titre ?? 'Orange Money - Connexion']) ?>

<!-- Navbar Client -->
<nav class="navbar navbar-expand-lg navbar-light navbar-orange">
<div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand navbar-brand-text" href="/client">
        <img src="<?= base_url('img/logo.png') ?>" alt="Orange Money" class="brand-logo">
        <span>Orange Money</span>
    </a>
    <span class="badge bg-orange-light text-orange border">
        Espace Client
    </span>
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
                        <input type="text" name="telephone" id="telephone" class="form-control form-control-lg" placeholder="ex: 0334263" required>
                        <div class="form-text">Préfixes acceptés : 032, 033, 037</div>
                    </div>
                    <button type="submit" class="btn btn-orange btn-lg w-100">Accéder à mon compte</button>
                </form>
            </div>

            <div class="card p-3 mt-4">
                <h6 class="text-muted text-uppercase fw-bold mb-3">Comptes de démonstration</h6>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary text-start" onclick="document.getElementById('telephone').value='0334263'">
                        Personne 1 : <strong>0334263</strong> (Jean RAKOTO)
                    </button>
                    <button type="button" class="btn btn-outline-secondary text-start" onclick="document.getElementById('telephone').value='0334021'">
                        Personne 2 : <strong>0334021</strong> (Marie RABE)
                    </button>
                </div>
            </div>

        <div class="d-flex align-items-center">
            <a href="/operateur" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-person-circle me-1"></i>Espace opérateur
            </a>
        </div>
        </div>
    </div>
</main>

<?= view('layouts/footer') ?>
