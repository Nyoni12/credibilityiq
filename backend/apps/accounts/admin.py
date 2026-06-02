from django.contrib import admin
from django.contrib.auth.admin import UserAdmin as BaseUserAdmin
from .models import User


@admin.register(User)
class UserAdmin(BaseUserAdmin):
    list_display = ['email', 'full_name', 'company', 'is_superadmin', 'is_active', 'created_at']
    list_filter = ['is_superadmin', 'is_active', 'company']
    search_fields = ['email', 'first_name', 'last_name']
    ordering = ['-created_at']
    fieldsets = (
        (None, {'fields': ('email', 'password')}),
        ('Personal', {'fields': ('first_name', 'last_name')}),
        ('Company', {'fields': ('company',)}),
        ('Permissions', {'fields': ('is_active', 'is_staff', 'is_superadmin', 'is_superuser', 'groups', 'user_permissions')}),
    )
    add_fieldsets = (
        (None, {'fields': ('email', 'first_name', 'last_name', 'password1', 'password2', 'company', 'is_superadmin')}),
    )
