<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-gear text-orange me-2"></i>Configurations Globales</h1>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-sliders text-orange me-2"></i>Paramètres des Transferts</h5>
            </div>
            <div class="card-body">
                <form action="/operateur/configurations/sauvegarder" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Commission transfert vers autres opérateurs (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="commission_externe" class="form-control" value="<?= esc($commission_externe) ?>" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Ce pourcentage sera ajouté en supplément pour les transferts vers les autres opérateurs.</div>
                    </div>
                    <button type="submit" class="btn btn-orange">Sauvegarder</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/operateur_footer') ?>
