# CV Builder — Template de déploiement

Template vierge pour déployer une nouvelle instance du CV Builder sur OVH.

---

## Procédure de déploiement

### 1. Préparer le dossier

Renomme ce dossier `cv-template` en `cv-[nom]` (ex: `cv-dupont`, `cv-martin`).

### 2. Créer la base de données MySQL

Sur OVH (Hébergement > Bases de données) :
- Créer une nouvelle base MySQL
- Noter : **host**, **nom**, **utilisateur**, **mot de passe**

### 3. Créer les tables

Dans phpMyAdmin, importer le fichier :
```
sql/install.sql
```

### 4. Configurer l'application

Ouvre `config.php` et remplis :

```php
define('CV_DB_HOST', 'mysql5-xx.perso.ovh.net');  // host OVH
define('CV_DB_NAME', 'nom_de_la_base');
define('CV_DB_USER', 'utilisateur');
define('CV_DB_PASS', 'mot_de_passe');

define('CV_USERNAME', 'admin');       // identifiant de connexion
define('CV_PASSWORD_HASH', '');       // à remplir à l'étape 6

define('CV_SESSION_NAME', 'cv_dupont_sess');  // nom unique (évite conflits si plusieurs instances)

define('CV_BASE_URL', '/cv-dupont');  // nom du dossier sur OVH

define('CV_THEME', 'glass');          // 'default', 'glass' ou 'dark'
```

### 5. Uploader le dossier sur OVH

Via FTP (FileZilla) : déposer le dossier `cv-[nom]` à la racine du site.

### 6. Générer le hash du mot de passe

Dans un navigateur, accéder à :
```
https://ton-site.fr/cv-[nom]/setup.php
```

- Saisir le mot de passe souhaité (min. 8 caractères)
- Copier le hash généré (commence par `$2y$10$...`)
- L'ouvrir `config.php` via FTP et coller le hash dans `CV_PASSWORD_HASH`
- **Supprimer `setup.php` du serveur immédiatement après**

### 7. Tester la connexion

Accéder à :
```
https://ton-site.fr/cv-[nom]/
```

Se connecter avec l'identifiant (`CV_USERNAME`) et le mot de passe choisi.

---

## Options avancées

### PDFShift (export PDF côté serveur)
Décommenter dans `config.php` :
```php
define('PDFSHIFT_API_KEY', 'sk_...');
```

### API Claude directe
Décommenter dans `config.php` :
```php
define('CV_ANTHROPIC_KEY', 'sk-ant-...');
define('CV_MODEL', 'claude-sonnet-4-6');
```

### Thèmes disponibles
| Valeur | Rendu |
|---|---|
| `'default'` | Fond clair, palette aubergine + gold |
| `'glass'` | Fond sombre, glassmorphisme noir & blanc |
| `'dark'` | Fond très sombre, palette aubergine + gold |

---

## Structure des fichiers

```
config.php          ← credentials (NE PAS uploader dans git)
setup.php           ← générateur de hash (supprimer après usage)
sql/install.sql     ← schéma MySQL à importer une seule fois
index.php           ← page de login
dashboard.php       ← tableau de bord
new-application.php ← création candidature
knowledge-base.php  ← base de connaissance
history.php         ← historique
includes/           ← header, footer, auth, db
php/                ← endpoints AJAX
css/                ← feuilles de style (3 thèmes)
js/                 ← scripts
```

---

## Checklist déploiement

- [ ] Base MySQL créée
- [ ] `sql/install.sql` importé dans phpMyAdmin
- [ ] `config.php` rempli (DB + BASE_URL + SESSION_NAME)
- [ ] Dossier uploadé sur OVH
- [ ] Hash généré via `setup.php`
- [ ] Hash copié dans `config.php`
- [ ] `setup.php` supprimé du serveur
- [ ] Connexion testée
