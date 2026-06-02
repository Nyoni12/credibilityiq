from django.urls import path
from .views import (
    AssessmentListCreateView, AssessmentDetailView, ScorecardView,
    TrainingProgramListCreateView, TrainingProgramDetailView,
)

urlpatterns = [
    path('assessments/', AssessmentListCreateView.as_view(), name='assessment_list'),
    path('assessments/<int:pk>/', AssessmentDetailView.as_view(), name='assessment_detail'),
    path('assessments/<int:assessment_id>/scorecard/', ScorecardView.as_view(), name='scorecard'),
    path('training-programs/', TrainingProgramListCreateView.as_view(), name='training_list'),
    path('training-programs/<int:pk>/', TrainingProgramDetailView.as_view(), name='training_detail'),
]
