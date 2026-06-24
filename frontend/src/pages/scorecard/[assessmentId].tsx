import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import CredibilityRing from '@/components/CredibilityRing';
import ValueBarChart from '@/components/ValueBarChart';
import FinancialLeakageTable from '@/components/FinancialLeakageTable';
import TrainingRecommendations from '@/components/TrainingRecommendations';
import { assessmentsAPI } from '@/lib/api';
import { Scorecard } from '@/types';
import clsx from 'clsx';

const TABS = ['Overview', 'Value Breakdown', 'Financial Leakage', 'Training'] as const;
type Tab = typeof TABS[number];

export default function ScorecardPage() {
  const router = useRouter();
  const { assessmentId } = router.query;
  const [scorecard, setScorecard] = useState<Scorecard | null>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<Tab>('Overview');
  const [downloading, setDownloading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!assessmentId) return;
    assessmentsAPI.scorecard(Number(assessmentId))
      .then(({ data }) => setScorecard(data))
      .catch(() => setError('Failed to load scorecard.'))
      .finally(() => setLoading(false));
  }, [assessmentId]);

  const downloadPDF = async () => {
    if (!assessmentId) return;
    setDownloading(true);
    try {
      const res = await assessmentsAPI.pdfReport(Number(assessmentId));
      const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }));
      const a = document.createElement('a');
      a.href = url;
      a.download = `${scorecard?.company_name}_scorecard.pdf`;
      a.click();
      URL.revokeObjectURL(url);
    } catch {
      alert('PDF generation failed.');
    } finally {
      setDownloading(false);
    }
  };

  if (loading) return (
    <ProtectedRoute><Layout>
      <div className="flex items-center justify-center h-64">
        <div className="w-10 h-10 border-4 border-brand-500 border-t-transparent rounded-full animate-spin" />
      </div>
    </Layout></ProtectedRoute>
  );

  if (error || !scorecard) return (
    <ProtectedRoute><Layout>
      <div className="text-center py-20 text-gray-400">{error || 'Scorecard not found.'}</div>
    </Layout></ProtectedRoute>
  );

  return (
    <ProtectedRoute>
      <Layout>
        <div className="space-y-6">
          {/* Page header */}
          <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
              <div className="flex items-center gap-2 mb-1">
                <h1 className="text-2xl font-bold text-gray-900">{scorecard.company_name}</h1>
                <span className={clsx(
                  'text-xs px-2 py-1 rounded-full font-semibold',
                  scorecard.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
                )}>
                  {scorecard.is_active ? 'Active' : 'Closed'}
                </span>
              </div>
              <p className="text-gray-500 text-sm">
                {scorecard.assessment_title} &mdash; {scorecard.total_responses} respondents &mdash;{' '}
                {new Date(scorecard.created_at).toLocaleDateString()}
              </p>
            </div>
            <button
              onClick={downloadPDF}
              disabled={downloading}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60 transition-colors flex items-center gap-2 whitespace-nowrap"
            >
              {downloading ? 'Generating…' : '⬇ Download PDF'}
            </button>
          </div>

          {/* Tabs */}
          <div className="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
            {TABS.map((tab) => (
              <button
                key={tab}
                onClick={() => setActiveTab(tab)}
                className={clsx(
                  'px-4 py-2 rounded-lg text-sm font-medium transition-all',
                  activeTab === tab
                    ? 'bg-white text-brand-600 shadow-sm'
                    : 'text-gray-500 hover:text-gray-700'
                )}
              >
                {tab}
              </button>
            ))}
          </div>

          {/* Tab content */}
          {activeTab === 'Overview' && (
            <div className="grid md:grid-cols-3 gap-6">
              <div className="md:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-8 px-4">
                <CredibilityRing score={scorecard.overall_score} band={scorecard.overall_band} size={200} />
                <div className="mt-4 text-center">
                  <p className="text-sm text-gray-500">{scorecard.total_responses} responses</p>
                </div>
              </div>

              <div className="md:col-span-2 grid grid-cols-2 gap-4 content-start">
                <MetricCard
                  icon="💸"
                  label="Total Financial Leakage"
                  value={`$${scorecard.total_financial_loss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`}
                  sub="Estimated budget at risk"
                  danger
                />
                <MetricCard
                  icon="📊"
                  label="Values Assessed"
                  value={scorecard.values.length}
                  sub="Company values"
                />
                <MetricCard
                  icon="⚠️"
                  label="Training Flags"
                  value={scorecard.training_recommendations.length}
                  sub="Recommended interventions"
                  warn={scorecard.training_recommendations.length > 0}
                />
                <MetricCard
                  icon="🏆"
                  label="Top Performing Value"
                  value={scorecard.values.reduce((a, b) => a.avg_score > b.avg_score ? a : b, scorecard.values[0])?.value_name ?? '—'}
                  sub="Highest average score"
                  good
                />

                {/* Top 3 gaps */}
                <div className="col-span-2 bg-red-50 rounded-xl border border-red-100 p-4">
                  <h3 className="text-sm font-semibold text-red-800 mb-3">Top 3 Costliest Gaps</h3>
                  <div className="space-y-2">
                    {scorecard.top_3_gaps.map((v) => (
                      <div key={v.value_id} className="flex items-center justify-between text-sm">
                        <span className="text-gray-700 font-medium">{v.value_name}</span>
                        <span className="text-red-600 font-semibold">
                          ${v.financial_loss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {activeTab === 'Value Breakdown' && (
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-6">Average Score per Value (out of 10)</h2>
              <ValueBarChart values={scorecard.values} />
              <div className="mt-6 flex gap-4 text-xs flex-wrap">
                {[
                  { color: '#059669', label: 'CC — Celebrity Credibility (>8.9/10)' },
                  { color: '#ca8a04', label: 'GW — General Ward (6.6–8.9/10)'       },
                  { color: '#ea580c', label: 'HDU — High Dependency Unit (5.1–6.5/10)' },
                  { color: '#dc2626', label: 'ICU — Intensive Care Unit (≤5.0/10)'  },
                ].map(({ color, label }) => (
                  <span key={label} className="flex items-center gap-1.5">
                    <span className="w-3 h-3 rounded-sm inline-block" style={{ background: color }} />
                    {label}
                  </span>
                ))}
              </div>
            </div>
          )}

          {activeTab === 'Financial Leakage' && (
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-lg font-semibold text-gray-900">Financial Leakage Report</h2>
                <div className="text-right">
                  <p className="text-xs text-gray-400">Total at risk</p>
                  <p className="text-2xl font-bold text-red-600">
                    ${scorecard.total_financial_loss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </p>
                </div>
              </div>
              <FinancialLeakageTable values={scorecard.values} totalLoss={scorecard.total_financial_loss} />
            </div>
          )}

          {activeTab === 'Training' && (
            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-6">
                Training Recommendations
                {scorecard.training_recommendations.length > 0 && (
                  <span className="ml-2 bg-orange-100 text-orange-700 text-sm px-2 py-0.5 rounded-full font-medium">
                    {scorecard.training_recommendations.length}
                  </span>
                )}
              </h2>
              <TrainingRecommendations recommendations={scorecard.training_recommendations} />
            </div>
          )}
        </div>
      </Layout>
    </ProtectedRoute>
  );
}

function MetricCard({
  icon, label, value, sub, danger, warn, good,
}: {
  icon: string; label: string; value: string | number; sub: string;
  danger?: boolean; warn?: boolean; good?: boolean;
}) {
  return (
    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
      <div className="flex items-start gap-3">
        <span className="text-2xl">{icon}</span>
        <div className="min-w-0">
          <p className="text-xs text-gray-500 font-medium truncate">{label}</p>
          <p className={clsx(
            'text-lg font-bold mt-0.5 truncate',
            danger ? 'text-red-600' : warn ? 'text-amber-600' : good ? 'text-green-600' : 'text-gray-900'
          )}>
            {value}
          </p>
          <p className="text-xs text-gray-400 mt-0.5">{sub}</p>
        </div>
      </div>
    </div>
  );
}
