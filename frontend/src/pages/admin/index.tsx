import { useEffect, useState } from 'react';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { companiesAPI, dashboardAPI } from '@/lib/api';
import { SuperAdminSummary, AdminCompany } from '@/types';
import clsx from 'clsx';

function KpiCard({ label, value, sub, color = 'brand' }: {
  label: string; value: string | number; sub?: string; color?: string;
}) {
  return (
    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">{label}</p>
      <p className={clsx('text-3xl font-bold mt-1', `text-${color}-600`)}>{value}</p>
      {sub && <p className="text-xs text-gray-400 mt-1">{sub}</p>}
    </div>
  );
}

function clcStage(score: number) {
  if (score > 89) return { short: 'CC',  cls: 'text-green-700 bg-green-50'  };
  if (score > 65) return { short: 'GW',  cls: 'text-yellow-700 bg-yellow-50' };
  if (score > 50) return { short: 'HDU', cls: 'text-orange-700 bg-orange-50' };
  return              { short: 'ICU', cls: 'text-red-700 bg-red-50'    };
}

function ScoreBadge({ score }: { score: number | null }) {
  if (score == null) return <span className="text-xs text-gray-300">No data</span>;
  const { short, cls } = clcStage(score);
  return (
    <div className="flex items-center gap-1.5">
      <span className={clsx('text-xs font-bold px-2 py-0.5 rounded-full', cls)}>{score}%</span>
      <span className={clsx('text-xs font-semibold', cls)}>{short}</span>
    </div>
  );
}

