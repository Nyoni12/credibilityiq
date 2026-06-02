# CredibilityIQ — Corporate Credibility Scorecard Platform

## Quick Start (Docker)

```bash
cd credibilityiq
docker compose up --build
```

- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api/
- Django Admin: http://localhost:8000/django-admin/

**Default Super Admin login:**
- Email: `admin@credibilityiq.com`
- Password: `Admin@123456`

---

## Architecture

```
credibilityiq/
├── docker-compose.yml
├── .env                          ← environment config
├── backend/                      ← Django REST API
│   ├── apps/
│   │   ├── accounts/             ← User model, JWT auth
│   │   ├── companies/            ← Company & CompanyValue models
│   │   ├── assessments/          ← Assessment, Response, Rating, Training
│   │   ├── survey/               ← Public survey (no auth)
│   │   └── reports/              ← PDF export, dashboard summary
│   ├── services/scoring.py       ← All business logic
│   └── templates/reports/        ← WeasyPrint PDF template
└── frontend/                     ← Next.js + Tailwind
    └── src/
        ├── pages/
        │   ├── login.tsx
        │   ├── dashboard/        ← Company Admin home
        │   ├── setup/values      ← Value configuration
        │   ├── assessments/      ← Assessment management
        │   ├── scorecard/[id]    ← Full scorecard with 4 tabs
        │   ├── survey/[token]    ← Public anonymous survey
        │   └── admin/            ← Super Admin panel
        ├── components/           ← CredibilityRing, Charts, Tables
        └── lib/api.ts            ← Axios client with JWT refresh
```

## User Roles

| Role | Access |
|------|--------|
| **Super Admin** | All companies, onboarding, license management |
| **Company Admin** | Own company only — values, assessments, scorecard |
| **Staff** | Anonymous survey via unique link (no login) |

## API Endpoints

| Method | Endpoint | Auth |
|--------|----------|------|
| POST | `/api/auth/login/` | Public |
| GET | `/api/dashboard/summary/` | Any Admin |
| GET/POST | `/api/companies/` | Super Admin |
| GET/POST | `/api/companies/{id}/values/` | Company Admin+ |
| PUT | `/api/companies/{id}/values/bulk/` | Company Admin+ |
| POST | `/api/assessments/` | Company Admin+ |
| GET | `/api/assessments/{id}/scorecard/` | Company Admin+ |
| GET | `/api/assessments/{id}/report/pdf/` | Company Admin+ |
| GET | `/api/survey/{token}/` | Public |
| POST | `/api/survey/{token}/submit/` | Public |

## Scoring Formulas

```python
avg_score       = mean(all ratings for a value)
gap_percentage  = (1 - avg_score / 10) * 100
financial_loss  = (gap_percentage / 100) * financial_weight
overall_score   = mean(all avg_scores) / 10 * 100
```

## Environment Variables (.env)

```env
DJANGO_SECRET_KEY=your-secret-key
DEBUG=True
DATABASE_URL=postgresql://postgres:postgres123@db:5432/credibilityiq
CORS_ALLOWED_ORIGINS=http://localhost:3000
SUPERADMIN_EMAIL=admin@credibilityiq.com
SUPERADMIN_PASSWORD=Admin@123456
NEXT_PUBLIC_API_URL=http://localhost:8000
```
