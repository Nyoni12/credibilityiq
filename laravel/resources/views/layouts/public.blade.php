<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#1F2192">
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

<title>@yield('title', "Credibility Factory Afrique | Africa's Authority on Credibility")</title>

@yield('meta')

<link rel="preload" as="image" href="{{ asset('images/logo.png') }}">
@yield('preload')

@yield('schema')

<style>
:root {
  --brand-50:   #EEEFFE;
  --brand-100:  #C8CCF5;
  --brand-200:  #9DA5EC;
  --brand-300:  #717FE3;
  --brand-400:  #4659DA;
  --brand:      #1F2192;
  --brand-600:  #191B7A;
  --brand-700:  #131562;
  --brand-800:  #0D0E4A;
  --brand-900:  #070831;
  --accent-100: rgba(0,166,81,0.10);
  --accent:     #00A651;
  --accent-600: #00833F;
  --accent-700: #006632;
  --cfa:        #00A651;
  --grad:       #1F2192;
  --off-white:  #F7F5F0;
  --surface:    #FFFFFF;
  --text:       #0C0F3A;
  --text-muted: #4A5080;
  --border:     #DDE0F5;
  --shadow:     0 4px 24px rgba(31,33,146,0.10);
}
@media (prefers-color-scheme: dark) { :root {
  --off-white:#06082C; --text:#E8EAFF; --text-muted:#8B92CC;
  --surface:#0F1240; --border:#1E2269; --shadow:0 4px 24px rgba(0,0,0,0.4);
  --accent-100:rgba(0,166,81,0.15);
}}
:root[data-theme="light"] { --off-white:#F7F5F0; --text:#0C0F3A; --text-muted:#4A5080; --surface:#FFFFFF; --border:#DDE0F5; --accent-100:rgba(0,166,81,0.10); }
:root[data-theme="dark"]  { --off-white:#06082C; --text:#E8EAFF; --text-muted:#8B92CC; --surface:#0F1240; --border:#1E2269; --accent-100:rgba(0,166,81,0.15); }

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:"Segoe UI",system-ui,-apple-system,sans-serif;background:var(--off-white);color:var(--text);line-height:1.65;overflow-x:hidden}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
button{cursor:pointer;border:none;background:none;font:inherit}
svg{display:block;flex-shrink:0}

.display{font-family:Cambria,"Times New Roman",Georgia,serif;font-weight:700;line-height:1.08;letter-spacing:-0.02em;text-wrap:balance}
h1.display{font-size:clamp(2.8rem,6vw,5.5rem)}
h2.display{font-size:clamp(2rem,4vw,3.2rem)}
.eyebrow{font-size:0.72rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:var(--accent)}
.lead{font-size:1.15rem;line-height:1.75;color:var(--text-muted)}
.gt{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.container{max-width:1180px;margin:0 auto;padding:0 24px}
.section{padding:100px 0}

/* NAV */
nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(7,8,49,0.97);backdrop-filter:blur(12px);border-bottom:1px solid rgba(255,255,255,0.07);transition:box-shadow 0.3s}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:68px;max-width:1180px;margin:0 auto;padding:0 24px}
.nav-logo img{height:38px;width:auto}
.nav-links{display:flex;align-items:center;gap:4px;list-style:none}
.nav-links a{color:rgba(255,255,255,0.72);font-size:0.85rem;font-weight:500;padding:6px 14px;border-radius:6px;transition:color 0.2s,background 0.2s}
.nav-links a:hover{color:white;background:rgba(255,255,255,0.09)}
.nav-cta{background:var(--accent)!important;color:white!important;padding:8px 20px!important;border-radius:8px!important;font-weight:600!important}
.nav-cta:hover{background:var(--accent-600)!important}
.nav-ciq{border:1px solid rgba(163,41,204,0.5)!important;color:rgba(200,153,230,0.9)!important}
.nav-ciq:hover{background:rgba(163,41,204,0.15)!important;border-color:var(--accent)!important;color:white!important}
.nav-hamburger{display:none;flex-direction:column;gap:5px;padding:8px}
.nav-hamburger span{display:block;width:22px;height:2px;background:white;border-radius:2px}
.mobile-menu{display:none;flex-direction:column;background:#0B0D3E;padding:16px 24px;border-top:1px solid rgba(255,255,255,0.07)}
.mobile-menu a{color:rgba(255,255,255,0.8);padding:12px 0;font-size:0.95rem;display:block;border-bottom:1px solid rgba(255,255,255,0.05)}
.mobile-menu.open{display:flex}

/* HERO */
.hero{min-height:100vh;background:var(--brand-900) url("{{ asset('images/hero-bg.jpg') }}") center/cover no-repeat;display:flex;align-items:center;position:relative;overflow:hidden;padding-top:68px}
.hero::before{content:'';position:absolute;inset:0;background:rgba(7,8,49,0.72);z-index:1}
.hero-wm{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;user-select:none;font-family:Cambria,Georgia,serif;font-size:clamp(8rem,22vw,20rem);font-weight:700;letter-spacing:-0.04em;color:rgba(255,255,255,0.025);white-space:nowrap;z-index:2}
.hero-orb{position:absolute;right:-80px;top:50%;transform:translateY(-50%);width:min(560px,55vw);height:min(560px,55vw);border-radius:50%;background:radial-gradient(circle at 60% 60%,rgba(31,33,146,0.25),transparent 70%);border:1px solid rgba(31,33,146,0.20);z-index:2}
.hero-inner{position:relative;z-index:2;max-width:1180px;margin:0 auto;padding:80px 24px;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center}
.hero-content{color:white}
.hero-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:0.72rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:var(--brand-300);margin-bottom:28px}
.hero-eyebrow::before{content:'';display:block;width:32px;height:2px;background:var(--brand-300)}
.hero h1{color:white;margin-bottom:28px}
.hero-lead{font-size:1.1rem;color:rgba(255,255,255,0.62);line-height:1.8;margin-bottom:44px;max-width:480px}
.hero-actions{display:flex;gap:14px;flex-wrap:wrap}
.btn-primary{display:inline-flex;align-items:center;gap:8px;background:var(--brand);color:white;font-weight:600;font-size:0.95rem;padding:14px 28px;border-radius:6px;transition:opacity 0.2s,transform 0.2s;box-shadow:0 4px 20px rgba(31,33,146,0.28)}
.btn-primary:hover{opacity:0.9;transform:translateY(-1px)}
.btn-outline{display:inline-flex;align-items:center;gap:8px;border:1.5px solid rgba(255,255,255,0.3);color:white;font-weight:600;font-size:0.95rem;padding:14px 28px;border-radius:6px;transition:border-color 0.2s,background 0.2s}
.btn-outline:hover{border-color:white;background:rgba(255,255,255,0.08)}
.hero-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
.stat-card{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);border-radius:16px;padding:28px 24px}
.stat-num{font-family:Cambria,Georgia,serif;font-size:2.8rem;font-weight:700;color:white;line-height:1;margin-bottom:6px}
.stat-num span{background:var(--grad);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.stat-label{font-size:0.82rem;color:rgba(255,255,255,0.48);line-height:1.4}

/* TICKER */
.about-strip{background:var(--brand);padding:22px 0;overflow:hidden}
.ticker-track{display:flex;gap:40px;white-space:nowrap;animation:ticker 32s linear infinite}
.ticker-item{display:flex;align-items:center;gap:12px;font-size:0.78rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:white;flex-shrink:0}
.ticker-sep{width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,0.35);flex-shrink:0}
@keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@keyframes slide-clients{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ABOUT */
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center}
.about-quote{position:relative;padding-left:28px;border-left:3px solid var(--accent);margin:32px 0}
.about-quote blockquote{font-family:Cambria,Georgia,serif;font-size:1.2rem;font-style:italic;color:var(--text);line-height:1.7}
.about-pillars{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:36px}
.pillar{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:13px 10px;font-size:0.8rem;font-weight:600;color:var(--text-muted);text-align:center;transition:border-color 0.2s,color 0.2s}
.pillar:hover{border-color:var(--accent);color:var(--accent)}
.about-card{background:var(--brand-900);border-radius:10px;padding:44px 36px;color:white;position:relative;overflow:hidden}
.about-card-wm{position:absolute;bottom:-24px;right:-16px;font-family:Cambria,Georgia,serif;font-size:9rem;font-weight:700;color:rgba(255,255,255,0.04);line-height:1;pointer-events:none;user-select:none}
.about-card-eyebrow{color:var(--brand-300);font-size:0.72rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;margin-bottom:16px}
.about-card h3{font-family:Cambria,Georgia,serif;font-size:1.45rem;font-weight:700;margin-bottom:20px;line-height:1.3}
.about-card p{color:rgba(255,255,255,0.62);font-size:0.93rem;line-height:1.75;margin-bottom:24px}
.about-tags{display:flex;flex-wrap:wrap;gap:8px}
.tag{background:rgba(163,41,204,0.2);border:1px solid rgba(163,41,204,0.3);color:rgba(200,153,230,0.9);font-size:0.75rem;font-weight:600;padding:5px 12px;border-radius:20px}
.badge-row{display:flex;gap:14px;margin-top:36px}
.badge{flex:1;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px;display:flex;align-items:center;gap:12px}
.badge-mark{width:40px;height:40px;border-radius:10px;background:var(--accent-100);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.badge-text strong{display:block;font-size:0.83rem;font-weight:700;color:var(--text)}
.badge-text span{font-size:0.73rem;color:var(--text-muted)}

/* BUILDING BLOCKS */
.blocks-section{background:var(--brand-900);position:relative;overflow:hidden}
.blocks-wm{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-family:Cambria,Georgia,serif;font-size:clamp(6rem,18vw,15rem);font-weight:700;color:rgba(255,255,255,0.025);white-space:nowrap;pointer-events:none;user-select:none;letter-spacing:-0.03em}
.blocks-header{text-align:center;color:white;margin-bottom:56px;position:relative;z-index:2}
.blocks-header .eyebrow{color:rgba(163,41,204,0.85)}
.blocks-header h2{color:white;margin-top:12px}
.blocks-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;position:relative;z-index:2}
.block-item{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.07);border-radius:14px;padding:22px 18px;display:flex;align-items:flex-start;gap:14px;transition:background 0.2s,border-color 0.2s,transform 0.2s}
.block-item:hover{background:rgba(163,41,204,0.14);border-color:rgba(163,41,204,0.38);transform:translateY(-2px)}
.block-num{font-size:0.68rem;font-weight:700;color:var(--brand-300);min-width:20px;padding-top:2px;font-variant-numeric:tabular-nums}
.block-name{font-size:0.9rem;font-weight:600;color:rgba(255,255,255,0.83);line-height:1.4}

