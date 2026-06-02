from rest_framework import generics, permissions, status
from rest_framework.response import Response
from rest_framework.views import APIView
from rest_framework_simplejwt.views import TokenObtainPairView, TokenRefreshView
from rest_framework_simplejwt.tokens import RefreshToken
from .models import User
from .serializers import CustomTokenObtainPairSerializer, UserSerializer, UserProfileSerializer
from .permissions import IsSuperAdmin


class LoginView(TokenObtainPairView):
    serializer_class = CustomTokenObtainPairSerializer
    permission_classes = [permissions.AllowAny]


class LogoutView(APIView):
    def post(self, request):
        try:
            refresh_token = request.data.get('refresh')
            token = RefreshToken(refresh_token)
            token.blacklist()
            return Response({'detail': 'Logged out successfully.'})
        except Exception:
            return Response({'detail': 'Invalid token.'}, status=status.HTTP_400_BAD_REQUEST)


class ProfileView(generics.RetrieveUpdateAPIView):
    serializer_class = UserProfileSerializer

    def get_object(self):
        return self.request.user


class UserListCreateView(generics.ListCreateAPIView):
    serializer_class = UserSerializer
    permission_classes = [IsSuperAdmin]

    def get_queryset(self):
        return User.objects.select_related('company').order_by('-created_at')


class UserDetailView(generics.RetrieveUpdateDestroyAPIView):
    serializer_class = UserSerializer
    permission_classes = [IsSuperAdmin]
    queryset = User.objects.select_related('company')


class CompanyUserListView(generics.ListCreateAPIView):
    """List/create users for a specific company. Company admins only manage their own company."""
    serializer_class = UserSerializer

    def get_queryset(self):
        user = self.request.user
        if user.is_superadmin:
            company_id = self.kwargs.get('company_id')
            return User.objects.filter(company_id=company_id)
        return User.objects.filter(company=user.company)

    def perform_create(self, serializer):
        user = self.request.user
        company_id = self.kwargs.get('company_id') if user.is_superadmin else user.company_id
        serializer.save(company_id=company_id)
