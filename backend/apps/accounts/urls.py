from django.urls import path
from rest_framework_simplejwt.views import TokenRefreshView
from .views import LoginView, LogoutView, ProfileView, UserListCreateView, UserDetailView, CompanyUserListView, CompanySignupView

urlpatterns = [
    path('signup/', CompanySignupView.as_view(), name='company_signup'),
    path('login/', LoginView.as_view(), name='login'),
    path('logout/', LogoutView.as_view(), name='logout'),
    path('token/refresh/', TokenRefreshView.as_view(), name='token_refresh'),
    path('profile/', ProfileView.as_view(), name='profile'),
    path('users/', UserListCreateView.as_view(), name='user_list'),
    path('users/<int:pk>/', UserDetailView.as_view(), name='user_detail'),
    path('companies/<int:company_id>/users/', CompanyUserListView.as_view(), name='company_users'),
]
