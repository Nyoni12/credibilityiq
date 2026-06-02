import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { companiesAPI, assessmentsAPI } from '@/lib/api';
import { Company, Assessment } from '@/types';
import clsx from 'clsx';

export default function CompanyDetail() {
  const router = useRouter();
  const { id } = router.query;
  const [company, setCompany] = useState<Company | null>(null);
  const [assessments, setAssessments] = useState<Assessment[]>([]);
  const [loading, setLoading] = useState(true);
  const [regenerating, setRegenerating] = useState(false);

  useEffect(() => {
    if (!id) return;
    Promise.all([
      companiesAPI.get(Number(id)),
      assessmentsAPI.list(),
    ]).then(([compRes, assRes]) => {
      setCompany(compRes.data);
      const all: Assessment[] = assRes.data.results || assRes.data;
      setAssessments(all.filter((a) => a.company === Number(id)));
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

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-6">
          <div className="flex items-center gap-4">
            <button onClick={() => router.push('/admin')} className="text-gray-400 hover:text-gray-600">← Back</button>
            <h1 className="text-2xl font-bold text-gray-900">{company.name}</h1>
            <span className={clsx(
              'text-xs px-2 py-1 rounded-full font-medium capitalize',
              company.subscription_tier === 'enterprise' ? 'bg-purple-100 text-purple-700' :
              company.subscription_tier === 'professional' ? 'bg-blue-100 text-blue-700' :
              'bg-gray-100 text-gray-600'
            )}>{company.subscription_tier}</span>
          </div>

          {/* Info cards */}
          <div className="grid md:grid-cols-3 gap-4">
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5 col-span-2">
              <h2 className="font-semibold text-gray-900 mb-4">Company Info</h2>
              <dl className="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                {[
                  ['Industry', company.industry || '—'],
                  ['Status', company.is_active ? 'Active' : 'Inactive'],
                  ['Users', String(company.user_count ?? 0)],
                  ['Values', String(company.values_count ?? 0)],
                  ['Created', new Date(company.created_at).toLocaleDateString()],
                ].map(([k, v]) => (
                  <div key={k}>
                    <dt className="text-gray-400 font-medium">{k}</dt>
                    <dd className="text-gray-800 font-semibold">{v}</dd>
                  </div>
                ))}
              </dl>
            </div>

            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
              <h2 className="font-semibold text-gray-900 mb-3">Survey Link</h2>
              <code className="text-xs bg-gray-50 border border-gray-200 rounded p-2 block break-all text-gray-600">
                {`${typeof window !== 'undefined' ? window.location.origin : ''}/survey/${company.assessment_link_token}`}
              </code>
              <button
                onClick={regenerateToken} disabled={regenerating}
                className="mt-3 w-full py-2 border border-gray-300 rounded-lg text-xs font-medium hover:bg-gray-50 disabled:opacity-60"
              >
                {regenerating ? 'Regenerating…' : 'Regenerate Token'}
              </button>
            </div>
          </div>

          {/* Assessments */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100">
              <h2 className="font-semibold text-gray-900">Assessments ({assessments.length})</h2>
            </div>
            {assessments.length === 0 ? (
              <div className="p-8 text-center text-gray-400 text-sm">No assessments for this company.</div>
            ) : (
              <table className="w-full text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Title</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Created</th>
                    <th className="px-5 py-3" />
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {assessments.map((a) => (
                    <tr key={a.id} className="hover:bg-gray-50">
                      <td className="px-5 py-3 font-medium">{a.title}</td>
                      <td className="px-5 py-3">
                        <span className={clsx('text-xs px-2 py-1 rounded-full font-medium',
                          a.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')}>
                          {a.is_active ? 'Active' : 'Closed'}
                        </span>
                      </td>
                      <td className="px-5 py-3 text-gray-600">{a.response_count}</td>
                      <td className="px-5 py-3 text-gray-500">{new Date(a.created_at).toLocaleDateString()}</td>
                      <td className="px-5 py-3 text-right">
                        <Link href={`/scorecard/${a.id}`}>
                          <button className="text-brand-600 hover:text-brand-700 font-medium text-xs">
                            Scorecard →
                          </button>
                        </Link>
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
