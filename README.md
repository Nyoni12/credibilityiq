# CredibilityIQ — Corporate Credibility Scorecard Platform

A multi-tenant SaaS platform that measures how well company staff live out stated corporate values — and converts performance gaps directly into financial loss figures.

---

## Running on a New Machine

### Prerequisites

Make sure the following are installed before you begin:

| Tool | Version | Download |
|------|---------|----------|
| Docker Desktop | Latest | https://www.docker.com/products/docker-desktop |
| Git | Any | https://git-scm.com/downloads |

No Python, Node.js, or PostgreSQL installation needed — Docker handles everything.

---

### Step 1 — Clone the repository

```bash
git clone https://github.com/Nyoni12/credibilityiq.git
cd credibilityiq
```

### Step 2 — Create your environment file

Copy the example file and leave the defaults as-is for local development:

```bash
# Linux / macOS
cp .env.example .env

# Windows (Command Prompt)
copy .env.example .env

# Windows (PowerShell)
Copy-Item .env.example .env
```

The default `.env` values work out of the box locally. Only change them for a production deployment.

### Step 3 — Start Docker Desktop

Open **Docker Desktop** from your applications or Start Menu and wait until the whale icon in the system tray is **steady** (not animating). This takes 30–60 seconds.

### Step 4 — Build and start all services

```bash
docker compose up --build
```

The first build takes **3–5 minutes** (downloading base images, installing Python and Node packages). Subsequent starts take under 30 seconds.

On startup the system automatically:
1. Runs all database migrations
2. Creates the Super Admin account
3. Seeds sample company data with 105 staff responses

### Step 5 — Open the app

| Service | URL |
|---------|-----|
| App (frontend) | http://localhost:3000 |
| REST API | http://localhost:8000/api/ |
| Django Admin | http://localhost:8000/django-admin/ |

---

## Login Accounts

### Super Admin

Has access to all companies, user management, and platform settings.

| Field | Value |
|-------|-------|
| Email | `admin@credibilityiq.com` |
| Password | `Admin@123456` |

---

### Company Admin Accounts (pre-seeded)

Three sample companies are seeded with realistic assessment data on first boot.

#### Nexus Financial Group *(Enterprise — 12 values, 35 responses)*
| Field | Value |
|-------|-------|
| Email | `admin@nexusfinancial.com` |
| Password | `Nexus@2025` |

#### PeakLogix Solutions *(Professional — 8 values, 35 responses)*
| Field | Value |
|-------|-------|
| Email | `admin@peaklogix.com` |
| Password | `PeakLogix@2025` |

#### BrightMinds Academy *(Starter — 6 values, 35 responses)*
| Field | Value |
|-------|-------|
| Email | `admin@brightminds.com` |
| Password | `BrightMinds@2025` |

> Each company admin can only see their own company's data. Log in with any account above to explore a fully populated scorecard with financial leakage figures and training recommendations.

---

## Useful Commands

```bash
# Start in background (detached)
docker compose up -d --build

# View live logs
docker compose logs -f

# Stop all services
docker compose down

# Stop and wipe the database (full reset)
docker compose down -v

# Re-seed the database manually
docker compose exec backend python manage.py seed_data --force

# Open a Django shell
docker compose exec backend python manage.py shell

# Create a new database migration
docker compose exec backend python manage.py makemigrations
```

---

## Architecture

```
credibilityiq/
├── docker-compose.yml
├── .env                          ← environment config (not committed)
├── .env.example                  ← safe template to copy
├── backend/                      ← Django REST API + PostgreSQL
│   ├── apps/
│   │   ├── accounts/             ← Custom User model, JWT auth
│   │   ├── companies/            ← Company & CompanyValue models
│   │   ├── assessments/          ← Assessment, Response, Rating, Training
│   │   ├── survey/               ← Public survey endpoints (no auth)
│   │   └── reports/              ← PDF export, dashboard summary
│   ├── services/scoring.py       ← All scoring business logic
│   └── templates/reports/        ← WeasyPrint branded PDF template
└── frontend/                     ← Next.js 14 + Tailwind CSS
    └── src/
        ├── pages/
        │   ├── login.tsx          ← JWT login (auto-redirects by role)
        │   ├── dashboard/         ← Company Admin home & survey link
        │   ├── setup/values       ← Configure up to 12 values + weights
        │   ├── assessments/       ← Create & manage assessments
        │   ├── scorecard/[id]     ← 4-tab scorecard (ring, chart, $, training)
        │   ├── survey/[token]     ← Anonymous public staff survey
        │   └── admin/             ← Super Admin panel
        ├── components/
        │   ├── CredibilityRing    ← SVG score ring (colour-coded by band)
        │   ├── ValueBarChart      ← Horizontal bar chart (Recharts)
        │   ├── FinancialLeakageTable ← $ leakage per value + total
        │   └── TrainingRecommendations ← Flagged training cards
        ├── context/AuthContext    ← JWT auth state + auto token refresh
        └── lib/api.ts             ← Axios client with silent refresh
```

---

## User Roles

| Role | Access |
|------|--------|
| **Super Admin** | All companies, user onboarding, license management |
| **Company Admin** | Own company only — values, assessments, scorecard, PDF |
| **Staff** | Anonymous survey via unique link — no account needed |

---

## Scoring Formulas

```
avg_score      = mean of all staff ratings for a given value (1–10)
gap_percentage = (1 − avg_score / 10) × 100
financial_loss = (gap_percentage / 100) × financial_weight ($)
overall_score  = mean(all avg_scores) / 10 × 100
```

A training program is flagged when `avg_score < trigger_threshold`.

---

## API Reference

| Method | Endpoint | Auth Required |
|--------|----------|---------------|
| POST | `/api/auth/login/` | Public |
| POST | `/api/auth/logout/` | Any user |
| GET | `/api/auth/token/refresh/` | Public |
| GET | `/api/dashboard/summary/` | Company Admin+ |
| GET / POST | `/api/companies/` | Super Admin |
| GET / PUT / DELETE | `/api/companies/{id}/` | Super Admin |
| GET / POST | `/api/companies/{id}/values/` | Company Admin+ |
| PUT | `/api/companies/{id}/values/bulk/` | Company Admin+ |
| POST | `/api/assessments/` | Company Admin+ |
| GET / PATCH | `/api/assessments/{id}/` | Company Admin+ |
| GET | `/api/assessments/{id}/scorecard/` | Company Admin+ |
| GET | `/api/assessments/{id}/report/pdf/` | Company Admin+ |
| GET | `/api/survey/{token}/` | Public |
| POST | `/api/survey/{token}/submit/` | Public |

---

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DJANGO_SECRET_KEY` | Django secret (change in production) | — |
| `DEBUG` | Django debug mode | `True` |
| `DATABASE_URL` | PostgreSQL connection string | set by Docker |
| `CORS_ALLOWED_ORIGINS` | Allowed frontend origins | `http://localhost:3000` |
| `SUPERADMIN_EMAIL` | Auto-created super admin email | `admin@credibilityiq.com` |
| `SUPERADMIN_PASSWORD` | Auto-created super admin password | `Admin@123456` |
| `NEXT_PUBLIC_API_URL` | Backend URL (used by frontend) | `http://localhost:8000` |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Django 4.2, Django REST Framework, SimpleJWT |
| Database | PostgreSQL 15 |
| PDF Generation | WeasyPrint |
| Frontend | Next.js 14, TypeScript, Tailwind CSS |
| Charts | Recharts |
| HTTP Client | Axios (with silent JWT refresh) |
| Container | Docker + Docker Compose |