/* FRAMEWORKS */
.frameworks-section{background:var(--off-white)}
.fw-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:48px}
.fw-card{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:36px 30px;position:relative;overflow:hidden;transition:box-shadow 0.25s,transform 0.25s}
.fw-card:hover{box-shadow:var(--shadow);transform:translateY(-4px)}
.fw-card::before{content:'';position:absolute;top:0;left:0;bottom:0;right:auto;width:4px;height:auto;background:var(--accent)}
.fw-mark{width:48px;height:48px;border-radius:12px;background:var(--accent-100);display:flex;align-items:center;justify-content:center;margin-bottom:20px}
.fw-card h3{font-family:Cambria,Georgia,serif;font-size:1.1rem;font-weight:700;margin-bottom:12px;color:var(--text)}
.fw-card p{font-size:0.88rem;color:var(--text-muted);line-height:1.7}
.fw-label{display:inline-flex;margin-top:18px;font-size:0.72rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--accent)}

/* SERVICES */
.services-section{background:var(--surface)}
.services-intro{max-width:640px;margin-bottom:56px}
.services-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
.svc-card{border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--off-white);transition:box-shadow 0.25s,transform 0.25s}
.svc-card:hover{box-shadow:var(--shadow);transform:translateY(-3px)}
.svc-header{padding:28px 28px 20px;display:flex;align-items:flex-start;gap:18px}
.svc-badge{width:52px;height:52px;border-radius:6px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:var(--brand);font-family:Cambria,Georgia,serif;font-size:1.05rem;font-weight:700;color:white}
.svc-num{font-size:0.68rem;font-weight:700;color:var(--accent);letter-spacing:0.1em;margin-bottom:4px}
.svc-title{font-family:Cambria,Georgia,serif;font-size:1.1rem;font-weight:700;color:var(--text);line-height:1.3}
.svc-body{padding:0 28px 28px}
.svc-desc{font-size:0.9rem;color:var(--text-muted);line-height:1.75;margin-bottom:20px}
.svc-outcomes{list-style:none;display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
.svc-outcomes li{display:flex;align-items:flex-start;gap:10px;font-size:0.85rem;color:var(--text-muted)}
.svc-outcomes li::before{content:'';display:block;width:6px;height:6px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:6px}
.svc-for{display:inline-flex;align-items:center;background:var(--accent-100);color:var(--accent-700);font-size:0.75rem;font-weight:600;padding:5px 12px;border-radius:20px}

/* CREDIBILITYIQ */
.ciq-section{background:var(--brand-900);color:white;position:relative;overflow:hidden}
.ciq-inner{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;position:relative;z-index:2}
.ciq-eyebrow{color:var(--brand-300);font-size:0.72rem;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;margin-bottom:16px}
.ciq-inner h2{color:white;margin-bottom:20px}
.ciq-lead{color:rgba(255,255,255,0.58);font-size:1.05rem;line-height:1.75;margin-bottom:32px}
.ciq-features{display:flex;flex-direction:column;gap:14px;margin-bottom:32px}
.ciq-feat{display:flex;align-items:flex-start;gap:16px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:14px;padding:16px 20px}
.ciq-feat-num{width:32px;height:32px;border-radius:8px;flex-shrink:0;background:rgba(163,41,204,0.25);border:1px solid rgba(163,41,204,0.35);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:rgba(200,153,230,0.9)}
.ciq-feat-title{font-size:0.9rem;font-weight:700;color:white;margin-bottom:3px}
.ciq-feat-desc{font-size:0.82rem;color:rgba(255,255,255,0.48);line-height:1.5}
.ciq-bands{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.band-card{border-radius:14px;padding:20px;border:1px solid rgba(255,255,255,0.1)}
.band-label{font-size:0.68rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:4px}
.band-name{font-family:Cambria,Georgia,serif;font-size:1.05rem;font-weight:700;margin-bottom:4px}
.band-range{font-size:0.78rem;color:rgba(255,255,255,0.38)}
.ciq-caps{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:22px;margin-top:14px}
.ciq-cap-row{display:flex;justify-content:space-between;align-items:center;font-size:0.85rem;padding:7px 0;border-bottom:1px solid rgba(255,255,255,0.06)}
.ciq-cap-row:last-child{border-bottom:none}
.ciq-cap-row span:first-child{color:rgba(255,255,255,0.55)}
.ciq-cap-row span:last-child{color:#34D399;font-weight:600}

/* CLIENTS */
.clients-section{background:var(--off-white)}
.clients-track-outer{overflow-x:scroll;overflow-y:hidden;scrollbar-width:none;-ms-overflow-style:none;cursor:grab;margin-top:48px;padding:4px 0;-webkit-mask-image:linear-gradient(to right,transparent,black 80px,black calc(100% - 80px),transparent);mask-image:linear-gradient(to right,transparent,black 80px,black calc(100% - 80px),transparent)}
.clients-track-outer::-webkit-scrollbar{display:none}
.clients-track-outer:active{cursor:grabbing}
.clients-track{display:flex;gap:20px;width:max-content;padding:10px 0}
.clients-track .client-card{opacity:1!important}
.client-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:24px 20px;display:flex;flex-direction:column;gap:14px;transition:box-shadow 0.35s,transform 0.35s;flex-shrink:0;width:260px}
.client-card:hover{box-shadow:0 12px 40px rgba(31,33,146,0.18);transform:translateY(-5px) scale(1.02)}
.client-logo img{transition:transform 0.4s cubic-bezier(0.34,1.56,0.64,1)}
.client-card:hover .client-logo img{transform:scale(1.12)}
.client-card:hover{box-shadow:var(--shadow);transform:translateY(-2px)}
.client-logo{height:60px;display:flex;align-items:center;justify-content:center}
.client-logo picture{display:contents}
.client-logo svg,.client-logo img{height:100%;width:auto;max-width:100%;object-fit:contain;mix-blend-mode:multiply}
.client-name{font-size:0.88rem;font-weight:700;color:var(--text);line-height:1.3}
.client-year{font-size:0.75rem;color:var(--accent);font-weight:600}
.client-tag{font-size:0.73rem;color:var(--text-muted)}
.clients-note{margin-top:32px;padding:20px 24px;background:rgba(163,41,204,0.07);border:1px solid rgba(163,41,204,0.18);border-radius:14px;display:flex;align-items:flex-start;gap:16px}
.clients-note-mark{width:36px;height:36px;border-radius:8px;background:var(--accent);flex-shrink:0;display:flex;align-items:center;justify-content:center}
.clients-note p{font-size:0.88rem;color:var(--text-muted);line-height:1.6}
.clients-note strong{color:var(--text)}

/* TEAM */
.team-section{background:var(--surface)}
.team-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:48px}
.team-card{background:var(--off-white);border:1px solid var(--border);border-radius:8px;padding:32px 28px;display:flex;gap:22px;align-items:flex-start;transition:box-shadow 0.2s,transform 0.2s}
.team-card:hover{box-shadow:var(--shadow);transform:translateY(-2px)}
.team-avatar{width:64px;height:64px;border-radius:6px;flex-shrink:0;background:var(--brand);display:flex;align-items:center;justify-content:center;font-family:Cambria,Georgia,serif;font-size:1.55rem;font-weight:700;color:white;letter-spacing:-0.02em}
.team-name{font-family:Cambria,Georgia,serif;font-size:1.05rem;font-weight:700;color:var(--text);margin-bottom:2px}
.team-role{font-size:0.78rem;font-weight:700;color:var(--accent);letter-spacing:0.08em;text-transform:uppercase;margin-bottom:10px}
.team-bio{font-size:0.85rem;color:var(--text-muted);line-height:1.65;margin-bottom:12px}
.team-creds{display:flex;flex-wrap:wrap;gap:6px}
.cred-tag{font-size:0.72rem;font-weight:600;background:var(--border);color:var(--text-muted);padding:3px 10px;border-radius:10px}

/* CTA */
.cta-section{background:linear-gradient(150deg,#04051e 0%,#0d0f52 55%,#04051e 100%);color:white;text-align:center;padding:100px 0;position:relative;overflow:hidden}
.cta-section::before{content:'';position:absolute;inset:0;background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");pointer-events:none}
.cta-section h2{color:white;max-width:700px;margin:0 auto 20px;position:relative;z-index:1}
.cta-section p{color:rgba(255,255,255,0.7);max-width:500px;margin:0 auto 40px;position:relative;z-index:1}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1}
.btn-white{background:white;color:var(--brand);font-weight:700;font-size:0.95rem;padding:14px 30px;border-radius:6px;display:inline-flex;align-items:center;gap:8px;transition:transform 0.2s,box-shadow 0.2s}
.btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,0.2)}
.btn-ghost-white{border:2px solid rgba(255,255,255,0.4);color:white;font-weight:600;font-size:0.95rem;padding:14px 30px;border-radius:6px;display:inline-flex;align-items:center;gap:8px;transition:border-color 0.2s,background 0.2s}
.btn-ghost-white:hover{border-color:white;background:rgba(255,255,255,0.1)}

/* CONTACT */
.contact-section{background:var(--brand-900);color:white}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px}
.contact-left h2{color:white;margin-bottom:20px}
.contact-left > p{color:rgba(255,255,255,0.58);margin-bottom:36px}
.contact-items{display:flex;flex-direction:column;gap:20px}
.contact-item{display:flex;gap:16px;align-items:flex-start}
.contact-icon{width:42px;height:42px;border-radius:11px;flex-shrink:0;background:rgba(163,41,204,0.2);border:1px solid rgba(163,41,204,0.25);display:flex;align-items:center;justify-content:center}
.contact-detail strong{display:block;font-size:0.73rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.35);margin-bottom:4px}
.contact-detail a,.contact-detail span{color:rgba(255,255,255,0.82);font-size:0.93rem;display:block}
.contact-detail a:hover{color:var(--brand-300)}
.contact-form{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:36px 32px}
.contact-form h3{color:white;font-family:Cambria,Georgia,serif;font-size:1.2rem;margin-bottom:24px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:0.73rem;font-weight:600;color:rgba(255,255,255,0.45);margin-bottom:6px;letter-spacing:0.05em}
.form-group input,.form-group select,.form-group textarea{width:100%;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.14);border-radius:5px;padding:12px 16px;color:white;font:inherit;font-size:0.9rem;transition:border-color 0.2s;outline:none}
.form-group input::placeholder,.form-group textarea::placeholder{color:rgba(255,255,255,0.28)}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--accent)}
.form-group select option{background:#0B0D3E}
.form-group textarea{resize:vertical;min-height:100px}
.form-submit{width:100%;background:var(--brand);color:white;font-weight:700;font-size:0.95rem;padding:14px;border-radius:6px;transition:opacity 0.2s;box-shadow:0 4px 16px rgba(31,33,146,0.25)}
.form-submit:hover{opacity:0.88}

/* FOOTER */
footer{background:#05061F;border-top:1px solid rgba(255,255,255,0.06);padding:40px 0;color:rgba(255,255,255,0.38)}
.footer-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px}
.footer-logo img{height:30px;width:auto;filter:brightness(0) invert(1);opacity:0.55}
.footer-links{display:flex;gap:24px;flex-wrap:wrap}
.footer-links a{font-size:0.82rem;color:rgba(255,255,255,0.38);transition:color 0.2s}
.footer-links a:hover{color:white}
.footer-copy{font-size:0.78rem;text-align:right;line-height:1.8}

/* PAGE HERO (inner pages) */
.page-hero{background:var(--brand-900) center/cover no-repeat;color:white;padding:140px 0 90px;text-align:left;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background:rgba(7,8,49,0.72);pointer-events:none}
.page-hero .eyebrow{color:var(--brand-300)}
.page-hero h1{color:white;max-width:700px;margin:16px 0 0}
.page-hero-lead{color:rgba(255,255,255,0.62);font-size:1.1rem;max-width:580px;margin:20px 0 0;line-height:1.75}

/* ── SCROLL REVEALS ── */
.reveal{opacity:0;transform:translateY(32px);transition:opacity .7s ease,transform .7s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:none}
.reveal-delay-1{transition-delay:.12s}
.reveal-delay-2{transition-delay:.24s}
.reveal-delay-3{transition-delay:.36s}
.reveal-left {opacity:0;transform:translateX(-40px);transition:opacity .75s ease,transform .75s cubic-bezier(.22,1,.36,1)}
.reveal-right{opacity:0;transform:translateX(40px); transition:opacity .75s ease,transform .75s cubic-bezier(.22,1,.36,1)}
.reveal-left.visible,.reveal-right.visible{opacity:1;transform:none}

/* ── ANIMATION KEYFRAMES ── */
@keyframes fadeUp   {from{opacity:0;transform:translateY(38px)}  to{opacity:1;transform:none}}
@keyframes fadeLeft {from{opacity:0;transform:translateX(-32px)} to{opacity:1;transform:none}}
@keyframes pulse-orb{0%,100%{transform:translateY(-50%) scale(1);opacity:.6} 50%{transform:translateY(-50%) scale(1.1);opacity:.95}}
@keyframes wm-drift {0%,100%{letter-spacing:-0.04em;opacity:.025} 50%{letter-spacing:-0.025em;opacity:.042}}
@keyframes float-orb{0%,100%{transform:translateY(0)} 50%{transform:translateY(-22px)}}
@keyframes shimmer-sweep{0%{left:-70%} 100%{left:130%}}

/* ── HERO ENTRANCE (CSS, no JS required) ── */
.hero-eyebrow{opacity:0;animation:fadeLeft .7s cubic-bezier(.22,1,.36,1) .1s forwards}
.hero h1     {opacity:0;animation:fadeUp  .85s cubic-bezier(.22,1,.36,1) .35s forwards}
.hero-lead   {opacity:0;animation:fadeUp  .7s  cubic-bezier(.22,1,.36,1) .65s forwards}
.hero-actions{opacity:0;animation:fadeUp  .65s cubic-bezier(.22,1,.36,1) .88s forwards}

/* ── ORB PULSE ── */
.hero-orb{animation:pulse-orb 7s ease-in-out infinite;will-change:transform}

/* ── WATERMARK DRIFT ── */
.hero-wm{animation:wm-drift 14s ease-in-out infinite}
.blocks-wm{animation:wm-drift 18s ease-in-out infinite;animation-delay:3s}

/* ── BUTTON SHIMMER ── */
.btn-primary,.btn-white,.form-submit{position:relative;overflow:hidden}
.btn-primary::after,.btn-white::after,.form-submit::after{
  content:'';position:absolute;top:0;left:-70%;width:50%;height:100%;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
  pointer-events:none;opacity:0
}
.btn-primary:hover::after,.btn-white:hover::after,.form-submit:hover::after{
  opacity:1;animation:shimmer-sweep .55s ease forwards
}

/* ── BUILDING BLOCKS — column stagger ── */
.blocks-grid .block-item:nth-child(4n+2){transition-delay:.07s}
.blocks-grid .block-item:nth-child(4n+3){transition-delay:.14s}
.blocks-grid .block-item:nth-child(4n+4){transition-delay:.21s}

/* ── STAT CARDS — staged entrance via JS ── */
.hero-stats .stat-card{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s cubic-bezier(.22,1,.36,1)}
.hero-stats .stat-card.in{opacity:1;transform:none}

/* ── NAV ACTIVE SECTION ── */
.nav-links a.is-active{color:white!important;background:rgba(255,255,255,.12)!important}

/* ── CTA FLOATING ORBS ── */
.cta-orb{position:absolute;border-radius:50%;pointer-events:none;z-index:0;filter:blur(48px);animation:float-orb 9s ease-in-out infinite}
.cta-orb-1{width:340px;height:340px;bottom:-90px;left:-60px;background:radial-gradient(circle,rgba(163,41,204,.22),transparent 70%);animation-delay:0s}
.cta-orb-2{width:260px;height:260px;top:-40px;right:10px;background:radial-gradient(circle,rgba(70,89,218,.22),transparent 70%);animation-delay:4.5s}

/* ── FRAMEWORK CARD GLOW ── */
.fw-card::before{transition:box-shadow .3s}
.fw-card:hover::before{box-shadow:0 0 18px rgba(163,41,204,.45)}

/* Remove decorative orbs, watermarks, and ghost elements */
.hero-orb,.hero-wm,.blocks-wm,.about-card-wm,.cta-orb{display:none!important}

@media(prefers-reduced-motion:reduce){
  .reveal,.reveal-left,.reveal-right{opacity:1;transform:none;transition:none}
  .hero-eyebrow,.hero h1,.hero-lead,.hero-actions{opacity:1;animation:none}
  .hero-stats .stat-card{opacity:1;transform:none}
  .btn-primary::after,.btn-white::after,.form-submit::after{display:none}
}

@media(max-width:900px){
  .hero-inner,.about-grid,.ciq-inner,.contact-grid{grid-template-columns:1fr;gap:48px}
  .fw-grid{grid-template-columns:1fr 1fr}
  .services-grid,.team-grid{grid-template-columns:1fr}
  /* clients carousel — no grid override needed */
  .blocks-grid{grid-template-columns:repeat(2,1fr)}
  .hero-stats{grid-template-columns:repeat(2,1fr)}
  .nav-links{display:none}
  .nav-hamburger{display:flex}
}
@media(max-width:560px){
  .section{padding:72px 0}
  .fw-grid{grid-template-columns:1fr}
  .about-pillars{grid-template-columns:repeat(2,1fr)}
  .ciq-bands{grid-template-columns:1fr}
  .hero-actions,.cta-btns{flex-direction:column;align-items:flex-start}
  .cta-btns{align-items:center}
  .blocks-grid{grid-template-columns:1fr 1fr}
  .badge-row{flex-direction:column}
  .footer-inner{flex-direction:column;text-align:center}
  .footer-copy,.footer-links{text-align:center;justify-content:center}
}
</style>
</head>
<body>

<!-- NAV -->
<nav id="main-nav">
  <div class="nav-inner">
    <a href="{{ route('home') }}" class="nav-logo"><img src="{{ asset('images/logo.png') }}" alt="Credibility Factory Afrique" fetchpriority="high"></a>
    <ul class="nav-links">
      <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">About</a></li>
      <li><a href="{{ route('services') }}" class="{{ request()->routeIs('services') ? 'is-active' : '' }}">Services</a></li>
      <li><a href="{{ route('frameworks') }}" class="{{ request()->routeIs('frameworks') ? 'is-active' : '' }}">Frameworks</a></li>
      <li><a href="{{ route('platform') }}" class="nav-ciq {{ request()->routeIs('platform') ? 'is-active' : '' }}">CredibilityIQ</a></li>
      <li><a href="{{ route('team') }}" class="{{ request()->routeIs('team') ? 'is-active' : '' }}">Team</a></li>
<li><a href="{{ route('contact') }}" class="nav-cta">Get in Touch</a></li>
    </ul>
    <button class="nav-hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
  <div class="mobile-menu" id="mobile-menu">
    <a href="{{ route('about') }}">About</a>
    <a href="{{ route('services') }}">Services</a>
    <a href="{{ route('frameworks') }}">Frameworks</a>
    <a href="{{ route('platform') }}">CredibilityIQ Platform</a>
    <a href="{{ route('team') }}">Our Team</a>
<a href="{{ route('contact') }}">Get in Touch</a>
  </div>
</nav>

@yield('content')

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-inner">
      <div class="footer-logo"><img src="{{ asset('images/logo.png') }}" alt="Credibility Factory Afrique" loading="lazy"></div>
      <div class="footer-links">
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('services') }}">Services</a>
        <a href="{{ route('frameworks') }}">Frameworks</a>
        <a href="{{ route('login') }}">CredibilityIQ</a>
        <a href="{{ route('team') }}">Team</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
      </div>
      <div class="footer-copy">&copy; {{ date('Y') }} Credibility Factory Afrique. All rights reserved.<br>
      <span style="font-size:0.72rem;">www.credibilityfactory.net &nbsp;&middot;&nbsp; Developed by <a href="https://www.piquesquid.com/" target="_blank" rel="noopener" style="color:rgba(255,255,255,0.35);text-decoration:underline;text-underline-offset:2px;">Piquesquid Consultancy</a></span></div>
    </div>
  </div>
