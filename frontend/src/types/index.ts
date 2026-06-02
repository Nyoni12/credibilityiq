export interface User {
  id: number;
  email: string;
  full_name: string;
  is_superadmin: boolean;
  company_id: number | null;
}

export interface Company {
  id: number;
  name: string;
  logo: string | null;
  logo_url: string | null;
  industry: string;
  subscription_tier: 'starter' | 'professional' | 'enterprise';
  assessment_link_token: string;
  is_active: boolean;
  values_count: number;
  user_count: number;
  created_at: string;
  values?: CompanyValue[];
}

export interface CompanyValue {
  id: number;
  name: string;
  description: string;
  financial_weight: number;
  order: number;
  created_at: string;
}

export interface Assessment {
  id: number;
  company: number;
  company_name: string;
  title: string;
  is_active: boolean;
  response_count: number;
  created_at: string;
  closes_at: string | null;
}

export interface ValueResult {
  value_id: number;
  value_name: string;
  description: string;
  financial_weight: number;
  avg_score: number;
  gap_percentage: number;
  financial_loss: number;
  training_programs: TrainingRecommendation[];
}

export interface TrainingRecommendation {
  id: number;
  title: string;
  description: string;
  trigger_threshold: number;
  triggered_by?: string[];
}

export interface Scorecard {
  assessment_id: number;
  assessment_title: string;
  company_id: number;
  company_name: string;
  company_logo: string | null;
  total_responses: number;
  overall_score: number;
  overall_band: 'excellent' | 'good' | 'fair' | 'poor';
  total_financial_loss: number;
  values: ValueResult[];
  top_3_gaps: ValueResult[];
  training_recommendations: TrainingRecommendation[];
  is_active: boolean;
  created_at: string;
}

export interface DashboardSummary {
  company_id: number;
  company_name: string;
  total_assessments: number;
  active_assessment: { id: number; title: string; response_count: number } | null;
  total_responses: number;
  latest_score: number | null;
  values_count: number;
  assessment_link_token: string;
}

export interface SuperAdminSummary {
  companies: {
    id: number;
    name: string;
    industry: string;
    subscription_tier: string;
    is_active: boolean;
    user_count: number;
    assessment_count: number;
    created_at: string;
  }[];
  total: number;
}

export interface SurveyData {
  company_id: number;
  company_name: string;
  company_logo: string | null;
  assessment_id: number;
  assessment_title: string;
  values: CompanyValue[];
}

export interface AuthTokens {
  access: string;
  refresh: string;
}
