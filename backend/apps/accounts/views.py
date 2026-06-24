from rest_framework import generics, permissions, status
from rest_framework.response import Response
from rest_framework.views import APIView
from rest_framework_simplejwt.views import TokenObtainPairView, TokenRefreshView
from rest_framework_simplejwt.tokens import RefreshToken
from .models import User
from .serializers import CustomTokenObtainPairSerializer, UserSerializer, UserProfileSerializer
from .permissions import IsSuperAdmin


class CompanySignupView(APIView):
    """Public endpoint: register a new company + its first admin user."""
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        data = request.data
        company_name = data.get('company_name', '').strip()
        domain = data.get('domain', '').strip().lower().lstrip('https://').lstrip('http://').lstrip('www.').split('/')[0]
        industry = data.get('industry', '').strip()
        tier = data.get('subscription_tier', 'starter')
        first_name = data.get('first_name', '').strip()
        last_name = data.get('last_name', '').strip()
        email = data.get('email', '').strip().lower()
        password = data.get('password', '')

        errors = {}
        if not company_name:
            errors['company_name'] = 'Company name is required.'
        if not domain:
            errors['domain'] = 'Company domain is required.'
        if not first_name:
            errors['first_name'] = 'First name is required.'
        if not last_name:
            errors['last_name'] = 'Last name is required.'
        if not email or '@' not in email:
            errors['email'] = 'A valid email address is required.'
        if not password or len(password) < 8:
            errors['password'] = 'Password must be at least 8 characters.'
        if tier not in ('starter', 'professional', 'enterprise'):
            tier = 'starter'

        if errors:
            return Response(errors, status=status.HTTP_400_BAD_REQUEST)

        from apps.companies.models import Company
        if domain and Company.objects.filter(domain=domain).exists():
            return Response({'domain': 'A company with this domain is already registered.'}, status=status.HTTP_400_BAD_REQUEST)
        if User.objects.filter(email=email).exists():
            return Response({'email': 'An account with this email already exists.'}, status=status.HTTP_400_BAD_REQUEST)

        company = Company.objects.create(
            name=company_name,
            domain=domain,
            industry=industry,
            subscription_tier=tier,
        )

        user = User(
            email=email,
            first_name=first_name,
            last_name=last_name,
            company=company,
            is_superadmin=False,
        )
        user.set_password(password)
        user.save()

        refresh = RefreshToken.for_user(user)
        refresh['email'] = user.email
        refresh['is_superadmin'] = False
        refresh['full_name'] = user.full_name
        refresh['company_id'] = company.id
        refresh['company_name'] = company.name

        return Response({
            'access': str(refresh.access_token),
            'refresh': str(refresh),
            'user': {
                'id': user.id,
                'email': user.email,
                'full_name': user.full_name,
                'is_superadmin': False,
                'company_id': company.id,
            },
        }, status=status.HTTP_201_CREATED)


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
