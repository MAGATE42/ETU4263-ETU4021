<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-graph-up-arrow text-orange me-2"></i>Situation des Gains</h1>
    <p class="text-muted mb-0 d-none d-md-block">Revenus générés via les frais de retrait et de transfert</p>
</div>

<?php
$gainRetrait   = 0; $nbRetrait   = 0;
$gainTransfert = 0; $nbTransfert = 0;
foreach ($gains as $g) {
    if ($g['type_code'] === 'RETRAIT')   { $gainRetrait   = $g['total_frais']; $nbRetrait   = $g['nb_transactions']; }
    if ($g['type_code'] === 'TRANSFERT') { $gainTransfert = $g['total_frais']; $nbTransfert = $g['nb_transactions']; }
}
?>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body py-4">
                <i class="bi bi-currency-exchange text-warning mb-3 d-block" style="font-size: 2.5rem;"></i>
                <h6 class="text-muted text-uppercase fw-bold">Gain Total</h6>
                <h2 class="text-warning fw-bold mb-0"><?= number_format($total_gain, 0, ',', ' ') ?> Ar</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body py-4">
                <i class="bi bi-arrow-up-circle text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                <h6 class="text-muted text-uppercase fw-bold">Gains Retraits</h6>
                <h2 class="text-danger fw-bold mb-1"><?= number_format($gainRetrait, 0, ',', ' ') ?> Ar</h2>
                <small class="text-muted"><?= $nbRetrait ?> opérations</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center h-100">
            <div class="card-body py-4">
                <i class="bi bi-send text-orange mb-3 d-block" style="font-size: 2.5rem;"></i>
                <h6 class="text-muted text-uppercase fw-bold">Gains Transferts</h6>
                <h2 class="text-orange fw-bold mb-1"><?= number_format($gainTransfert, 0, ',', ' ') ?> Ar</h2>
                <small class="text-muted"><?= $nbTransfert ?> opérations</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-orange me-2"></i>Détail des opérations payantes</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <?php $txPayantes = array_filter($transactions, fn($t) => $t['frais'] > 0); ?>
            
            <?php if (empty($txPayantes)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-3">Aucune opération payante enregistrée.</p>
                </div>
            <?php else: ?>
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Référence</th>
                            <th>Client</th>
                            <th>Opération</th>
                            <th>Montant</th>
                            <th>Frais perçus</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($txPayantes as $t): ?>
                            <?php
                                $isRetrait = $t['type_code'] === 'RETRAIT';
                                $fcolor    = $isRetrait ? 'text-danger' : 'text-orange';
                                $ficon     = $isRetrait ? 'bi-arrow-up-circle' : 'bi-send';
                            ?>
                            <tr>
                                <td class="px-3 text-muted small"><?= esc(substr($t['reference'],0,15)) ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc(trim($t['compte_prenom'] . ' ' . $t['compte_nom'])) ?></div>
                                    <div class="small text-muted"><?= esc($t['compte_telephone']) ?></div>
                                </td>
                                <td>
                                    <i class="bi <?= $ficon ?> <?= $fcolor ?> me-1"></i>
                                    <span><?= esc($t['type_libelle']) ?></span>
                                    <?php if ($t['dest_telephone']): ?>
                                        <div class="small text-muted">→ <?= esc($t['dest_telephone']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark">
                                    <?= number_format($t['montant'], 0, ',', ' ') ?> Ar
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                        +<?= number_format($t['frais'], 0, ',', ' ') ?> Ar
                                    </span>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('layouts/operateur_footer') ?>
