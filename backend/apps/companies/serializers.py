from rest_framework import serializers
from .models import Company, CompanyValue


class CompanyValueSerializer(serializers.ModelSerializer):
    class Meta:
        model = CompanyValue
        fields = ['id', 'name', 'description', 'financial_weight', 'order', 'created_at']
        read_only_fields = ['id', 'created_at']


class CompanySerializer(serializers.ModelSerializer):
    values_count = serializers.IntegerField(source='values.count', read_only=True)
    logo_url = serializers.SerializerMethodField()
    assessment_link_token = serializers.UUIDField(read_only=True)
    user_count = serializers.IntegerField(source='users.count', read_only=True)

    class Meta:
        model = Company
        fields = [
            'id', 'name', 'logo', 'logo_url', 'industry', 'subscription_tier',
            'assessment_link_token', 'is_active', 'values_count', 'user_count', 'created_at',
        ]
        read_only_fields = ['id', 'assessment_link_token', 'created_at']

    def get_logo_url(self, obj):
        if obj.logo:
            request = self.context.get('request')
            if request:
                return request.build_absolute_uri(obj.logo.url)
            return obj.logo.url
        return None


class CompanyDetailSerializer(CompanySerializer):
    values = CompanyValueSerializer(many=True, read_only=True)

    class Meta(CompanySerializer.Meta):
        fields = CompanySerializer.Meta.fields + ['values']