</footer>

<script>
// ── HAMBURGER ──
var burger = document.getElementById('hamburger');
var mobileMenu = document.getElementById('mobile-menu');
burger.addEventListener('click', function() { mobileMenu.classList.toggle('open'); });
mobileMenu.querySelectorAll('a').forEach(function(a) { a.addEventListener('click', function() { mobileMenu.classList.remove('open'); }); });

// ── SCROLL REVEAL ──
var revealObserver = new IntersectionObserver(function(entries) {
  entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('visible'); revealObserver.unobserve(e.target); } });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(function(el) { revealObserver.observe(el); });

// ── NAV SHADOW ──
var nav = document.getElementById('main-nav');
window.addEventListener('scroll', function() {
  nav.style.boxShadow = window.scrollY > 20 ? '0 4px 24px rgba(0,0,0,0.35)' : '';
}, { passive: true });

// ── HERO-SPECIFIC JS (guarded) ──
if (document.querySelector('.hero')) {
  // Stat cards staggered entrance
  document.querySelectorAll('.hero-stats .stat-card').forEach(function(card, i) {
    setTimeout(function() { card.classList.add('in'); }, 640 + i * 175);
  });

  // Stat count-up
  function countUp(el, target, duration) {
    var suffixEl = el.querySelector('span');
    var suffixHTML = suffixEl ? suffixEl.outerHTML : '';
    var t0 = performance.now();
    function tick(now) {
      var p = Math.min((now - t0) / duration, 1);
      var v = Math.round((1 - Math.pow(1 - p, 3)) * target);
      el.innerHTML = v + suffixHTML;
      if (p < 1) requestAnimationFrame(tick);
      else el.innerHTML = target + suffixHTML;
    }
    requestAnimationFrame(tick);
  }
  document.querySelectorAll('.hero .stat-num').forEach(function(el, i) {
    var target = parseInt(el.textContent);
    if (!isNaN(target)) setTimeout(function() { countUp(el, target, 1200); }, 760 + i * 175);
  });

  // Hero parallax
  var heroSection = document.querySelector('.hero');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!reducedMotion) {
    window.addEventListener('scroll', function() {
      var y = window.scrollY;
      if (y < window.innerHeight * 1.2) {
        heroSection.style.backgroundPositionY = 'calc(center + ' + (y * 0.28) + 'px)';
      }
    }, { passive: true });
  }
}

