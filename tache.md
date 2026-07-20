- **4263:** fonction `index` dans `UtilisateurController.php` (Page de login)
- **4263:** fonction `verifierUtilisateur` dans `UtilisateurController.php` (Connexion/Création automatique via téléphone)
- **4263:** fonction `dashboard` dans `UtilisateurController.php` (Tableau de bord client)
- **4263:** fonction `voirSolde` dans `UtilisateurController.php` (API de consultation du solde)
- **4263:** fonction `faireDepot` dans `UtilisateurController.php` (Dépôt automatique sans frais)
- **4263:** fonction `faireRetrait` dans `UtilisateurController.php` (Retrait avec application du barème)
- **4263:** fonction `faireTransfert` dans `UtilisateurController.php` (Transfert vers un autre compte avec vérification de préfixe)
- **4263:** fonction `voirHistoriques` dans `UtilisateurController.php` (Historique des transactions du client)
- **4263:** fonction `calculerFraisAjax` dans `UtilisateurController.php` (Simulation de frais en temps réel)
- **4263:** fonction `deconnexion` dans `UtilisateurController.php`
- **4263:** Création de la vue `client/login.php`
- **4263:** Création de la vue `client/dashboard.php`
- **4263:** Création de la vue `client/historiques.php`
- **4263:** Configuration de l'environnement client dans `MainSeeder.php` (Création de Personne 1 - 0334263)


- **4021:** fonction `index` dans `OperateurController.php` (Dashboard opérateur global)
- **4021:** fonction `gererPrefixes` dans `OperateurController.php` (Liste des préfixes valables)
- **4021:** fonction `ajouterPrefixe` dans `OperateurController.php`
- **4021:** fonction `supprimerPrefixe` dans `OperateurController.php`
- **4021:** fonction `togglePrefixe` dans `OperateurController.php`
- **4021:** fonction `gererTypesOperations` dans `OperateurController.php` (Affichage des types d'opérations : Dépôt, Retrait, Transfert)
- **4021:** fonction `gererBaremes` dans `OperateurController.php` (Liste des barèmes de frais par tranche)
- **4021:** fonction `ajouterBareme` dans `OperateurController.php`
- **4021:** fonction `modifierBareme` dans `OperateurController.php`
- **4021:** fonction `supprimerBareme` dans `OperateurController.php`
- **4021:** fonction `situationGains` dans `OperateurController.php` (Visualisation des gains liés aux retraits et transferts)
- **4021:** fonction `situationComptes` dans `OperateurController.php` (Liste et suivi des comptes clients)
- **4021:** fonction `getBaremesParType` dans `OperateurController.php` (API AJAX pour les formulaires)
- **4021:** Création des vues opérateur : `dashboard.php`, `prefixes.php`, `types_operations.php`, `baremes.php`, `gains.php`, `comptes.php`
- **4021:** Configuration de l'environnement client dans `MainSeeder.php` (Création de Personne 2 - 0334021)

## Tâches Communes 
- Création de `PrefixeModel`, `TypeOperationModel`, `BaremeFraisModel`, `CompteModel`, `TransactionModel`.
- Développement des migrations de la base de données : `prefixes`, `types_operations`, `baremes_frais`, `comptes`, `transactions`.
- Conception du fichier `Routes.php`.
- Mise en page globale avec `Bootstrap 5` (fichiers `layouts/header.php`, `layouts/footer.php`, `layouts/operateur_header.php`, `layouts/operateur_footer.php`).
- Css
