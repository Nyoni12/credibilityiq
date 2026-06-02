import uuid
from django.db import models


class Company(models.Model):
    SUBSCRIPTION_TIERS = [
        ('starter', 'Starter'),
        ('professional', 'Professional'),
        ('enterprise', 'Enterprise'),
    ]

    name = models.CharField(max_length=200)
    logo = models.ImageField(upload_to='logos/', null=True, blank=True)
    industry = models.CharField(max_length=100, blank=True)
    subscription_tier = models.CharField(max_length=20, choices=SUBSCRIPTION_TIERS, default='starter')
    assessment_link_token = models.UUIDField(default=uuid.uuid4, unique=True, editable=False)
    is_active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        db_table = 'companies'
        verbose_name_plural = 'companies'
        ordering = ['-created_at']

    def __str__(self):
        return self.name

    def regenerate_token(self):
        self.assessment_link_token = uuid.uuid4()
        self.save(update_fields=['assessment_link_token'])


class CompanyValue(models.Model):
    company = models.ForeignKey(Company, on_delete=models.CASCADE, related_name='values')
    name = models.CharField(max_length=100)
    description = models.TextField(blank=True)
    financial_weight = models.DecimalField(max_digits=12, decimal_places=2, default=0)
    order = models.PositiveSmallIntegerField(default=1)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        db_table = 'company_values'
        ordering = ['order']
        unique_together = [('company', 'order')]

    def __str__(self):
        return f'{self.company.name} – {self.name}'
