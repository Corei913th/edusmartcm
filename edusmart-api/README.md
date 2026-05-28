<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PostgreSQL-15-4169E1?logo=postgresql" alt="PostgreSQL 15">
  <img src="https://img.shields.io/badge/PHP-^8.2-777BB4?logo=php" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Status-Development-yellow" alt="Status">
</p>

# EduSmart CM — Backend API

Système de gestion scolaire (School Management System) pour le Ministère des Enseignements Secondaires du Cameroun (MINESEC). Backend Laravel 12 avec PostgreSQL 15, conçu pour supporter les 150 établissements pilotes du programme EDU-SMART.

---

## Architecture

### Domain-Driven Design

```
app/
├── Casts/              # Custom Eloquent casts (EncryptedBytea)
├── Constants/          # Application constants (TokenConstants)
├── Enums/              # 23 PHP enums (backend enum types)
├── Helpers/            # ResponseHelper, DatabaseHelper, global helpers
├── Http/
│   ├── Controllers/    # API controllers (à implémenter)
│   └── Middleware/      # (à implémenter)
├── Models/             # 42 Eloquent models
├── Providers/          # Service providers
└── Traits/             # 5 reusable traits
```

### Modules (Domaines)

| Module | Description | Tables clés |
|--------|-------------|-------------|
| **Administration** | Référentiels, établissements, classes, enseignants, élèves, inscriptions | `regions`, `departements`, `etablissements`, `classes`, `enseignants`, `eleves`, `inscriptions` |
| **Academics** | Notes, absences, appréciations, bulletins, progressions | `notes`, `absences`, `bulletins`, `moyennes_matieres`, `appreciations`, `progressions_cours` |
| **Security** | RBAC, authentification, MFA OTP, audit logging | `utilisateurs`, `roles`, `permissions`, `sessions`, `otp_codes`, `audit_logs` |
| **Parent** | Parents/tuteurs, rattachement, notifications | `parents_tuteurs`, `rattachements_parent_eleve`, `preferences_notifications` |
| **Messaging** | Messagerie interne (parents/enseignants/direction) | `fils_discussion`, `messages`, `pieces_jointes` |
| **Notifications** | Notifications multicanal (SMS, Email, Push, In-App) | `notifications`, `envois_notifications`, `push_subscriptions` |
| **Synchronization** | Offline-first sync | `sync_queue`, `conflits_sync` |

---

## Fonctionnalités

### 🔐 Sécurité & Conformité
- **RBAC** complet : SUPER_ADMIN, ADMIN_ETABLISSEMENT, DIRECTION, ENSEIGNANT, PARENT, ELEVE
- **Chiffrement AES-256** des données PII (nom, prénom, téléphone) — conforme loi n°2010/012
- **MFA** via OTP (SMS/Email)
- **Journal d'audit** tracé de toutes les actions critiques
- **Sessions JWT** avec hash SHA-256 des tokens

### 📊 Gestion Administrative
- Référentiel géographique (régions, départements)
- Établissements avec UAI, type, géolocalisation, connectivité
- Années scolaires et périodes (trimestres)
- Niveaux, séries, matières, salles
- Classes avec effectif max
- Enseignants et personnels administratifs

### 📝 Module Enseignant
- Saisie de notes offline-first (DEVOIR, COMPOSITION, ORAL, TP, EXAMEN)
- Moyennes calculées et mises en cache par matière/période
- Absences avec justificatifs
- Appréciations comportementales
- Progressions pédagogiques (planification, suivi)

### 🏆 Bulletins
- Génération PDF des bulletins
- Calcul des rangs et moyennes générales
- Publication contrôlée

### 👨‍👩‍👧 Module Parent/Élève
- Consultation des notes et absences
- Notification multicanal (SMS, Email, Push)
- Paramètres de notification par type d'événement

### 📱 Offline-First
- File d'attente de synchronisation pour les requêtes hors ligne
- Gestion des conflits (Last-Write-Wins)
- Support des terminaux mobiles (zone à connectivité limitée)

### 💬 Messagerie Interne
- Fils de discussion par type : parent↔enseignant, parent↔direction, enseignant↔direction
- Messages chiffrés au repos
- Pièces jointes (2 Mo max)

---

## Stack Technique

- **Framework**: Laravel 12
- **PHP**: ^8.2
- **Base de données**: PostgreSQL 15
- **Extensions PostgreSQL**: `uuid-ossp`, `pgcrypto`
- **Encryption**: AES-256 via `pgcrypto` + Laravel Crypt
- **File Storage**: Local (VPS) ou S3-compatible
- **Queue**: Database driver
- **Cache**: Database driver
- **Session**: Database driver

---

## Modèles de Données

### Traits Réutilisables

