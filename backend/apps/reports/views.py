import base64
import os
from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from django.http import HttpResponse
from django.shortcuts import get_object_or_404
from django.template.loader import render_to_string
from django.conf import settings
from apps.assessments.models import Assessment
from apps.accounts.permissions import IsSuperAdminOrCompanyAdmin
from services.scoring import calculate_scorecard


def _logo_data_url():
    try:
        logo_path = settings.BASE_DIR / 'static' / 'images' / 'cfa_logo.png'
        with open(logo_path, 'rb') as f:
            return 'data:image/png;base64,' + base64.b64encode(f.read()).decode()
    except (FileNotFoundError, OSError):
        return ''


class PDFReportView(APIView):
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get(self, request, assessment_id):
        assessment = get_object_or_404(Assessment, pk=assessment_id)
        user = request.user
        if not user.is_superadmin and assessment.company != user.company:
            return Response({'detail': 'Forbidden.'}, status=status.HTTP_403_FORBIDDEN)

        scorecard = calculate_scorecard(assessment_id)

        html_content = render_to_string('reports/scorecard_pdf.html', {
            'scorecard': scorecard,
            'request': request,
            'cfa_logo': _logo_data_url(),
        })

        try:
            import weasyprint
            pdf = weasyprint.HTML(string=html_content, base_url=request.build_absolute_uri('/')).write_pdf()
            response = HttpResponse(pdf, content_type='application/pdf')
            safe_name = assessment.company.name.replace(' ', '_')
            response['Content-Disposition'] = f'attachment; filename="{safe_name}_scorecard.pdf"'
            return response
        except Exception as exc:
            return Response(
                {'detail': f'PDF generation failed: {str(exc)}'},
                status=status.HTTP_500_INTERNAL_SERVER_ERROR,
            )


class DashboardSummaryView(APIView):
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get(self, request):
        user = request.user
        if user.is_superadmin:
            from apps.companies.models import Company
            from apps.assessments.models import Assessment, AssessmentResponse, ValueRating
            from django.db.models import Count, Avg

            companies = Company.objects.annotate(
                user_count=Count('users', distinct=True),
                assessment_count=Count('assessments', distinct=True),
            ).order_by('-created_at')

            total_assessments = Assessment.objects.count()
            total_responses = AssessmentResponse.objects.count()
            tier_breakdown = {
                t: Company.objects.filter(subscription_tier=t).count()
                for t in ('starter', 'professional', 'enterprise')
            }

            data = []
            for c in companies:
                # Quick credibility score from the most recent active assessment
                latest_score = None
                active_asmt = (
                    Assessment.objects
                    .filter(company=c, is_active=True)
                    .annotate(resp_count=Count('responses'))
                    .filter(resp_count__gt=0)
                    .order_by('-created_at')
                    .first()
                )
                if active_asmt:
                    agg = ValueRating.objects.filter(
                        response__assessment=active_asmt
                    ).aggregate(avg=Avg('score'))
                    if agg['avg']:
                        latest_score = round(float(agg['avg']) / 10 * 100, 1)

                # Total responses for this company
                company_responses = AssessmentResponse.objects.filter(
                    assessment__company=c
                ).count()

                data.append({
                    'id': c.id,
                    'name': c.name,
                    'industry': c.industry,
                    'subscription_tier': c.subscription_tier,
                    'is_active': c.is_active,
                    'user_count': c.user_count,
                    'assessment_count': c.assessment_count,
                    'total_responses': company_responses,
                    'latest_score': latest_score,
                    'created_at': c.created_at.isoformat(),
                })

            return Response({
                'companies': data,
                'total': len(data),
                'active_count': sum(1 for d in data if d['is_active']),
                'total_assessments': total_assessments,
                'total_responses': total_responses,
                'tier_breakdown': tier_breakdown,
            })

        from services.scoring import get_company_summary
        summary = get_company_summary(user.company_id)
        return Response(summary)
