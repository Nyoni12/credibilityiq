import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { companiesAPI, assessmentsAPI, valuesAPI } from '@/lib/api';
import { Company, Assessment, CompanyValue } from '@/types';
import clsx from 'clsx';

function ScoreBadge({ score }: { score: number | null | undefined }) {
  if (score == null) return <span className="text-xs text-gray-300">—</span>;
  const { cls, short } =
    score > 89 ? { cls: 'text-green-700 bg-green-50 border-green-200',   short: 'CC'  } :
    score > 65 ? { cls: 'text-yellow-700 bg-yellow-50 border-yellow-200', short: 'GW'  } :
    score > 50 ? { cls: 'text-orange-700 bg-orange-50 border-orange-200', short: 'HDU' } :
                 { cls: 'text-red-700 bg-red-50 border-red-200',           short: 'ICU' };
  return (
    <span className={clsx('text-xs font-bold px-2 py-1 rounded-full border', cls)}>
      {score}% &nbsp;{short}
    </span>
  );
}

export default function CompanyDetail() {
  const router = useRouter();
  const { id } = router.query;
  const [company, setCompany] = useState<Company | null>(null);
  const [assessments, setAssessments] = useState<Assessment[]>([]);
  const [values, setValues] = useState<CompanyValue[]>([]);
  const [loading, setLoading] = useState(true);
  const [regenerating, setRegenerating] = useState(false);

  useEffect(() => {
    if (!id) return;
    const numId = Number(id);
    Promise.all([
      companiesAPI.get(numId),
      // Fetch ALL assessments (super admin scope) then filter — assessments endpoint
      // doesn't support ?company= filter yet, so we filter client-side from the full list
      assessmentsAPI.list(),
      valuesAPI.list(numId),
    ]).then(([compRes, assRes, valRes]) => {
      setCompany(compRes.data);
      const all: Assessment[] = assRes.data.results ?? assRes.data;
      setAssessments(all.filter((a) => a.company === numId));
      setValues(valRes.data.results ?? valRes.data);
    }).catch(console.error).finally(() => setLoading(false));
  }, [id]);

  const regenerateToken = async () => {
    if (!id) return;
    setRegenerating(true);
    try {
      const { data } = await companiesAPI.regenerateToken(Number(id));
      setCompany((prev) => prev ? { ...prev, assessment_link_token: data.assessment_link_token } : prev);
    } finally {
      setRegenerating(false);
    }
  };

  if (loading) return (
    <ProtectedRoute requireSuperAdmin><Layout>
      <div className="flex items-center justify-center h-64">
        <div className="w-10 h-10 border-4 border-brand-500 border-t-transparent rounded-full animate-spin" />
      </div>
    </Layout></ProtectedRoute>
  );

  if (!company) return null;

  const totalResponses = assessments.reduce((s, a) => s + a.response_count, 0);
  const scoredAssessments = assessments.filter((a) => a.overall_score != null);
  const avgScore = scoredAssessments.length
    ? Math.round(scoredAssessments.reduce((s, a) => s + a.overall_score!, 0) / scoredAssessments.length)
    : null;

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-6">

          {/* Header */}
          <div className="flex items-center gap-3 flex-wrap">
            <button onClick={() => router.push('/admin')} className="text-gray-400 hover:text-gray-600 text-sm">← Back</button>
            <div className="w-10 h-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center text-sm font-bold">
              {company.name[0]}
            </div>
            <div>
              <h1 className="text-xl font-bold text-gray-900">{company.name}</h1>
              <p className="text-xs text-gray-400">{company.industry || 'No industry set'}</p>
            </div>
            <span className={clsx(
              'text-xs px-2 py-1 rounded-full font-medium capitalize ml-auto',
              company.subscription_tier === 'enterprise' ? 'bg-purple-100 text-purple-700' :
              company.subscription_tier === 'professional' ? 'bg-blue-100 text-blue-700' :
              'bg-gray-100 text-gray-600'
            )}>{company.subscription_tier}</span>
            <span className={clsx(
              'text-xs px-2 py-1 rounded-full font-medium',
              company.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'
            )}>{company.is_active ? 'Active' : 'Inactive'}</span>
          </div>

          {/* KPI row */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {[
              { label: 'Assessments', value: assessments.length, color: 'text-brand-600' },
              { label: 'Total Responses', value: totalResponses, color: 'text-green-600' },
              { label: 'Values Configured', value: values.length, color: 'text-blue-600' },
              { label: 'Avg Credibility', value: avgScore != null ? `${avgScore}%` : '—',
                color: avgScore != null && avgScore >= 60 ? 'text-green-600' : 'text-amber-600' },
            ].map(({ label, value, color }) => (
              <div key={label} className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p className="text-xs text-gray-500 font-medium">{label}</p>
                <p className={clsx('text-2xl font-bold mt-1', color)}>{value}</p>
              </div>
            ))}
          </div>

          {/* Info + Survey link */}
          <div className="grid md:grid-cols-3 gap-4">
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5 col-span-2">
              <h2 className="font-semibold text-gray-900 mb-4">Company Info</h2>
              <dl className="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                {[
                  ['Industry', company.industry || '—'],
                  ['Status', company.is_active ? 'Active' : 'Inactive'],
                  ['Admin Users', String(company.user_count ?? 0)],
                  ['Values Set', String(values.length)],
                  ['Joined', new Date(company.created_at).toLocaleDateString()],
                  ['Subscription', company.subscription_tier],
                ].map(([k, v]) => (
                  <div key={k}>
                    <dt className="text-gray-400 text-xs font-medium uppercase tracking-wide">{k}</dt>
                    <dd className="text-gray-800 font-semibold mt-0.5">{v}</dd>
                  </div>
                ))}
              </dl>
            </div>

            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col">
              <h2 className="font-semibold text-gray-900 mb-3">Staff Survey Link</h2>
              <code className="text-xs bg-gray-50 border border-gray-200 rounded-lg p-3 block break-all text-gray-600 flex-1">
                {`${typeof window !== 'undefined' ? window.location.origin : ''}/survey/${company.assessment_link_token}`}
              </code>
              <button
                onClick={regenerateToken} disabled={regenerating}
                className="mt-3 w-full py-2 border border-gray-200 rounded-lg text-xs font-medium hover:bg-gray-50 disabled:opacity-60 transition-colors"
              >
                {regenerating ? 'Regenerating…' : '↺ Regenerate Token'}
              </button>
            </div>
          </div>

          {/* Configured Values */}
          {values.length > 0 && (
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
              <div className="px-5 py-4 border-b border-gray-100">
                <h2 className="font-semibold text-gray-900">Configured Values ({values.length})</h2>
              </div>
              <div className="divide-y divide-gray-50">
                {values.map((v) => (
                  <div key={v.id} className="px-5 py-3 flex items-center justify-between text-sm">
                    <div>
                      <span className="font-medium text-gray-900">{v.name}</span>
                      {v.description && <span className="text-gray-400 ml-2 text-xs">{v.description.slice(0, 60)}{v.description.length > 60 ? '…' : ''}</span>}
                    </div>
                    <span className="text-gray-500 font-medium">${Number(v.financial_weight).toLocaleString()}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Assessments */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100">
              <h2 className="font-semibold text-gray-900">Assessments ({assessments.length})</h2>
            </div>
            {assessments.length === 0 ? (
              <div className="p-8 text-center text-gray-400 text-sm">No assessments yet for this company.</div>
            ) : (
              <table className="w-full text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Title</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Score</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Created</th>
                    <th className="px-5 py-3" />
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {assessments.map((a) => (
                    <tr key={a.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-5 py-3 font-medium text-gray-900">{a.title}</td>
                      <td className="px-5 py-3">
                        <span className={clsx('text-xs px-2 py-1 rounded-full font-medium',
                          a.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')}>
                          {a.is_active ? 'Active' : 'Closed'}
                        </span>
                      </td>
                      <td className="px-5 py-3 text-gray-600">{a.response_count}</td>
                      <td className="px-5 py-3">
                        <ScoreBadge score={a.overall_score} />
                      </td>
                      <td className="px-5 py-3 text-gray-400">{new Date(a.created_at).toLocaleDateString()}</td>
                      <td className="px-5 py-3 text-right">
                        {a.response_count > 0 ? (
                          <Link href={`/scorecard/${a.id}`}>
                            <span className="text-brand-600 hover:text-brand-700 font-medium text-xs cursor-pointer">View Scorecard →</span>
                          </Link>
                        ) : (
                          <span className="text-xs text-gray-300">No responses</span>
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
