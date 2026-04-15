# CMC Data API — Database Schema & System Structure

This document describes the **database schema** and the **backend structure** of the CMC Data API Laravel project.

> Stack (from `composer.json`)
> - PHP **8.3+**
> - Laravel Framework **13.x**
> - REST-oriented backend (open system: **no authentication/roles implemented**).

---

## 1) Domain Overview

The system models an academic/training center:

- **Pole**: high-level department.
- **Espace**: physical/organizational spaces inside a pole.
- **Formateur**: trainer/teacher attached to a pole.
- **Filiere**: program/track offered by a pole.
- **Niveau**: level/grade of a filiere.
- **TypeFormation**: training type (initial/continuing/etc.).
- **Annee**: academic year/session within a filiere.
- **Groupe**: learner group within an academic year.
- **Module**: training module taught within an academic year.
- **Affectation**: assignment of **(Formateur + Groupe + Module)**.
- **Seance**: session planned for an affectation.
- **DateSeance**: date/time of a seance (1–1).
- **Stagiaire**: trainee/student enrolled in a groupe.
- **Note**: evaluation result for a stagiaire in a seance.

---

## 2) Database Schema (Tables, Columns, Keys)

The schema is defined via Laravel migrations under `database/migrations/`.

### 2.1 `poles`
**Purpose:** Departments.

**Columns**
- `id` (PK, bigint)
- `libelle` (string, indexed)
- `created_at`, `updated_at`

**Relations**
- `poles.id` → `espaces.pole_id` (1–N, cascade on delete)
- `poles.id` → `formateurs.pole_id` (1–N, cascade on delete)
- `poles.id` → `filieres.pole_id` (1–N, cascade on delete)

---

### 2.2 `espaces`
**Purpose:** Spaces belonging to a pole.

**Columns**
- `id` (PK)
- `pole_id` (FK → `poles.id`, cascade on delete)
- `libelle` (string, indexed)
- timestamps

**Indexes**
- index on `libelle`
- composite index on (`pole_id`, `libelle`)

---

### 2.3 `formateurs`
**Purpose:** Trainers.

**Primary key**
- `mle` (PK, string(32)) **non-incrementing**

**Columns**
- `mle` (PK)
- `pole_id` (FK → `poles.id`, cascade on delete)
- `nom_prenom` (string)
- `statut` (string, nullable)
- `email_edu` (string, nullable, indexed)
- `mhs` (decimal(8,2), default 0)
- timestamps

**Indexes**
- composite index (`pole_id`, `nom_prenom`)

---

### 2.4 `niveaux`
**Purpose:** Reference table for academic levels.

**Columns**
- `id` (PK)
- `libelle` (string, **unique**)
- timestamps

---

### 2.5 `type_formations`
**Purpose:** Reference table for training types.

**Columns**
- `id` (PK)
- `libelle` (string, **unique**)
- timestamps

---

### 2.6 `filieres`
**Purpose:** Training programs.

**Primary key**
- `code_filiere` (PK, string(32)) **non-incrementing**

**Columns**
- `code_filiere` (PK)
- `pole_id` (FK → `poles.id`, cascade on delete)
- `niveau_id` (FK → `niveaux.id`, **restrict on delete**)
- `type_formation_id` (FK → `type_formations.id`, **restrict on delete**)
- `libelle` (string)
- timestamps

**Indexes**
- composite index (`pole_id`, `libelle`)

---

### 2.7 `annees`
**Purpose:** Academic years/sessions inside a filiere.

**Columns**
- `id` (PK)
- `libelle` (string, indexed)
- `filiere_code` (string(32), FK → `filieres.code_filiere`, cascade on delete)
- timestamps

**Constraints**
- FK: `filiere_code` → `filieres.code_filiere` (cascade on delete)
- **Unique** (`filiere_code`, `libelle`) to avoid duplicate year names per filiere

---

### 2.8 `groupes`
**Purpose:** Groups of trainees in a given academic year.

**Columns**
- `id` (PK)
- `annee_id` (FK → `annees.id`, cascade on delete)
- `code` (string, indexed)
- `effectif` (unsigned int, default 0)
- timestamps

**Constraints**
- **Unique** (`annee_id`, `code`)

---

### 2.9 `modules`
**Purpose:** Modules taught during an academic year.

**Primary key**
- `code_module` (PK, string(32)) **non-incrementing**

**Columns**
- `code_module` (PK)
- `annee_id` (FK → `annees.id`, cascade on delete)
- `libelle` (string, indexed)
- timestamps

**Indexes**
- composite index (`annee_id`, `libelle`)

---

### 2.10 `affectations`
**Purpose:** Central assignment entity connecting **Groupe + Module + Formateur**.

**Columns**
- `id` (PK)
- `groupe_id` (FK → `groupes.id`, cascade on delete)
- `module_code` (string(32), FK → `modules.code_module`, restrict on delete)
- `formateur_mle` (string(32), FK → `formateurs.mle`, restrict on delete)
- `mode` (string(32), default `presentiel`) — e.g. `presentiel|synchrone|async`
- `mh_affecte` (decimal(8,2), default 0)
- timestamps

**Constraints**
- FK: `module_code` → `modules.code_module` (**restrict**)
- FK: `formateur_mle` → `formateurs.mle` (**restrict**)
- **Unique triplet** (`groupe_id`, `module_code`, `formateur_mle`) name: `affectations_unique_triplet`

**Indexes**
- (`groupe_id`, `module_code`)
- (`formateur_mle`)

---

### 2.11 `seances`
**Purpose:** Sessions planned for an affectation.

**Columns**
- `id` (PK)
- `affectation_id` (FK → `affectations.id`, cascade on delete)
- `type` (string(32), default `cours`) — e.g. `cours|cc|efm|exam`
- timestamps

