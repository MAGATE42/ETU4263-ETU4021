<?= view('layouts/header', ['titre' => $titre ?? 'Historiques - Orange Money']) ?>

<nav class="navbar navbar-expand-lg navbar-light navbar-orange mb-4">
    <div class="container">
        <a class="navbar-brand navbar-brand-text" href="/client/dashboard">
            <img src="/img/logo.png" alt="Orange Money" class="brand-logo">
            Orange Money
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-orange-light text-orange border px-3 py-2">
                <i class="bi bi-phone me-1"></i><?= esc($compte['telephone']) ?>
            </span>
            <a href="/client/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house me-1"></i>Accueil</a>
            <a href="/client/deconnexion" class="btn btn-danger btn-sm">Quitter</a>
        </div>
    </div>
</nav>

<main class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Mes Historiques</h2>
            <p class="text-muted">Toutes les opérations de votre compte <?= esc($compte['telephone']) ?></p>
        </div>
    </div>

    <div class="card p-4">
        <?php if (empty($historique)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3">Aucune transaction enregistrée</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Opération</th>
                            <th>Montant</th>
                            <th>Frais</th>
                            <th>Détail</th>
                            <th>Statut</th>
                            <th>Date & Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historique as $t): ?>
                        <?php
                            $isDepot = $t['type_code'] === 'DEPOT' || str_starts_with($t['reference'], 'REC-');
                            $color = $isDepot ? 'text-success' : 'text-danger';
                            $sign = $isDepot ? '+' : '-';
                            
                            $icons = [
                                'DEPOT' => 'bi-arrow-down-circle',
                                'RETRAIT' => 'bi-arrow-up-circle',
                                'TRANSFERT' => 'bi-send'
                            ];
                            $icon = $icons[$t['type_code']] ?? 'bi-circle';
                        ?>
                        <tr>
                            <td><small class="text-muted"><?= esc(substr($t['reference'],0,15)) ?></small></td>
                            <td>
                                <i class="bi <?= $icon ?> <?= $color ?> me-2"></i><?= esc($t['type_libelle']) ?>
                                <?php if (str_starts_with($t['reference'], 'REC-')): ?>
                                    <span class="badge bg-success ms-1">Reçu</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold <?= $color ?>">
                                <?= $sign ?><?= number_format($t['montant'], 0, ',', ' ') ?> Ar
                            </td>
                            <td>
                                <?= $t['frais'] > 0 ? number_format($t['frais'], 0, ',', ' ') . ' Ar' : '<span class="text-muted">—</span>' ?>
                            </td>
                            <td class="text-muted small">
                                <?php if ($t['type_code'] === 'TRANSFERT' && $t['dest_telephone']): ?>
                                    Vers <?= esc($t['dest_telephone']) ?>
                                    <?= $t['dest_nom'] ? '(' . esc($t['dest_nom']) . ')' : '' ?>
                                <?php elseif (!empty($t['note'])): ?>
                                    <?= esc($t['note']) ?>
                                <?php else: ?>—
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['statut'] === 'success'): ?>
                                    <span class="badge bg-success">Réussi</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Échoué</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?= view('layouts/footer') ?>
