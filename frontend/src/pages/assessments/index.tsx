import { useEffect, useState } from 'react';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { assessmentsAPI } from '@/lib/api';
import { Assessment } from '@/types';
import clsx from 'clsx';

export default function AssessmentsPage() {
  const [assessments, setAssessments] = useState<Assessment[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchAssessments = () => {
    assessmentsAPI.list()
      .then(({ data }) => setAssessments(data.results || data))
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchAssessments(); }, []);

  const toggleStatus = async (a: Assessment) => {
    await assessmentsAPI.update(a.id, { is_active: !a.is_active });
    fetchAssessments();
  };

  return (
    <ProtectedRoute>
      <Layout>
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold text-gray-900">Assessments</h1>
            <Link href="/assessments/new">
              <button className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600">
                + New Assessment
              </button>
            </Link>
          </div>

          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {loading ? (
              <div className="p-10 text-center text-gray-400">Loading...</div>
            ) : assessments.length === 0 ? (
              <div className="p-10 text-center text-gray-400">
                <p className="mb-3">No assessments yet</p>
                <Link href="/assessments/new">
                  <button className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm">Create one</button>
                </Link>
              </div>
            ) : (
              <table className="w-full text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Title</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Closes</th>
                    <th className="text-left px-5 py-3 font-medium text-gray-600">Created</th>
                    <th className="px-5 py-3" />
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-50">
                  {assessments.map((a) => (
                    <tr key={a.id} className="hover:bg-gray-50">
                      <td className="px-5 py-3 font-medium">{a.title}</td>
                      <td className="px-5 py-3">
                        <button
                          onClick={() => toggleStatus(a)}
                          className={clsx(
                            'text-xs px-2.5 py-1 rounded-full font-medium transition-colors',
                            a.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                          )}
                        >
                          {a.is_active ? 'Active' : 'Closed'}
                        </button>
                      </td>
                      <td className="px-5 py-3 text-gray-600">{a.response_count}</td>
                      <td className="px-5 py-3 text-gray-500">
                        {a.closes_at ? new Date(a.closes_at).toLocaleDateString() : '—'}
                      </td>
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