// ── CLIENT CAROUSEL (guarded) ──
if (!window._carouselInit && document.getElementById('clients-outer')) {
  window._carouselInit = true;
  (function(){
    var outer = document.getElementById('clients-outer');
    var track = document.getElementById('clients-track');
    if (!outer || !track) return;
    track.innerHTML += track.innerHTML;
    var speed = 0.55, paused = false, dragging = false, startX = 0, startScroll = 0;
    function half() { return track.scrollWidth / 2; }
    function wrap() { if (outer.scrollLeft >= half()) outer.scrollLeft -= half(); if (outer.scrollLeft < 0) outer.scrollLeft += half(); }
    (function tick() { if (!paused && !dragging) { outer.scrollLeft += speed; wrap(); } requestAnimationFrame(tick); })();
    outer.addEventListener('mouseenter', function() { paused = true; });
    outer.addEventListener('mouseleave', function() { paused = false; dragging = false; });
    outer.addEventListener('mousedown', function(e) { dragging = true; startX = e.pageX; startScroll = outer.scrollLeft; outer.style.cursor = 'grabbing'; });
    window.addEventListener('mouseup', function() { dragging = false; outer.style.cursor = 'grab'; });
    outer.addEventListener('mousemove', function(e) {
      if (!dragging) return;
      e.preventDefault();
      outer.scrollLeft = startScroll - (e.pageX - startX);
      wrap();
    });
    var tx = 0, ts = 0;
    outer.addEventListener('touchstart', function(e) { tx = e.touches[0].pageX; ts = outer.scrollLeft; paused = true; }, { passive: true });
    outer.addEventListener('touchmove', function(e) { outer.scrollLeft = ts - (e.touches[0].pageX - tx); wrap(); }, { passive: true });
    outer.addEventListener('touchend', function() { paused = false; });
  })();
}
</script>

@stack('scripts')

</body>
</html>
