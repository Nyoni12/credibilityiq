import { useEffect, useState } from 'react';
import Link from 'next/link';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { companiesAPI, usersAPI } from '@/lib/api';
import { Company } from '@/types';
import clsx from 'clsx';

export default function CompaniesPage() {
  const [companies, setCompanies] = useState<Company[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [tierFilter, setTierFilter] = useState('all');
  const [showModal, setShowModal] = useState(false);

  const fetchCompanies = () => {
    setLoading(true);
    companiesAPI.list()
      .then((res) => setCompanies(res.data.results ?? res.data))
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchCompanies(); }, []);

  const toggleActive = async (company: Company) => {
    const fd = new FormData();
    fd.append('is_active', String(!company.is_active));
    await companiesAPI.update(company.id, fd);
    fetchCompanies();
  };

  const deleteCompany = async (id: number) => {
    if (!confirm('Delete this company and ALL its data? This cannot be undone.')) return;
    await companiesAPI.delete(id);
    fetchCompanies();
  };

  const filtered = companies.filter((c) => {
    const matchSearch = c.name.toLowerCase().includes(search.toLowerCase()) ||
      (c.industry || '').toLowerCase().includes(search.toLowerCase());
    const matchTier = tierFilter === 'all' || c.subscription_tier === tierFilter;
    return matchSearch && matchTier;
  });

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-6">

          {/* Header */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Companies</h1>
              <p className="text-gray-500 text-sm mt-1">
                {loading ? '…' : `${companies.length} companies registered`}
              </p>
            </div>
            <button
              onClick={() => setShowModal(true)}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 transition-colors"
            >
              + Onboard Company
            </button>
          </div>

          {/* Filters */}
          <div className="flex flex-col sm:flex-row gap-3">
            <input
              type="search"
              placeholder="Search by name or industry…"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
            <select
              value={tierFilter}
              onChange={(e) => setTierFilter(e.target.value)}
              className="border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-brand-500 bg-white"
            >
              <option value="all">All Tiers</option>
              <option value="starter">Starter</option>
              <option value="professional">Professional</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>

          {/* Stats summary */}
          {!loading && (
            <div className="grid grid-cols-3 gap-4">
              {[
                { label: 'Starter', count: companies.filter((c) => c.subscription_tier === 'starter').length, color: 'bg-gray-100 text-gray-700' },
                { label: 'Professional', count: companies.filter((c) => c.subscription_tier === 'professional').length, color: 'bg-blue-100 text-blue-700' },
                { label: 'Enterprise', count: companies.filter((c) => c.subscription_tier === 'enterprise').length, color: 'bg-purple-100 text-purple-700' },
              ].map(({ label, count, color }) => (
                <div key={label} className="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                  <span className={clsx('text-xs font-semibold px-2 py-1 rounded-full', color)}>{label}</span>
                  <span className="text-2xl font-bold text-gray-900">{count}</span>
                </div>
              ))}
            </div>
          )}

          {/* Table */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
              <h2 className="font-semibold text-gray-900">
                {filtered.length !== companies.length
                  ? `${filtered.length} of ${companies.length} companies`
                  : `All Companies (${companies.length})`}
              </h2>
            </div>

            {loading ? (
              <div className="p-10 text-center text-gray-400">Loading…</div>
            ) : filtered.length === 0 ? (
              <div className="p-10 text-center text-gray-400">
                {search || tierFilter !== 'all' ? 'No companies match your filters.' : 'No companies yet.'}
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Company</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Tier</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Users</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Values</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Joined</th>
                      <th className="px-5 py-3" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {filtered.map((company) => (
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
                        <td className="px-5 py-3 text-gray-600">{company.user_count ?? 0}</td>
                        <td className="px-5 py-3 text-gray-600">{company.values_count ?? 0}</td>
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
                              <span className="text-brand-600 hover:text-brand-700 font-medium text-xs cursor-pointer whitespace-nowrap">
                                Manage →
                              </span>
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

        {showModal && (
          <CreateCompanyModal
            onClose={() => setShowModal(false)}
            onCreated={() => { setShowModal(false); fetchCompanies(); }}
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
    setSaving(true); setError('');
    try {
      const fd = new FormData();
      fd.append('name', form.name);
      fd.append('industry', form.industry);
      fd.append('subscription_tier', form.subscription_tier);
      const { data: company } = await companiesAPI.create(fd);
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

  const set = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }));

  return (
    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">Onboard New Company</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
        </div>
        <form onSubmit={handleSubmit} className="px-6 py-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">{error}</div>}
          <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Company</p>
          <div><label className="field-label">Name *</label><input required value={form.name} onChange={(e) => set('name', e.target.value)} className="field-input" placeholder="Acme Corp" /></div>
          <div><label className="field-label">Industry</label><input value={form.industry} onChange={(e) => set('industry', e.target.value)} className="field-input" placeholder="Technology" /></div>
          <div>
            <label className="field-label">Tier</label>
            <select value={form.subscription_tier} onChange={(e) => set('subscription_tier', e.target.value)} className="field-input">
              <option value="starter">Starter</option>
              <option value="professional">Professional</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </div>
          <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider pt-2">Admin Account</p>
          <div className="grid grid-cols-2 gap-3">
            <div><label className="field-label">First Name *</label><input required value={form.admin_first_name} onChange={(e) => set('admin_first_name', e.target.value)} className="field-input" /></div>
            <div><label className="field-label">Last Name *</label><input required value={form.admin_last_name} onChange={(e) => set('admin_last_name', e.target.value)} className="field-input" /></div>
          </div>
          <div><label className="field-label">Email *</label><input required type="email" value={form.admin_email} onChange={(e) => set('admin_email', e.target.value)} className="field-input" placeholder="admin@company.com" /></div>
          <div><label className="field-label">Password *</label><input required type="password" minLength={8} value={form.admin_password} onChange={(e) => set('admin_password', e.target.value)} className="field-input" placeholder="Min. 8 characters" /></div>
          <div className="flex gap-3 pt-2">
            <button type="button" onClick={onClose} className="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving} className="flex-1 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60">{saving ? 'Creating…' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
