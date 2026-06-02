from django.core.management.base import BaseCommand
from django.conf import settings
from apps.accounts.models import User


class Command(BaseCommand):
    help = 'Creates the default super admin if none exists'

    def handle(self, *args, **options):
        email = settings.SUPERADMIN_EMAIL
        password = settings.SUPERADMIN_PASSWORD

        if not User.objects.filter(is_superadmin=True).exists():
            User.objects.create_superuser(
                email=email,
                password=password,
                first_name='Super',
                last_name='Admin',
            )
            self.stdout.write(self.style.SUCCESS(f'Super admin created: {email}'))
        else:
            self.stdout.write('Super admin already exists.')