**Index**
- (`affectation_id`, `type`)

---

### 2.12 `date_seances`
**Purpose:** Date/time of a session.

**Columns**
- `id` (PK)
- `seance_id` (FK → `seances.id`, cascade on delete)
- `date` (datetime, indexed)
- timestamps

**Constraints**
- **Unique**(`seance_id`) → enforces **1 DateSeance per Seance**

---

### 2.13 `stagiaires`
**Purpose:** Trainees (students).

**Primary key**
- `cef` (PK, string(32)) **non-incrementing**

**Columns**
- `cef` (PK)
- `groupe_id` (FK → `groupes.id`, cascade on delete)
- `cni` (string(32), **unique**)
- `nom` (string)
- `prenom` (string)
- `date_naissance` (date, nullable)
- `genre` (string(16), nullable)
- timestamps

**Indexes**
- (`groupe_id`, `nom`, `prenom`)

---

### 2.14 `notes`
**Purpose:** Evaluation of one stagiaire for one seance.

**Columns**
- `id` (PK)
- `seance_id` (FK → `seances.id`, cascade on delete)
- `stagiaire_cef` (string(32), FK → `stagiaires.cef`, cascade on delete)
- `valeur` (decimal(5,2), nullable)
- `type` (string(32), default `cc`) — `cc|efm|exam|...`
- `decision` (string(64), nullable)
- timestamps

**Constraints**
- FK: `stagiaire_cef` → `stagiaires.cef` (cascade on delete)
- **Unique** (`seance_id`, `stagiaire_cef`, `type`) to prevent duplicate grading per type

**Index**
- (`stagiaire_cef`)

---

## 3) Relationships Summary

### Core hierarchy
- **Pole** hasMany **Espace**
- **Pole** hasMany **Formateur**
- **Pole** hasMany **Filiere**

- **Filiere** belongsTo **Niveau**
- **Filiere** belongsTo **TypeFormation**
- **Filiere** hasMany **Annee**

- **Annee** hasMany **Groupe**
- **Annee** hasMany **Module**

- **Groupe** hasMany **Stagiaire**

### Central assignment
- **Affectation** belongsTo **Groupe**
- **Affectation** belongsTo **Module**
- **Affectation** belongsTo **Formateur**

### Planning
- **Affectation** hasMany **Seance**
- **Seance** hasOne **DateSeance**

### Evaluation
- **Seance** hasMany **Note**
- **Stagiaire** hasMany **Note**

---

## 4) Laravel Application Structure

This project follows a layered structure (typical clean architecture approach within Laravel conventions):

### 4.1 Models (`app/Models`)
Eloquent models represent the domain entities.

Notable details:
- Non-incrementing string primary keys:
  - `Formateur::$primaryKey = 'mle'`
  - `Filiere::$primaryKey = 'code_filiere'`
  - `Module::$primaryKey = 'code_module'`
  - `Stagiaire::$primaryKey = 'cef'`

### 4.2 Migrations (`database/migrations`)
- Defines tables, constraints, indexes.
- Uses `constrained()` + `cascadeOnDelete()` / `restrictOnDelete()` to enforce referential integrity.

### 4.3 Factories (`database/factories`)
Factories generate realistic data using Faker.

Factories created for:
- `Pole`, `Espace`, `Formateur`, `Filiere`, `Niveau`, `TypeFormation`, `Annee`, `Groupe`, `Module`, `Affectation`, `Seance`, `DateSeance`, `Stagiaire`, `Note`.

Good practices used:
- relationship-aware FK generation by assigning `Model::factory()` to FK fields
- factory states for controlled variation:
  - Affectation: `presentiel()`, `synchrone()`, `async()`, plus `coherentAnnee()` helper
  - Seance: `cours()`, `efm()`
  - Note: `cc()`, `efm()`, `exam()`

### 4.4 Seeders (`database/seeders`)
Seeding is organized by dependency order:

1. `ReferenceSeeder`
   - Seeds stable reference data (`Niveau`, `TypeFormation`, `Pole`).

2. `AcademicStructureSeeder`
   - Seeds academic structure (`Filiere` → `Annee` → `Groupe` + `Module`).

3. `UsersSeeder`
   - Seeds people (`Formateur`, `Stagiaire`).

4. `PlanningSeeder`
   - Seeds assignments & planning (`Affectation` + `Seance` + `DateSeance`).

5. `EvaluationSeeder`
   - Seeds evaluations (`Note`).

`DatabaseSeeder` calls them in this order.

---

## 5) Data Integrity Rules (Enforced)

- A **Filiere** always has a `niveau_id` + `type_formation_id`.
- An **Annee** always belongs to a **Filiere** (`filiere_code`).
- A **Groupe** always belongs to an **Annee**.
- A **Stagiaire** always belongs to a **Groupe**.
- An **Affectation** always links exactly:
  - 1 Groupe
  - 1 Module
  - 1 Formateur
- A **Seance** always belongs to an **Affectation**.
- A **DateSeance** always belongs to a **Seance** (and only one per seance).
- A **Note** always belongs to:
  - 1 Seance
  - 1 Stagiaire

---

## 6) How to Seed the Database

From the project root:

```bash
php artisan migrate:fresh --seed
```

> Note: your current environment shows a Postgres PDO driver issue (`could not find driver`). Ensure the proper DB driver/extension is available for your configured `DB_CONNECTION`.

---

## 7) Extensibility Notes

- For larger datasets, prefer:
  - chunked inserts
  - `createQuietly()`/`updateQuietly()` when audit timestamps/events are not required
  - using transactions per seeder if you want all-or-nothing behavior

- If you later introduce authentication/roles, keep it separate from this academic domain (bounded context separation).

