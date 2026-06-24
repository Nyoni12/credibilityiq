import uuid
from django.db import models


class Assessment(models.Model):
    company = models.ForeignKey('companies.Company', on_delete=models.CASCADE, related_name='assessments')
    title = models.CharField(max_length=200)
    is_active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    closes_at = models.DateTimeField(null=True, blank=True)

    class Meta:
        db_table = 'assessments'
        ordering = ['-created_at']

    def __str__(self):
        return f'{self.company.name} – {self.title}'


class AssessmentResponse(models.Model):
    assessment = models.ForeignKey(Assessment, on_delete=models.CASCADE, related_name='responses')
    submitted_at = models.DateTimeField(auto_now_add=True)
    session_token = models.UUIDField(default=uuid.uuid4, unique=True)

    class Meta:
        db_table = 'assessment_responses'
        ordering = ['-submitted_at']

    def __str__(self):
        return f'Response {self.session_token} for {self.assessment}'


class ValueRating(models.Model):
    response = models.ForeignKey(AssessmentResponse, on_delete=models.CASCADE, related_name='ratings')
    company_value = models.ForeignKey('companies.CompanyValue', on_delete=models.CASCADE, related_name='ratings')
    score = models.PositiveSmallIntegerField()

    class Meta:
        db_table = 'value_ratings'
        unique_together = [('response', 'company_value')]

    def clean(self):
        from django.core.exceptions import ValidationError
        if not (1 <= self.score <= 10):
            raise ValidationError('Score must be between 1 and 10.')

    def __str__(self):
        return f'{self.company_value.name}: {self.score}/10'


class TrainingProgram(models.Model):
    company_values = models.ManyToManyField('companies.CompanyValue', related_name='training_programs')
    title = models.CharField(max_length=200)
    description = models.TextField(blank=True)
    trigger_threshold = models.FloatField(
        default=6.0,
        help_text='Flag this program when avg score falls below this value.'
    )
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        db_table = 'training_programs'
        ordering = ['title']

    def __str__(self):
        return self.title
