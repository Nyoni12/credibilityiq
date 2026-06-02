"""
Seed the database with realistic sample data for CredibilityIQ.
Run via: python manage.py seed_data
Called automatically on first docker compose up.
"""
import random
import uuid
from datetime import timedelta
from django.core.management.base import BaseCommand
from django.utils import timezone
from apps.accounts.models import User
from apps.companies.models import Company, CompanyValue
from apps.assessments.models import Assessment, AssessmentResponse, ValueRating, TrainingProgram


COMPANIES = [
    {
        'name': 'Nexus Financial Group',
        'industry': 'Banking & Finance',
        'tier': 'enterprise',
        'values': [
            ('Integrity',         'We act with complete honesty and transparency in every transaction.',       95000),
            ('Client Focus',      'We put client outcomes at the centre of every decision.',                  80000),
            ('Accountability',    'We own our actions and deliver on every commitment.',                      70000),
            ('Innovation',        'We challenge conventions to find smarter financial solutions.',            60000),
            ('Collaboration',     'We achieve more together than any individual could alone.',                55000),
            ('Risk Awareness',    'We identify and manage risk proactively at every level.',                  75000),
            ('Excellence',        'We hold ourselves to the highest professional standards.',                 65000),
            ('Inclusivity',       'We build a culture where every voice is valued and heard.',                40000),
            ('Sustainability',    'We make decisions with long-term societal impact in mind.',                35000),
            ('Agility',           'We adapt quickly to change without losing focus.',                         45000),
            ('Trust',             'We earn and protect the trust of clients and each other.',                 85000),
            ('Communication',     'We share information openly, clearly, and in a timely manner.',            50000),
        ],
        'admin': {'email': 'admin@nexusfinancial.com', 'first': 'Sarah', 'last': 'Mokoena', 'pw': 'Nexus@2025'},
        'scores': [6.8, 5.2, 7.1, 4.9, 6.3, 8.1, 5.8, 6.7, 4.4, 7.2, 6.0, 5.5],
    },
    {
        'name': 'PeakLogix Solutions',
        'industry': 'Logistics & Supply Chain',
        'tier': 'professional',
        'values': [
            ('On-Time Delivery',  'We commit to delivering every shipment on schedule.',                      120000),
            ('Safety First',      'We never compromise the safety of our team or cargo.',                     100000),
            ('Efficiency',        'We eliminate waste and optimise every route and process.',                  80000),
            ('Transparency',      'We give clients real-time visibility into every shipment.',                 60000),
            ('Reliability',       'We are consistent and dependable under all conditions.',                    90000),
            ('Team Spirit',       'We support each other to get the job done no matter what.',                50000),
            ('Customer Service',  'We go the extra mile to resolve issues swiftly.',                          70000),
            ('Innovation',        'We invest in technology to stay ahead of the industry.',                    55000),
        ],
        'admin': {'email': 'admin@peaklogix.com', 'first': 'James', 'last': 'Dube', 'pw': 'PeakLogix@2025'},
        'scores': [5.5, 8.4, 6.9, 5.1, 7.3, 7.8, 5.9, 4.7],
    },
    {
        'name': 'BrightMinds Academy',
        'industry': 'Education & Training',
        'tier': 'starter',
        'values': [
            ('Student Success',   'Every decision we make is measured by student outcomes.',                  45000),
            ('Curiosity',         'We foster a love of learning in all that we do.',                          30000),
            ('Respect',           'We honour the dignity of every learner and educator.',                     25000),
            ('Excellence',        'We pursue the highest standards in teaching and assessment.',              40000),
            ('Inclusivity',       'We create an environment where every learner can thrive.',                 35000),
            ('Growth Mindset',    'We believe ability is developed through dedication and effort.',           28000),
        ],
        'admin': {'email': 'admin@brightminds.com', 'first': 'Thandiwe', 'last': 'Khumalo', 'pw': 'BrightMinds@2025'},
        'scores': [8.2, 7.5, 8.8, 7.1, 8.0, 6.9],
    },
]

