from rest_framework import generics, permissions, status
from rest_framework.decorators import action
from rest_framework.response import Response
from rest_framework.views import APIView
from django.shortcuts import get_object_or_404
from .models import Company, CompanyValue
from .serializers import CompanySerializer, CompanyDetailSerializer, CompanyValueSerializer
from apps.accounts.permissions import IsSuperAdmin, IsCompanyAdmin, IsSuperAdminOrCompanyAdmin


class CompanyListCreateView(generics.ListCreateAPIView):
    permission_classes = [IsSuperAdmin]

    def get_serializer_class(self):
        if self.request.method == 'GET':
            return CompanyDetailSerializer
        return CompanySerializer

    def get_queryset(self):
        return Company.objects.prefetch_related('values', 'users').order_by('-created_at')

    def get_serializer_context(self):
        return {'request': self.request}


class CompanyDetailView(generics.RetrieveUpdateDestroyAPIView):
    queryset = Company.objects.prefetch_related('values', 'users')

    def get_permissions(self):
        if self.request.method in ['PUT', 'PATCH', 'DELETE']:
            return [IsSuperAdmin()]
        return [IsSuperAdminOrCompanyAdmin()]

    def get_serializer_class(self):
        return CompanyDetailSerializer

    def get_object(self):
        obj = super().get_object()
        user = self.request.user
        if not user.is_superadmin and obj != user.company:
            from rest_framework.exceptions import PermissionDenied
            raise PermissionDenied('You do not have access to this company.')
        return obj

    def get_serializer_context(self):
        return {'request': self.request}


class RegenerateTokenView(APIView):
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def post(self, request, pk):
        company = get_object_or_404(Company, pk=pk)
        user = request.user
        if not user.is_superadmin and company != user.company:
            return Response({'detail': 'Forbidden.'}, status=status.HTTP_403_FORBIDDEN)
        company.regenerate_token()
        return Response({'assessment_link_token': str(company.assessment_link_token)})


class CompanyValueListCreateView(generics.ListCreateAPIView):
    serializer_class = CompanyValueSerializer
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get_queryset(self):
        company = self._get_company()
        return CompanyValue.objects.filter(company=company).order_by('order')

    def _get_company(self):
        company_id = self.kwargs['company_id']
        company = get_object_or_404(Company, pk=company_id)
        user = self.request.user
        if not user.is_superadmin and company != user.company:
            from rest_framework.exceptions import PermissionDenied
            raise PermissionDenied('You do not have access to this company.')
        return company

    def perform_create(self, serializer):
        company = self._get_company()
        existing_count = CompanyValue.objects.filter(company=company).count()
        if existing_count >= 12:
            from rest_framework.exceptions import ValidationError
            raise ValidationError('Maximum 12 values per company.')
        serializer.save(company=company)


class CompanyValueDetailView(generics.RetrieveUpdateDestroyAPIView):
    serializer_class = CompanyValueSerializer
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def get_object(self):
        company_id = self.kwargs['company_id']
        value_id = self.kwargs['value_id']
        value = get_object_or_404(CompanyValue, pk=value_id, company_id=company_id)
        user = self.request.user
        if not user.is_superadmin and value.company != user.company:
            from rest_framework.exceptions import PermissionDenied
            raise PermissionDenied('You do not have access to this value.')
        return value


class BulkUpdateValuesView(APIView):
    """Replace all values for a company in one request."""
    permission_classes = [IsSuperAdminOrCompanyAdmin]

    def put(self, request, company_id):
        company = get_object_or_404(Company, pk=company_id)
        user = request.user
        if not user.is_superadmin and company != user.company:
            return Response({'detail': 'Forbidden.'}, status=status.HTTP_403_FORBIDDEN)

        values_data = request.data.get('values', [])
        if len(values_data) > 12:
            return Response({'detail': 'Maximum 12 values.'}, status=status.HTTP_400_BAD_REQUEST)

        CompanyValue.objects.filter(company=company).delete()
        created = []
        for idx, v in enumerate(values_data, start=1):
            value = CompanyValue.objects.create(
                company=company,
                name=v.get('name', ''),
                description=v.get('description', ''),
                financial_weight=v.get('financial_weight', 0),
                order=idx,
            )
            created.append(value)

        serializer = CompanyValueSerializer(created, many=True)
        return Response(serializer.data)
