<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-speedometer2 text-orange me-2"></i>Tableau de bord</h1>
</div>

<!-- Statistiques principales -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase fw-bold">Comptes Clients</h6>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <h2 class="mb-0 fw-bold"><?= number_format($nb_comptes, 0, ',', ' ') ?></h2>
                    <i class="bi bi-people-fill text-success fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase fw-bold">Transactions</h6>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <h2 class="mb-0 fw-bold"><?= number_format($nb_transactions, 0, ',', ' ') ?></h2>
                    <i class="bi bi-arrow-left-right text-orange fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase fw-bold">Gains Totaux</h6>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <h2 class="mb-0 fw-bold text-warning"><?= number_format($total_gain, 0, ',', ' ') ?> Ar</h2>
                    <i class="bi bi-graph-up-arrow text-warning fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted text-uppercase fw-bold">Soldes Cumulés</h6>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <?php $soldesTotaux = array_sum(array_column($comptes, 'solde')); ?>
                    <h2 class="mb-0 fw-bold text-info"><?= number_format($soldesTotaux, 0, ',', ' ') ?> Ar</h2>
                    <i class="bi bi-bank text-info fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Dernières transactions -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-activity text-orange me-2"></i>Dernières opérations</h5>
                <a href="/operateur/gains" class="btn btn-sm btn-outline-orange">Tout voir</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Client</th>
                                <th>Opération</th>
                                <th>Montant</th>
                                <th>Frais perçus</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucune transaction</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                    <tr>
                                        <td class="px-3">
                                            <div class="fw-bold"><?= esc(trim($t['compte_prenom'] . ' ' . $t['compte_nom'])) ?></div>
                                            <div class="small text-muted"><?= esc($t['compte_telephone']) ?></div>
                                        </td>
                                        <td><?= esc($t['type_libelle']) ?></td>
                                        <td class="fw-bold"><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                                        <td>
                                            <?php if ($t['frais'] > 0): ?>
                                                <span class="badge bg-warning text-dark">+<?= number_format($t['frais'], 0, ',', ' ') ?> Ar</span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition des gains -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-orange me-2"></i>Répartition des gains</h5>
            </div>
            <div class="card-body">
                <?php if (empty($gains)): ?>
                    <div class="text-center text-muted py-4">Aucun gain enregistré</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($gains as $g): ?>
                            <?php 
                                $pct = $total_gain > 0 ? round(($g['total_frais'] / $total_gain) * 100) : 0; 
                                $isRetrait = $g['type_code'] === 'RETRAIT';
                                $colorClass = $isRetrait ? 'text-danger' : 'text-orange';
                                $bgClass = $isRetrait ? 'bg-danger' : 'bg-orange';
                            ?>
                            <li class="list-group-item px-0 py-3 border-bottom-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="<?= $colorClass ?>"><?= esc($g['type_libelle']) ?></strong>
                                    <span class="fw-bold"><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar <?= $bgClass ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted d-block mt-1"><?= $pct ?>% du total (<?= $g['nb_transactions'] ?> opérations)</small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/operateur_footer') ?>
