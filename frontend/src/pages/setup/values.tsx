import { useEffect, useState } from 'react';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { useAuth } from '@/context/AuthContext';
import { valuesAPI } from '@/lib/api';
import { CompanyValue } from '@/types';
import clsx from 'clsx';

const CFA_VALUES = [
  { name: 'Teamwork',                    hint: 'Collaborative effort toward shared goals' },
  { name: 'Integrity',                   hint: 'Adherence to ethical principles and moral standards' },
  { name: 'Reputation',                  hint: 'How the organisation is perceived externally' },
  { name: 'Respect for self and others', hint: 'Dignified treatment at all organisational levels' },
  { name: 'Humility',                    hint: 'Openness to feedback and continuous learning' },
  { name: 'Empathy',                     hint: "Understanding others' perspectives and emotional needs" },
  { name: 'Fairness',                    hint: 'Just and impartial treatment of all stakeholders' },
  { name: 'Hardwork',                    hint: 'Consistent diligence and effort in all tasks' },
  { name: 'Responsibility',              hint: 'Ownership of actions and their outcomes' },
  { name: 'Accountability',              hint: 'Answering for results and commitments made' },
  { name: 'Trustworthiness',             hint: 'Reliability and dependability in all dealings' },
  { name: 'Honesty without offense',     hint: 'Truthful communication delivered with respect and care' },
];

interface SelectedValue {
  description: string;
  financial_weight: number | string;
}

export default function ValuesSetup() {
  const { user } = useAuth();
  const companyId = user?.company_id;
  const [selected, setSelected] = useState<Record<string, SelectedValue>>({});
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!companyId) return;
    valuesAPI.list(companyId).then(({ data }) => {
      const existing: Record<string, SelectedValue> = {};
      (data.results || data).forEach((v: CompanyValue) => {
        if (CFA_VALUES.some((cfav) => cfav.name === v.name)) {
          existing[v.name] = {
            description: v.description || '',
            financial_weight: v.financial_weight ?? 0,
          };
        }
      });
      setSelected(existing);
    });
  }, [companyId]);

  const toggle = (name: string) => {
    setSelected((prev) => {
      if (prev[name]) {
        const next = { ...prev };
        delete next[name];
        return next;
      }
      return { ...prev, [name]: { description: '', financial_weight: 0 } };
    });
  };

  const update = (name: string, field: keyof SelectedValue, val: string | number) => {
    setSelected((prev) => ({ ...prev, [name]: { ...prev[name], [field]: val } }));
  };

  const saveAll = async () => {
    if (!companyId) return;
    setSaving(true);
    setError('');
    try {
      const payload = Object.entries(selected).map(([name, data], idx) => ({
        name,
        description: data.description,
        financial_weight: Number(data.financial_weight) || 0,
        order: idx + 1,
      }));
      await valuesAPI.bulkUpdate(companyId, payload);
      setSaved(true);
      setTimeout(() => setSaved(false), 3000);
    } catch (err: unknown) {
      const msg = (err as { response?: { data?: { detail?: string } } })?.response?.data?.detail;
      setError(msg || 'Save failed. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const selectedCount = Object.keys(selected).length;

  return (
    <ProtectedRoute>
      <Layout>
        <div className="max-w-3xl mx-auto space-y-6">

          {/* Header */}
          <div className="flex items-start justify-between gap-4">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Credibility Building Blocks</h1>
              <p className="text-sm text-gray-500 mt-1">
                Select the values that apply to your organisation. Assign a financial weight (annual $ at risk) to each.
              </p>
            </div>
            <button
              onClick={saveAll}
              disabled={saving || selectedCount === 0}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-50 transition-colors whitespace-nowrap"
            >
              {saving ? 'Saving…' : saved ? '✓ Saved' : `Save (${selectedCount} selected)`}
            </button>
          </div>

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">{error}</div>
          )}

          {/* Progress strip */}
          <div className="flex items-center gap-3 bg-brand-50 rounded-xl px-4 py-3 border border-brand-100">
            <div className="flex-1 h-2 bg-brand-100 rounded-full overflow-hidden">
              <div
                className="h-2 bg-brand-500 rounded-full transition-all"
                style={{ width: `${(selectedCount / 12) * 100}%` }}
              />
            </div>
            <span className="text-sm font-semibold text-brand-600 whitespace-nowrap">{selectedCount} / 12</span>
          </div>

          {/* Value cards */}
          <div className="space-y-3">
            {CFA_VALUES.map(({ name, hint }, index) => {
              const isSelected = !!selected[name];
              return (
                <div
                  key={name}
                  className={clsx(
                    'rounded-xl border-2 transition-all duration-150',
                    isSelected ? 'border-brand-400 bg-white shadow-sm' : 'border-gray-100 bg-gray-50'
                  )}
                >
                  {/* Row header — always visible, click to toggle */}
                  <div
                    className="flex items-center gap-3 px-4 py-3 cursor-pointer select-none"
                    onClick={() => toggle(name)}
                  >
                    {/* Checkbox */}
                    <div className={clsx(
                      'w-5 h-5 rounded flex items-center justify-center flex-shrink-0 border-2 transition-all',
                      isSelected ? 'bg-brand-500 border-brand-500' : 'border-gray-300 bg-white'
                    )}>
                      {isSelected && (
                        <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                          <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                      )}
                    </div>

                    {/* Index number */}
                    <span className="text-xs font-bold text-gray-300 w-5 text-center flex-shrink-0">
                      {String(index + 1).padStart(2, '0')}
                    </span>

                    {/* Value name + hint */}
                    <div className="flex-1 min-w-0">
                      <p className={clsx('text-sm font-semibold', isSelected ? 'text-gray-900' : 'text-gray-500')}>
                        {name}
                      </p>
                      <p className="text-xs text-gray-400 mt-0.5">{hint}</p>
                    </div>

                    {isSelected && (
                      <span className="text-xs bg-brand-100 text-brand-600 px-2.5 py-0.5 rounded-full font-semibold flex-shrink-0">
                        Selected
                      </span>
                    )}
                  </div>

                  {/* Expanded inputs — only shown when selected */}
                  {isSelected && (
                    <div className="border-t border-gray-100 px-4 pb-4 pt-3 grid grid-cols-2 gap-3">
                      <div>
                        <label className="block text-xs font-medium text-gray-500 mb-1">
                          Annual $ at Risk <span className="text-red-400">*</span>
                        </label>
                        <div className="relative">
                          <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                          <input
                            type="number"
                            min="0"
                            step="1000"
                            value={selected[name].financial_weight}
                            onChange={(e) => update(name, 'financial_weight', e.target.value)}
                            onClick={(e) => e.stopPropagation()}
                            placeholder="0"
                            className="w-full border border-gray-200 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                          />
                        </div>
                      </div>
                      <div>
                        <label className="block text-xs font-medium text-gray-500 mb-1">
                          Description <span className="text-gray-300">(optional)</span>
                        </label>
                        <input
                          type="text"
                          value={selected[name].description}
                          onChange={(e) => update(name, 'description', e.target.value)}
                          onClick={(e) => e.stopPropagation()}
                          placeholder="How this value shows up in your organisation…"
                          className="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                        />
                      </div>
                    </div>
                  )}
                </div>
              );
            })}
          </div>

          <div className="flex items-center justify-between text-xs text-gray-400 pb-6">
            <span>{selectedCount} of 12 values selected</span>
            <span>Financial weights represent the total annual budget at risk per value</span>
          </div>
        </div>
      </Layout>
    </ProtectedRoute>
  );
}
