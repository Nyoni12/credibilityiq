import { useEffect, useState } from 'react';
import Layout from '@/components/Layout';
import ProtectedRoute from '@/components/ProtectedRoute';
import { usersAPI, companiesAPI } from '@/lib/api';
import { Company } from '@/types';
import clsx from 'clsx';

interface User {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  is_superadmin: boolean;
  company: number | null;
  company_name: string | null;
  created_at: string;
}

export default function UsersPage() {
  const [users, setUsers] = useState<User[]>([]);
  const [companies, setCompanies] = useState<Company[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [companyFilter, setCompanyFilter] = useState('all');
  const [showModal, setShowModal] = useState(false);
  const [deleting, setDeleting] = useState<number | null>(null);

  const fetchData = () => {
    setLoading(true);
    Promise.all([usersAPI.list(), companiesAPI.list()])
      .then(([uRes, cRes]) => {
        setUsers(uRes.data.results ?? uRes.data);
        setCompanies(cRes.data.results ?? cRes.data);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  useEffect(() => { fetchData(); }, []);

  const deleteUser = async (id: number) => {
    if (!confirm('Remove this user account? They will lose access immediately.')) return;
    setDeleting(id);
    try {
      await usersAPI.delete(id);
      fetchData();
    } finally {
      setDeleting(null);
    }
  };

  const filtered = users.filter((u) => {
    const name = `${u.first_name} ${u.last_name} ${u.email}`.toLowerCase();
    const matchSearch = name.includes(search.toLowerCase());
    const matchCompany = companyFilter === 'all'
      ? true
      : companyFilter === 'superadmin'
        ? u.is_superadmin
        : String(u.company) === companyFilter;
    return matchSearch && matchCompany;
  });

  const superAdmins = users.filter((u) => u.is_superadmin).length;
  const companyAdmins = users.filter((u) => !u.is_superadmin).length;

  return (
    <ProtectedRoute requireSuperAdmin>
      <Layout>
        <div className="space-y-6">

          {/* Header */}
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Users</h1>
              <p className="text-gray-500 text-sm mt-1">
                {loading ? '…' : `${users.length} accounts across ${companies.length} companies`}
              </p>
            </div>
            <button
              onClick={() => setShowModal(true)}
              className="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 transition-colors"
            >
              + Add User
            </button>
          </div>

          {/* Stats */}
          {!loading && (
            <div className="grid grid-cols-3 gap-4">
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p className="text-xs text-gray-500 font-medium">Total Users</p>
                <p className="text-2xl font-bold text-brand-600 mt-1">{users.length}</p>
              </div>
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p className="text-xs text-gray-500 font-medium">Company Admins</p>
                <p className="text-2xl font-bold text-blue-600 mt-1">{companyAdmins}</p>
              </div>
              <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p className="text-xs text-gray-500 font-medium">Super Admins</p>
                <p className="text-2xl font-bold text-purple-600 mt-1">{superAdmins}</p>
              </div>
            </div>
          )}

          {/* Filters */}
          <div className="flex flex-col sm:flex-row gap-3">
            <input
              type="search"
              placeholder="Search by name or email…"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
            <select
              value={companyFilter}
              onChange={(e) => setCompanyFilter(e.target.value)}
              className="border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-brand-500 bg-white"
            >
              <option value="all">All Companies</option>
              <option value="superadmin">Super Admins only</option>
              {companies.map((c) => (
                <option key={c.id} value={String(c.id)}>{c.name}</option>
              ))}
            </select>
          </div>

          {/* Table */}
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div className="px-5 py-4 border-b border-gray-100">
              <h2 className="font-semibold text-gray-900">
                {filtered.length !== users.length
                  ? `${filtered.length} of ${users.length} users`
                  : `All Users (${users.length})`}
              </h2>
            </div>

            {loading ? (
              <div className="p-10 text-center text-gray-400">Loading…</div>
            ) : filtered.length === 0 ? (
              <div className="p-10 text-center text-gray-400">No users match your filters.</div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">User</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Email</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Role</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Company</th>
                      <th className="text-left px-5 py-3 font-medium text-gray-600">Joined</th>
                      <th className="px-5 py-3" />
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {filtered.map((user) => (
                      <tr key={user.id} className="hover:bg-gray-50 transition-colors">
                        <td className="px-5 py-3">
                          <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                              {user.first_name?.[0] ?? user.email[0].toUpperCase()}
                            </div>
                            <span className="font-medium text-gray-900">
                              {user.first_name} {user.last_name}
                            </span>
                          </div>
                        </td>
                        <td className="px-5 py-3 text-gray-600">{user.email}</td>
                        <td className="px-5 py-3">
                          {user.is_superadmin ? (
                            <span className="text-xs px-2 py-1 rounded-full font-medium bg-purple-100 text-purple-700">
                              Super Admin
                            </span>
                          ) : (
                            <span className="text-xs px-2 py-1 rounded-full font-medium bg-blue-100 text-blue-700">
                              Company Admin
                            </span>
                          )}
                        </td>
                        <td className="px-5 py-3 text-gray-600">
                          {user.company_name ? (
                            <a href={`/admin/companies/${user.company}`}
                              className="text-brand-600 hover:underline text-xs font-medium">
                              {user.company_name}
                            </a>
                          ) : '—'}
                        </td>
                        <td className="px-5 py-3 text-gray-400 text-xs">
                          {new Date(user.created_at).toLocaleDateString()}
                        </td>
                        <td className="px-5 py-3 text-right">
                          {!user.is_superadmin && (
                            <button
                              onClick={() => deleteUser(user.id)}
                              disabled={deleting === user.id}
                              className="text-red-400 hover:text-red-600 text-xs font-medium disabled:opacity-40"
                            >
                              {deleting === user.id ? 'Removing…' : 'Remove'}
                            </button>
                          )}
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
          <AddUserModal
            companies={companies}
            onClose={() => setShowModal(false)}
            onCreated={() => { setShowModal(false); fetchData(); }}
          />
        )}
      </Layout>
    </ProtectedRoute>
  );
}

function AddUserModal({
  companies, onClose, onCreated,
}: { companies: Company[]; onClose: () => void; onCreated: () => void }) {
  const [form, setForm] = useState({
    first_name: '', last_name: '', email: '', password: '',
    company: companies[0]?.id ? String(companies[0].id) : '',
    is_superadmin: false,
  });
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true); setError('');
    try {
      await usersAPI.create({
        ...form,
        company: form.is_superadmin ? null : Number(form.company),
      });
      onCreated();
    } catch (err: unknown) {
      const data = (err as { response?: { data?: Record<string, string[]> } })?.response?.data;
      setError(data ? Object.values(data).flat().join(' ') : 'Failed to create user.');
    } finally {
      setSaving(false);
    }
  };

  const set = (f: string, v: string | boolean) => setForm((p) => ({ ...p, [f]: v }));

  return (
    <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div className="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
          <h2 className="text-lg font-semibold text-gray-900">Add User</h2>
          <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
        </div>
        <form onSubmit={handleSubmit} className="px-6 py-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">{error}</div>}
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
              <input required value={form.first_name} onChange={(e) => set('first_name', e.target.value)} className="field-input" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
              <input required value={form.last_name} onChange={(e) => set('last_name', e.target.value)} className="field-input" />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input required type="email" value={form.email} onChange={(e) => set('email', e.target.value)} className="field-input" placeholder="user@company.com" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input required type="password" minLength={8} value={form.password} onChange={(e) => set('password', e.target.value)} className="field-input" placeholder="Min. 8 characters" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <div className="flex gap-4 mt-1">
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="radio" checked={!form.is_superadmin} onChange={() => set('is_superadmin', false)} className="accent-brand-500" />
                <span className="text-sm text-gray-700">Company Admin</span>
              </label>
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="radio" checked={form.is_superadmin} onChange={() => set('is_superadmin', true)} className="accent-brand-500" />
                <span className="text-sm text-gray-700">Super Admin</span>
              </label>
            </div>
          </div>
          {!form.is_superadmin && (
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Assign to Company *</label>
              <select required value={form.company} onChange={(e) => set('company', e.target.value)} className="field-input">
                <option value="">Select company…</option>
                {companies.map((c) => (
                  <option key={c.id} value={String(c.id)}>{c.name}</option>
                ))}
              </select>
            </div>
          )}
          <div className="flex gap-3 pt-2">
            <button type="button" onClick={onClose} className="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">Cancel</button>
            <button type="submit" disabled={saving} className="flex-1 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 disabled:opacity-60">{saving ? 'Creating…' : 'Create User'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
