from django.contrib import admin
from .models import Assessment, AssessmentResponse, ValueRating, TrainingProgram


class ValueRatingInline(admin.TabularInline):
    model = ValueRating
    extra = 0
    readonly_fields = ['company_value', 'score']


class AssessmentResponseInline(admin.TabularInline):
    model = AssessmentResponse
    extra = 0
    readonly_fields = ['session_token', 'submitted_at']


@admin.register(Assessment)
class AssessmentAdmin(admin.ModelAdmin):
    list_display = ['title', 'company', 'is_active', 'response_count', 'created_at']
    list_filter = ['is_active', 'company']
    search_fields = ['title', 'company__name']
    inlines = [AssessmentResponseInline]

    def response_count(self, obj):
        return obj.responses.count()
    response_count.short_description = 'Responses'


@admin.register(AssessmentResponse)
class AssessmentResponseAdmin(admin.ModelAdmin):
    list_display = ['session_token', 'assessment', 'submitted_at']
    list_filter = ['assessment__company']
    inlines = [ValueRatingInline]
    readonly_fields = ['session_token', 'submitted_at']


@admin.register(TrainingProgram)
class TrainingProgramAdmin(admin.ModelAdmin):
    list_display = ['title', 'trigger_threshold', 'created_at']
    filter_horizontal = ['company_values']
