# Guide de test Postman - EduSmartCM API

## 🚀 Configuration rapide

### 1. Serveur Laravel démarré
Le serveur Laravel est déjà démarré sur : **http://127.0.0.1:8000**

### 2. Données de test créées
- **Utilisateur de test** : `test@edusmartcm.com` / `password`
- **Base de données** : SQLite avec données de test
- **5 notes** et **5 absences** de test créées

## 📥 Import de la collection Postman

1. Ouvrez Postman
2. Cliquez sur **Import**
3. Sélectionnez le fichier `postman_collection.json`
4. La collection "EduSmartCM - Notes & Absences API" sera importée

## 🔐 Authentification

### Étape 1 : Login
1. Allez dans **Authentication > Login**
2. Exécutez la requête (les identifiants sont déjà configurés)
3. Le token sera automatiquement sauvé dans les variables de collection

### Étape 2 : Test de l'authentification
1. Exécutez **Authentication > Get User Info** pour vérifier que le token fonctionne

## 📝 Tests des Notes

### Liste des notes
- **GET** `/api/v1/notes` - Récupère toutes les notes avec pagination
- Filtres disponibles : `type_evaluation`, `date_evaluation_from`, `per_page`

### Création d'une note
- **POST** `/api/v1/notes` - Crée une nouvelle note
- L'ID de la note créée sera automatiquement sauvé pour les tests suivants

### Lecture d'une note
- **GET** `/api/v1/notes/{id}` - Récupère une note spécifique

### Modification d'une note
- **PATCH** `/api/v1/notes/{id}` - Met à jour une note (champs partiels)

### Suppression d'une note
- **DELETE** `/api/v1/notes/{id}` - Supprime une note

## 🏃‍♂️ Tests des Absences

### Liste des absences
- **GET** `/api/v1/absences` - Récupère toutes les absences avec pagination
- Filtres disponibles : `statut`, `date_absence_from`, `per_page`

### Création d'une absence
- **POST** `/api/v1/absences` - Crée une nouvelle absence
- L'ID de l'absence créée sera automatiquement sauvé

### Lecture d'une absence
- **GET** `/api/v1/absences/{id}` - Récupère une absence spécifique

### Modification d'une absence
- **PATCH** `/api/v1/absences/{id}` - Met à jour une absence (champs partiels)

### Modification du statut uniquement
- **PATCH** `/api/v1/absences/{id}/statut` - Met à jour uniquement le statut et le justificatif

### Suppression d'une absence
- **DELETE** `/api/v1/absences/{id}` - Supprime une absence

## 🎯 Ordre de test recommandé

1. **Login** pour obtenir le token
2. **List Notes** pour voir les données existantes
3. **Create Note** pour tester la création
4. **Get Note** pour vérifier la note créée
5. **Update Note** pour tester la modification
6. **List Absences** pour voir les absences existantes
7. **Create Absence** pour tester la création
8. **Update Absence Status** pour tester la fonctionnalité spéciale
9. **Delete** pour tester la suppression (optionnel)

## 📊 Exemples de réponses

### Succès (200/201)
```json
{
  "success": true,
  "message": "Note créée avec succès",
  "data": {
    "id": "uuid",
    "type_evaluation": "DEVOIR",
    "note": 16.5,
    "coefficient": 2,
    ...
  }
}
```

### Erreur de validation (422)
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "note": ["La note ne peut pas être supérieure à 20."]
  }
}
```

### Non autorisé (401)
```json
{
  "message": "Unauthenticated."
}
```

## 🔧 Variables de collection

Les variables suivantes sont automatiquement gérées :
- `base_url` : http://127.0.0.1:8000/api
- `auth_token` : Token Sanctum (auto-rempli après login)
- `note_id` : ID de la dernière note créée
- `absence_id` : ID de la dernière absence créée

## 🚨 Dépannage

### Erreur 401 "Unauthenticated"
- Vérifiez que vous avez exécuté le **Login** en premier
- Le token est valide et sauvé dans les variables

### Erreur 404 "Not Found"
- Vérifiez que le serveur Laravel est démarré
- L'URL de base est correcte : http://127.0.0.1:8000

### Erreur 422 "Validation Error"
- Vérifiez les données envoyées dans le body
- Consultez les messages d'erreur en français

### Erreur 500 "Server Error"
- Vérifiez les logs Laravel : `php artisan log:tail`
- La base de données SQLite est accessible

## 📋 Données de test disponibles

Vous pouvez utiliser ces UUIDs existants pour vos tests :
- Consultez d'abord **List Notes** et **List Absences** pour voir les IDs réels
- Ou créez de nouvelles données avec les endpoints **Create**

## 🎉 Prêt à tester !

Votre environnement est maintenant configuré. Commencez par le **Login** et explorez tous les endpoints !