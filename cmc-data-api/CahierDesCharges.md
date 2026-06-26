# Cahier des charges fonctionnel et technique — CMC Data API

## 1) Cahier des charges fonctionnel (CDC-F)

### 1.1 Contexte & objectif
Le projet **CMC Data API** est une **API REST** qui expose des données d’un **centre de formation** (structure académique, planification, évaluations). Le système est **ouvert** : **pas d’authentification ni rôles implémentés** actuellement.

### 1.2 Périmètre fonctionnel
Le système couvre :
- **Pôles** (`Pole`)
- **Espaces** (`Espace`) rattachés à un pôle
- **Niveaux** (`Niveau`)
- **Types de formation** (`TypeFormation`)
- **Filières** (`Filiere`) rattachées à un pôle + niveau + type de formation
- **Années** (`Annee`) rattachées à une filière
- **Groupes** (`Groupe`) rattachés à une année
- **Modules** (`Module`) rattachés à une année
- **Affectations** (`Affectation`) = association **(Groupe + Module + Formateur)**
- **Séances** (`Seance`) rattachées à une affectation
- **TimeRanges** (`TimeRange`) (créneau horaire)
- **Formateurs** (`Formateur`)
- **Stagiaires** (`Stagiaire`) rattachés à un groupe
- **Notes** (`Note`) rattachées à une séance + stagiaire

### 1.3 Acteurs
- **Front-office / application client**
- **Administrateur métier**
- **Service scolarité/coordination**
- **Formateur** (à sécuriser plus tard par auth)

### 1.4 Fonctionnalités attendues
- CRUD complet par ressource (liste, détail, création, mise à jour, suppression)
- Règles de gestion principales (intégrité référentielle, unicités, pivot central)
- Consultation avancée par “includes” (paramètre `?include=a,b.c` avec allowlist)

## 2) Cahier des charges technique (CDC-T)

### 2.1 Stack & contraintes
- PHP **8.3+**
- Laravel **13.x**
- Architecture REST
- Pas d’authentification/roles implémentés (à prévoir en phase 2)

### 2.2 Architecture applicative
- `routes/api.php` : routes versionnées `/v1`
- Controllers : `app/Http/Controllers/Api/*Controller.php`
- Validation : `app/Http/Requests/**/Store*Request.php` et `Update*Request.php`
- Sérialisation : `app/Http/Resources/*Resource.php`
- Modèle : `app/Models/*` (Eloquent)
- Pagination bornée (1..100, défaut 15)
- Includes via allowlist
- Eager-loading sur ressources centrales

### 2.3 Contrat API
- Endpoints `/api/v1/{resource}` (REST, apiResource)
- Pagination : paramètre `per_page` (max 100)
- Includes : paramètre `include` (CSV, intersection avec allowlist)

### 2.4 Modèle de données (résumé)
- Hiérarchie : Pole → Espace/Formateur/Filiere → Annee → Groupe/Module → Stagiaire
- Pivot : Affectation = (Groupe + Module + Formateur)
- Planning : Affectation → Seance → TimeRange
- Évaluation : Seance → Note, Stagiaire → Note
- Clés primaires non-incrémentales (string) sur certaines entités

### 2.5 Exigences non-fonctionnelles
- Performance : eager-loading, includes contrôlés, pagination, index DB
- Sécurité : API ouverte (cible phase 2 : auth, rate limiting, CORS, validation stricte)
- Qualité : tests PHPUnit, seeders/factories, logs applicatifs

### 2.6 Critères d’acceptation
- Pagination bornée à 100 max
- Includes ignorent tout champ non autorisé
- Unicité des affectations (triplet)
- Validation stricte des FK
- Suppression en cascade ou bloquée selon contraintes

---

*Document généré automatiquement à partir de l’analyse du code et du schéma du projet.*

