from django.urls import path
from .views import PDFReportView, DashboardSummaryView

urlpatterns = [
    path('assessments/<int:assessment_id>/report/pdf/', PDFReportView.as_view(), name='pdf_report'),
    path('dashboard/summary/', DashboardSummaryView.as_view(), name='dashboard_summary'),
]
