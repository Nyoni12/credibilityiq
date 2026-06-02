from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status
from django.http import HttpResponse
from django.shortcuts import get_object_or_404
from django.template.loader import render_to_string
from apps.assessments.models import Assessment
from apps.accounts.permissions import IsSuperAdminOrCompanyAdmin
from services.scoring import calculate_scorecard


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
            from django.db.models import Count
            companies = Company.objects.annotate(
                user_count=Count('users', distinct=True),
                assessment_count=Count('assessments', distinct=True),
            ).order_by('-created_at')
            data = []
            for c in companies:
                data.append({
                    'id': c.id,
                    'name': c.name,
                    'industry': c.industry,
                    'subscription_tier': c.subscription_tier,
                    'is_active': c.is_active,
                    'user_count': c.user_count,
                    'assessment_count': c.assessment_count,
                    'created_at': c.created_at.isoformat(),
                })
            return Response({'companies': data, 'total': len(data)})

        from services.scoring import get_company_summary
        summary = get_company_summary(user.company_id)
        return Response(summary)
