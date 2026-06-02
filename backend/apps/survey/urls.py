from django.urls import path
from .views import SurveyFetchView, SurveySubmitView

urlpatterns = [
    path('<uuid:token>/', SurveyFetchView.as_view(), name='survey_fetch'),
    path('<uuid:token>/submit/', SurveySubmitView.as_view(), name='survey_submit'),
]
