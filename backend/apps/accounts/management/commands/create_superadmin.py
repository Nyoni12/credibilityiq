from django.core.management.base import BaseCommand
from django.conf import settings
from apps.accounts.models import User


class Command(BaseCommand):
    help = 'Creates the default super admin if none exists'

    def handle(self, *args, **options):
        email = settings.SUPERADMIN_EMAIL
        password = settings.SUPERADMIN_PASSWORD

        user, created = User.objects.get_or_create(
            email=email,
            defaults={
                'first_name': 'Super',
                'last_name': 'Admin',
                'is_superadmin': True,
                'is_staff': True,
                'is_active': True,
            }
        )
        # Always sync the password from env so rebuilds don't leave a stale hash
        user.set_password(password)
        user.is_superadmin = True
        user.is_active = True
        user.save(update_fields=['password', 'is_superadmin', 'is_active'])

        if created:
            self.stdout.write(self.style.SUCCESS(f'Super admin created: {email}'))
        else:
            self.stdout.write(self.style.SUCCESS(f'Super admin password synced: {email}'))
