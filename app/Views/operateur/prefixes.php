<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-telephone text-orange me-2"></i>Préfixes Valides</h1>
</div>

<div class="row">
        <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-orange me-2"></i>Ajouter un préfixe</h5>
            </div>
            <div class="card-body">
                <form action="/operateur/prefixes/ajouter" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Préfixe</label>
                        <input type="text" name="prefixe" class="form-control" placeholder="ex: 032" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description (optionnel)</label>
                        <input type="text" name="description" class="form-control" placeholder="ex: Orange Mada">
                    </div>
                    <button type="submit" class="btn btn-orange w-100">Ajouter</button>
                </form>
            </div>
        </div>
    </div>

        <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list text-orange me-2"></i>Préfixes enregistrés</h5>
                <span class="badge bg-secondary"><?= count($prefixes) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">#</th>
                                <th>Préfixe</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th class="text-end px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prefixes)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucun préfixe configuré</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($prefixes as $p): ?>
                                    <tr>
                                        <td class="px-3 text-muted"><?= $p['id'] ?></td>
                                        <td class="fw-bold font-monospace fs-5"><?= esc($p['prefixe']) ?></td>
                                        <td class="text-muted"><?= esc($p['description'] ?: '—') ?></td>
                                        <td>
                                            <?php if ($p['actif']): ?>
                                                <span class="badge bg-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end px-3">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="/operateur/prefixes/toggle/<?= $p['id'] ?>" method="POST" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Activer/Désactiver">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form action="/operateur/prefixes/supprimer/<?= $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe ?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/operateur_footer') ?>
