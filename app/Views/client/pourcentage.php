<?= view('layouts/header', ['titre' => $titre ?? 'Historiques - Orange Money']) ?>

<!-- Navbar Client -->
 <div class="card-body">
                <form action="/client/configurations/sauvegarder" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Commission transfert vers autres opérateurs (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="pourcentage_epargne" class="form-control" value="" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Ce pourcentage sera ajouté en configuration du compte epargne</div>
                    </div>
                    <button type="submit" class="btn btn-orange">Sauvegarder</button>
                </form>
            </div>
<?= view('layouts/footer') ?>
