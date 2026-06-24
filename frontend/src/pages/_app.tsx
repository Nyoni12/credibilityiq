import type { AppProps } from 'next/app';
import Head from 'next/head';
import { AuthProvider } from '@/context/AuthContext';
import '@/styles/globals.css';

export default function App({ Component, pageProps }: AppProps) {
  return (
    <AuthProvider>
      <Head>
        <title>Credibility Factory Afrique</title>
        <meta name="description" content="Corporate Credibility Scorecard Platform — turn company values into measurable financial insights." />
        <link rel="icon" href="/logo.png" />
      </Head>
      <Component {...pageProps} />
    </AuthProvider>
  );
}
