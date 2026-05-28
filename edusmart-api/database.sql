-- ══════════════════════════════════════════════════════════════════════════════
-- EDUSMART-CM — Modèle relationnel PostgreSQL 15
-- Couverture : Module Administration · Module Enseignant · Module Parent/Élève
-- Contraintes : RBAC · AES-256 chiffrement colonnes PII · audit_logs · HTTPS
-- ══════════════════════════════════════════════════════════════════════════════

-- Extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";  -- pour encrypt() AES-256

-- ══════════════════════════════════════════════════════════════════════════════
-- 1. RÉFÉRENTIELS GÉOGRAPHIQUES ET ADMINISTRATIFS
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE regions (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(10) NOT NULL UNIQUE,   -- ex: 'CE', 'LT', 'NO'
    nom             VARCHAR(100) NOT NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE departements (
    id              SERIAL PRIMARY KEY,
    region_id       INTEGER NOT NULL REFERENCES regions(id),
    code            VARCHAR(10) NOT NULL UNIQUE,
    nom             VARCHAR(100) NOT NULL
);

CREATE TABLE etablissements (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    code_uai        VARCHAR(20) NOT NULL UNIQUE,   -- identifiant MINESEC officiel
    nom             VARCHAR(200) NOT NULL,
    type            VARCHAR(50) NOT NULL CHECK (type IN ('LYCEE','CES','COLLEGE')),
    departement_id  INTEGER NOT NULL REFERENCES departements(id),
    adresse         TEXT,
    telephone       BYTEA,                          -- chiffré AES-256 (loi 2010/012)
    email           VARCHAR(200),
    est_pilote      BOOLEAN NOT NULL DEFAULT FALSE, -- établissements des 150 pilotes
    latitude        DECIMAL(10,7),
    longitude       DECIMAL(10,7),
    connectivite    VARCHAR(20) CHECK (connectivite IN ('3G','4G','FIBRE','ADSL','NONE')),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE annees_scolaires (
    id              SERIAL PRIMARY KEY,
    libelle         VARCHAR(20) NOT NULL UNIQUE,    -- ex: '2025-2026'
    date_debut      DATE NOT NULL,
    date_fin        DATE NOT NULL,
    est_active      BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_annee_dates CHECK (date_fin > date_debut)
);

CREATE TABLE periodes (
    id              SERIAL PRIMARY KEY,
    annee_id        INTEGER NOT NULL REFERENCES annees_scolaires(id),
    numero          SMALLINT NOT NULL CHECK (numero BETWEEN 1 AND 3),  -- trimestres
    libelle         VARCHAR(30) NOT NULL,   -- 'Premier trimestre'
    date_debut      DATE NOT NULL,
    date_fin        DATE NOT NULL,
    UNIQUE (annee_id, numero)
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 2. UTILISATEURS, RÔLES ET SÉCURITÉ
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE roles (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(30) NOT NULL UNIQUE,
    -- SUPER_ADMIN · ADMIN_ETABLISSEMENT · DIRECTION · ENSEIGNANT · PARENT · ELEVE
    libelle         VARCHAR(100) NOT NULL
);

CREATE TABLE permissions (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(100) NOT NULL UNIQUE,
    -- ex: 'marks:read:own' | 'marks:write:class' | 'absences:write' | 'bulletins:export'
    description     TEXT
);

CREATE TABLE role_permissions (
    role_id         INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id   INTEGER NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE utilisateurs (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    role_id         INTEGER NOT NULL REFERENCES roles(id),
    etablissement_id UUID REFERENCES etablissements(id),
    -- PII chiffrées conformément à la loi n°2010/012
    nom             BYTEA NOT NULL,                 -- AES-256
    prenom          BYTEA NOT NULL,                 -- AES-256
    telephone       BYTEA UNIQUE,                   -- AES-256 — sert à l'auth MFA
    email           VARCHAR(200) UNIQUE,
    password_hash   VARCHAR(255),                   -- bcrypt cost 12
    est_actif       BOOLEAN NOT NULL DEFAULT TRUE,
    derniere_connexion TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Sessions JWT
CREATE TABLE sessions (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    token_hash      VARCHAR(255) NOT NULL,           -- hash SHA-256 du JWT
    ip_address      INET,
    user_agent      TEXT,
    expire_at       TIMESTAMPTZ NOT NULL,
    revoque         BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- OTP pour MFA
CREATE TABLE otp_codes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    code_hash       VARCHAR(255) NOT NULL,           -- bcrypt du code à 6 chiffres
    type            VARCHAR(20) NOT NULL CHECK (type IN ('SMS','EMAIL')),
    tentatives      SMALLINT NOT NULL DEFAULT 0,
    expire_at       TIMESTAMPTZ NOT NULL,
    utilise         BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Journal d'audit — toutes les actions critiques
CREATE TABLE audit_logs (
    id              BIGSERIAL PRIMARY KEY,
    utilisateur_id  UUID REFERENCES utilisateurs(id),
    action          VARCHAR(100) NOT NULL,  -- 'LOGIN_SUCCESS' · 'MARK_READ' · 'PDF_EXPORT'
    ressource_type  VARCHAR(50),            -- 'marks' · 'bulletin' · 'absence'
    ressource_id    UUID,
    ip_address      INET,
    user_agent      TEXT,
    metadata        JSONB,                  -- données complémentaires contextuelles
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_audit_utilisateur ON audit_logs(utilisateur_id, created_at DESC);
CREATE INDEX idx_audit_action ON audit_logs(action, created_at DESC);

-- ══════════════════════════════════════════════════════════════════════════════
-- 3. MODULE ADMINISTRATION
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE niveaux (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(10) NOT NULL UNIQUE,   -- '6eme' '5eme' '4eme' '3eme' '2nde' '1ere' 'Tle'
    libelle         VARCHAR(50) NOT NULL,
    ordre           SMALLINT NOT NULL
);

CREATE TABLE series (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(10) NOT NULL UNIQUE,   -- 'A' 'C' 'D' 'TI' etc.
    libelle         VARCHAR(100) NOT NULL
);

CREATE TABLE salles (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    nom             VARCHAR(50) NOT NULL,
    capacite        SMALLINT,
    type            VARCHAR(30) CHECK (type IN ('CLASSE','LABO','AMPHI','SALLE_INFO')),
    UNIQUE (etablissement_id, nom)
);

CREATE TABLE classes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    annee_id        INTEGER NOT NULL REFERENCES annees_scolaires(id),
    niveau_id       INTEGER NOT NULL REFERENCES niveaux(id),
    serie_id        INTEGER REFERENCES series(id),
    nom             VARCHAR(30) NOT NULL,           -- ex: '3ème A'
    effectif_max    SMALLINT NOT NULL DEFAULT 60,
    salle_id        UUID REFERENCES salles(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (etablissement_id, annee_id, nom)
);

CREATE TABLE matieres (
    id              SERIAL PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,
    nom             VARCHAR(100) NOT NULL,
    coefficient_defaut SMALLINT NOT NULL DEFAULT 1,
    type            VARCHAR(20) CHECK (type IN ('GENERALE','TECHNIQUE','EPS','OPTION'))
);

CREATE TABLE enseignants (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL UNIQUE REFERENCES utilisateurs(id),
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    matricule       VARCHAR(30) UNIQUE,
    grade           VARCHAR(50),
    specialite      VARCHAR(100),
    date_prise_fonction DATE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Enseignant affecté à une matière dans une classe pour une année
CREATE TABLE affectations_enseignement (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    enseignant_id   UUID NOT NULL REFERENCES enseignants(id),
    classe_id       UUID NOT NULL REFERENCES classes(id),
    matiere_id      INTEGER NOT NULL REFERENCES matieres(id),
    annee_id        INTEGER NOT NULL REFERENCES annees_scolaires(id),
    coefficient     SMALLINT NOT NULL DEFAULT 1,
    UNIQUE (classe_id, matiere_id, annee_id)
);

CREATE TABLE personnel_administratif (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL UNIQUE REFERENCES utilisateurs(id),
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    fonction        VARCHAR(100) NOT NULL,  -- 'PROVISEUR' · 'CENSEUR' · 'SECRETAIRE'
    date_prise_fonction DATE
);

-- Élèves
CREATE TABLE eleves (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID UNIQUE REFERENCES utilisateurs(id),  -- compte optionnel
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    matricule       VARCHAR(30) NOT NULL UNIQUE,
    -- PII — loi n°2010/012
    nom             BYTEA NOT NULL,                 -- AES-256
    prenom          BYTEA NOT NULL,                 -- AES-256
    date_naissance  DATE,
    lieu_naissance  BYTEA,                          -- AES-256
    sexe            CHAR(1) CHECK (sexe IN ('M','F')),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Inscription d'un élève dans une classe pour une année
CREATE TABLE inscriptions (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    eleve_id        UUID NOT NULL REFERENCES eleves(id),
    classe_id       UUID NOT NULL REFERENCES classes(id),
    annee_id        INTEGER NOT NULL REFERENCES annees_scolaires(id),
    date_inscription DATE NOT NULL DEFAULT CURRENT_DATE,
    statut          VARCHAR(20) NOT NULL DEFAULT 'ACTIF'
                    CHECK (statut IN ('ACTIF','TRANSFERE','RADIE','DIPLOME')),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (eleve_id, annee_id)
);

CREATE TABLE transferts (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    etablissement_origine_id UUID NOT NULL REFERENCES etablissements(id),
    etablissement_destination_id UUID NOT NULL REFERENCES etablissements(id),
    date_transfert  DATE NOT NULL,
    motif           TEXT,
    created_by      UUID REFERENCES utilisateurs(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE radiations (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    date_radiation  DATE NOT NULL,
    motif           VARCHAR(50) CHECK (motif IN ('EXCLUSION','ABANDON','DECES','AUTRE')),
    observations    TEXT,
    created_by      UUID REFERENCES utilisateurs(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Emplois du temps
CREATE TABLE creneaux_horaires (
    id              SERIAL PRIMARY KEY,
    jour            SMALLINT NOT NULL CHECK (jour BETWEEN 1 AND 6),  -- 1=Lundi
    heure_debut     TIME NOT NULL,
    heure_fin       TIME NOT NULL,
    UNIQUE (jour, heure_debut)
);

CREATE TABLE emplois_du_temps (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    affectation_id  UUID NOT NULL REFERENCES affectations_enseignement(id),
    salle_id        UUID REFERENCES salles(id),
    creneau_id      INTEGER NOT NULL REFERENCES creneaux_horaires(id),
    annee_id        INTEGER NOT NULL REFERENCES annees_scolaires(id),
    UNIQUE (affectation_id, creneau_id, annee_id)
);

-- Bulletins générés
CREATE TABLE bulletins (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    periode_id      INTEGER NOT NULL REFERENCES periodes(id),
    rang_classe     SMALLINT,
    moyenne_generale DECIMAL(5,2),
    appreciation_generale TEXT,
    pdf_path        TEXT,                           -- chemin fichier PDF sur VPS
    pdf_genere_at   TIMESTAMPTZ,
    est_publie      BOOLEAN NOT NULL DEFAULT FALSE,
    publie_at       TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (inscription_id, periode_id)
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 4. MODULE ENSEIGNANT
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE notes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    affectation_id  UUID NOT NULL REFERENCES affectations_enseignement(id),
    periode_id      INTEGER NOT NULL REFERENCES periodes(id),
    type_evaluation VARCHAR(30) NOT NULL CHECK (
                    type_evaluation IN ('DEVOIR','COMPOSITION','ORAL','TP','EXAMEN')),
    note            DECIMAL(5,2) NOT NULL CHECK (note >= 0 AND note <= 20),
    coefficient     SMALLINT NOT NULL DEFAULT 1,
    date_evaluation DATE NOT NULL,
    saisie_hors_ligne BOOLEAN NOT NULL DEFAULT FALSE,  -- offline-first tracking
    sync_at         TIMESTAMPTZ,                        -- horodatage de synchronisation
    created_by      UUID NOT NULL REFERENCES utilisateurs(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_notes_inscription ON notes(inscription_id, periode_id);
CREATE INDEX idx_notes_affectation ON notes(affectation_id, periode_id);

-- Notes agrégées par matière / période (calculées et mises en cache)
CREATE TABLE moyennes_matieres (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    affectation_id  UUID NOT NULL REFERENCES affectations_enseignement(id),
    periode_id      INTEGER NOT NULL REFERENCES periodes(id),
    moyenne         DECIMAL(5,2),
    rang_matiere    SMALLINT,
    appreciation    VARCHAR(30) CHECK (appreciation IN
                    ('TRES_BIEN','BIEN','ASSEZ_BIEN','PASSABLE','MEDIOCRE','INSUFFISANT')),
    calculated_at   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (inscription_id, affectation_id, periode_id)
);

CREATE TABLE absences (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    affectation_id  UUID REFERENCES affectations_enseignement(id),  -- null = absence générale
    date_absence    DATE NOT NULL,
    heure_debut     TIME,
    heure_fin       TIME,
    duree_heures    SMALLINT,
    motif           TEXT,
    statut          VARCHAR(20) NOT NULL DEFAULT 'INJUSTIFIEE'
                    CHECK (statut IN ('JUSTIFIEE','INJUSTIFIEE','EN_ATTENTE')),
    justificatif_path TEXT,
    saisie_hors_ligne BOOLEAN NOT NULL DEFAULT FALSE,
    sync_at         TIMESTAMPTZ,
    created_by      UUID NOT NULL REFERENCES utilisateurs(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_absences_inscription ON absences(inscription_id, date_absence DESC);

CREATE TABLE appreciations_comportementales (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    inscription_id  UUID NOT NULL REFERENCES inscriptions(id),
    periode_id      INTEGER NOT NULL REFERENCES periodes(id),
    discipline      VARCHAR(30) CHECK (discipline IN ('EXCELLENT','BIEN','MOYEN','FAIBLE')),
    ponctualite     VARCHAR(30) CHECK (ponctualite IN ('EXCELLENT','BIEN','MOYEN','FAIBLE')),
    travail         VARCHAR(30) CHECK (travail IN ('EXCELLENT','BIEN','MOYEN','FAIBLE')),
    commentaire     TEXT,
    created_by      UUID NOT NULL REFERENCES utilisateurs(id),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (inscription_id, periode_id)
);

CREATE TABLE progressions_cours (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    affectation_id  UUID NOT NULL REFERENCES affectations_enseignement(id),
    periode_id      INTEGER NOT NULL REFERENCES periodes(id),
    chapitre        VARCHAR(200) NOT NULL,
    objectif        TEXT,
    statut          VARCHAR(20) NOT NULL DEFAULT 'PLANIFIE'
                    CHECK (statut IN ('PLANIFIE','EN_COURS','TERMINE')),
    date_debut_prevu DATE,
    date_fin_prevu  DATE,
    date_fin_reel   DATE,
    taux_avancement SMALLINT DEFAULT 0 CHECK (taux_avancement BETWEEN 0 AND 100),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 5. MODULE PARENT / ÉLÈVE
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE parents_tuteurs (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL UNIQUE REFERENCES utilisateurs(id),
    -- PII — loi n°2010/012
    nom             BYTEA NOT NULL,
    prenom          BYTEA NOT NULL,
    telephone       BYTEA,                  -- AES-256
    email           VARCHAR(200),
    profession      VARCHAR(100),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Rattachement parent ↔ élève (un parent peut avoir plusieurs enfants)
CREATE TABLE rattachements_parent_eleve (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    parent_id       UUID NOT NULL REFERENCES parents_tuteurs(id),
    eleve_id        UUID NOT NULL REFERENCES eleves(id),
    lien            VARCHAR(30) NOT NULL CHECK (lien IN ('PERE','MERE','TUTEUR','AUTRE')),
    est_contact_principal BOOLEAN NOT NULL DEFAULT FALSE,
    peut_consulter_notes  BOOLEAN NOT NULL DEFAULT TRUE,
    peut_recevoir_notifs  BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (parent_id, eleve_id)
);

-- Préférences de notification par parent
CREATE TABLE preferences_notifications (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    parent_id       UUID NOT NULL REFERENCES parents_tuteurs(id),
    type_evenement  VARCHAR(50) NOT NULL CHECK (type_evenement IN (
                    'NOUVEAU_BULLETIN','ABSENCE_INJUSTIFIEE','NOTE_DISPONIBLE',
                    'REUNION_PARENTS','MESSAGE_RECU','ALERTE_SECURITE')),
    canal_sms       BOOLEAN NOT NULL DEFAULT TRUE,
    canal_email     BOOLEAN NOT NULL DEFAULT TRUE,
    canal_push      BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (parent_id, type_evenement)
);

-- Web Push subscriptions (endpoint navigateur)
CREATE TABLE push_subscriptions (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    endpoint        TEXT NOT NULL UNIQUE,
    p256dh          TEXT NOT NULL,          -- clé publique ECDH du navigateur
    auth            TEXT NOT NULL,          -- secret d'authentification
    user_agent      TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 6. MESSAGERIE (transversale aux 3 modules)
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE fils_discussion (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    etablissement_id UUID NOT NULL REFERENCES etablissements(id),
    sujet           VARCHAR(200) NOT NULL,
    type            VARCHAR(30) NOT NULL CHECK (type IN (
                    'PARENT_ENSEIGNANT','PARENT_DIRECTION','ENSEIGNANT_DIRECTION','INTERNE')),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE participants_fil (
    fil_id          UUID NOT NULL REFERENCES fils_discussion(id) ON DELETE CASCADE,
    utilisateur_id  UUID NOT NULL REFERENCES utilisateurs(id) ON DELETE CASCADE,
    lu_at           TIMESTAMPTZ,
    PRIMARY KEY (fil_id, utilisateur_id)
);

CREATE TABLE messages (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    fil_id          UUID NOT NULL REFERENCES fils_discussion(id) ON DELETE CASCADE,
    expediteur_id   UUID NOT NULL REFERENCES utilisateurs(id),
    -- contenu chiffré au repos — loi n°2010/012
    contenu         BYTEA NOT NULL,                 -- AES-256
    est_archive     BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_messages_fil ON messages(fil_id, created_at DESC);

CREATE TABLE pieces_jointes (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    message_id      UUID NOT NULL REFERENCES messages(id) ON DELETE CASCADE,
    nom_fichier     VARCHAR(255) NOT NULL,
    type_mime       VARCHAR(100) NOT NULL,
    taille_octets   INTEGER NOT NULL CHECK (taille_octets <= 2097152),  -- 2 Mo max
    chemin_stockage TEXT NOT NULL,          -- chemin VPS ou objet storage
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 7. NOTIFICATIONS
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE notifications (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    destinataire_id UUID NOT NULL REFERENCES utilisateurs(id),
    type_evenement  VARCHAR(50) NOT NULL,
    titre           VARCHAR(200) NOT NULL,
    corps           TEXT NOT NULL,
    donnees_meta    JSONB,                  -- lien vers la ressource concernée
    lu              BOOLEAN NOT NULL DEFAULT FALSE,
    lu_at           TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE envois_notifications (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    notification_id UUID NOT NULL REFERENCES notifications(id) ON DELETE CASCADE,
    canal           VARCHAR(20) NOT NULL CHECK (canal IN ('SMS','EMAIL','PUSH','INAPP')),
    statut          VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE'
                    CHECK (statut IN ('EN_ATTENTE','ENVOYE','ECHEC','IGNORE')),
    tentatives      SMALLINT NOT NULL DEFAULT 0,
    derniere_tentative TIMESTAMPTZ,
    erreur          TEXT,                   -- message d'erreur si statut = ECHEC
    envoye_at       TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_envois_statut ON envois_notifications(statut, created_at)
    WHERE statut IN ('EN_ATTENTE', 'ECHEC');

-- ══════════════════════════════════════════════════════════════════════════════
-- 8. SYNCHRONISATION OFFLINE-FIRST
-- ══════════════════════════════════════════════════════════════════════════════

-- File d'attente de synchronisation pour les requêtes hors ligne
CREATE TABLE sync_queue (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    utilisateur_id  UUID NOT NULL REFERENCES utilisateurs(id),
    methode         VARCHAR(10) NOT NULL CHECK (methode IN ('POST','PUT','PATCH','DELETE')),
    endpoint        VARCHAR(200) NOT NULL,
    payload         JSONB NOT NULL,
    tentatives      SMALLINT NOT NULL DEFAULT 0,
    statut          VARCHAR(20) NOT NULL DEFAULT 'EN_ATTENTE'
                    CHECK (statut IN ('EN_ATTENTE','EN_COURS','SYNCHRONISE','CONFLIT','ECHEC')),
    timestamp_client TIMESTAMPTZ NOT NULL,  -- horodatage UTC généré côté terminal
    sync_at         TIMESTAMPTZ,
    erreur          TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Journal des conflits de synchronisation (Last-Write-Wins)
CREATE TABLE conflits_sync (
    id              UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    sync_queue_id   UUID NOT NULL REFERENCES sync_queue(id),
    ressource_type  VARCHAR(50) NOT NULL,           -- 'note' · 'absence'
    ressource_id    UUID NOT NULL,
    valeur_cliente  JSONB NOT NULL,
    valeur_serveur  JSONB NOT NULL,
    timestamp_client TIMESTAMPTZ NOT NULL,
    timestamp_serveur TIMESTAMPTZ NOT NULL,
    resolution      VARCHAR(20) NOT NULL DEFAULT 'LWW_SERVER'
                    CHECK (resolution IN ('LWW_CLIENT','LWW_SERVER','MANUEL')),
    resolu_at       TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- ══════════════════════════════════════════════════════════════════════════════
-- 9. INDEX COMPLÉMENTAIRES ET CONTRAINTES
-- ══════════════════════════════════════════════════════════════════════════════

CREATE INDEX idx_inscriptions_eleve ON inscriptions(eleve_id, annee_id);
CREATE INDEX idx_inscriptions_classe ON inscriptions(classe_id, annee_id);
CREATE INDEX idx_rattachements_parent ON rattachements_parent_eleve(parent_id);
CREATE INDEX idx_rattachements_eleve ON rattachements_parent_eleve(eleve_id);
CREATE INDEX idx_notes_sync ON notes(saisie_hors_ligne, sync_at) WHERE saisie_hors_ligne = TRUE;
CREATE INDEX idx_absences_sync ON absences(saisie_hors_ligne, sync_at) WHERE saisie_hors_ligne = TRUE;
CREATE INDEX idx_bulletins_publie ON bulletins(est_publie, publie_at) WHERE est_publie = TRUE;
CREATE INDEX idx_notifications_dest ON notifications(destinataire_id, lu, created_at DESC);
CREATE INDEX idx_sync_queue_pending ON sync_queue(statut, tentatives) WHERE statut = 'EN_ATTENTE';

-- ══════════════════════════════════════════════════════════════════════════════
-- 10. TRIGGER — updated_at automatique
-- ══════════════════════════════════════════════════════════════════════════════

CREATE OR REPLACE FUNCTION trigger_set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_updated_at BEFORE UPDATE ON utilisateurs
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
CREATE TRIGGER set_updated_at BEFORE UPDATE ON eleves
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
CREATE TRIGGER set_updated_at BEFORE UPDATE ON notes
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
CREATE TRIGGER set_updated_at BEFORE UPDATE ON absences
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
CREATE TRIGGER set_updated_at BEFORE UPDATE ON progressions_cours
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();
CREATE TRIGGER set_updated_at BEFORE UPDATE ON etablissements
    FOR EACH ROW EXECUTE FUNCTION trigger_set_updated_at();