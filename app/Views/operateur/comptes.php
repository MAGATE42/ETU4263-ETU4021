<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-people text-orange me-2"></i>Comptes Clients</h1>
    <p class="text-muted mb-0 d-none d-md-block">Vue d'ensemble de tous les comptes enregistrés</p>
</div>

<?php
$totalSoldes       = array_sum(array_column($comptes, 'solde'));
$totalTransactions = array_sum(array_column($comptes, 'nb_transactions'));
$totalFrais        = array_sum(array_column($comptes, 'total_frais_generes'));
$actifs            = count(array_filter($comptes, fn($c) => $c['statut'] === 'actif'));
?>

<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-people text-secondary fs-4 me-2"></i>
                    <h6 class="card-title text-muted text-uppercase fw-bold mb-0" style="font-size: 0.8rem;">Total Comptes</h6>
                </div>
                <h3 class="mb-0 fw-bold"><?= count($comptes) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-person-check text-success fs-4 me-2"></i>
                    <h6 class="card-title text-muted text-uppercase fw-bold mb-0" style="font-size: 0.8rem;">Comptes Actifs</h6>
                </div>
                <h3 class="mb-0 fw-bold text-success"><?= $actifs ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-bank text-orange fs-4 me-2"></i>
                    <h6 class="card-title text-muted text-uppercase fw-bold mb-0" style="font-size: 0.8rem;">Soldes Cumulés</h6>
                </div>
                <h3 class="mb-0 fw-bold text-orange"><?= number_format($totalSoldes, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-graph-up text-warning fs-4 me-2"></i>
                    <h6 class="card-title text-muted text-uppercase fw-bold mb-0" style="font-size: 0.8rem;">Frais Générés</h6>
                </div>
                <h3 class="mb-0 fw-bold text-warning"><?= number_format($totalFrais, 0, ',', ' ') ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul text-orange me-2"></i>Liste des comptes</h5>
        
                <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher...">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="comptesTable">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">#</th>
                        <th>Client</th>
                        <th>Solde</th>
                        <th>Transactions</th>
                        <th>Frais générés</th>
                        <th>Statut</th>
                        <th>Inscrit le</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comptes)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun compte client enregistré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comptes as $c): ?>
                            <tr>
                                <td class="px-3 text-muted small"><?= $c['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-orange-light text-orange fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
                                            <?= strtoupper(substr($c['prenom'] ?: $c['telephone'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= esc(trim($c['prenom'] . ' ' . $c['nom'])) ?: '<span class="text-muted fst-italic">Anonyme</span>' ?></div>
                                            <div class="font-monospace text-muted small"><?= esc($c['telephone']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold <?= $c['solde'] > 0 ? 'text-success' : 'text-muted' ?>">
                                    <?= number_format($c['solde'], 0, ',', ' ') ?> Ar
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $c['nb_transactions'] ?></span>
                                </td>
                                <td class="fw-bold text-warning">
                                    <?= number_format($c['total_frais_generes'], 0, ',', ' ') ?> Ar
                                </td>
                                <td>
                                    <?php if ($c['statut'] === 'actif'): ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= ucfirst($c['statut']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?= $c['created_at'] ? date('d/m/Y', strtotime($c['created_at'])) : '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#comptesTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

<?= view('layouts/operateur_footer') ?>
