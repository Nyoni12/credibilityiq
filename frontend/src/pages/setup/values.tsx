import { useEffect, useState } from 'react';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { useAuth } from '@/context/AuthContext';
import { valuesAPI } from '@/lib/api';
import { CompanyValue } from '@/types';

interface EditableValue extends Partial<CompanyValue> {
  _key: string;
}

export default function ValuesSetup() {
  const { user } = useAuth();
  const companyId = user?.company_id;
  const [values, setValues] = useState<EditableValue[]>([]);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!companyId) return;
    valuesAPI.list(companyId).then(({ data }) => {
      setValues(
        (data.results || data).map((v: CompanyValue) => ({ ...v, _key: String(v.id) }))
      );
    });
  }, [companyId]);

  const addValue = () => {
    if (values.length >= 12) return;
    setValues((prev) => [
      ...prev,
      { _key: Date.now().toString(), name: '', description: '', financial_weight: 0, order: prev.length + 1 },
    ]);
  };

  const removeValue = (key: string) => {
    setValues((prev) => prev.filter((v) => v._key !== key));
  };

  const updateField = (key: string, field: keyof EditableValue, val: string | number) => {
    setValues((prev) =>
      prev.map((v) => (v._key === key ? { ...v, [field]: val } : v))
    );
  };

  const saveAll = async () => {
    if (!companyId) return;
    setSaving(true);
    setError('');
    try {
      const payload = values.map((v, idx) => ({
        name: v.name,
        description: v.description,
        financial_weight: Number(v.financial_weight),
        order: idx + 1,
      }));
      const { data } = await valuesAPI.bulkUpdate(companyId, payload);
      setValues(data.map((v: CompanyValue) => ({ ...v, _key: String(v.id) })));
      setSaved(true);
      setTimeout(() => setSaved(false), 3000);
    } catch (err: unknown) {
      setError((err as { response?: { data?: { detail?: string } } })?.response?.data?.detail || 'Save failed.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <ProtectedRoute>
      <Layout>
        <div className="max-w-3xl mx-auto space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Company Values Setup</h1>
              <p className="text-sm text-gray-500 mt-1">
                Configure up to 12 values and assign a financial weight to each
              </p>
            </div>
            <div className="flex gap-3">
              {values.length < 12 && (
                <button
                  onClick={addValue}
                  className="px-4 py-2 border border-brand-300 text-brand-600 rounded-lg text-sm font-medium hover:bg-brand-50 transition-colors"
                >
                  + Add Value
                </button>
              )}
              <button
                onClick={saveAll}
                disabled={saving}
                className="px-5 py-2 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60 transition-colors"
              >
                {saving ? 'Saving…' : saved ? '✓ Saved' : 'Save All'}
              </button>
            </div>
          </div>

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
              {error}
            </div>
          )}

          {values.length === 0 ? (
            <div className="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
              <p className="text-gray-400 mb-4">No values configured yet</p>
              <button
                onClick={addValue}
                className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm"
              >
                Add First Value
              </button>
            </div>
          ) : (
            <div className="space-y-3">
              {/* Column headers */}
              <div className="grid grid-cols-12 gap-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wide">
                <div className="col-span-1">#</div>
                <div className="col-span-3">Name</div>
                <div className="col-span-5">Description</div>
                <div className="col-span-2">$ Weight</div>
                <div className="col-span-1" />
              </div>

              {values.map((v, idx) => (
                <div
                  key={v._key}
                  className="grid grid-cols-12 gap-3 items-center bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3"
                >
                  <div className="col-span-1 text-sm font-medium text-gray-400 text-center">
                    {idx + 1}
                  </div>
                  <div className="col-span-3">
                    <input
                      value={v.name || ''}
                      onChange={(e) => updateField(v._key, 'name', e.target.value)}
                      placeholder="e.g. Integrity"
                      className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    />
                  </div>
                  <div className="col-span-5">
                    <input
                      value={v.description || ''}
                      onChange={(e) => updateField(v._key, 'description', e.target.value)}
                      placeholder="Short description..."
                      className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                    />
                  </div>
                  <div className="col-span-2">
                    <div className="relative">
                      <span className="absolute left-2.5 top-2 text-gray-400 text-sm">$</span>
                      <input
                        type="number" min="0" step="100"
                        value={v.financial_weight || 0}
                        onChange={(e) => updateField(v._key, 'financial_weight', e.target.value)}
                        className="w-full border border-gray-200 rounded-lg pl-6 pr-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                      />
                    </div>
                  </div>
                  <div className="col-span-1 flex justify-center">
                    <button
                      onClick={() => removeValue(v._key)}
                      className="text-gray-300 hover:text-red-500 transition-colors text-lg leading-none"
                    >
                      ×
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}

          <div className="text-xs text-gray-400 text-center">
            {values.length}/12 values &mdash; Financial weights represent the total budget at risk for each value
          </div>
        </div>
      </Layout>
    </ProtectedRoute>
  );
}
