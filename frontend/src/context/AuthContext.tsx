import React, { createContext, useContext, useEffect, useState, useCallback } from 'react';
import Cookies from 'js-cookie';
import { authAPI } from '@/lib/api';
import { User } from '@/types';
import { useRouter } from 'next/router';

interface AuthContextValue {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue>({
  user: null,
  loading: true,
  login: async () => {},
  logout: async () => {},
});

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  const loadUser = useCallback(async () => {
    const token = Cookies.get('access_token');
    if (!token) { setLoading(false); return; }
    try {
      const { data } = await authAPI.profile();
      setUser(data);
    } catch {
      Cookies.remove('access_token');
      Cookies.remove('refresh_token');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { loadUser(); }, [loadUser]);

  const login = async (email: string, password: string) => {
    const { data } = await authAPI.login(email, password);
    Cookies.set('access_token', data.access, { expires: 1 / 96 });
    Cookies.set('refresh_token', data.refresh, { expires: 7 });
    setUser(data.user);
    if (data.user.is_superadmin) {
      router.push('/admin');
    } else {
      router.push('/dashboard');
    }
  };

  const logout = async () => {
    try {
      const refresh = Cookies.get('refresh_token');
      if (refresh) await authAPI.logout(refresh);
    } catch {}
    Cookies.remove('access_token');
    Cookies.remove('refresh_token');
    setUser(null);
    router.push('/login');
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(AuthContext);
}
