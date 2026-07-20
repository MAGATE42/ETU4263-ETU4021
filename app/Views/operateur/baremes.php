<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-table text-orange me-2"></i>Barèmes de Frais</h1>
</div>

<div class="row">
        <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-orange me-2"></i>Ajouter une tranche</h5>
            </div>
            <div class="card-body">
                <form action="/operateur/baremes/ajouter" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type d'opération</label>
                        <select name="type_operation_id" class="form-select" required>
                            <?php foreach ($types as $t): ?>
                                <?php if ($t['code'] !== 'DEPOT'): ?>
                                    <option value="<?= $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant minimum (Ar)</label>
                        <input type="number" name="montant_min" class="form-control" placeholder="ex: 100" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant maximum (Ar)</label>
                        <input type="number" name="montant_max" class="form-control" placeholder="ex: 1000" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Frais (Ar)</label>
                        <input type="number" name="frais" class="form-control" placeholder="ex: 50" min="0" required>
                    </div>

                    <button type="submit" class="btn btn-orange w-100">Ajouter la tranche</button>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info mt-3 d-flex align-items-center">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <strong>Dépôts gratuits</strong><br>
                Les dépôts sont toujours sans frais. Aucun barème n'est nécessaire.
            </div>
        </div>
    </div>

        <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list text-orange me-2"></i>Barèmes configurés</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Type</th>
                                <th>Tranche (Ar)</th>
                                <th>Frais (Ar)</th>
                                <th class="text-end px-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($baremes)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aucun barème configuré</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($baremes as $b): ?>
                                    <?php
                                        $isRetrait = $b['type_code'] === 'RETRAIT';
                                        $bcolor = $isRetrait ? 'text-danger' : 'text-orange';
                                        $badgeClass = $isRetrait ? 'bg-danger' : 'bg-orange';
                                    ?>
                                    <tr>
                                        <td class="px-3">
                                            <span class="badge <?= $badgeClass ?>"><?= esc($b['type_libelle']) ?></span>
                                        </td>
                                        <td class="fw-bold text-muted">
                                            <?= number_format($b['montant_min'], 0, ',', ' ') ?> — <?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar
                                        </td>
                                        <td class="fw-bold <?= $bcolor ?>">
                                            <?= number_format($b['frais'], 0, ',', ' ') ?> Ar
                                        </td>
                                        <td class="text-end px-3">
                                            <form action="/operateur/baremes/supprimer/<?= $b['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tranche ?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </form>
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
