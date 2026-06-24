from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('companies', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='company',
            name='domain',
            field=models.CharField(blank=True, db_index=True, max_length=253),
        ),
    ]
