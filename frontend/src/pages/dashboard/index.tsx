import { useEffect, useState } from 'react';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { useAuth } from '@/context/AuthContext';
import { dashboardAPI, assessmentsAPI } from '@/lib/api';
import { DashboardSummary, Assessment } from '@/types';
import clsx from 'clsx';

function StatCard({ label, value, sub, color = 'brand' }: {
  label: string; value: string | number; sub?: string; color?: string;
}) {
  return (
    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <p className="text-sm text-gray-500 font-medium">{label}</p>
      <p className={clsx('text-3xl font-bold mt-1', `text-${color}-600`)}>{value}</p>
      {sub && <p className="text-xs text-gray-400 mt-1">{sub}</p>}
    </div>
  );
}

export default function Dashboard() {
  const { user } = useAuth();
  const [summary, setSummary] = useState<DashboardSummary | null>(null);
  const [assessments, setAssessments] = useState<Assessment[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([dashboardAPI.summary(), assessmentsAPI.list()])
      .then(([sumRes, assRes]) => {
        setSummary(sumRes.data);
        setAssessments(assRes.data.results || assRes.data);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  }, []);

  const surveyLink = summary
    ? `${window.location.origin}/survey/${summary.assessment_link_token}`
    : '';

  const copyLink = () => {
    if (surveyLink) {
      navigator.clipboard.writeText(surveyLink);
      alert('Survey link copied to clipboard!');
    }
  };

  return (
    <ProtectedRoute>
      <Layout>
        <div className="space-y-8">
          {/* Header */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">
                Welcome back, {user?.full_name?.split(' ')[0]}
              </h1>
              <p className="text-gray-500 text-sm mt-1">{summary?.company_name}</p>
            </div>
            <div className="flex gap-3 flex-wrap">
              <Link href="/setup/values">
                <button className="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                  Setup Values
                </button>
              </Link>
              <Link href="/assessments/new">
                <button className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">
                  + New Assessment
                </button>
              </Link>
            </div>
          </div>

          {/* Stats */}
          {loading ? (
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-pulse">
              {[...Array(4)].map((_, i) => (
                <div key={i} className="h-28 bg-gray-100 rounded-xl" />
              ))}
            </div>
          ) : (
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
              <StatCard
                label="Overall Score"
                value={summary?.latest_score != null ? `${summary.latest_score}%` : '—'}
                sub="Latest assessment"
                color="brand"
              />
              <StatCard
                label="Total Responses"
                value={summary?.total_responses ?? 0}
                sub="All assessments"
                color="green"
              />
              <StatCard
                label="Assessments"
                value={summary?.total_assessments ?? 0}
                sub="All time"
                color="blue"
              />
              <StatCard
                label="Values Configured"
                value={summary?.values_count ?? 0}
                sub="Max 12"
                color="purple"
              />
            </div>
          )}

          {/* Survey Link */}
          {summary?.assessment_link_token && (
            <div className="bg-white rounded-xl border border-brand-200 shadow-sm p-5">
              <h2 className="font-semibold text-gray-900 mb-2">Staff Survey Link</h2>
              <p className="text-sm text-gray-500 mb-3">Share this link with your staff for anonymous assessment</p>
              <div className="flex gap-3 items-center flex-wrap">
                <code className="flex-1 text-xs bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 truncate">
                  {surveyLink}
                </code>
                <button
                  onClick={copyLink}
                  className="px-4 py-2 bg-brand-500 text-white text-sm rounded-lg hover:bg-brand-600 transition-colors whitespace-nowrap"
                >
                  Copy Link
                </button>
              </div>
            </div>
          )}

          {/* Assessments Table */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h2 className="font-semibold text-gray-900">Assessments</h2>
              <Link href="/assessments/new">
                <button className="px-3 py-1.5 bg-brand-50 text-brand-600 rounded-lg text-xs font-semibold hover:bg-brand-100 transition-colors">
                  + New
                </button>
              </Link>
            </div>
            {loading ? (
              <div className="p-8 text-center text-gray-400">Loading...</div>
            ) : assessments.length === 0 ? (
              <div className="p-10 text-center text-gray-400">
                <p className="text-lg mb-2">No assessments yet</p>
                <Link href="/assessments/new">
                  <button className="mt-2 px-4 py-2 bg-brand-500 text-white rounded-lg text-sm">
                    Create your first assessment
                  </button>
                </Link>
              </div>
            ) : (
              <table className="w-full text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Title</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Score</th>
                    <th className="px-5 py-3" />
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {assessments.map((a) => (
                    <tr key={a.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-5 py-3 font-medium text-gray-900">{a.title}</td>
                      <td className="px-5 py-3">
                        <span className={clsx(
                          'text-xs px-2 py-1 rounded-full font-medium',
                          a.is_active
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-500'
                        )}>
                          {a.is_active ? 'Active' : 'Closed'}
                        </span>
                      </td>
                      <td className="px-5 py-3 text-gray-600">{a.response_count}</td>
                      <td className="px-5 py-3">
                        {a.overall_score != null ? (
                          <span className={clsx(
                            'text-sm font-bold',
                            a.overall_score > 89 ? 'text-green-600' :
                            a.overall_score > 65 ? 'text-yellow-600' :
                            a.overall_score > 50 ? 'text-orange-600' : 'text-red-600'
                          )}>
                            {a.overall_score}%
                          </span>
                        ) : (
                          <span className="text-xs text-gray-400">
                            {a.response_count === 0 ? 'Awaiting responses' : '—'}
                          </span>
                        )}
                      </td>
                      <td className="px-5 py-3 text-right">
                        {a.response_count > 0 ? (
                          <Link href={`/scorecard/${a.id}`}>
                            <button className="px-3 py-1.5 bg-brand-500 text-white rounded-lg text-xs font-semibold hover:bg-brand-600 transition-colors">
                              Calculate Score →
                            </button>
                          </Link>
                        ) : (
                          <span className="text-xs text-gray-300 px-3 py-1.5">No responses yet</span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>
        </div>
      </Layout>
    </ProtectedRoute>
  );
}
