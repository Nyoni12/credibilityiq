from rest_framework import serializers
from .models import Assessment, AssessmentResponse, ValueRating, TrainingProgram


class TrainingProgramSerializer(serializers.ModelSerializer):
    class Meta:
        model = TrainingProgram
        fields = ['id', 'title', 'description', 'trigger_threshold', 'created_at']
        read_only_fields = ['id', 'created_at']


class ValueRatingSerializer(serializers.ModelSerializer):
    class Meta:
        model = ValueRating
        fields = ['company_value', 'score']

    def validate_score(self, value):
        if not (1 <= value <= 10):
            raise serializers.ValidationError('Score must be between 1 and 10.')
        return value


class AssessmentSerializer(serializers.ModelSerializer):
    response_count = serializers.IntegerField(read_only=True)
    company_name = serializers.CharField(source='company.name', read_only=True)
    overall_score = serializers.SerializerMethodField()

    def get_overall_score(self, obj):
        from django.db.models import Avg
        count = getattr(obj, 'response_count', None)
        if count == 0:
            return None
        agg = ValueRating.objects.filter(response__assessment=obj).aggregate(avg=Avg('score'))
        if agg['avg'] is None:
            return None
        return round(agg['avg'] / 10 * 100, 1)

    class Meta:
        model = Assessment
        fields = ['id', 'company', 'company_name', 'title', 'is_active',
                  'response_count', 'overall_score', 'created_at', 'closes_at']
        read_only_fields = ['id', 'created_at', 'response_count', 'company_name', 'overall_score']


class AssessmentCreateSerializer(serializers.ModelSerializer):
    class Meta:
        model = Assessment
        fields = ['title', 'closes_at']

    def create(self, validated_data):
        company = self.context['company']
        return Assessment.objects.create(company=company, **validated_data)


class SurveySubmitSerializer(serializers.Serializer):
    ratings = ValueRatingSerializer(many=True)

    def validate_ratings(self, value):
        if not value:
            raise serializers.ValidationError('At least one rating is required.')
        return value
