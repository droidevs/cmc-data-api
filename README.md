# CMC Data API

A centralized academic data management platform designed for the **Cité des Métiers et des Compétences (CMC) Béni Mellal**.

The project provides a RESTful API that serves as a single source of truth for academic and administrative data, including training programs, specialties, modules, trainers, trainees, groups, schedules, evaluations, and more.

## Overview

Academic data within training centers is often distributed across multiple Excel files and managed by different departments. This fragmentation makes reporting, filtering, analytics, and data consistency difficult.

CMC Data API addresses these challenges by:

* Centralizing academic data.
* Providing advanced filtering capabilities.
* Importing data from Excel and CSV files.
* Exposing a secure and versioned REST API.
* Supporting pagination, sorting, searching, and analytics.
* Serving as a foundation for dashboards, mobile applications, and reporting tools.

---

## Features

### Academic Structure Management

* Pôles
* Filières
* Specialties
* Academic Years
* Modules
* Groups

### Human Resources

* Trainers (Formateurs)
* Trainee Management
* Administrative Staff

### Academic Operations

* Course Sessions
* Timetables
* Evaluations
* Attendance Tracking
* Academic Progress Monitoring

### Data Import

* Excel Import
* CSV Import
* Validation and Error Reporting
* Duplicate Detection
* Referential Integrity Verification

### API Features

* RESTful Architecture
* API Versioning
* Filtering
* Sorting
* Searching
* Pagination
* Resource Transformation
* Validation
* Authentication & Authorization

### Performance

* Query Optimization
* Response Caching
* Eager Loading
* Index Optimization

### Security

* Request Validation
* Authentication
* Authorization Policies
* Secure API Responses
* Audit Logging

---

## Technology Stack

### Backend

* PHP 8.3+
* Laravel 12
* Eloquent ORM

### Database

* SQLite (Development)
* MySQL / PostgreSQL (Production)

### Caching

* Redis

### Data Processing

* Laravel Excel
* CSV Processing

### Testing

* PHPUnit
* Pest

---

## Architecture

The project follows a layered architecture:

```text
Routes
   ↓
Form Requests
   ↓
Filters
   ↓
Services
   ↓
Models
   ↓
Resources
```

### Key Principles

* SOLID Principles
* Clean Architecture
* Separation of Concerns
* Fat Models, Thin Controllers
* Service Layer Pattern
* Resource Transformation Pattern

---

## Project Structure

```text
app/
├── Actions/
├── Enums/
├── Filters/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Observers/
├── Policies/
├── Services/
└── Support/

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php
└── web.php
```

---

## Installation

### Clone the repository

```bash
git clone https://github.com/your-username/cmc-data-api.git
cd cmc-data-api
```

### Install dependencies

```bash
composer install
```

### Configure environment

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### Configure database

Update your `.env` file.

Run migrations:

```bash
php artisan migrate
```

### Seed data

```bash
php artisan db:seed
```

### Start development server

```bash
php artisan serve
```

---

## Running Tests

```bash
php artisan test
```

or

```bash
vendor/bin/pest
```

---

## API Documentation

API endpoints are versioned:

```text
/api/v1/*
```

Example:

```http
GET /api/v1/formateurs
GET /api/v1/stagiaires
GET /api/v1/groupes
GET /api/v1/modules
```

---

## Filtering Example

```http
GET /api/v1/formateurs?search=ahmed
```

```http
GET /api/v1/stagiaires?filiere=DD&annee=2
```

```http
GET /api/v1/seances?date=2026-05-01
```

---

## Import Example

```http
POST /api/v1/imports/formateurs
```

Upload:

```text
formateurs.xlsx
```

The system automatically:

* Validates rows
* Detects errors
* Creates missing entities
* Generates import reports

---

## Future Enhancements

* Dashboard & Analytics
* Data Warehouse Integration
* Machine Learning Insights
* Mobile Application Support
* Real-Time Notifications
* Advanced Reporting
* Business Intelligence Integration

---

## Author

Developed as an academic and professional project for:

**OFPPT – Cité des Métiers et des Compétences Béni Mellal**

Digital Development – Full Stack Web Development

---

## License

This project is released under the MIT License.
