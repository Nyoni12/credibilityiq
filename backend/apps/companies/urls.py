from django.urls import path
from .views import (
    CompanyListCreateView, CompanyDetailView, RegenerateTokenView,
    CompanyValueListCreateView, CompanyValueDetailView, BulkUpdateValuesView,
)

urlpatterns = [
    path('companies/', CompanyListCreateView.as_view(), name='company_list'),
    path('companies/<int:pk>/', CompanyDetailView.as_view(), name='company_detail'),
    path('companies/<int:pk>/regenerate-token/', RegenerateTokenView.as_view(), name='regenerate_token'),
    path('companies/<int:company_id>/values/', CompanyValueListCreateView.as_view(), name='value_list'),
    path('companies/<int:company_id>/values/bulk/', BulkUpdateValuesView.as_view(), name='bulk_values'),
    path('companies/<int:company_id>/values/<int:value_id>/', CompanyValueDetailView.as_view(), name='value_detail'),
]
