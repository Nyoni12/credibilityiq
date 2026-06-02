import { useState } from 'react';
import { useRouter } from 'next/router';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { assessmentsAPI } from '@/lib/api';

export default function NewAssessment() {
  const router = useRouter();
  const [title, setTitle] = useState('');
  const [closesAt, setClosesAt] = useState('');
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setError('');
    try {
      await assessmentsAPI.create({ title, closes_at: closesAt || null });
      router.push('/assessments');
    } catch (err: unknown) {
      const data = (err as { response?: { data?: Record<string, string[]> } })?.response?.data;
      setError(data ? Object.values(data).flat().join(' ') : 'Failed to create assessment.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <ProtectedRoute>
      <Layout>
        <div className="max-w-lg mx-auto space-y-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Create New Assessment</h1>
            <p className="text-gray-500 text-sm mt-1">Start collecting staff value ratings</p>
          </div>

          <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            {error && (
              <div className="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                {error}
              </div>
            )}
            <form onSubmit={handleSubmit} className="space-y-5">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Assessment Title *</label>
                <input
                  required value={title} onChange={(e) => setTitle(e.target.value)}
                  className="field-input"
                  placeholder="e.g. Q2 2025 Culture Assessment"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Closes At <span className="text-gray-400 font-normal">(optional)</span>
                </label>
                <input
                  type="datetime-local" value={closesAt} onChange={(e) => setClosesAt(e.target.value)}
                  className="field-input"
                />
                <p className="text-xs text-gray-400 mt-1">Leave blank to keep open indefinitely</p>
              </div>
              <div className="flex gap-3 pt-2">
                <button
                  type="button" onClick={() => router.back()}
                  className="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit" disabled={saving}
                  className="flex-1 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60"
                >
                  {saving ? 'Creating…' : 'Create Assessment'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Layout>
    </ProtectedRoute>
  );
}
