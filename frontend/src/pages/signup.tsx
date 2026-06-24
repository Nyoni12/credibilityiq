import { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/router';
import Cookies from 'js-cookie';
import { authAPI } from '@/lib/api';

const INDUSTRIES = [
  'Financial Services', 'Technology', 'Healthcare', 'Education', 'Retail',
  'Manufacturing', 'Logistics', 'Consulting', 'Real Estate', 'Other',
];

const TIERS = [
  { value: 'starter', label: 'Starter', desc: 'Up to 6 values · 50 responses/mo', price: '$299/mo' },
  { value: 'professional', label: 'Professional', desc: 'Up to 12 values · Unlimited responses', price: '$699/mo' },
  { value: 'enterprise', label: 'Enterprise', desc: 'Custom frameworks · White-label', price: 'Custom' },
];

interface FormErrors {
  [key: string]: string;
}

export default function SignupPage() {
  const router = useRouter();
  const [step, setStep] = useState<1 | 2>(1);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<FormErrors>({});

  const [form, setForm] = useState({
    company_name: '',
    domain: '',
    industry: '',
    subscription_tier: 'professional',
    first_name: '',
    last_name: '',
    email: '',
    password: '',
  });

  const set = (field: string, value: string) => {
    setForm((f) => ({ ...f, [field]: value }));
    setErrors((e) => { const n = { ...e }; delete n[field]; return n; });
  };

  const validateStep1 = () => {
    const e: FormErrors = {};
    if (!form.company_name.trim()) e.company_name = 'Required';
    if (!form.domain.trim()) e.domain = 'Required';
    else if (!/^[a-z0-9-]+(\.[a-z]{2,})+$/.test(form.domain.trim().toLowerCase()))
      e.domain = 'Enter a valid domain e.g. acme.com';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const validateStep2 = () => {
    const e: FormErrors = {};
    if (!form.first_name.trim()) e.first_name = 'Required';
    if (!form.last_name.trim()) e.last_name = 'Required';
    if (!form.email.trim() || !form.email.includes('@')) e.email = 'Valid email required';
    if (!form.password || form.password.length < 8) e.password = 'At least 8 characters';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleNext = () => {
    if (validateStep1()) setStep(2);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateStep2()) return;
    setLoading(true);
    try {
      const res = await authAPI.signup({
        ...form,
        domain: form.domain.trim().toLowerCase(),
      });
      const { access, refresh } = res.data;
      Cookies.set('access_token', access, { expires: 1 / 96 });
      Cookies.set('refresh_token', refresh, { expires: 7 });
      router.push('/setup/values');
    } catch (err: unknown) {
      const axiosErr = err as { response?: { data?: FormErrors } };
      if (axiosErr.response?.data) {
        setErrors(axiosErr.response.data);
        // If error is on step-1 fields, go back
        const step1Fields = ['company_name', 'domain'];
        if (step1Fields.some((f) => axiosErr.response!.data![f])) setStep(1);
      } else {
        setErrors({ general: 'Something went wrong. Please try again.' });
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen gradient-bg flex items-center justify-center px-4 py-12">
      {/* Background blobs */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -left-40 w-96 h-96 bg-accent-500/20 rounded-full blur-3xl" />
        <div className="absolute -bottom-20 right-0 w-80 h-80 bg-cfa-green-500/20 rounded-full blur-3xl" />
      </div>

      <div className="relative w-full max-w-lg">
        {/* Brand */}
        <div className="text-center mb-8">
          <Link href="/">
            <div className="inline-block bg-white rounded-2xl px-6 py-3 shadow-xl cursor-pointer hover:shadow-2xl transition-shadow">
              <img src="/logo.png" alt="Credibility Factory Afrique" className="h-10 w-auto" />
            </div>
          </Link>
          <p className="text-white/60 mt-3 text-sm">Set up your company in 2 minutes</p>
        </div>

        {/* Progress */}
        <div className="flex items-center gap-3 mb-6 px-2">
          {[1, 2].map((s) => (
            <div key={s} className="flex-1 flex flex-col items-center gap-1">
              <div className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all
                ${step >= s ? 'bg-white text-brand-600' : 'bg-white/20 text-white/50'}`}>
                {s}
              </div>
              <span className={`text-xs ${step >= s ? 'text-white' : 'text-white/40'}`}>
                {s === 1 ? 'Company' : 'Your Account'}
              </span>
            </div>
          ))}
          <div className={`flex-1 h-px transition-all ${step >= 2 ? 'bg-white/60' : 'bg-white/20'}`} style={{ flexGrow: 2, marginTop: '-16px' }} />
        </div>

        <form onSubmit={handleSubmit} className="bg-white rounded-3xl shadow-2xl overflow-hidden">
          {errors.general && (
            <div className="bg-red-50 border-b border-red-100 px-6 py-3 text-sm text-red-600">{errors.general}</div>
          )}

          {/* ── Step 1: Company ─────────────────────────── */}
          {step === 1 && (
            <div className="p-8 space-y-5">
              <div>
                <h2 className="text-xl font-bold text-gray-900">Your Company</h2>
                <p className="text-sm text-gray-400 mt-1">Tell us about the organisation you're setting up.</p>
              </div>

              <Field label="Company Name" error={errors.company_name} required>
                <input
                  type="text"
                  placeholder="Acme Corporation"
                  value={form.company_name}
                  onChange={(e) => set('company_name', e.target.value)}
                  className={inputClass(errors.company_name)}
                />
              </Field>

              <Field label="Company Domain" error={errors.domain} required
                hint="Your organisation's website domain, e.g. acme.com">
                <div className="relative">
                  <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                  <input
                    type="text"
                    placeholder="acme.com"
                    value={form.domain}
                    onChange={(e) => set('domain', e.target.value.toLowerCase().replace(/^https?:\/\/|^www\./g, ''))}
                    className={`${inputClass(errors.domain)} pl-7`}
                  />
                </div>
              </Field>

              <div className="grid grid-cols-2 gap-4">
                <Field label="Industry" error={errors.industry}>
                  <select value={form.industry} onChange={(e) => set('industry', e.target.value)} className={inputClass()}>
                    <option value="">Select…</option>
                    {INDUSTRIES.map((i) => <option key={i}>{i}</option>)}
                  </select>
                </Field>
              </div>

              <div>
                <p className="text-sm font-medium text-gray-700 mb-3">Plan</p>
                <div className="space-y-2">
                  {TIERS.map((t) => (
                    <label key={t.value} className={`flex items-center gap-4 p-3 rounded-xl border-2 cursor-pointer transition-all
                      ${form.subscription_tier === t.value
                        ? 'border-brand-500 bg-brand-50'
                        : 'border-gray-100 hover:border-gray-200'}`}>
                      <input type="radio" name="tier" value={t.value} checked={form.subscription_tier === t.value}
                        onChange={() => set('subscription_tier', t.value)} className="accent-brand-500" />
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-semibold text-gray-900">{t.label}</p>
                        <p className="text-xs text-gray-400">{t.desc}</p>
                      </div>
                      <span className="text-sm font-bold text-brand-600 whitespace-nowrap">{t.price}</span>
                    </label>
                  ))}
                </div>
              </div>

              <button type="button" onClick={handleNext}
                className="w-full py-3 bg-brand-500 text-white rounded-xl font-bold text-sm hover:bg-brand-600 transition-colors">
                Continue →
              </button>

              <p className="text-center text-sm text-gray-400">
                Already have an account?{' '}
                <Link href="/login"><span className="text-brand-600 font-medium cursor-pointer hover:underline">Sign in</span></Link>
              </p>
            </div>
          )}

          {/* ── Step 2: Admin account ───────────────────── */}
          {step === 2 && (
            <div className="p-8 space-y-5">
              <div>
                <button type="button" onClick={() => setStep(1)}
                  className="text-gray-400 hover:text-gray-600 text-sm mb-3 flex items-center gap-1">
                  ← Back
                </button>
                <h2 className="text-xl font-bold text-gray-900">Your Admin Account</h2>
                <p className="text-sm text-gray-400 mt-1">
                  This account will manage <span className="font-medium text-gray-700">{form.company_name}</span>.
                </p>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <Field label="First Name" error={errors.first_name} required>
                  <input type="text" placeholder="Jane" value={form.first_name}
                    onChange={(e) => set('first_name', e.target.value)} className={inputClass(errors.first_name)} />
                </Field>
                <Field label="Last Name" error={errors.last_name} required>
                  <input type="text" placeholder="Smith" value={form.last_name}
                    onChange={(e) => set('last_name', e.target.value)} className={inputClass(errors.last_name)} />
                </Field>
              </div>

              <Field label="Work Email" error={errors.email} required
                hint={form.domain ? `Recommended: yourname@${form.domain}` : undefined}>
                <input type="email" placeholder={form.domain ? `you@${form.domain}` : 'you@company.com'}
                  value={form.email} onChange={(e) => set('email', e.target.value)}
                  className={inputClass(errors.email)} />
              </Field>

              <Field label="Password" error={errors.password} required hint="Minimum 8 characters">
                <input type="password" placeholder="••••••••" value={form.password}
                  onChange={(e) => set('password', e.target.value)} className={inputClass(errors.password)} />
              </Field>

              <button type="submit" disabled={loading}
                className="w-full py-3 bg-brand-500 text-white rounded-xl font-bold text-sm hover:bg-brand-600 disabled:opacity-60 transition-colors flex items-center justify-center gap-2">
                {loading
                  ? <><span className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" /> Creating account…</>
                  : 'Create Account →'}
              </button>

              <p className="text-xs text-center text-gray-400">
                By signing up you agree to our{' '}
                <a href="#" className="underline">Terms of Service</a> and{' '}
                <a href="#" className="underline">Privacy Policy</a>.
              </p>
            </div>
          )}
        </form>
      </div>
    </div>
  );
}

function Field({ label, error, hint, required, children }: {
  label: string; error?: string; hint?: string; required?: boolean; children: React.ReactNode;
}) {
  return (
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">
        {label}{required && <span className="text-brand-500 ml-0.5">*</span>}
      </label>
      {children}
      {hint && !error && <p className="text-xs text-gray-400 mt-1">{hint}</p>}
      {error && <p className="text-xs text-red-500 mt-1">{error}</p>}
    </div>
  );
}

function inputClass(error?: string) {
  return `w-full px-3 py-2.5 rounded-xl border text-sm outline-none transition-all
    ${error
      ? 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-100'
      : 'border-gray-200 bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100'}`;
}
