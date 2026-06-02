from rest_framework import status
from rest_framework.response import Response
from rest_framework.views import APIView
from rest_framework.permissions import AllowAny
from django.shortcuts import get_object_or_404
from django.utils import timezone
from apps.companies.models import Company, CompanyValue
from apps.assessments.models import Assessment, AssessmentResponse, ValueRating
from apps.companies.serializers import CompanyValueSerializer


RATE_LIMIT_HEADER = 'HTTP_X_SESSION_TOKEN'


class SurveyFetchView(APIView):
    """Public endpoint: fetch company name + values by token."""
    permission_classes = [AllowAny]
    authentication_classes = []

    def get(self, request, token):
        company = get_object_or_404(Company, assessment_link_token=token, is_active=True)

        # Get the active assessment
        assessment = Assessment.objects.filter(
            company=company,
            is_active=True,
        ).first()

        if not assessment:
            return Response(
                {'detail': 'No active assessment found for this company.'},
                status=status.HTTP_404_NOT_FOUND
            )

        if assessment.closes_at and assessment.closes_at < timezone.now():
            return Response(
                {'detail': 'This assessment has closed.'},
                status=status.HTTP_410_GONE
            )

        values = CompanyValue.objects.filter(company=company).order_by('order')
        logo_url = request.build_absolute_uri(company.logo.url) if company.logo else None

        return Response({
            'company_id': company.id,
            'company_name': company.name,
            'company_logo': logo_url,
            'assessment_id': assessment.id,
            'assessment_title': assessment.title,
            'values': CompanyValueSerializer(values, many=True).data,
        })


class SurveySubmitView(APIView):
    """Public endpoint: submit all ratings anonymously."""
    permission_classes = [AllowAny]
    authentication_classes = []

    def post(self, request, token):
        company = get_object_or_404(Company, assessment_link_token=token, is_active=True)

        assessment = Assessment.objects.filter(company=company, is_active=True).first()
        if not assessment:
            return Response({'detail': 'No active assessment.'}, status=status.HTTP_404_NOT_FOUND)

        if assessment.closes_at and assessment.closes_at < timezone.now():
            return Response({'detail': 'Assessment is closed.'}, status=status.HTTP_410_GONE)

        ratings_data = request.data.get('ratings', [])
        if not ratings_data:
            return Response({'detail': 'ratings field is required.'}, status=status.HTTP_400_BAD_REQUEST)

        # Validate all scores before saving anything
        company_value_ids = set(
            CompanyValue.objects.filter(company=company).values_list('id', flat=True)
        )
        errors = []
        for item in ratings_data:
            val_id = item.get('company_value')
            score = item.get('score')
            if val_id not in company_value_ids:
                errors.append(f'Invalid company_value: {val_id}')
            if not isinstance(score, int) or not (1 <= score <= 10):
                errors.append(f'Score for value {val_id} must be 1-10.')
        if errors:
            return Response({'errors': errors}, status=status.HTTP_400_BAD_REQUEST)

        # Persist the response
        response_obj = AssessmentResponse.objects.create(assessment=assessment)
        ValueRating.objects.bulk_create([
            ValueRating(
                response=response_obj,
                company_value_id=item['company_value'],
                score=item['score'],
            )
            for item in ratings_data
        ])

        return Response({
            'detail': 'Thank you! Your response has been recorded.',
            'response_id': str(response_obj.session_token),
        }, status=status.HTTP_201_CREATED)
