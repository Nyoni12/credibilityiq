from django.db.models import Avg
from apps.assessments.models import Assessment, AssessmentResponse, ValueRating, TrainingProgram
from apps.companies.models import CompanyValue


def calculate_scorecard(assessment_id: int) -> dict:
    assessment = Assessment.objects.select_related('company').get(pk=assessment_id)
    company = assessment.company
    values = CompanyValue.objects.filter(company=company).order_by('order')
    responses = AssessmentResponse.objects.filter(assessment=assessment)
    total_responses = responses.count()

    value_results = []
    total_score_sum = 0.0
    total_financial_loss = 0.0

    for value in values:
        agg = ValueRating.objects.filter(
            response__in=responses,
            company_value=value,
        ).aggregate(avg=Avg('score'))

        avg_score = float(agg['avg'] or 0)
        gap_percentage = (1 - avg_score / 10) * 100
        financial_loss = (gap_percentage / 100) * float(value.financial_weight)

        total_score_sum += avg_score
        total_financial_loss += financial_loss

        flagged_programs = TrainingProgram.objects.filter(
            company_values=value,
            trigger_threshold__gt=avg_score,
        ).values('id', 'title', 'description', 'trigger_threshold')

        value_results.append({
            'value_id': value.id,
            'value_name': value.name,
            'description': value.description,
            'financial_weight': float(value.financial_weight),
            'avg_score': round(avg_score, 2),
            'gap_percentage': round(gap_percentage, 2),
            'financial_loss': round(financial_loss, 2),
            'training_programs': list(flagged_programs),
        })

    count = len(value_results)
    overall_score = (total_score_sum / count / 10 * 100) if count > 0 else 0.0

    # Credibility band
    if overall_score >= 80:
        band = 'excellent'
    elif overall_score >= 60:
        band = 'good'
    elif overall_score >= 40:
        band = 'fair'
    else:
        band = 'poor'

    # Top 3 costliest gaps
    top_3_gaps = sorted(value_results, key=lambda x: x['financial_loss'], reverse=True)[:3]

    # All flagged training programs (deduplicated)
    all_training = {}
    for vr in value_results:
        for prog in vr['training_programs']:
            pid = prog['id']
            if pid not in all_training:
                all_training[pid] = {**prog, 'triggered_by': []}
            all_training[pid]['triggered_by'].append(vr['value_name'])

    logo_url = None
    if company.logo:
        logo_url = company.logo.url

    return {
        'assessment_id': assessment_id,
        'assessment_title': assessment.title,
        'company_id': company.id,
        'company_name': company.name,
        'company_logo': logo_url,
        'total_responses': total_responses,
        'overall_score': round(overall_score, 2),
        'overall_band': band,
        'total_financial_loss': round(total_financial_loss, 2),
        'values': value_results,
        'top_3_gaps': top_3_gaps,
        'training_recommendations': list(all_training.values()),
        'is_active': assessment.is_active,
        'created_at': assessment.created_at.isoformat(),
    }


def get_company_summary(company_id: int) -> dict:
    """Dashboard summary metrics for a company."""
    from apps.companies.models import Company
    from django.db.models import Count

    company = Company.objects.get(pk=company_id)
    assessments = Assessment.objects.filter(company=company).annotate(
        resp_count=Count('responses')
    )

    active = assessments.filter(is_active=True).first()
    total_responses = sum(a.resp_count for a in assessments)

    latest_score = None
    if active and active.resp_count > 0:
        data = calculate_scorecard(active.id)
        latest_score = data['overall_score']

    return {
        'company_id': company_id,
        'company_name': company.name,
        'total_assessments': assessments.count(),
        'active_assessment': {
            'id': active.id,
            'title': active.title,
            'response_count': active.resp_count,
        } if active else None,
        'total_responses': total_responses,
        'latest_score': latest_score,
        'values_count': company.values.count(),
        'assessment_link_token': str(company.assessment_link_token),
    }
