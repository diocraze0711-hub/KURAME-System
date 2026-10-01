@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
    --bg: #09111f;
    --bg-soft: #101a2c;
    --surface: #152335;
    --surface-light: #1d2c43;
    --text: #edf3ff;
    --muted: #a6bbd7;
    --primary: #4f46e5;
    --secondary: #06b6d4;
    --danger: #ef4444;
    --warning: #f59e0b;
    --success: #22c55e;
    --info: #38bdf8;
    --border: rgba(255,255,255,0.1);
    --shadow: 0 20px 45px rgba(0,0,0,0.25);
}

* { box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--bg), var(--bg-soft));
    color: var(--text);
}

a { color: inherit; text-decoration: none; }
img { max-width: 100%; }
input, textarea, select, button {
    font: inherit;
}

.container {
    width: min(1180px, 92%);
    margin: 0 auto;
}

.topbar {
    background: rgba(8, 15, 27, 0.9);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 10;
    backdrop-filter: blur(10px);
}

.nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 0;
    gap: 18px;
}

.brand {
    font-size: 1.55rem;
    font-weight: 800;
    letter-spacing: 0.04em;
}

.brand span {
    color: var(--secondary);
}

nav {
    display: flex;
    align-items: center;
    gap: 18px;
    flex-wrap: wrap;
}

nav a {
    color: var(--muted);
    transition: 0.2s ease;
}

nav a:hover { color: var(--text); }

.page-content {
    padding: 32px 0 60px;
}

.hero-card {
    display: grid;
    grid-template-columns: 1.5fr 0.8fr;
    gap: 24px;
    padding: 40px 32px;
    background: linear-gradient(135deg, rgba(79,70,229,.18), rgba(6,182,212,.1));
    border: 1px solid var(--border);
    border-radius: 24px;
    box-shadow: var(--shadow);
}

.eyebrow {
    color: var(--secondary);
    text-transform: uppercase;
    font-size: 0.76rem;
    letter-spacing: 0.12em;
    margin-bottom: 12px;
    font-weight: 700;
}

.hero-card h1 {
    font-size: clamp(2.3rem, 4vw, 4rem);
    line-height: 1.12;
    margin: 0 0 18px;
}

.lead {
    color: var(--muted);
    line-height: 1.75;
    max-width: 760px;
}

.hero-actions {
    display: flex;
    gap: 16px;
    margin-top: 28px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 12px 18px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.btn:hover { opacity: 0.95; transform: translateY(-1px); }

.btn-primary { background: var(--primary); color: #fff; }
.btn-secondary { background: var(--secondary); color: #06131b; }
.btn-warning { background: var(--warning); color: #1e1600; }
.btn-danger { background: var(--danger); color: #fff; }
.full { width: 100%; }

.panel {
    background: rgba(18, 30, 46, 0.92);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 22px 20px;
    box-shadow: var(--shadow);
}

.stats-panel {
    display: flex;
    flex-direction: column;
    gap: 14px;
    justify-content: center;
}

.mini-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 4px;
    border-bottom: 1px solid var(--border);
}

.mini-stat:last-child { border-bottom: none; }

.mini-stat strong {
    font-size: 1.4rem;
}

.mini-stat span { color: var(--muted); }

.feature-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
    margin-top: 28px;
}

.second-grid { margin-top: 24px; }

.feature-grid .panel p, .panel p {
    color: var(--muted);
    line-height: 1.7;
}

.auth-page {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 40px;
}

.auth-card {
    width: min(480px, 100%);
    background: rgba(18, 30, 46, 0.92);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 28px 22px;
    box-shadow: var(--shadow);
}

.auth-card h2 {
    margin-top: 0;
    font-size: 1.8rem;
}

label {
    display: block;
    margin: 12px 0 8px;
    color: var(--muted);
    font-weight: 500;
}

input, textarea, select {
    width: 100%;
    background: rgba(9, 17, 31, 0.8);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 12px;
    padding: 12px 14px;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.alert {
    margin: 12px 0 20px;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid transparent;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(239, 68, 68, 0.35);
    color: #ffc4c4;
}

.alert-success {
    background: rgba(34, 197, 94, 0.12);
    border-color: rgba(34, 197, 94, 0.3);
    color: #d1fae5;
}

.subtle {
    margin-top: 16px;
    color: var(--muted);
    text-align: center;
}

.welcome-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.welcome-bar h2 { margin: 0; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.stat-box {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

.stat-box span { color: var(--muted); }
.stat-box strong { font-size: clamp(1.6rem, 3vw, 2.3rem); }

.two-column {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 22px;
}

.list-compact {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 14px;
}

.list-item {
    padding: 16px 14px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: rgba(12, 19, 31, 0.6);
}

.meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.meta-row small { color: var(--muted); }

.tags-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}

.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.badge-primary { background: rgba(79,70,229,.18); color: #c7d2fe; }
.badge-secondary { background: rgba(148,163,184,.18); color: #e2e8f0; }
.badge-warning { background: rgba(245,158,11,.18); color: #fde68a; }
.badge-info { background: rgba(56,189,248,.16); color: #bae6fd; }
.badge-success { background: rgba(34,197,94,.15); color: #bbf7d0; }
.badge-danger { background: rgba(239,68,68,.18); color: #fecaca; }

.composer {
    margin-bottom: 24px;
}

.post-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.post-card {
    padding: 20px 18px;
}

.comment-block {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
}

.comment-item {
    padding: 12px 14px;
    border-radius: 12px;
    background: rgba(9,17,31,0.75);
}

.comment-form {
    display: flex;
    gap: 10px;
    margin-top: 16px;
}

.comment-form input {
    flex: 1;
}

.case-item {
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 14px;
    margin-bottom: 12px;
    background: rgba(12, 19, 31, 0.6);
}

.case-update {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 12px;
}

.case-update select {
    flex: 1;
}

.footer {
    border-top: 1px solid var(--border);
    background: rgba(9, 17, 31, 0.8);
    padding: 18px 0 28px;
    color: var(--muted);
    text-align: center;
}

@media (max-width: 900px) {
    .hero-card,
    .two-column,
    .feature-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .nav {
        flex-direction: column;
        align-items: flex-start;
    }

    .comment-form,
    .case-update {
        flex-direction: column;
        align-items: stretch;
    }
}
