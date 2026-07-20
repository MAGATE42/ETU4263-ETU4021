<?= view('layouts/header', ['titre' => $titre ?? 'Mon Compte - Orange Money']) ?>

<!-- Navbar Client -->
<nav class="navbar navbar-expand-lg navbar-light navbar-orange mb-4">
    <div class="container">
        <a class="navbar-brand navbar-brand-text" href="/client">
            <img src="<?= base_url('img/logo.png') ?>" alt="Orange Money" class="brand-logo">
            <span>Orange Money</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-orange-light text-orange border px-3 py-2">
                <i class="bi bi-phone me-1"></i><?= esc($compte['telephone']) ?>
            </span>
            <a href="/client/historiques" class="btn btn-outline-secondary btn-sm d-none d-md-inline-block">Historique</a>
            <a href="/client/deconnexion" class="btn btn-danger btn-sm">Quitter</a>
        </div>
    </div>
</nav>

<main class="container">
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('erreur')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Solde -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card p-4 shadow-sm border-0" style="background-color: var(--orange-light);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-muted mb-1">Bonjour,</h5>
                        <h2 class="fw-bold mb-0 text-dark"><?= esc(trim($compte['prenom'] . ' ' . $compte['nom'])) ?></h2>
                    </div>
                    <div class="text-end">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.8rem;">Solde disponible</div>
                        <h1 class="text-orange fw-bold mb-0"><?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</h1>
                        <a href="/client/dashboard" class="btn btn-sm btn-outline-orange mt-2">
                            <i class="bi bi-arrow-clockwise"></i> Actualiser
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne Actions (Formulaires) -->
        <div class="col-md-7">
            
            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-arrow-down-circle text-success me-2"></i>Faire un dépôt</h5>
                <form action="/client/depot" method="POST">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input type="number" name="montant" class="form-control" placeholder="Montant (Ar)" min="100" required>
                        <button class="btn btn-success" type="submit">Déposer</button>
                    </div>
                    <small class="text-muted">Les dépôts sont gratuits (0 Ar de frais).</small>
                </form>
            </div>

            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-arrow-up-circle text-danger me-2"></i>Faire un retrait</h5>
                <form action="/client/retrait" method="POST">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input type="number" name="montant" class="form-control" placeholder="Montant à retirer (Ar)" min="100" required>
                        <button class="btn btn-danger" type="submit">Retirer</button>
                    </div>
                    <small class="text-muted">Des frais s'appliqueront selon le barème de retrait.</small>
                </form>
            </div>

            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-send text-orange me-2"></i>Faire un transfert</h5>
                <form action="/client/transfert" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Numéro(s) destinataire(s)</label>
                        <div id="destinatairesContainer">
                            <div class="input-group mb-2 dest-row">
                                <input type="text" name="telephone_destinataire[]" class="form-control tel-input" placeholder="N° destinataire" required>
                                <button type="button" class="btn btn-outline-danger btn-remove-dest d-none"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAddDest">+ Ajouter un numéro</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Montant Total (Ar)</label>
                        <input type="number" name="montant" class="form-control" placeholder="Montant à diviser (Ar)" min="100" required>
                    </div>
                    <div class="mb-3 form-check" id="containerFraisRetrait">
                        <input type="checkbox" name="inclure_frais_retrait" value="1" class="form-check-input" id="checkFraisRetrait">
                        <label class="form-check-label small" for="checkFraisRetrait">Inclure les frais de retrait pour le destinataire</label>
                        <div id="infoFrais" class="form-text text-info d-none">Non applicable pour les autres opérateurs.</div>
                    </div>
                    <button class="btn btn-orange w-100" type="submit">Transférer l'argent</button>
                    <small class="text-muted d-block mt-2 text-center">Frais à la charge de l'expéditeur.</small>
                </form>
            </div>

        </div>

        <!-- Colonne Historique récent -->
        <div class="col-md-5">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Dernières opérations</h5>
                        <a href="/client/historique" class="btn btn-sm btn-outline-orange mt-2">voir plus</a>
                </div>
                
                <?php if (empty($historique)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">Aucune transaction pour le moment.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
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
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark"><i class="bi <?= $icon ?> <?= $color ?> me-2"></i><?= esc($t['type_libelle']) ?></strong>
                                <strong class="<?= $color ?>"><?= $sign ?><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                <span class="text-muted">
                                    <?php if ($t['type_code'] === 'TRANSFERT' && $t['dest_telephone']): ?>
                                        vers <?= esc($t['dest_telephone']) ?>
                                    <?php elseif (str_starts_with($t['reference'], 'REC-')): ?>
                                        <span class="text-success">Reçu</span>
                                    <?php else: ?>
                                        <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                                    <?php endif; ?>
                                </span>
                                <span class="text-muted">
                                    <?= $t['frais'] > 0 ? 'Frais: ' . number_format($t['frais'], 0, ',', ' ') . ' Ar' : '' ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    const prefixesStr = '<?= json_encode($prefixes) ?>';
    const prefixes = JSON.parse(prefixesStr);

    const container = document.getElementById('destinatairesContainer');
    const btnAdd = document.getElementById('btnAddDest');
    const checkFrais = document.getElementById('checkFraisRetrait');
    const infoFrais = document.getElementById('infoFrais');

    function checkOperators() {
        let hasOtherOperator = false;
        const inputs = document.querySelectorAll('.tel-input');
        
        inputs.forEach(input => {
            let val = input.value.trim();
            if (val.length > 0) {
                let prefixObj = prefixes.find(p => val.startsWith(p.prefixe));
                if (prefixObj && prefixObj.est_autre_operateur == 1) {
                    hasOtherOperator = true;
                }
            }
        });

        if (hasOtherOperator) {
            checkFrais.disabled = true;
            checkFrais.checked = false;
            infoFrais.classList.remove('d-none');
        } else {
            checkFrais.disabled = false;
            infoFrais.classList.add('d-none');
        }
    }

    // Delegation pour l'input et la suppression
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('tel-input')) {
            checkOperators();
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-dest')) {
            const row = e.target.closest('.dest-row');
            row.remove();
            checkOperators();
            
            // Masquer le bouton de suppression s'il ne reste qu'un champ
            const rows = document.querySelectorAll('.dest-row');
            if (rows.length === 1) {
                rows[0].querySelector('.btn-remove-dest').classList.add('d-none');
            }
        }
    });

    btnAdd.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'input-group mb-2 dest-row';
        div.innerHTML = `
            <input type="text" name="telephone_destinataire[]" class="form-control tel-input" placeholder="N° destinataire" required>
            <button type="button" class="btn btn-outline-danger btn-remove-dest"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
        
        // Afficher le bouton de suppression sur le premier champ s'il y a plus d'un champ
        document.querySelector('.dest-row .btn-remove-dest').classList.remove('d-none');
    });
</script>

<?= view('layouts/footer') ?>
