from django.db import migrations

# Keyword fragments → canonical CFA value name
MATCH_MAP = [
    (['team'],                                    'Teamwork'),
    (['integr'],                                  'Integrity'),
    (['reput'],                                   'Reputation'),
    (['respect'],                                 'Respect for self and others'),
    (['humil'],                                   'Humility'),
    (['empat'],                                   'Empathy'),
    (['fair'],                                    'Fairness'),
    (['hardwork', 'hard work', 'hard-work',
      'diligen', 'work eth', 'dedicat'],          'Hardwork'),
    (['responsib'],                               'Responsibility'),
    (['accountab'],                               'Accountability'),
    (['trust'],                                   'Trustworthiness'),
    (['honest'],                                  'Honesty without offense'),
]


def migrate_values_to_cfa(apps, schema_editor):
    CompanyValue = apps.get_model('companies', 'CompanyValue')
    for value in CompanyValue.objects.all():
        name_lower = value.name.lower()
        for keywords, canonical in MATCH_MAP:
            if any(kw in name_lower for kw in keywords):
                if value.name != canonical:
                    value.name = canonical
                    value.save(update_fields=['name'])
                break


def reverse_migration(apps, schema_editor):
    pass  # Original names are not stored — cannot reverse


class Migration(migrations.Migration):

    dependencies = [
        ('companies', '0002_company_domain'),
    ]

    operations = [
        migrations.RunPython(migrate_values_to_cfa, reverse_migration),
    ]
