from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from rest_framework.decorators import api_view, permission_classes
from rest_framework.permissions import AllowAny
from rest_framework.response import Response


@api_view(['GET'])
@permission_classes([AllowAny])
def api_root(request):
    base = request.build_absolute_uri('/api')
    return Response({
        'name': 'Credibility Factory Afrique API',
        'version': '1.0',
        'endpoints': {
            'auth': {
                'login':          f'{base}/auth/login/',
                'logout':         f'{base}/auth/logout/',
                'token_refresh':  f'{base}/auth/token/refresh/',
                'profile':        f'{base}/auth/profile/',
            },
            'companies':     f'{base}/companies/',
            'values':        f'{base}/companies/{{id}}/values/',
            'assessments':   f'{base}/assessments/',
            'scorecard':     f'{base}/assessments/{{id}}/scorecard/',
            'pdf_report':    f'{base}/assessments/{{id}}/report/pdf/',
            'survey_fetch':  f'{base}/survey/{{token}}/',
            'survey_submit': f'{base}/survey/{{token}}/submit/',
            'dashboard':     f'{base}/dashboard/summary/',
        },
    })


urlpatterns = [
    path('django-admin/', admin.site.urls),
    path('api/', api_root, name='api_root'),
    path('api/auth/', include('apps.accounts.urls')),
    path('api/', include('apps.companies.urls')),
    path('api/', include('apps.assessments.urls')),
    path('api/survey/', include('apps.survey.urls')),
    path('api/', include('apps.reports.urls')),
] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
