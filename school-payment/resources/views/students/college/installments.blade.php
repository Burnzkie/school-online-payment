@extends('students.college.layouts.student-app')

@section('title', 'Installment Plans')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>

/* =====================================================
   INSTALLMENT PAGE — ENHANCED ENGAGEMENT REDESIGN
===================================================== */

.ip-root *, .ip-root *::before, .ip-root *::after { box-sizing: border-box; }

.ip-root {
    font-family: 'Sora', sans-serif;
    background: #f9fafb;
    min-height: 100vh;
    color: #1f2937;
    -webkit-font-smoothing: antialiased;
}

/* ── Animated dot ────────────────────────────────── */
.ip-live-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #10b981; flex-shrink: 0;
    animation: ip-dot-pulse 2.5s ease-out infinite;
}
@keyframes ip-dot-pulse {
    0%   { box-shadow: 0 0 0 0   rgba(16,185,129,.55); }
    70%  { box-shadow: 0 0 0 9px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0   rgba(16,185,129,0); }
}

/* ── Page hero ───────────────────────────────────── */
.ip-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(140deg, #eef2ff 0%, #e0e7ff 55%, #f0f9ff 100%);
    border-bottom: 1px solid #c7d2fe;
    padding: 52px 24px 44px; text-align: center;
}
.ip-hero-grid {
    position: absolute; inset: 0; opacity: .045; pointer-events: none;
    background-image:
        linear-gradient(rgba(79,70,229,0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(79,70,229,0.08) 1px, transparent 1px);
    background-size: 44px 44px;
}
.ip-hero-glow-l {
    position: absolute; top: -80px; left: -80px;
    width: 320px; height: 320px; border-radius: 50%;
    background: radial-gradient(circle, rgba(59,85,230,.22) 0%, transparent 70%);
    pointer-events: none;
}
.ip-hero-glow-r {
    position: absolute; bottom: -60px; right: -40px;
    width: 260px; height: 260px; border-radius: 50%;
    background: radial-gradient(circle, rgba(99,130,255,.15) 0%, transparent 70%);
    pointer-events: none;
}
.ip-hero-eyebrow {
    position: relative;
    display: inline-flex; align-items: center; gap: 7px;
    padding: 5px 14px; border-radius: 999px;
    background: #fff; border: 1px solid #c7d2fe;
    font-size: 11px; font-weight: 700; letter-spacing: .09em;
    text-transform: uppercase; color: #6b7280; margin-bottom: 18px;
}
.ip-hero h1 {
    position: relative;
    font-family: 'DM Serif Display', serif;
    font-size: clamp(28px, 5vw, 44px); font-weight: 400; color: #1f2937;
    line-height: 1.15; margin: 0 0 10px;
}
.ip-hero h1 em { font-style: italic; color: #4f46e5; }
.ip-hero-sub {
    position: relative;
    font-size: 14px; font-weight: 500; color: #6b7280;
    max-width: 380px; margin: 0 auto 26px; line-height: 1.7;
}
.ip-balance-pill {
    position: relative;
    display: inline-flex; align-items: center; gap: 10px;
    padding: 11px 22px;
    background: #fff; border: 1px solid #c7d2fe;
    border-radius: 999px;
    font-size: 13px; color: #6b7280; font-weight: 600;
}
.ip-balance-pill strong {
    color: #1f2937; font-size: 17px; font-weight: 800;
    font-family: 'JetBrains Mono', monospace;
}

/* ── Content wrapper ─────────────────────────────── */
.ip-wrap { max-width: 960px; margin: 0 auto; padding: 32px 20px 80px; }

/* ── Flash alerts ────────────────────────────────── */
.ip-alert {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px; border-radius: 14px;
    margin-bottom: 20px; font-size: 14px; font-weight: 600;
}
.ip-alert-ok  { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #166534; }
.ip-alert-err { background: #fff1f2; border: 1.5px solid #fecdd3; color: #9f1239; }
.ip-alert-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 20px; line-height: 1; color: inherit; opacity: .6; font-family: inherit; }
.ip-alert-close:hover { opacity: 1; }

/* ── Step tracker ────────────────────────────────── */
.ip-steps {
    display: flex; align-items: center;
    background: #fff; border: 1.5px solid #dde4f5;
    border-radius: 18px; padding: 18px 28px;
    box-shadow: 0 2px 16px rgba(37,99,235,.08); margin-bottom: 28px;
    position: relative; overflow: hidden;
}
.ip-steps::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #2563eb, #818cf8, #a78bfa);
    border-radius: 18px 18px 0 0;
}
.ip-step { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
.ip-step-num {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 13px; flex-shrink: 0;
    transition: transform .3s cubic-bezier(.34,1.56,.64,1);
}
.ip-step-num.on {
    background: linear-gradient(145deg, #4f8ef7, #2563eb); color: #fff;
    box-shadow: 0 0 0 5px #eff6ff, 0 4px 12px rgba(37,99,235,.3);
    animation: ip-step-bounce .6s cubic-bezier(.34,1.56,.64,1) both;
}
.ip-step-num.off { background: #f1f5f9; color: #9aaac4; }
@keyframes ip-step-bounce {
    0% { transform: scale(.4); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.ip-step-name { font-size: 12px; font-weight: 800; color: #1f2937; }
.ip-step-hint { font-size: 11px; font-weight: 500; color: #9aaac4; }
.ip-step-line {
    flex: 1; height: 2px; background: #e8eef8; margin: 0 10px; border-radius: 99px;
    position: relative; overflow: hidden;
}
.ip-step-line::after {
    content: ''; position: absolute; inset: 0; border-radius: 99px;
    background: linear-gradient(90deg, #2563eb, #818cf8);
    transform: scaleX(0); transform-origin: left;
    transition: transform .8s cubic-bezier(.4,0,.2,1) .3s;
}
.ip-steps.step-1-done .ip-step-line:first-of-type::after { transform: scaleX(1); }

/* ── Plan grid ───────────────────────────────────── */
.ip-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; align-items: start; }
@media (max-width: 880px) { .ip-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px) { .ip-grid { grid-template-columns: 1fr; } }

.ip-card-wrap { position: relative; }
.ip-card-wrap.ip-top-space { padding-top: 16px; }

/* ── Pricing card ────────────────────────────────── */
.ip-card {
    background: #fff; border-radius: 22px; border: 2px solid #e4ebf8;
    cursor: pointer; position: relative; overflow: hidden;
    display: grid;
    grid-template-rows: auto auto auto auto 1fr auto;
    box-shadow: 0 4px 20px rgba(37,99,235,.07);
    transition: transform .28s cubic-bezier(.34,1.3,.64,1), box-shadow .28s ease, border-color .2s ease;
}
/* Shimmer sweep */
.ip-card::before {
    content: ''; position: absolute; top: 0; left: -100%; width: 60%; height: 100%;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.5) 50%, transparent 60%);
    transition: left 0s; pointer-events: none; z-index: 1;
}
.ip-card:hover::before { left: 160%; transition: left .55s ease; }
.ip-card:hover { transform: translateY(-8px); box-shadow: 0 20px 52px rgba(37,99,235,.16); border-color: #93c5fd; }
.ip-card.ip-selected { transform: translateY(-8px); border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37,99,235,.1), 0 20px 52px rgba(37,99,235,.18); }

/* featured */
.ip-card.ip-featured {
    background: linear-gradient(155deg, #3b5ce6 0%, #1d4ed8 55%, #1a44c8 100%);
    border-color: transparent;
    box-shadow: 0 22px 60px rgba(37,99,235,.42);
    transform: translateY(-14px);
}
.ip-card.ip-featured::before { background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.18) 50%, transparent 60%); }
.ip-card.ip-featured:hover { transform: translateY(-20px); box-shadow: 0 30px 72px rgba(37,99,235,.52); }
.ip-card.ip-featured.ip-selected { transform: translateY(-20px); box-shadow: 0 0 0 4px rgba(255,255,255,.35), 0 30px 72px rgba(37,99,235,.52); }

/* card icon */
.ip-card-icon-strip { padding: 24px 24px 0; display: flex; justify-content: center; }
.ip-plan-icon {
    width: 54px; height: 54px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center; font-size: 26px;
    background: #f9fafb; border: 1.5px solid #e4ebf8;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1);
}
.ip-card:hover .ip-plan-icon { transform: scale(1.12) rotate(-5deg); }
.ip-card.ip-featured .ip-plan-icon { background: rgba(255,255,255,.15); border-color: rgba(255,255,255,.2); }

/* card label */
.ip-card-label { padding: 10px 24px 2px; text-align: center; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #9aaac4; }
.ip-card.ip-featured .ip-card-label { color: rgba(255,255,255,.5); }

/* price */
.ip-card-price { padding: 8px 24px 16px; text-align: center; }
.ip-price-amount { font-size: 44px; font-weight: 900; letter-spacing: -.04em; color: #1f2937; line-height: 1; font-family: 'JetBrains Mono', monospace; }
.ip-card.ip-featured .ip-price-amount { color: #fff; }
.ip-price-amount sup { font-size: 18px; font-weight: 800; vertical-align: super; line-height: 0; margin-right: 1px; }
.ip-price-sub { display: block; font-size: 11px; font-weight: 600; color: #9aaac4; margin-top: 6px; }
.ip-card.ip-featured .ip-price-sub { color: rgba(255,255,255,.45); }

/* divider */
.ip-card-divider { height: 1px; background: #e8edf8; margin: 0 20px; }
.ip-card.ip-featured .ip-card-divider { background: rgba(255,255,255,.2); }

/* features */
.ip-card-features { padding: 16px 24px 12px; }
.ip-feat { display: flex; align-items: center; gap: 9px; padding: 5px 0; font-size: 12.5px; color: #5a6a8a; font-weight: 600; line-height: 1.5; }
.ip-card.ip-featured .ip-feat { color: rgba(255,255,255,.82); }
.ip-feat-check {
    width: 18px; height: 18px; border-radius: 6px; flex-shrink: 0;
    background: #eff6ff; display: flex; align-items: center; justify-content: center;
}
.ip-card.ip-featured .ip-feat-check { background: rgba(255,255,255,.2); }

/* CTA */
.ip-card-cta { padding: 0 20px 22px; }
.ip-cta-btn {
    width: 100%; padding: 13px 0; border-radius: 12px;
    font-size: 13px; font-weight: 800; letter-spacing: .02em;
    cursor: pointer; border: 2px solid #e4ebf8;
    background: #f8faff; color: #1f2937;
    font-family: 'Sora', sans-serif; transition: all .2s ease;
}
.ip-cta-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; box-shadow: 0 4px 16px rgba(37,99,235,.14); transform: translateY(-1px); }
.ip-card.ip-featured .ip-cta-btn { background: #fff; border-color: transparent; color: #1d4ed8; box-shadow: 0 4px 18px rgba(0,0,0,.12); }
.ip-card.ip-featured .ip-cta-btn:hover { background: #eff6ff; box-shadow: 0 8px 28px rgba(255,255,255,.28); }

/* selected check */
.ip-check {
    position: absolute; top: 12px; right: 12px;
    width: 26px; height: 26px; border-radius: 50%; background: #2563eb; color: #fff;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transform: scale(.3) rotate(-90deg);
    transition: opacity .22s ease, transform .3s cubic-bezier(.34,1.56,.64,1);
    z-index: 3; box-shadow: 0 4px 12px rgba(37,99,235,.4);
}
.ip-card.ip-featured .ip-check { background: rgba(255,255,255,.25); box-shadow: none; }
.ip-card.ip-selected .ip-check { opacity: 1; transform: scale(1) rotate(0); }

/* popular badge */
.ip-pop {
    position: absolute; top: -13px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff;
    font-size: 10px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase;
    padding: 5px 14px; border-radius: 999px;
    box-shadow: 0 4px 14px rgba(245,158,11,.35);
    white-space: nowrap; z-index: 5; font-family: 'Sora', sans-serif;
    animation: ip-pop-float 3s ease-in-out infinite;
}
@keyframes ip-pop-float {
    0%,100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-3px); }
}

/* savings tag on cards */
.ip-save-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 800; letter-spacing: .03em;
    background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
}
.ip-save-badge-wrap { text-align: center; padding: 0 24px 10px; }

/* ── Reminder ────────────────────────────────────── */
.ip-reminder {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 22px;
    background: linear-gradient(135deg, #eff6ff, #e8eefb);
    border: 1.5px solid #bfdbfe; border-radius: 14px;
    margin-bottom: 24px; font-size: 13px; color: #1d4ed8; font-weight: 600;
}
.ip-bell { display: inline-block; font-size: 18px; animation: ip-bell-ring 4s ease-in-out infinite; }
@keyframes ip-bell-ring {
    0%,85%,100% { transform: rotate(0); }
    88% { transform: rotate(-15deg); }
    92% { transform: rotate(13deg); }
    96% { transform: rotate(-7deg); }
    99% { transform: rotate(4deg); }
}

/* ── Live summary ────────────────────────────────── */
.ip-summary {
    background: #fff; border-radius: 20px; padding: 20px 28px;
    border: 1.5px solid #dde4f5; box-shadow: 0 4px 20px rgba(37,99,235,.07);
    margin-bottom: 32px; display: flex; align-items: center; gap: 0; flex-wrap: wrap;
    position: relative; overflow: hidden;
}
.ip-summary::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #2563eb, #818cf8, #a78bfa);
    opacity: 0; transition: opacity .4s ease;
}
.ip-summary.has-selection::before { opacity: 1; }
.ip-sum-lbl { font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #9aaac4; margin-bottom: 5px; }
.ip-sum-val { font-size: 16px; font-weight: 800; color: #1f2937; font-family: 'JetBrains Mono', monospace; }
.ip-sum-val.blue { color: #2563eb; }
.ip-sum-val.green { color: #10b981; }
.ip-sum-div { width: 1px; height: 36px; background: #dde4f5; flex-shrink: 0; margin: 0 18px; }
.ip-sum-col { flex: 1; min-width: 80px; }
.ip-sum-empty { font-size: 13px; font-weight: 600; color: #9aaac4; animation: ip-sum-blink 2s ease-in-out infinite; }
@keyframes ip-sum-blink {
    0%,100% { opacity: .6; } 50% { opacity: 1; }
}

/* ── Modal ───────────────────────────────────────── */
.ip-overlay {
    position: fixed; inset: 0; background: rgba(8,15,40,.62);
    backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
    z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 16px;
}
.ip-modal {
    background: #fff; border-radius: 28px; width: 100%; max-width: 490px;
    box-shadow: 0 48px 120px rgba(0,0,0,.28); overflow: hidden;
    animation: ip-modal-in .3s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes ip-modal-in {
    from { transform: scale(.86) translateY(28px); opacity: 0; }
    to   { transform: scale(1) translateY(0); opacity: 1; }
}
.ip-modal-top {
    padding: 32px 28px 26px; text-align: center; position: relative; overflow: hidden;
    background: linear-gradient(150deg, #eef2ff 0%, #e0e7ff 60%, #f0f9ff 100%);
    border-bottom: 1px solid #c7d2fe;
}
.ip-modal-top::before {
    content: ''; position: absolute; inset: 0;
    background-image: linear-gradient(rgba(79,70,229,.06) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(79,70,229,.06) 1px, transparent 1px);
    background-size: 36px 36px; pointer-events: none;
}
.ip-modal-top::after {
    content: ''; position: absolute; top: -50px; right: -50px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(59,85,230,.28) 0%, transparent 70%); pointer-events: none;
}
.ip-modal-plan-icon {
    position: relative; z-index: 1;
    width: 64px; height: 64px; border-radius: 20px; margin: 0 auto 18px;
    background: #fff; border: 1.5px solid #c7d2fe;
    display: flex; align-items: center; justify-content: center; font-size: 30px;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
}
.ip-modal-eye  { position: relative; z-index: 1; font-size: 10px; font-weight: 800; letter-spacing: .10em; text-transform: uppercase; color: #9ca3af; margin-bottom: 8px; }
.ip-modal-name { position: relative; z-index: 1; font-family: 'DM Serif Display', serif; font-size: 26px; font-weight: 400; color: #1f2937; }
.ip-modal-desc { position: relative; z-index: 1; font-size: 13px; font-weight: 500; color: #6b7280; margin-top: 6px; }
.ip-modal-stats { display: grid; grid-template-columns: repeat(3, 1fr); border-bottom: 1.5px solid #edf0f8; }
.ip-modal-stat { padding: 20px 12px; text-align: center; border-right: 1.5px solid #edf0f8; }
.ip-modal-stat:last-child { border-right: none; }
.ip-mstat-lbl { font-size: 10px; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; color: #9aaac4; margin-bottom: 6px; }
.ip-mstat-val { font-size: 15px; font-weight: 800; color: #1f2937; font-family: 'JetBrains Mono', monospace; }
.ip-mstat-val.blue { color: #2563eb; }
.ip-modal-tl { padding: 20px 24px 10px; }
.ip-tl-lbl { font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #9aaac4; margin-bottom: 14px; }
.ip-tl-bars { display: flex; align-items: flex-end; gap: 7px; }
.ip-tl-bar { flex: 1; border-radius: 6px 6px 4px 4px; }
.ip-tl-num { font-size: 11px; font-weight: 700; color: #9aaac4; text-align: center; margin-top: 6px; }
.ip-modal-warn {
    margin: 4px 24px 0; display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 16px; background: #fffbeb; border: 1.5px solid #fde68a;
    border-radius: 12px; font-size: 12px; font-weight: 600; color: #92400e; line-height: 1.5;
}
.ip-modal-chk { padding: 14px 24px; }
.ip-chk-label {
    display: flex; align-items: flex-start; gap: 12px;
    cursor: pointer; padding: 10px 12px; border-radius: 12px;
    font-size: 13px; color: #5a6a8a; font-weight: 600; line-height: 1.5; transition: background .15s;
}
.ip-chk-label:hover { background: #f5f8ff; }
.ip-chk-label input { margin-top: 2px; flex-shrink: 0; cursor: pointer; accent-color: #2563eb; }
.ip-modal-btns { display: flex; gap: 12px; padding: 8px 24px 24px; flex-wrap: wrap; }
.ip-btn-confirm {
    flex: 1; min-width: 140px;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 20px; background: linear-gradient(145deg, #2d65f0, #1d4ed8);
    color: #fff; font-size: 14px; font-weight: 800; border: none; border-radius: 14px;
    cursor: pointer; font-family: 'Sora', sans-serif;
    box-shadow: 0 6px 20px rgba(37,99,235,.32); transition: all .2s ease;
}
.ip-btn-confirm:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(37,99,235,.42); }
.ip-btn-confirm:disabled { opacity: .38; cursor: not-allowed; }
.ip-btn-back {
    flex: 1; min-width: 120px; padding: 14px 20px;
    background: #fff; color: #5a6a8a; font-size: 14px; font-weight: 700;
    border: 1.5px solid #dde4f5; border-radius: 14px; cursor: pointer;
    font-family: 'Sora', sans-serif; transition: all .2s ease;
}
.ip-btn-back:hover { background: #f5f8ff; border-color: #93c5fd; color: #2563eb; }

/* ═══════════════════════════
   ACTIVE PLAN DASHBOARD
═══════════════════════════ */

.ip-active-hero {
    background: linear-gradient(140deg, #eef2ff 0%, #e0e7ff 55%, #f0f9ff 100%);
    border: 1px solid #c7d2fe;
    border-radius: 24px; padding: 36px 36px 32px;
    color: #1f2937; position: relative; overflow: hidden; margin-bottom: 20px;
}
.ip-active-hero-grid {
    position: absolute; inset: 0; opacity: 1; pointer-events: none;
    background-image:
        linear-gradient(rgba(79,70,229,0.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(79,70,229,0.06) 1px, transparent 1px);
    background-size: 36px 36px;
}
.ip-active-hero::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse 65% 75% at 100% 0%, rgba(59,85,230,.2) 0%, transparent 55%);
    pointer-events: none;
}
.ip-active-hero::after {
    content: ''; position: absolute; right: -60px; bottom: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.04); pointer-events: none;
}
.ip-active-inner { position: relative; display: flex; align-items: center; gap: 32px; flex-wrap: wrap; }
.ip-active-left { flex: 1; min-width: 200px; }
.ip-active-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.ip-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px;
    background: #fff; border: 1px solid #c7d2fe;
    color: #4f46e5; font-size: 11px; font-weight: 700;
}
.ip-active-h2 {
    font-family: 'DM Serif Display', serif; font-size: clamp(22px, 4vw, 32px);
    font-weight: 400; color: #1f2937; line-height: 1.2; margin: 0 0 10px;
}
.ip-active-p { font-size: 14px; font-weight: 500; color: #6b7280; line-height: 1.7; margin: 0; }
.ip-active-p strong { color: #1f2937; }
.ip-paid-hl { color: #059669; font-weight: 700; }

/* progress */
.ip-progress-wrap { margin-top: 22px; max-width: 340px; }
.ip-progress-row { display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; color: #9ca3af; margin-bottom: 8px; }
.ip-progress-row span:last-child { color: #4f46e5; font-size: 12px; }
.ip-progress-track { height: 8px; border-radius: 99px; overflow: hidden; background: #e0e7ff; }
.ip-progress-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, #60a5fa, #818cf8, #a78bfa);
    transition: width 1.4s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.ip-progress-fill::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.28), transparent);
    animation: ip-shimmer 2.8s ease-in-out infinite;
}
@keyframes ip-shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(200%); } }
.ip-milestones { display: flex; justify-content: space-between; padding: 0 1px; margin-top: 7px; }
.ip-ms { width: 6px; height: 6px; border-radius: 50%; background: #c7d2fe; transition: background .4s; position: relative; }
.ip-ms.hit { background: #6366f1; }
.ip-ms-label { position: absolute; top: 10px; left: 50%; transform: translateX(-50%); font-size: 9px; font-weight: 700; color: #9ca3af; white-space: nowrap; }
.ip-ms.hit .ip-ms-label { color: #4f46e5; }

/* ring */
.ip-ring-wrap { position: relative; width: 96px; height: 96px; flex-shrink: 0; }
.ip-ring-wrap svg { transform: rotate(-90deg); }
.ip-ring-bg   { fill: none; stroke: #e0e7ff; stroke-width: 8; }
.ip-ring-fill { fill: none; stroke: url(#ringGrad); stroke-width: 8; stroke-linecap: round; transition: stroke-dashoffset 1.5s cubic-bezier(.4,0,.2,1); }
.ip-ring-inner { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.ip-ring-pct { font-family: 'JetBrains Mono', monospace; font-weight: 900; font-size: 22px; color: #1f2937; line-height: 1; }
.ip-ring-done { font-size: 9px; font-weight: 700; color: #9ca3af; letter-spacing: .06em; text-transform: uppercase; margin-top: 2px; }
.ip-ring-caption { font-size: 11px; font-weight: 600; color: #6b7280; text-align: center; margin-top: 7px; }

/* metric cards */
.ip-metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
@media (max-width: 700px) { .ip-metrics { grid-template-columns: repeat(2, 1fr); } }
.ip-metric {
    background: #fff; border: 1.5px solid #dde4f5; border-radius: 18px;
    padding: 20px 20px; box-shadow: 0 2px 12px rgba(37,99,235,.05);
    transition: transform .22s ease, box-shadow .22s ease, border-color .2s;
}
.ip-metric:hover { transform: translateY(-5px); box-shadow: 0 14px 36px rgba(37,99,235,.12); border-color: #bfdbfe; }
.ip-metric-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.ip-metric-icon { width: 36px; height: 36px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.ip-metric-status { font-size: 10px; font-weight: 800; letter-spacing: .05em; padding: 3px 8px; border-radius: 999px; }
.ip-metric-lbl { font-size: 10px; font-weight: 800; color: #9aaac4; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 6px; }
.ip-metric-val { font-size: 22px; font-weight: 900; color: #1f2937; line-height: 1.1; font-family: 'JetBrains Mono', monospace; }
.ip-metric-val.g { color: #10b981; }
.ip-metric-val.a { color: #f59e0b; }
.ip-metric-val.r { color: #ef4444; }
.ip-metric-sub { font-size: 11px; font-weight: 600; color: #9aaac4; margin-top: 4px; }

/* schedule */
.ip-sched-box { background: #fff; border: 1.5px solid #dde4f5; border-radius: 22px; padding: 26px; box-shadow: 0 4px 16px rgba(37,99,235,.07); margin-bottom: 20px; }
.ip-sched-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.ip-sched-title { font-size: 15px; font-weight: 900; color: #1f2937; }
.ip-pill { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 800; }
.ip-pill-green { background: #ecfdf5; color: #065f46; }
.ip-sched-row {
    display: flex; align-items: center; gap: 16px;
    padding: 14px 16px; border-radius: 16px; border: 1.5px solid;
    transition: transform .22s cubic-bezier(.34,1.2,.64,1), box-shadow .22s ease, background .15s ease; cursor: default;
    position: relative;
}
.ip-sched-row:hover { transform: translateX(6px) translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,.1); }
.ip-sched-row.paid    { background: #f0fdf4; border-color: #bbf7d0; }
.ip-sched-row.paid:hover { background: #ecfdf5; }
.ip-sched-row.overdue { background: #fff1f2; border-color: #fecdd3; }
.ip-sched-row.overdue:hover { background: #ffe4e6; }
.ip-sched-row.pending { background: #fafbff; border-color: #dde4f5; }
.ip-sched-row.pending:hover { background: #eff6ff; border-color: #bfdbfe; }
.ip-sched-connector { width: 2px; height: 10px; margin: 0 22px; background: #dde4f5; border-radius: 99px; }
.ip-sched-connector.paid { background: #86efac; }
.ip-sched-dot {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 14px; flex-shrink: 0;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1);
}
.ip-sched-row:hover .ip-sched-dot { transform: scale(1.12) rotate(-4deg); }
.ip-sched-dot.paid    { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 14px rgba(16,185,129,.3); }
.ip-sched-dot.overdue { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 14px rgba(239,68,68,.28); }
.ip-sched-dot.pending { background: #f1f5f9; color: #9aaac4; }
.ip-sched-info { flex: 1; min-width: 0; }
.ip-sched-name  { font-size: 14px; font-weight: 800; color: #1f2937; }
.ip-sched-date  { font-size: 12px; font-weight: 500; color: #9aaac4; margin-top: 3px; }
.ip-sched-paidon { font-size: 12px; font-weight: 700; color: #10b981; margin-top: 3px; display: flex; align-items: center; gap: 5px; }
.ip-sched-right { text-align: right; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.ip-sched-amt { font-size: 15px; font-weight: 900; font-family: 'JetBrains Mono', monospace; display: block; }
.ip-sched-amt.paid    { color: #059669; }
.ip-sched-amt.overdue { color: #dc2626; }
.ip-sched-amt.pending { color: #1f2937; }

/* schedule count-up progress header */
.ip-sched-progress-bar { height: 4px; background: #e8eef8; border-radius: 99px; overflow: hidden; margin-bottom: 20px; }
.ip-sched-progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #10b981, #34d399); transition: width 1.2s cubic-bezier(.4,0,.2,1); }

/* cashier note */
.ip-note { display: flex; align-items: flex-start; gap: 16px; padding: 22px 24px; background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 18px; }
.ip-note-icon { width: 44px; height: 44px; border-radius: 13px; background: #fef3c7; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.ip-note-title { font-size: 14px; font-weight: 800; color: #78350f; margin-bottom: 5px; }
.ip-note-body  { font-size: 13px; font-weight: 500; color: #92400e; line-height: 1.6; }

/* fade-in */
.ip-fade { animation: ip-fadein .45s ease both; }
.ip-d1 { animation-delay: .06s; }
.ip-d2 { animation-delay: .12s; }
.ip-d3 { animation-delay: .18s; }
.ip-d4 { animation-delay: .24s; }
@keyframes ip-fadein {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Urgency pulse for overdue metric ───────────── */
@keyframes ip-urgent-pulse {
    0%,100% { box-shadow: 0 2px 12px rgba(37,99,235,.05); }
    50% { box-shadow: 0 0 0 6px rgba(239,68,68,.12), 0 8px 24px rgba(239,68,68,.1); }
}
.ip-metric.urgent { animation: ip-urgent-pulse 2s ease-in-out infinite; border-color: #fecdd3; }

/* ── 100% complete banner ────────────────────────── */
.ip-complete-banner {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0;
    border-radius: 20px; padding: 28px 32px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 20px; position: relative; overflow: hidden;
}
.ip-complete-banner::before {
    content: '🎉'; position: absolute; right: 24px; top: 50%;
    transform: translateY(-50%); font-size: 64px; opacity: .15;
    pointer-events: none;
}
.ip-complete-icon {
    width: 56px; height: 56px; border-radius: 18px;
    background: rgba(255,255,255,.15); display: flex; align-items: center;
    justify-content: center; font-size: 28px; flex-shrink: 0;
    animation: ip-bounce-icon 2s ease-in-out infinite;
}
@keyframes ip-bounce-icon {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
.ip-complete-text { flex: 1; }
.ip-complete-title { font-size: 20px; font-weight: 900; color: #065f46; font-family: 'DM Serif Display', serif; }
.ip-complete-sub   { font-size: 13px; font-weight: 500; color: #047857; margin-top: 4px; }

/* ── Metric card number counter animation ────────── */
@keyframes ip-count-up {
    from { transform: translateY(8px); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}
.ip-metric-val { animation: ip-count-up .5s ease both; }
.ip-d1 .ip-metric-val { animation-delay: .1s; }
.ip-d2 .ip-metric-val { animation-delay: .2s; }
.ip-d3 .ip-metric-val { animation-delay: .3s; }
.ip-d4 .ip-metric-val { animation-delay: .4s; }

/* ── Plan card keyboard nav highlight ───────────── */
.ip-card:focus-visible {
    outline: 3px solid #2563eb; outline-offset: 3px; border-radius: 22px;
}

/* ── Sticky CTA bar (mobile) ─────────────────────── */
.ip-sticky-cta {
    position: fixed; bottom: 0; left: 0; right: 0;
    padding: 12px 20px 20px;
    background: linear-gradient(to top, rgba(249,250,251,1) 60%, transparent);
    z-index: 100; pointer-events: none;
    transform: translateY(100%);
    transition: transform .35s cubic-bezier(.34,1.3,.64,1);
}
.ip-sticky-cta.visible { transform: translateY(0); pointer-events: all; }
.ip-sticky-inner {
    background: linear-gradient(145deg, #2d65f0, #1d4ed8);
    color: #fff; border-radius: 16px; padding: 14px 24px;
    font-size: 14px; font-weight: 800; text-align: center;
    box-shadow: 0 8px 32px rgba(37,99,235,.4); cursor: pointer;
    border: none; width: 100%; font-family: 'Sora', sans-serif;
}

</style>
@endpush


@section('content')
<div class="ip-root">

    {{-- ════════════════════════════════════════════
         HERO HEADER
    ════════════════════════════════════════════════ --}}
    <div class="ip-hero">
        <div class="ip-hero-grid"></div>
        <div class="ip-hero-glow-l"></div>
        <div class="ip-hero-glow-r"></div>
        <div class="ip-hero-eyebrow">
            <span class="ip-live-dot"></span>
            Student Billing · {{ $currentSemesterLabel ?? '1st Semester' }}
        </div>
        <h1>Your <em>Payment Plan</em></h1>
        <p class="ip-hero-sub">Transparent, flexible, and locked in for the semester.<br>Pick what fits your budget.</p>
        <span class="ip-balance-pill">
            <span class="ip-live-dot"></span>
            Semester Balance: &nbsp;
            <strong>₱{{ number_format($totalBalance ?? 0, 2) }}</strong>
        </span>
    </div>


    {{-- ════════════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════════════ --}}
    <div class="ip-wrap">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="ip-alert ip-alert-ok ip-fade" x-data x-init="setTimeout(()=>$el.remove(),5000)">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span style="flex:1">{{ session('success') }}</span>
            <button class="ip-alert-close" @click="$el.parentElement.remove()">×</button>
        </div>
        @endif
        @if(session('error'))
        <div class="ip-alert ip-alert-err ip-fade" x-data x-init="setTimeout(()=>$el.remove(),6000)">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span style="flex:1">{{ session('error') }}</span>
            <button class="ip-alert-close" @click="$el.parentElement.remove()">×</button>
        </div>
        @endif


        {{-- ════════════════════════════════════════
             A) PLAN SELECTION
        ════════════════════════════════════════════ --}}
        @if(!isset($activePlan))

        <div x-data="{
                sel: null,
                showModal: false,
                confirmed: false,
                total: {{ $totalBalance ?? 0 }},
                plans: {
                    full: { label:'Full Payment',       installments:1, emoji:'💎', desc:'Pay everything upfront — zero follow-ups.' },
                    '2':  { label:'2-Installment Plan', installments:2, emoji:'✌️',  desc:'Split your balance into 2 equal payments.' },
                    '3':  { label:'3-Installment Plan', installments:3, emoji:'📅', desc:'Three comfortable monthly payments.' },
                    '4':  { label:'4-Installment Plan', installments:4, emoji:'🗓', desc:'Smallest monthly amount — easiest on budget.' }
                },
                get plan()   { return this.sel ? this.plans[this.sel] : null; },
                get perAmt() { return this.plan ? this.total / this.plan.installments : 0; },
                get remain() { return this.plan ? this.total - this.perAmt : this.total; },
                fmt(v)       { return '₱' + parseFloat(v).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); },
                pick(k)      { this.sel=k; this.confirmed=false; this.showModal=true; }
            }">

            {{-- Step tracker --}}
            <div class="ip-steps ip-fade">
                <div class="ip-step">
                    <div class="ip-step-num on">1</div>
                    <div>
                        <div class="ip-step-name">Choose Plan</div>
                        <div class="ip-step-hint">Select a payment option</div>
                    </div>
                </div>
                <div class="ip-step-line"></div>
                <div class="ip-step">
                    <div class="ip-step-num off">2</div>
                    <div>
                        <div class="ip-step-name" style="color:#9aaac4">Review &amp; Confirm</div>
                        <div class="ip-step-hint">Verify your details</div>
                    </div>
                </div>
                <div class="ip-step-line"></div>
                <div class="ip-step">
                    <div class="ip-step-num off">3</div>
                    <div>
                        <div class="ip-step-name" style="color:#9aaac4">Done!</div>
                        <div class="ip-step-hint">Plan activated</div>
                    </div>
                </div>
            </div>


            {{-- 4 PRICING CARDS --}}
            <div class="ip-grid">

                {{-- Plan A: Full Payment --}}
                <div class="ip-card-wrap ip-fade ip-d1">
                    <div class="ip-card" :class="sel==='full' ? 'ip-selected' : ''"
                         @click="pick('full')" tabindex="0" role="button" aria-label="Full Payment Plan"
                         @keydown.enter="pick('full')" @keydown.space.prevent="pick('full')">
                        <div class="ip-check">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="ip-card-icon-strip"><div class="ip-plan-icon">💎</div></div>
                        <div class="ip-card-label">Plan A · Full</div>
                        <div class="ip-card-price">
                            <div class="ip-price-amount"><sup>₱</sup>{{ number_format(($totalBalance??0), 0) }}</div>
                            <span class="ip-price-sub">pay once · 1 payment</span>
                        </div>
                        <div class="ip-save-badge-wrap">
                            <span class="ip-save-badge">🏆 Best value · no split fees</span>
                        </div>
                        <div class="ip-card-divider"></div>
                        <div class="ip-card-features">
                            @foreach(['Full upfront payment','Instant enrollment clearance','Zero remaining balance','No extra charges','Best value overall'] as $f)
                            <div class="ip-feat">
                                <span class="ip-feat-check"><svg width="10" height="8" fill="none" stroke="#2563eb" stroke-width="2.5" viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span>{{ $f }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="ip-card-cta"><button class="ip-cta-btn">Choose Plan A</button></div>
                    </div>
                </div>

                {{-- Plan B: 2 Installments — FEATURED --}}
                <div class="ip-card-wrap ip-top-space ip-fade ip-d2">
                    <div class="ip-pop">⭐ Most Popular</div>
                    <div class="ip-card ip-featured" :class="sel==='2' ? 'ip-selected' : ''"
                         @click="pick('2')" tabindex="0" role="button" aria-label="2-Installment Plan"
                         @keydown.enter="pick('2')" @keydown.space.prevent="pick('2')">
                        <div class="ip-check">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="ip-card-icon-strip"><div class="ip-plan-icon">✌️</div></div>
                        <div class="ip-card-label">Plan B · 2×</div>
                        <div class="ip-card-price">
                            <div class="ip-price-amount"><sup>₱</sup>{{ number_format(($totalBalance??0)/2, 0) }}</div>
                            <span class="ip-price-sub">per installment · 2 payments</span>
                        </div>
                        <div class="ip-save-badge-wrap">
                            <span class="ip-save-badge">⚡ 50% today · 50% next month</span>
                        </div>
                        <div class="ip-card-divider"></div>
                        <div class="ip-card-features">
                            @foreach(['2 equal monthly payments','50% down to get started','SMS & email reminders','Flexible schedule','Easy on monthly budget'] as $f)
                            <div class="ip-feat">
                                <span class="ip-feat-check"><svg width="10" height="8" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2.5" viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span>{{ $f }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="ip-card-cta"><button class="ip-cta-btn">Choose Plan B</button></div>
                    </div>
                </div>

                {{-- Plan C: 3 Installments --}}
                <div class="ip-card-wrap ip-fade ip-d3">
                    <div class="ip-card" :class="sel==='3' ? 'ip-selected' : ''"
                         @click="pick('3')" tabindex="0" role="button" aria-label="3-Installment Plan"
                         @keydown.enter="pick('3')" @keydown.space.prevent="pick('3')">
                        <div class="ip-check">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="ip-card-icon-strip"><div class="ip-plan-icon">📅</div></div>
                        <div class="ip-card-label">Plan C · 3×</div>
                        <div class="ip-card-price">
                            <div class="ip-price-amount"><sup>₱</sup>{{ number_format(($totalBalance??0)/3, 0) }}</div>
                            <span class="ip-price-sub">per installment · 3 payments</span>
                        </div>
                        <div class="ip-save-badge-wrap">
                            <span class="ip-save-badge">📊 Spread over 3 months</span>
                        </div>
                        <div class="ip-card-divider"></div>
                        <div class="ip-card-features">
                            @foreach(['3 equal monthly payments','33% down to get started','SMS & email reminders','Balanced schedule','Lower monthly obligation'] as $f)
                            <div class="ip-feat">
                                <span class="ip-feat-check"><svg width="10" height="8" fill="none" stroke="#2563eb" stroke-width="2.5" viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span>{{ $f }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="ip-card-cta"><button class="ip-cta-btn">Choose Plan C</button></div>
                    </div>
                </div>

                {{-- Plan D: 4 Installments --}}
                <div class="ip-card-wrap ip-fade ip-d4">
                    <div class="ip-card" :class="sel==='4' ? 'ip-selected' : ''"
                         @click="pick('4')" tabindex="0" role="button" aria-label="4-Installment Plan"
                         @keydown.enter="pick('4')" @keydown.space.prevent="pick('4')">
                        <div class="ip-check">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none"><path d="M1 5l3.5 3.5L11 1" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="ip-card-icon-strip"><div class="ip-plan-icon">🗓</div></div>
                        <div class="ip-card-label">Plan D · 4×</div>
                        <div class="ip-card-price">
                            <div class="ip-price-amount"><sup>₱</sup>{{ number_format(($totalBalance??0)/4, 0) }}</div>
                            <span class="ip-price-sub">per installment · 4 payments</span>
                        </div>
                        <div class="ip-save-badge-wrap">
                            <span class="ip-save-badge">💡 Lowest monthly amount</span>
                        </div>
                        <div class="ip-card-divider"></div>
                        <div class="ip-card-features">
                            @foreach(['4 equal monthly payments','25% down to get started','SMS & email reminders','Easiest on monthly budget','Maximum flexibility'] as $f)
                            <div class="ip-feat">
                                <span class="ip-feat-check"><svg width="10" height="8" fill="none" stroke="#2563eb" stroke-width="2.5" viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                                <span>{{ $f }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="ip-card-cta"><button class="ip-cta-btn">Choose Plan D</button></div>
                    </div>
                </div>

            </div>{{-- /ip-grid --}}


            {{-- Reminder --}}
            <div class="ip-reminder ip-fade">
                <span class="ip-bell">🔔</span>
                <span>Automatic reminders sent before each due date via <strong>email &amp; SMS.</strong></span>
            </div>


            {{-- Live summary --}}
            <div class="ip-summary ip-fade" :class="sel ? 'has-selection' : ''">
                <div style="flex-shrink:0;">
                    <div class="ip-sum-lbl">Selected Plan</div>
                    <div class="ip-sum-val blue" x-show="sel" x-text="plan ? plan.label : ''" style="display:none"></div>
                    <div class="ip-sum-empty" x-show="!sel">← Pick a plan above to preview</div>
                </div>
                <div class="ip-sum-div"></div>
                <div class="ip-sum-col">
                    <div class="ip-sum-lbl">Total Balance</div>
                    <div class="ip-sum-val">₱{{ number_format($totalBalance??0,2) }}</div>
                </div>
                <div class="ip-sum-div"></div>
                <div class="ip-sum-col">
                    <div class="ip-sum-lbl">Down Payment</div>
                    <div class="ip-sum-val blue" x-text="sel ? fmt(perAmt) : '—'">—</div>
                </div>
                <div class="ip-sum-div"></div>
                <div class="ip-sum-col">
                    <div class="ip-sum-lbl">Remaining</div>
                    <div class="ip-sum-val" x-text="sel ? fmt(remain) : '—'">—</div>
                </div>
                <div class="ip-sum-div"></div>
                <div class="ip-sum-col">
                    <div class="ip-sum-lbl">Per Payment</div>
                    <div class="ip-sum-val blue" x-text="sel ? fmt(perAmt) : '—'">—</div>
                </div>
                <div class="ip-sum-div" x-show="sel" style="display:none"></div>
                <div class="ip-sum-col" x-show="sel" style="display:none">
                    <div class="ip-sum-lbl">Payments</div>
                    <div class="ip-sum-val green" x-text="plan ? plan.installments + '×' : '—'">—</div>
                </div>
            </div>


            {{-- CONFIRMATION MODAL --}}
            <div x-show="showModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.self="showModal=false"
                 @keydown.escape.window="showModal=false"
                 class="ip-overlay"
                 style="display:none">

                <div class="ip-modal" @click.stop>

                    <div class="ip-modal-top">
                        <div class="ip-modal-plan-icon" x-text="plan ? plan.emoji : '📋'"></div>
                        <div class="ip-modal-eye">You Selected</div>
                        <div class="ip-modal-name" x-text="plan ? plan.label : ''"></div>
                        <div class="ip-modal-desc" x-text="plan ? plan.desc : ''"></div>
                    </div>

                    <div class="ip-modal-stats">
                        <div class="ip-modal-stat">
                            <div class="ip-mstat-lbl">Total Balance</div>
                            <div class="ip-mstat-val" x-text="fmt(total)"></div>
                        </div>
                        <div class="ip-modal-stat">
                            <div class="ip-mstat-lbl">Per Payment</div>
                            <div class="ip-mstat-val blue" x-text="fmt(perAmt)"></div>
                        </div>
                        <div class="ip-modal-stat">
                            <div class="ip-mstat-lbl">Payments</div>
                            <div class="ip-mstat-val" x-text="(plan ? plan.installments : 0) + '×'"></div>
                        </div>
                    </div>

                    <div class="ip-modal-tl">
                        <div class="ip-tl-lbl">Payment Timeline</div>
                        <div class="ip-tl-bars" x-show="plan">
                            <template x-for="n in (plan ? plan.installments : 0)" :key="n">
                                <div style="flex:1;text-align:center">
                                    <div class="ip-tl-bar"
                                         :style="{
                                             height: (16 + n * 8) + 'px',
                                             background: n===1
                                                 ? 'linear-gradient(180deg,#4f8ef7,#1d4ed8)'
                                                 : 'linear-gradient(180deg,#c7d2fe,#dde8f8)'
                                         }"></div>
                                    <div class="ip-tl-num" x-text="'#'+n"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="ip-modal-warn">
                        <svg width="16" height="16" fill="#f59e0b" viewBox="0 0 20 20" style="flex-shrink:0;margin-top:1px"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>This plan is <strong>locked for the semester</strong> once confirmed. Contact the Cashier's Office for any changes.</span>
                    </div>

                    <div class="ip-modal-chk">
                        <form method="POST" action="{{ route('student.installments.choose') }}" id="planForm">
                            @csrf
                            <input type="hidden" name="plan_type" :value="sel">
                            <input type="hidden" name="confirmed" value="1">
                            <label class="ip-chk-label">
                                <input type="checkbox" x-model="confirmed">
                                <span>
                                    I understand this is final and agree to pay
                                    <strong style="color:#1a2340"
                                            x-text="plan && plan.installments===1
                                                ? fmt(total)+' in full'
                                                : plan?.installments+' installments of '+fmt(perAmt)">
                                    </strong>.
                                </span>
                            </label>
                        </form>
                    </div>

                    <div class="ip-modal-btns">
                        <button type="submit" form="planForm"
                                class="ip-btn-confirm"
                                :disabled="!confirmed"
                                :style="!confirmed ? 'opacity:.38;cursor:not-allowed' : ''">
                            <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Confirm My Plan
                        </button>
                        <button type="button" @click="showModal=false" class="ip-btn-back">
                            ← Go Back
                        </button>
                    </div>

                </div>
            </div>{{-- /modal --}}

        </div>{{-- /x-data --}}


        {{-- ════════════════════════════════════════
             B) ACTIVE PLAN DASHBOARD
        ════════════════════════════════════════════ --}}
        @else

        @php
            $paidCount  = $activePlan['paid_count']  ?? 0;
            $totalCount = $activePlan['installments'] ?? 1;
            $remaining  = $totalCount - $paidCount;
            $pctDone    = $totalCount > 0 ? round(($paidCount / $totalCount) * 100) : 0;
            $r          = 38;
            $circ       = 2 * M_PI * $r;
            $offset     = $circ * (1 - $pctDone / 100);
        @endphp

        {{-- 100% celebration banner --}}
        @if($pctDone >= 100)
        <div class="ip-complete-banner ip-fade">
            <div class="ip-complete-icon">🎉</div>
            <div class="ip-complete-text">
                <div class="ip-complete-title">All Installments Cleared!</div>
                <div class="ip-complete-sub">Your account is fully settled for this semester. Nothing more to pay.</div>
            </div>
        </div>
        @endif

        {{-- Active Hero --}}
        <div class="ip-active-hero ip-fade">
            <div class="ip-active-hero-grid"></div>
            <div class="ip-active-inner">
                <div class="ip-active-left">
                    <div class="ip-active-chips">
                        @if(($activePlan['type'] ?? '') === 'full')
                            <span class="ip-chip">💎 Full Payment Plan</span>
                        @else
                            <span class="ip-chip">📅 {{ $totalCount }}-Installment Plan</span>
                        @endif
                        <span class="ip-chip">
                            <span class="ip-live-dot" style="background:#86efac"></span>&nbsp;Active
                        </span>
                    </div>
                    <h2 class="ip-active-h2">Your Payment Plan</h2>
                    <p class="ip-active-p">
                        @if(($activePlan['type'] ?? '') === 'full')
                            Full semester payment — <strong>all cleared! 🎉</strong>
                        @else
                            Paying in <strong>{{ $totalCount }}</strong> equal installments.
                            <span class="ip-paid-hl">{{ $paidCount }} paid</span>,
                            {{ $remaining }} remaining.
                        @endif
                    </p>
                    @if(($activePlan['type'] ?? '') !== 'full')
                    <div class="ip-progress-wrap">
                        <div class="ip-progress-row">
                            <span>Overall Progress</span>
                            <span>{{ $pctDone }}% Complete</span>
                        </div>
                        <div class="ip-progress-track">
                            <div class="ip-progress-fill" style="width:{{ $pctDone }}%"></div>
                        </div>
                        <div class="ip-milestones">
                            @foreach([25 => '25%', 50 => '50%', 75 => '75%', 100 => '100%'] as $pct => $lbl)
                            <div class="ip-ms {{ $pctDone >= $pct ? 'hit' : '' }}">
                                <span class="ip-ms-label">{{ $lbl }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Progress Ring --}}
                <div style="display:flex;flex-direction:column;align-items:center;gap:7px;flex-shrink:0">
                    <div class="ip-ring-wrap">
                        <svg width="96" height="96" viewBox="0 0 96 96">
                            <defs>
                                <linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%"   stop-color="#60a5fa"/>
                                    <stop offset="50%"  stop-color="#818cf8"/>
                                    <stop offset="100%" stop-color="#a78bfa"/>
                                </linearGradient>
                            </defs>
                            <circle class="ip-ring-bg"   cx="48" cy="48" r="{{ $r }}"/>
                            <circle class="ip-ring-fill" cx="48" cy="48" r="{{ $r }}"
                                    stroke-dasharray="{{ round($circ, 2) }}"
                                    stroke-dashoffset="{{ round($offset, 2) }}"/>
                        </svg>
                        <div class="ip-ring-inner">
                            <div class="ip-ring-pct">{{ $pctDone }}%</div>
                            <div class="ip-ring-done">Done</div>
                        </div>
                    </div>
                    <div class="ip-ring-caption">{{ $paidCount }}/{{ $totalCount }} paid</div>
                </div>
            </div>
        </div>

        {{-- Metric cards --}}
        <div class="ip-metrics">
            <div class="ip-metric ip-fade ip-d1 {{ isset($activePlan['days_until']) && $activePlan['days_until'] < 0 ? 'urgent' : '' }}">
                <div class="ip-metric-head">
                    <div class="ip-metric-icon" style="background:#eff6ff">📅</div>
                    @if(isset($activePlan['days_until']))
                        @if($activePlan['days_until'] < 0)
                            <span class="ip-metric-status" style="background:#fff1f2;color:#9f1239;">Overdue</span>
                        @elseif($activePlan['days_until'] <= 7)
                            <span class="ip-metric-status" style="background:#fffbeb;color:#92400e;">Due Soon</span>
                        @else
                            <span class="ip-metric-status" style="background:#f0fdf4;color:#065f46;">On Track</span>
                        @endif
                    @endif
                </div>
                <div class="ip-metric-lbl">Next Due</div>
                @if($activePlan['next_due'] ?? false)
                    <div class="ip-metric-val" style="font-size:15px;font-family:'Sora',sans-serif">{{ $activePlan['next_due'] }}</div>
                    @if(isset($activePlan['days_until']))
                    <div class="ip-metric-sub" style="{{ $activePlan['days_until'] < 0 ? 'color:#dc2626' : ($activePlan['days_until'] <= 7 ? 'color:#f59e0b' : '') }}">
                        @if($activePlan['days_until'] < 0) ⚠ {{ abs($activePlan['days_until']) }}d overdue
                        @elseif($activePlan['days_until'] === 0) ⚠ Due today!
                        @else {{ $activePlan['days_until'] }}d remaining @endif
                    </div>
                    @endif
                @else
                    <div class="ip-metric-val g" style="font-size:14px;font-family:'Sora',sans-serif">🎉 All paid!</div>
                @endif
            </div>

            <div class="ip-metric ip-fade ip-d2">
                <div class="ip-metric-head">
                    <div class="ip-metric-icon" style="background:#ecfdf5">💰</div>
                </div>
                <div class="ip-metric-lbl">{{ ($activePlan['type']??'')==='full' ? 'Total Amount' : 'Per Installment' }}</div>
                <div class="ip-metric-val g">₱{{ number_format($activePlan['amount_per']??0,2) }}</div>
                @if(($activePlan['type']??'')!=='full')
                    <div class="ip-metric-sub">× {{ $totalCount }} payments</div>
                @endif
            </div>

            <div class="ip-metric ip-fade ip-d3">
                <div class="ip-metric-head">
                    <div class="ip-metric-icon" style="background:#fffbeb">⏳</div>
                </div>
                <div class="ip-metric-lbl">Remaining</div>
                <div class="ip-metric-val a">{{ $remaining }}</div>
                <div class="ip-metric-sub">payment{{ $remaining!==1?'s':'' }} left</div>
            </div>

            <div class="ip-metric ip-fade ip-d4">
                <div class="ip-metric-head">
                    <div class="ip-metric-icon" style="background:#f1f5f9">📋</div>
                </div>
                <div class="ip-metric-lbl">Total Plan</div>
                <div class="ip-metric-val" style="font-size:16px">₱{{ number_format($activePlan['total_amount']??0,2) }}</div>
                <div class="ip-metric-sub">Full semester</div>
            </div>
        </div>

        {{-- Payment Schedule --}}
        @if(isset($schedule) && count($schedule) > 0)
        <div class="ip-sched-box ip-fade">
            <div class="ip-sched-head">
                <div class="ip-sched-title">🗓&nbsp; Payment Schedule</div>
                <span class="ip-pill ip-pill-green">{{ $paidCount }}/{{ $totalCount }} Done</span>
            </div>
            {{-- Progress bar --}}
            <div class="ip-sched-progress-bar">
                <div class="ip-sched-progress-fill" style="width:{{ $pctDone }}%"></div>
            </div>
            <div>
                @foreach($schedule as $i => $pay)
                    @if(!$loop->first)
                        <div class="ip-sched-connector {{ ($pay['paid']??false) ? 'paid' : '' }}"></div>
                    @endif
                    <div class="ip-sched-row {{ ($pay['paid']??false) ? 'paid' : (($pay['overdue']??false) ? 'overdue' : 'pending') }}">
                        <div class="ip-sched-dot {{ ($pay['paid']??false) ? 'paid' : (($pay['overdue']??false) ? 'overdue' : 'pending') }}">
                            @if($pay['paid']??false)
                                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @elseif($pay['overdue']??false)
                                <svg width="16" height="16" fill="#fff" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            @else
                                <span style="font-family:'JetBrains Mono',monospace">{{ $i + 1 }}</span>
                            @endif
                        </div>
                        <div class="ip-sched-info">
                            <div class="ip-sched-name">Installment {{ $i + 1 }}</div>
                            <div class="ip-sched-date">Due {{ $pay['due_date'] ?? '—' }}</div>
                            @if($pay['paid']??false)
                                <div class="ip-sched-paidon">
                                    <svg width="11" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 12 10"><path d="M1 5l3.5 3.5L11 1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Paid on {{ $pay['paid_at']??'—' }}
                                </div>
                            @endif
                        </div>
                        <div class="ip-sched-right">
                            <span class="ip-sched-amt {{ ($pay['paid']??false) ? 'paid' : (($pay['overdue']??false) ? 'overdue' : 'pending') }}">
                                ₱{{ number_format($pay['amount']??0, 2) }}
                            </span>
                            <span class="ip-pill" style="{{ ($pay['paid']??false) ? 'background:#ecfdf5;color:#065f46' : (($pay['overdue']??false) ? 'background:#fff1f2;color:#9f1239' : 'background:#f0f4ff;color:#3730a3') }}">
                                @if($pay['paid']??false) ✓ Paid
                                @elseif($pay['overdue']??false) ⚠ Overdue
                                @else ⏰ Pending @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Cashier note --}}
        <div class="ip-note ip-fade">
            <div class="ip-note-icon">🏛️</div>
            <div>
                <div class="ip-note-title">Need to change your plan?</div>
                <div class="ip-note-body">Visit or contact the <strong>Cashier's Office</strong> for modification requests. Changes are subject to approval.</div>
            </div>
        </div>

        @endif

    </div>{{-- /ip-wrap --}}

    {{-- Sticky mobile CTA (shown when cards scroll off screen) --}}
    @if(!isset($activePlan))
    <div class="ip-sticky-cta">
        <button class="ip-sticky-inner">
            Choose Your Plan &nbsp;↑
        </button>
    </div>
    @endif

</div>{{-- /ip-root --}}

<script>
// Sticky CTA: show when plan cards scroll out of view on mobile
(function() {
    const grid = document.querySelector('.ip-grid');
    if (!grid) return;
    const bar = document.querySelector('.ip-sticky-cta');
    if (!bar) return;
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { bar.classList.toggle('visible', !e.isIntersecting); });
    }, { threshold: 0.1 });
    obs.observe(grid);
    document.querySelector('.ip-sticky-inner')?.addEventListener('click', () => {
        grid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        bar.classList.remove('visible');
    });
})();
// Animate schedule progress bar on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.ip-sched-progress-fill').forEach(el => {
        const w = el.style.width; el.style.width = '0';
        requestAnimationFrame(() => setTimeout(() => el.style.width = w, 100));
    });
});
</script>
@endsection