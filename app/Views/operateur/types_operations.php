<?= view('layouts/operateur_header', ['titre' => $titre]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="bi bi-grid-3x3-gap text-orange me-2"></i>Types d'Opérations</h1>
</div>

<div class="row g-4">
    <?php
    $typeConfig = [
        'DEPOT'     => ['icon' => 'bi-arrow-down-circle', 'color' => 'text-success', 'desc' => 'Dépôt automatique sans frais. Le client peut déposer de l\'argent à tout moment.'],
        'RETRAIT'   => ['icon' => 'bi-arrow-up-circle',   'color' => 'text-danger', 'desc' => 'Retrait avec frais selon le barème par tranche de montant.'],
        'TRANSFERT' => ['icon' => 'bi-send',               'color' => 'text-orange', 'desc' => 'Transfert vers un autre compte Orange. Frais à la charge de l\'expéditeur.'],
    ];
    ?>
    <?php foreach ($types as $type): ?>
    <?php $cfg = $typeConfig[$type['code']] ?? ['icon' => 'bi-circle', 'color' => 'text-secondary', 'desc' => '']; ?>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi <?= $cfg['icon'] ?> <?= $cfg['color'] ?> mb-3" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mb-1"><?= esc($type['libelle']) ?></h4>
                <div class="mb-3">
                    <span class="badge <?= $type['actif'] ? 'bg-success' : 'bg-danger' ?>">
                        <?= $type['actif'] ? 'Actif' : 'Inactif' ?>
                    </span>
                    <span class="badge bg-light text-dark border">Code: <?= esc($type['code']) ?></span>
                </div>
                <p class="text-muted small">
                    <?= $cfg['desc'] ?>
                </p>
            </div>
            <div class="card-footer bg-white border-top-0 text-center pb-4">
                <a href="/operateur/baremes" class="btn btn-outline-orange btn-sm">
                    <i class="bi bi-table me-1"></i> Gérer les barèmes
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card shadow-sm mt-5 bg-light border-0">
    <div class="card-body p-4 d-flex align-items-start">
        <i class="bi bi-info-circle text-info fs-3 me-3"></i>
        <div>
            <h5 class="fw-bold">Politique de frais Orange Money</h5>
            <p class="text-muted mb-0">
                Les <strong>dépôts</strong> sont toujours gratuits pour encourager l'alimentation des comptes.<br>
                Les <strong>retraits</strong> et <strong>transferts</strong> génèrent des frais calculés automatiquement selon le barème par tranche de montant. Ces frais constituent le gain de l'opérateur.
            </p>
        </div>
    </div>
</div>

<?= view('layouts/operateur_footer') ?>
