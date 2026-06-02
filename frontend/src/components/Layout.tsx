import React from 'react';
import Link from 'next/link';
import { useRouter } from 'next/router';
import { useAuth } from '@/context/AuthContext';
import clsx from 'clsx';

interface NavItem { label: string; href: string; }

const adminNav: NavItem[] = [
  { label: 'Dashboard', href: '/admin' },
  { label: 'Companies', href: '/admin/companies' },
  { label: 'Users', href: '/admin/users' },
];

const companyNav: NavItem[] = [
  { label: 'Dashboard', href: '/dashboard' },
  { label: 'Values Setup', href: '/setup/values' },
  { label: 'Assessments', href: '/assessments' },
];

export default function Layout({ children }: { children: React.ReactNode }) {
  const { user, logout } = useAuth();
  const router = useRouter();
  const navItems = user?.is_superadmin ? adminNav : companyNav;

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col">
      <header className="bg-brand-500 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center gap-8">
              <Link href={user?.is_superadmin ? '/admin' : '/dashboard'}>
                <span className="text-white font-bold text-xl tracking-tight">CredibilityIQ</span>
              </Link>
              <nav className="hidden md:flex gap-1">
                {navItems.map((item) => (
                  <Link
                    key={item.href}
                    href={item.href}
                    className={clsx(
                      'px-3 py-2 rounded-md text-sm font-medium transition-colors',
                      router.pathname === item.href
                        ? 'bg-brand-700 text-white'
                        : 'text-brand-100 hover:bg-brand-600 hover:text-white'
                    )}
                  >
                    {item.label}
                  </Link>
                ))}
              </nav>
            </div>
            <div className="flex items-center gap-3">
              <span className="text-brand-100 text-sm hidden sm:block">
                {user?.full_name}
                {user?.is_superadmin && (
                  <span className="ml-2 bg-yellow-400 text-yellow-900 text-xs px-2 py-0.5 rounded-full font-semibold">
                    Super Admin
                  </span>
                )}
              </span>
              <button
                onClick={logout}
                className="text-sm text-brand-100 hover:text-white px-3 py-1.5 rounded border border-brand-500 hover:border-white transition-colors"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      <main className="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        {children}
      </main>

      <footer className="text-center text-xs text-gray-400 py-4 border-t border-gray-200">
        CredibilityIQ &copy; {new Date().getFullYear()} &mdash; Corporate Credibility Scorecard Platform
      </footer>
    </div>
  );
}
