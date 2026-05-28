# API Endpoints - Notes & Absences

## Authentification
Toutes les routes nécessitent l'authentification Sanctum via le header :
```
Authorization: Bearer {token}
```

## Notes API

### GET `/api/v1/notes`
Liste paginée des notes avec filtres optionnels

**Paramètres de requête :**
- `inscription_id` (UUID) - Filtrer par inscription
- `affectation_id` (UUID) - Filtrer par affectation d'enseignement  
- `periode_id` (UUID) - Filtrer par période
- `type_evaluation` (string) - Filtrer par type (DEVOIR, COMPOSITION, ORAL, TP, EXAMEN)
- `date_evaluation_from` (date) - Date de début (YYYY-MM-DD)
- `date_evaluation_to` (date) - Date de fin (YYYY-MM-DD)
- `per_page` (int) - Nombre d'éléments par page (défaut: 15)

### POST `/api/v1/notes`
Créer une nouvelle note

**Corps de la requête :**
```json
{
  "inscription_id": "uuid",
  "affectation_id": "uuid", 
  "periode_id": "uuid",
  "type_evaluation": "DEVOIR|COMPOSITION|ORAL|TP|EXAMEN",
  "note": 15.5,
  "coefficient": 2,
  "date_evaluation": "2024-01-15",
  "saisie_hors_ligne": false
}
```

### GET `/api/v1/notes/{id}`
Récupérer une note spécifique

### PUT/PATCH `/api/v1/notes/{id}`
Mettre à jour une note (tous les champs optionnels)

### DELETE `/api/v1/notes/{id}`
Supprimer une note

## Absences API

### GET `/api/v1/absences`
Liste paginée des absences avec filtres optionnels

**Paramètres de requête :**
- `inscription_id` (UUID) - Filtrer par inscription
- `affectation_id` (UUID) - Filtrer par affectation d'enseignement
- `statut` (string) - Filtrer par statut (JUSTIFIEE, INJUSTIFIEE, EN_ATTENTE)
- `date_absence_from` (date) - Date de début (YYYY-MM-DD)
- `date_absence_to` (date) - Date de fin (YYYY-MM-DD)
- `per_page` (int) - Nombre d'éléments par page (défaut: 15)

### POST `/api/v1/absences`
Créer une nouvelle absence

**Corps de la requête :**
```json
{
  "inscription_id": "uuid",
  "affectation_id": "uuid",
  "date_absence": "2024-01-15",
  "heure_debut": "08:00",
  "heure_fin": "10:00", 
  "duree_heures": 2,
  "motif": "Maladie",
  "statut": "EN_ATTENTE",
  "justificatif_path": "/path/to/file.pdf",
  "saisie_hors_ligne": false
}
```

### GET `/api/v1/absences/{id}`
Récupérer une absence spécifique

### PUT/PATCH `/api/v1/absences/{id}`
Mettre à jour une absence (tous les champs optionnels)

### PATCH `/api/v1/absences/{id}/statut`
Mettre à jour uniquement le statut d'une absence

**Corps de la requête :**
```json
{
  "statut": "JUSTIFIEE|INJUSTIFIEE|EN_ATTENTE",
  "justificatif_path": "/path/to/file.pdf"
}
```

### DELETE `/api/v1/absences/{id}`
Supprimer une absence

## Format de réponse standardisé

### Succès
```json
{
  "success": true,
  "message": "Message de succès",
  "data": { ... }
}
```

### Erreur
```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": { ... }
}
```

### Pagination
```json
{
  "success": true,
  "message": "Liste récupérée avec succès",
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

## Validation

Tous les messages d'erreur de validation sont en français. Les règles principales :

**Notes :**
- `note` : entre 0 et 20
- `coefficient` : entre 1 et 10
- `date_evaluation` : format YYYY-MM-DD

**Absences :**
- `heure_fin` : doit être après `heure_debut`
- `duree_heures` : entre 1 et 24
- `motif` : maximum 500 caractères