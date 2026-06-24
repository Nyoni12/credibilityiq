from rest_framework import generics, permissions, status
from rest_framework.response import Response
from rest_framework.views import APIView
from django.shortcuts import get_object_or_404
from django.db.models import Count
from .models import Assessment, TrainingProgram
from .serializers import AssessmentSerializer, AssessmentCreateSerializer, TrainingProgramSerializer
from apps.accounts.permissions import IsSuperAdminOrCompanyAdmin, IsSuperAdmin
from services.scoring import calculate_scorecard


class AssessmentListCreateView(generics.ListCreateAPIView):
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get_serializer_class(self):
        if self.request.method == 'POST':
            return AssessmentCreateSerializer
        return AssessmentSerializer

    def get_queryset(self):
        user = self.request.user
        qs = Assessment.objects.annotate(response_count=Count('responses'))
        if not user.is_superadmin:
            qs = qs.filter(company=user.company)
        return qs.select_related('company').order_by('-created_at')

    def get_serializer_context(self):
        ctx = super().get_serializer_context()
        # Only resolve company context for POST (create) requests
        if self.request.method != 'POST':
            return ctx
        user = self.request.user
        if user.is_superadmin:
            company_id = self.request.data.get('company')
            from apps.companies.models import Company
            ctx['company'] = get_object_or_404(Company, pk=company_id)
        else:
            ctx['company'] = user.company
        return ctx

    def perform_create(self, serializer):
        serializer.save()


class AssessmentDetailView(generics.RetrieveUpdateDestroyAPIView):
    serializer_class = AssessmentSerializer
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get_queryset(self):
        user = self.request.user
        qs = Assessment.objects.annotate(response_count=Count('responses')).select_related('company')
        if not user.is_superadmin:
            qs = qs.filter(company=user.company)
        return qs

    def patch(self, request, *args, **kwargs):
        return self.partial_update(request, *args, **kwargs)


class ScorecardView(APIView):
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get(self, request, assessment_id):
        assessment = get_object_or_404(Assessment, pk=assessment_id)
        user = request.user
        if not user.is_superadmin and assessment.company != user.company:
            return Response({'detail': 'Forbidden.'}, status=status.HTTP_403_FORBIDDEN)

        scorecard = calculate_scorecard(assessment_id)
        return Response(scorecard)


class TrainingProgramListCreateView(generics.ListCreateAPIView):
    serializer_class = TrainingProgramSerializer
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get_queryset(self):
        user = self.request.user
        qs = TrainingProgram.objects.prefetch_related('company_values')
        if not user.is_superadmin:
            qs = qs.filter(company_values__company=user.company).distinct()
        return qs


class TrainingProgramDetailView(generics.RetrieveUpdateDestroyAPIView):
    serializer_class = TrainingProgramSerializer
    permission_classes = [IsSuperAdminOrCompanyAdmin]
    queryset = TrainingProgram.objects.all()
