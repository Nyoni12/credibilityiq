from django.contrib import admin
from .models import Company, CompanyValue


class CompanyValueInline(admin.TabularInline):
    model = CompanyValue
    extra = 0
    fields = ['order', 'name', 'description', 'financial_weight']


@admin.register(Company)
class CompanyAdmin(admin.ModelAdmin):
    list_display = ['name', 'industry', 'subscription_tier', 'is_active', 'created_at']
    list_filter = ['subscription_tier', 'is_active']
    search_fields = ['name', 'industry']
    inlines = [CompanyValueInline]
    readonly_fields = ['assessment_link_token', 'created_at', 'updated_at']


@admin.register(CompanyValue)
class CompanyValueAdmin(admin.ModelAdmin):
    list_display = ['name', 'company', 'financial_weight', 'order']
    list_filter = ['company']
    search_fields = ['name', 'company__name']