TRAINING_PROGRAMS = [
    {
        'title': 'Integrity in the Workplace',
        'description': 'A practical workshop on ethical decision-making, whistleblowing policies, and building a culture of honesty.',
        'threshold': 6.0,
        'targets': ['Integrity', 'Trust', 'Accountability'],
    },
    {
        'title': 'High-Performance Communication',
        'description': 'Coaching programme to improve feedback cycles, active listening, and clear written communication.',
        'threshold': 6.5,
        'targets': ['Communication', 'Collaboration', 'Team Spirit'],
    },
    {
        'title': 'Innovation & Creative Problem Solving',
        'description': 'Design-thinking and lean methodology bootcamp for teams struggling to innovate.',
        'threshold': 5.5,
        'targets': ['Innovation', 'Agility', 'Growth Mindset'],
    },
    {
        'title': 'Client-Centric Service Excellence',
        'description': 'CX training covering empathy, expectation management, and turning complaints into loyalty.',
        'threshold': 6.0,
        'targets': ['Client Focus', 'Customer Service', 'Reliability'],
    },
    {
        'title': 'Safety Leadership Programme',
        'description': 'Immersive safety culture training for managers and frontline staff.',
        'threshold': 7.0,
        'targets': ['Safety First', 'Risk Awareness'],
    },
    {
        'title': 'Inclusive Leadership',
        'description': 'Building diverse, equitable teams where every employee feels they belong and can contribute.',
        'threshold': 6.5,
        'targets': ['Inclusivity', 'Respect', 'Student Success'],
    },
]


def _jitter(base: float, spread: float = 1.2) -> int:
    return max(1, min(10, round(base + random.uniform(-spread, spread))))


class Command(BaseCommand):
    help = 'Seed realistic sample data into the database'

    def add_arguments(self, parser):
        parser.add_argument('--force', action='store_true', help='Re-seed even if data exists')

    def handle(self, *args, **options):
        if Company.objects.exists() and not options['force']:
            self.stdout.write('Seed data already present. Use --force to re-seed.')
            return

        random.seed(42)

        # Build training programs first (no FKs needed)
        programs_map: dict[str, TrainingProgram] = {}
        for tp_data in TRAINING_PROGRAMS:
            prog, _ = TrainingProgram.objects.get_or_create(
                title=tp_data['title'],
                defaults={
                    'description': tp_data['description'],
                    'trigger_threshold': tp_data['threshold'],
                },
            )
            for target_name in tp_data['targets']:
                programs_map.setdefault(target_name, prog)

        for company_data in COMPANIES:
            self.stdout.write(f"  Seeding {company_data['name']}…")

            # Company
            company, _ = Company.objects.get_or_create(
                name=company_data['name'],
                defaults={
                    'industry': company_data['industry'],
                    'subscription_tier': company_data['tier'],
                    'is_active': True,
                },
            )

            # Company admin user
            adm = company_data['admin']
            if not User.objects.filter(email=adm['email']).exists():
                User.objects.create_user(
                    email=adm['email'],
                    password=adm['pw'],
                    first_name=adm['first'],
                    last_name=adm['last'],
                    company=company,
                )

            # Values
            value_objs = []
            for idx, (name, desc, weight) in enumerate(company_data['values'], start=1):
                val, _ = CompanyValue.objects.get_or_create(
                    company=company, order=idx,
                    defaults={'name': name, 'description': desc, 'financial_weight': weight},
                )
                value_objs.append(val)

            # Link training programs to matching values
            for val in value_objs:
                if val.name in programs_map:
                    programs_map[val.name].company_values.add(val)

            # Assessment
            assessment, _ = Assessment.objects.get_or_create(
                company=company,
                title=f'{company.name} – Q2 2025 Culture Assessment',
                defaults={
                    'is_active': True,
                    'created_at': timezone.now() - timedelta(days=14),
                },
            )

            # Responses (simulate 35 staff members)
            base_scores = company_data['scores']
            existing_responses = assessment.responses.count()
            responses_to_create = max(0, 35 - existing_responses)

            for _ in range(responses_to_create):
                response = AssessmentResponse.objects.create(
                    assessment=assessment,
                    session_token=uuid.uuid4(),
                )
                ratings = [
                    ValueRating(
                        response=response,
                        company_value=val,
                        score=_jitter(base_scores[i], spread=1.5),
                    )
                    for i, val in enumerate(value_objs)
                ]
                ValueRating.objects.bulk_create(ratings)

            self.stdout.write(self.style.SUCCESS(
                f'    {company.name}: {len(value_objs)} values, {assessment.responses.count()} responses'
            ))

        self.stdout.write(self.style.SUCCESS('\nSeed complete.'))
        self.stdout.write('\nCompany Admin logins:')
        for c in COMPANIES:
            a = c['admin']
            self.stdout.write(f"  {a['email']}  /  {a['pw']}")