export default function AdminDashboard() {
  const [summary, setSummary] = useState<SuperAdminSummary | null>(null);
  const [loading, setLoading] = useState(true);
  const [showCreateModal, setShowCreateModal] = useState(false);

  const fetchData = () => {
    setLoading(true);
    dashboardAPI.summary()
      .then((res) => setSummary(res.data))
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchData(); }, []);

  const toggleActive = async (company: AdminCompany) => {
    const fd = new FormData();
    fd.append('is_active', String(!company.is_active));
    await companiesAPI.update(company.id, fd);
    fetchData();
  };

  const deleteCompany = async (id: number) => {
    if (!confirm('Delete this company and all its data? This cannot be undone.')) return;
    await companiesAPI.delete(id);
    fetchData();
  };

  const companies = summary?.companies ?? [];

  // Industry breakdown from loaded data
  const industryMap: Record<string, number> = {};
  companies.forEach((c) => {
    const ind = c.industry || 'Other';
    industryMap[ind] = (industryMap[ind] || 0) + 1;
  });
  const topIndustries = Object.entries(industryMap)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 5);

  // Average credibility score across companies that have scores
  const scored = companies.filter((c) => c.latest_score != null);
  const avgScore = scored.length
    ? Math.round(scored.reduce((s, c) => s + c.latest_score!, 0) / scored.length)
    : null;

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-8">

          {/* Header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Platform Overview</h1>
              <p className="text-gray-500 text-sm mt-1">All clients, assessments and platform analytics</p>
            </div>
            <button
              onClick={() => setShowCreateModal(true)}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 transition-colors"
            >
              + Onboard Company
            </button>
          </div>

          {/* KPI strip */}
          {loading ? (
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 animate-pulse">
              {[...Array(4)].map((_, i) => <div key={i} className="h-24 bg-gray-100 rounded-xl" />)}
            </div>
          ) : (
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <KpiCard label="Total Companies" value={summary?.total ?? 0} sub={`${summary?.active_count ?? 0} active`} color="brand" />
              <KpiCard label="Assessments Run" value={summary?.total_assessments ?? 0} sub="All time" color="blue" />
              <KpiCard label="Staff Responses" value={summary?.total_responses ?? 0} sub="All companies" color="green" />
              <KpiCard
                label="Avg Credibility Score"
                value={avgScore != null ? `${avgScore}%` : '—'}
                sub={scored.length ? `${scored.length} companies scored` : 'No active assessments'}
                color={avgScore != null && avgScore >= 60 ? 'green' : 'amber'}
              />
            </div>
          )}

          {/* Second row: tier breakdown + industry breakdown */}
          {!loading && summary && (
            <div className="grid md:grid-cols-2 gap-6">

              {/* Subscription tiers */}
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 className="font-semibold text-gray-900 mb-4">Subscription Tiers</h2>
                <div className="space-y-3">
                  {[
                    { tier: 'enterprise', label: 'Enterprise', color: 'bg-purple-500' },
                    { tier: 'professional', label: 'Professional', color: 'bg-blue-500' },
                    { tier: 'starter', label: 'Starter', color: 'bg-gray-400' },
                  ].map(({ tier, label, color }) => {
                    const count = summary.tier_breakdown[tier as keyof typeof summary.tier_breakdown] ?? 0;
                    const pct = summary.total ? Math.round((count / summary.total) * 100) : 0;
                    return (
                      <div key={tier}>
                        <div className="flex justify-between text-sm mb-1">
                          <span className="font-medium text-gray-700">{label}</span>
                          <span className="text-gray-500">{count} ({pct}%)</span>
                        </div>
                        <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                          <div className={clsx('h-2 rounded-full transition-all', color)} style={{ width: `${pct}%` }} />
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>

              {/* Industry breakdown */}
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h2 className="font-semibold text-gray-900 mb-4">Top Industries</h2>
                {topIndustries.length === 0 ? (
                  <p className="text-sm text-gray-400">No industry data</p>
                ) : (
                  <div className="space-y-3">
                    {topIndustries.map(([ind, count]) => {
                      const pct = summary.total ? Math.round((count / summary.total) * 100) : 0;
                      return (
                        <div key={ind}>
                          <div className="flex justify-between text-sm mb-1">
                            <span className="font-medium text-gray-700">{ind}</span>
                            <span className="text-gray-500">{count} ({pct}%)</span>
                          </div>
                          <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div className="h-2 bg-brand-400 rounded-full" style={{ width: `${pct}%` }} />
                          </div>
                        </div>
                      );
                    })}
                  </div>
                )}
              </div>
            </div>
          )}

          {/* CLC Lifecycle Distribution */}
          {!loading && scored.length > 0 && (() => {
            const stages = [
              { short: 'ICU', label: 'Intensive Care Unit',  range: '0 – 50%',    bg: 'bg-red-50',    border: 'border-red-200',    text: 'text-red-700',    bar: 'bg-red-500',    grp: scored.filter(c => c.latest_score! <= 50) },
              { short: 'HDU', label: 'High Dependency Unit', range: '51 – 65%',   bg: 'bg-orange-50', border: 'border-orange-200', text: 'text-orange-700', bar: 'bg-orange-500', grp: scored.filter(c => c.latest_score! > 50 && c.latest_score! <= 65) },
              { short: 'GW',  label: 'General Ward',         range: '66 – 89%',   bg: 'bg-yellow-50', border: 'border-yellow-200', text: 'text-yellow-700', bar: 'bg-yellow-400', grp: scored.filter(c => c.latest_score! > 65 && c.latest_score! <= 89) },
              { short: 'CC',  label: 'Celebrity Credibility',range: '90 – 100%',  bg: 'bg-green-50',  border: 'border-green-200',  text: 'text-green-700',  bar: 'bg-green-500',  grp: scored.filter(c => c.latest_score! > 89) },
            ];
            return (
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div className="flex items-start justify-between mb-5">
                  <div>
                    <h2 className="font-semibold text-gray-900">Credibility Life-Cycle Distribution</h2>
                    <p className="text-xs text-gray-400 mt-0.5">
                      CFA CLC Framework — where your client portfolio currently stands ({scored.length} scored)
                    </p>
                  </div>
                  <div className="flex gap-3 text-xs">
                    {stages.map(s => (
                      <span key={s.short} className={clsx('font-bold px-2 py-0.5 rounded-full', s.bg, s.text)}>{s.short}: {s.grp.length}</span>
                    ))}
                  </div>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                  {stages.map(({ short, label, range, bg, border, text, grp }) => (
                    <div key={short} className={clsx('rounded-xl border-2 p-4', bg, border)}>
                      <div className={clsx('text-3xl font-black leading-none mb-1', text)}>{grp.length}</div>
                      <div className={clsx('text-sm font-bold', text)}>{short}</div>
                      <div className="text-xs text-gray-500 font-medium mt-0.5 leading-tight">{label}</div>
                      <div className={clsx('text-xs font-medium mt-1', text)}>{range}</div>
                      <div className="mt-2 space-y-0.5">
                        {grp.slice(0, 3).map(c => (
                          <div key={c.id} className="text-xs text-gray-500 truncate">• {c.name}</div>
                        ))}
                        {grp.length > 3 && <div className="text-xs text-gray-400">+{grp.length - 3} more</div>}
                        {grp.length === 0 && <div className="text-xs text-gray-300 italic">None</div>}
                      </div>
                    </div>
                  ))}
                </div>

                <div className="h-3 rounded-full overflow-hidden flex bg-gray-100">
                  {stages.map(({ short, bar, grp }) => grp.length === 0 ? null : (
                    <div key={short} className={bar} style={{ width: `${(grp.length / scored.length) * 100}%` }} title={`${short}: ${grp.length}`} />
                  ))}
                </div>
                <div className="flex justify-between text-xs font-semibold mt-1.5">
                  <span className="text-red-500">ICU ≤50%</span>
                  <span className="text-orange-500">HDU 51–65%</span>
                  <span className="text-yellow-600">GW 66–89%</span>
                  <span className="text-green-600">CC 90–100%</span>
                </div>
              </div>
            );
          })()}

          {/* Companies table */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h2 className="font-semibold text-gray-900">
                All Companies
                {!loading && <span className="ml-2 text-gray-400 font-normal text-sm">({companies.length})</span>}
              </h2>
              <div className="flex items-center gap-2 text-xs text-gray-400">
                <span className="w-2 h-2 rounded-full bg-green-400 inline-block" /> Active
                <span className="w-2 h-2 rounded-full bg-gray-300 inline-block ml-2" /> Inactive
              </div>
            </div>

            {loading ? (
              <div className="p-10 text-center text-gray-400">Loading…</div>
            ) : companies.length === 0 ? (
              <div className="p-10 text-center text-gray-400">
                <p className="mb-3">No companies yet</p>
                <button onClick={() => setShowCreateModal(true)} className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm">
                  Add first company
                </button>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Company</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Tier</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Assessments</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Credibility / CLC</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Since</th>
                      <th className="px-5 py-3" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {companies.map((company) => (
                      <tr key={company.id} className={clsx('hover:bg-gray-50 transition-colors', !company.is_active && 'opacity-60')}>
                        <td className="px-5 py-3">
                          <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                              {company.name[0]}
                            </div>
                            <div>
                              <p className="font-medium text-gray-900">{company.name}</p>
                              <p className="text-xs text-gray-400">{company.industry || '—'}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-5 py-3">
                          <span className={clsx(
                            'text-xs px-2 py-1 rounded-full font-medium capitalize',
                            company.subscription_tier === 'enterprise' ? 'bg-purple-100 text-purple-700' :
                            company.subscription_tier === 'professional' ? 'bg-blue-100 text-blue-700' :
                            'bg-gray-100 text-gray-600'
                          )}>
                            {company.subscription_tier}
                          </span>
                        </td>
                        <td className="px-5 py-3 text-gray-700 font-medium">{company.assessment_count}</td>
                        <td className="px-5 py-3 text-gray-700">{company.total_responses}</td>
                        <td className="px-5 py-3">
                          <ScoreBadge score={company.latest_score} />
                        </td>
                        <td className="px-5 py-3">
                          <button
                            onClick={() => toggleActive(company)}
                            className={clsx(
                              'text-xs px-2 py-1 rounded-full font-medium transition-colors',
                              company.is_active
                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                            )}
                          >
                            {company.is_active ? 'Active' : 'Inactive'}
                          </button>
                        </td>
                        <td className="px-5 py-3 text-gray-400 text-xs">
                          {new Date(company.created_at).toLocaleDateString()}
                        </td>
                        <td className="px-5 py-3">
                          <div className="flex items-center gap-3 justify-end">
                            <Link href={`/admin/companies/${company.id}`}>
                              <span className="text-brand-600 hover:text-brand-700 font-medium text-xs cursor-pointer">Manage →</span>
                            </Link>
                            <button
                              onClick={() => deleteCompany(company.id)}
                              className="text-red-400 hover:text-red-600 text-xs font-medium"
                            >
                              Delete
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>

        {showCreateModal && (
          <CreateCompanyModal
            onClose={() => setShowCreateModal(false)}
            onCreated={() => { setShowCreateModal(false); fetchData(); }}
          />
        )}
      </Layout>
    </ProtectedRoute>
  );
}

function CreateCompanyModal({ onClose, onCreated }: { onClose: () => void; onCreated: () => void }) {
  const [form, setForm] = useState({
    name: '', industry: '', subscription_tier: 'starter',
    admin_email: '', admin_first_name: '', admin_last_name: '', admin_password: '',
  });
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    setError('');
    try {
      const fd = new FormData();
      fd.append('name', form.name);
      fd.append('industry', form.industry);
      fd.append('subscription_tier', form.subscription_tier);
      const { data: company } = await companiesAPI.create(fd);

      const { usersAPI } = await import('@/lib/api');
      await usersAPI.create({
        email: form.admin_email,
        first_name: form.admin_first_name,
        last_name: form.admin_last_name,
        password: form.admin_password,
        company: company.id,
        is_superadmin: false,
      });
      onCreated();
    } catch (err: unknown) {
      const data = (err as { response?: { data?: Record<string, string[]> } })?.response?.data;
      setError(data ? Object.values(data).flat().join(' ') : 'Failed to create company.');
    } finally {
      setSaving(false);
    }
  };

  const update = (field: string, val: string) => setForm((f) => ({ ...f, [field]: val }));

  return (
    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">Onboard New Company</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
        </div>

        <form onSubmit={handleSubmit} className="px-6 py-5 space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">{error}</div>
          )}

          <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Company Details</p>

          <Field label="Company Name" required>
            <input required value={form.name} onChange={(e) => update('name', e.target.value)}
              className="field-input" placeholder="Acme Corp" />
          </Field>
          <Field label="Industry">
            <input value={form.industry} onChange={(e) => update('industry', e.target.value)}
              className="field-input" placeholder="Technology" />
          </Field>
          <Field label="Subscription Tier">
            <select value={form.subscription_tier} onChange={(e) => update('subscription_tier', e.target.value)} className="field-input">
              <option value="starter">Starter</option>
              <option value="professional">Professional</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </Field>

          <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider pt-2">Admin Account</p>
          <div className="grid grid-cols-2 gap-3">
            <Field label="First Name" required>
              <input required value={form.admin_first_name} onChange={(e) => update('admin_first_name', e.target.value)} className="field-input" />
            </Field>
            <Field label="Last Name" required>
              <input required value={form.admin_last_name} onChange={(e) => update('admin_last_name', e.target.value)} className="field-input" />
            </Field>
          </div>
          <Field label="Admin Email" required>
            <input required type="email" value={form.admin_email} onChange={(e) => update('admin_email', e.target.value)}
              className="field-input" placeholder="admin@company.com" />
          </Field>
          <Field label="Temporary Password" required>
            <input required type="password" minLength={8} value={form.admin_password}
              onChange={(e) => update('admin_password', e.target.value)}
              className="field-input" placeholder="Min. 8 characters" />
          </Field>

          <div className="flex gap-3 pt-2">
            <button type="button" onClick={onClose}
              className="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" disabled={saving}
              className="flex-1 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60">
              {saving ? 'Creating…' : 'Create Company'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

function Field({ label, required, children }: { label: string; required?: boolean; children: React.ReactNode }) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">
        {label}{required && <span className="text-red-400 ml-0.5">*</span>}
      </label>
      {children}
    </div>
  );
}
