# Inclusif Connect — API (Laravel + MySQL)

Backend REST de la plateforme **Inclusif Connect**, correspondant au cahier des charges (fonctionnalités visiteurs et administrateur, thématiques, recherche, favoris, propositions de documents, lettre d'information, statistiques, journal d'activité).

## 1. Stack

- **Laravel 11** (PHP 8.2+)
- **MySQL / MariaDB**
- **Laravel Sanctum** pour l'authentification par token (adapté à un frontend séparé en HTML/CSS/JS)

## 2. Installation

Ce dossier contient le code applicatif (`app/`, `routes/`, `database/`, `config/`). Il doit être intégré dans un squelette Laravel standard :

```bash
# 1. Créer un projet Laravel neuf
composer create-project laravel/laravel inclusif-connect-api
cd inclusif-connect-api

# 2. Copier les fichiers de ce livrable par-dessus (app/, routes/, database/, config/cors.php, bootstrap/app.php)

# 3. Installer Sanctum
composer require laravel/sanctum

# 4. Copier la configuration d'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données MySQL dans .env
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Créer la base MySQL
mysql -u root -p -e "CREATE DATABASE inclusif_connect CHARACTER SET utf8mb4;"

# 7. Migrer et peupler avec les données de démonstration
php artisan migrate --seed

# 8. Lier le stockage public (fichiers uploadés : documents, guides, etc.)
php artisan storage:link

# 9. Lancer le serveur de développement
php artisan serve
```

L'API est alors disponible sur `http://localhost:8000/api`.

### Compte administrateur de démonstration

Créé automatiquement par `AdminUserSeeder` :

- Email : `admin@inclusif-connect.org`
- Mot de passe : `ChangeMoi!2026`

**⚠️ À changer immédiatement en production.**

## 3. Authentification

L'API utilise des **tokens Sanctum** (Bearer token), adaptés à un frontend statique HTML/CSS/JS comme celui déjà livré :

```
POST /api/auth/register   → crée un compte, renvoie { user, token }
POST /api/auth/login      → renvoie { user, token }
POST /api/auth/logout     → (authentifié) révoque le token
GET  /api/me               → (authentifié) profil courant
```

Le frontend doit envoyer le token reçu dans l'en-tête de chaque requête protégée :

```
Authorization: Bearer <token>
```

## 4. Points d'accès principaux

### Publics (visiteurs, sans compte)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/categories` | Liste des 7 thématiques |
| GET | `/api/documents?q=&category=&type=&sort=&per_page=` | Recherche / filtre / tri des ressources publiées |
| GET | `/api/documents/{slug}` | Fiche détaillée (incrémente les vues) |
| POST | `/api/documents/{id}/download` | Télécharge le fichier (incrémente les téléchargements) |
| POST | `/api/newsletter/subscribe` | Inscription à la lettre d'information |
| POST | `/api/contact` | Formulaire d'aide / contact |

### Authentifiées (visiteur connecté)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/favorites` | Mes documents favoris |
| POST / DELETE | `/api/favorites/{document}` | Ajouter / retirer un favori |
| POST | `/api/documents` | Proposer un document (statut `en_attente`) |

### Administrateur (`role: administrateur`)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/admin/stats` | Tableau de bord (documents, utilisateurs, newsletter…) |
| GET | `/api/admin/activity-log` | Historique des actions admin |
| CRUD | `/api/admin/categories` | Gérer les thématiques |
| CRUD | `/api/admin/documents` | Gérer les documents |
| POST | `/api/admin/documents/{id}/approve` | Valider une proposition |
| POST | `/api/admin/documents/{id}/reject` | Refuser une proposition |
| GET | `/api/admin/users` | Liste des comptes |
| PATCH | `/api/admin/users/{id}/role` | Changer un rôle |
| DELETE | `/api/admin/users/{id}` | Supprimer un compte |

## 5. Modèle de données

- **users** : visiteurs et administrateurs (`role`)
- **categories** : les 7 thématiques
- **documents** : ressources, avec `status` (`en_attente` / `publie` / `refuse`), compteurs de vues/téléchargements, mots-clés (JSON)
- **favorites** : table pivot utilisateur ↔ document
- **newsletter_subscribers**
- **contact_messages**
- **activity_logs** : journal des actions administrateur (exigence du cahier des charges, §4.B)

## 6. Sécurité & conformité (cahier des charges §6)

- Mots de passe hachés (`bcrypt` via `Hash::make`)
- CORS restreint au domaine du frontend (`config/cors.php`, variable `FRONTEND_URL`)
- Validation stricte des entrées (Form Requests)
- Séparation des rôles via le middleware `admin`
- Prêt pour HTTPS derrière un serveur web (Nginx/Apache) — à activer en production
- Respect RGPD : les emails ne sont utilisés que pour l'usage déclaré (newsletter, compte)

## 7. Prochaines étapes suggérées

- Ajouter l'envoi d'e-mails réels pour la newsletter (actuellement stockage en base uniquement)
- Ajouter la pagination/tri côté `/api/admin/documents` avancé si le volume grandit
- Ajouter des tests Pest/PHPUnit sur les contrôleurs critiques (auth, propositions, favoris)
- Générer une clé API distincte si un système de paiement est ajouté (hors périmètre v1, cf. cahier des charges §3.1)
