import { useEffect, useState } from 'react';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { companiesAPI, dashboardAPI } from '@/lib/api';
import { Company } from '@/types';
import clsx from 'clsx';

export default function AdminDashboard() {
  const [companies, setCompanies] = useState<Company[]>([]);
  const [totalCompanies, setTotalCompanies] = useState(0);
  const [loading, setLoading] = useState(true);
  const [showCreateModal, setShowCreateModal] = useState(false);

  const fetchData = () => {
    Promise.all([dashboardAPI.summary(), companiesAPI.list()])
      .then(([sumRes, compRes]) => {
        setTotalCompanies(sumRes.data.total ?? 0);
        setCompanies(compRes.data.results || compRes.data);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchData(); }, []);

  const toggleActive = async (company: Company) => {
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

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-8">
          {/* Header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Super Admin Panel</h1>
              <p className="text-gray-500 text-sm mt-1">Manage all clients, licenses, and platform settings</p>
            </div>
            <button
              onClick={() => setShowCreateModal(true)}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 transition-colors"
            >
              + Onboard Company
            </button>
          </div>

          {/* Stats bar */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {[
              { label: 'Total Companies', value: totalCompanies },
              { label: 'Active Companies', value: companies.filter((c) => c.is_active).length },
              { label: 'Total Users', value: companies.reduce((s, c) => s + (c.user_count ?? 0), 0) },
              { label: 'Assessments Created', value: '—' },
            ].map(({ label, value }) => (
              <div key={label} className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p className="text-xs text-gray-500 font-medium">{label}</p>
                <p className="text-2xl font-bold text-brand-600 mt-1">{value}</p>
              </div>
            ))}
          </div>

          {/* Companies Table */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100">
              <h2 className="font-semibold text-gray-900">All Companies</h2>
            </div>

            {loading ? (
              <div className="p-10 text-center text-gray-400">Loading...</div>
            ) : companies.length === 0 ? (
              <div className="p-10 text-center text-gray-400">
                <p className="mb-2">No companies yet</p>
                <button
                  onClick={() => setShowCreateModal(true)}
                  className="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm"
                >
                  Add first company
                </button>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Company</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Industry</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Tier</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Users</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Values</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Created</th>
                      <th className="px-5 py-3" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {companies.map((company) => (
                      <tr key={company.id} className="hover:bg-gray-50 transition-colors">
                        <td className="px-5 py-3">
                          <div className="flex items-center gap-3">
                            {company.logo_url ? (
                              <img src={company.logo_url} alt="" className="w-7 h-7 rounded object-cover" />
                            ) : (
                              <div className="w-7 h-7 rounded bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-bold">
                                {company.name[0]}
                              </div>
                            )}
                            <span className="font-medium">{company.name}</span>
                          </div>
                        </td>
                        <td className="px-5 py-3 text-gray-500">{company.industry || '—'}</td>
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
                        <td className="px-5 py-3 text-gray-500">
                          {new Date(company.created_at).toLocaleDateString()}
                        </td>
                        <td className="px-5 py-3">
                          <div className="flex items-center gap-2 justify-end">
                            <a
                              href={`/admin/companies/${company.id}`}
                              className="text-brand-600 hover:text-brand-700 font-medium text-xs"
                            >
                              Manage
                            </a>
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

function CreateCompanyModal({
  onClose, onCreated,
}: { onClose: () => void; onCreated: () => void }) {
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

      // Create company admin user
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
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl">×</button>
        </div>

        <form onSubmit={handleSubmit} className="px-6 py-5 space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
              {error}
            </div>
          )}

          <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wide">Company Details</h3>
          <Field label="Company Name" required>
            <input required value={form.name} onChange={(e) => update('name', e.target.value)}
              className="field-input" placeholder="Acme Corp" />
          </Field>
          <Field label="Industry">
            <input value={form.industry} onChange={(e) => update('industry', e.target.value)}
              className="field-input" placeholder="Technology" />
          </Field>
          <Field label="Subscription Tier">
            <select value={form.subscription_tier} onChange={(e) => update('subscription_tier', e.target.value)}
              className="field-input">
              <option value="starter">Starter</option>
              <option value="professional">Professional</option>
              <option value="enterprise">Enterprise</option>
            </select>
          </Field>

          <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wide pt-2">Company Admin Account</h3>
          <div className="grid grid-cols-2 gap-3">
            <Field label="First Name" required>
              <input required value={form.admin_first_name} onChange={(e) => update('admin_first_name', e.target.value)}
                className="field-input" />
            </Field>
            <Field label="Last Name" required>
              <input required value={form.admin_last_name} onChange={(e) => update('admin_last_name', e.target.value)}
                className="field-input" />
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