| Trait | Rôle |
|-------|------|
| `UsesUuidAsPrimaryKey` | Génération automatique UUID v4 à la création |
| `HasEncryptedPii` | Chiffrement/déchiffrement transparent des colonnes BYTEA |
| `HasUpdatedAtTrigger` | Désactive `updated_at` Eloquent (géré par trigger PG) |
| `BelongsToEtablissement` | Relation et scope `byEtablissement()` |
| `HasOfflineSyncTracking` | Scopes `pendingSync()`, `synced()`, `offlineOnly()` |

### 42 Modèles Eloquent

```
Region, Departement, Etablissement, AnneeScolaire, Periode,
Niveau, Serie, Salle, Classe, Matiere,
Enseignant, AffectationEnseignement, PersonnelAdministratif,
Eleve, Inscription, Transfert, Radiation,
CreneauHoraire, EmploiDuTemps, Bulletin,
Note, MoyenneMatiere, Absence, AppreciationComportementale, ProgressionCours,
ParentTuteur, RattachementParentEleve, PreferenceNotification,
Role, Permission, Utilisateur, Session, OtpCode, AuditLog, PushSubscription,
FilDiscussion, ParticipantFil, Message, PieceJointe,
Notification, EnvoiNotification,
SyncQueue, ConflitSync
```

### 23 Enums PHP

| Enum | Valeurs |
|------|---------|
| `Role` | SUPER_ADMIN, ADMIN_ETABLISSEMENT, DIRECTION, ENSEIGNANT, PARENT, ELEVE |
| `EtablissementType` | LYCEE, CES, COLLEGE |
| `Connectivite` | 3G, 4G, FIBRE, ADSL, NONE |
| `SalleType` | CLASSE, LABO, AMPHI, SALLE_INFO |
| `MatiereType` | GENERALE, TECHNIQUE, EPS, OPTION |
| `StatutInscription` | ACTIF, TRANSFERE, RADIE, DIPLOME |
| `MotifRadiation` | EXCLUSION, ABANDON, DECES, AUTRE |
| `Sexe` | M, F |
| `TypeEvaluation` | DEVOIR, COMPOSITION, ORAL, TP, EXAMEN |
| `Appreciation` | TRES_BIEN, BIEN, ASSEZ_BIEN, PASSABLE, MEDIOCRE, INSUFFISANT |
| `NiveauAppreciation` | EXCELLENT, BIEN, MOYEN, FAIBLE |
| `StatutAbsence` | JUSTIFIEE, INJUSTIFIEE, EN_ATTENTE |
| `StatutProgression` | PLANIFIE, EN_COURS, TERMINE |
| `LienParent` | PERE, MERE, TUTEUR, AUTRE |
| `TypeEvenement` | NOUVEAU_BULLETIN, ABSENCE_INJUSTIFIEE, NOTE_DISPONIBLE, REUNION_PARENTS, MESSAGE_RECU, ALERTE_SECURITE |
| `CanalNotification` | SMS, EMAIL, PUSH, INAPP |
| `StatutEnvoi` | EN_ATTENTE, ENVOYE, ECHEC, IGNORE |
| `OtpType` | SMS, EMAIL |
| `TypeDiscussion` | PARENT_ENSEIGNANT, PARENT_DIRECTION, ENSEIGNANT_DIRECTION, INTERNE |
| `MethodeHTTP` | POST, PUT, PATCH, DELETE |
| `StatutSync` | EN_ATTENTE, EN_COURS, SYNCHRONISE, CONFLIT, ECHEC |
| `ResolutionConflit` | LWW_CLIENT, LWW_SERVER, MANUEL |
| `FonctionAdministratif` | PROVISEUR, CENSEUR, SECRETAIRE |

---

## Installation

```bash
# Cloner et installer
composer install
cp .env.example .env
php artisan key:generate

# Configurer .env (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=edusmartcm_api
DB_USERNAME=root
DB_PASSWORD=

# Laravel + PostgreSQL extensions
# Assurez-vous que les extensions uuid-ossp et pgcrypto sont activées :
# CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
# CREATE EXTENSION IF NOT EXISTS "pgcrypto";

# Migrer et seed
php artisan migrate
php artisan db:seed

# Lancer le serveur
php artisan serve
```

---

## Seeders Disponibles

| Seeder | Données |
|--------|---------|
| `RegionSeeder` | Régions du Cameroun (10) |
| `DepartementSeeder` | Départements par région (58) |
| `NiveauSeeder` | Niveaux (6ème → Tle) |
| `SerieSeeder` | Séries (A, C, D, TI, etc.) |
| `MatiereSeeder` | Matières par niveau |
| `PermissionSeeder` | Permissions RBAC |
| `UtilisateurSeeder` | Utilisateurs de test |

---

## API

Les routes API sont à définir dans `routes/v1/api.php`.

Standards de réponse :
```json
{
  "success": true,
  "message": "optional message",
  "data": { ... }
}
```

Helpers disponibles :
- `api_success()`, `api_error()`, `api_created()`, `api_updated()`, `api_deleted()`
- `api_not_found()`, `api_unauthorized()`, `api_forbidden()`, `api_validation_error()`
- `api_paginated()`

---

## Licence

Propriétaire — MINESEC Cameroun
