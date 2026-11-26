<style>
    .auth-page {
        display: grid;
        flex: 1;
        place-items: center;
        padding: clamp(32px, 7vh, 72px) 18px;
    }
    .auth-card {
        width: min(440px, 100%);
        padding: clamp(24px, 6vw, 38px);
        border: 1px solid #e4e9ef;
        border-radius: 18px;
        background: #fff;
        color: #172b46;
        box-shadow: 0 18px 48px rgba(0, 0, 0, .18);
    }
    .auth-card-header { margin-bottom: 26px; }
    .auth-card-header h1 {
        margin: 0;
        color: #142640;
        font-size: clamp(1.7rem, 5vw, 2rem);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.2;
    }
    .auth-card-header p { margin: 9px 0 0; color: #64748b; font-size: .94rem; line-height: 1.55; }
    .auth-form { display: grid; gap: 18px; }
    .auth-field { display: grid; gap: 8px; color: #344a63; font-size: .88rem; font-weight: 700; }
    .auth-field input {
        width: 100%;
        min-height: 48px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        outline: none;
        background: #fff;
        color: #172b46;
        font: inherit;
        font-size: .95rem;
        font-weight: 450;
        transition: border-color .16s ease, box-shadow .16s ease;
    }
    .auth-field input::placeholder { color: #94a3b8; }
    .auth-field input:focus { border-color: #3478c8; box-shadow: 0 0 0 3px rgba(52, 120, 200, .14); }
    .auth-password-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .auth-password-row a, .auth-card-footer a { color: #1768a6; font-size: .86rem; font-weight: 700; text-decoration: none; }
    .auth-password-row a:hover, .auth-card-footer a:hover { text-decoration: underline; }
    .auth-submit {
        display: inline-flex;
        width: 100%;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        margin-top: 2px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: #1768a6;
        color: #fff;
        font: inherit;
        font-size: .96rem;
        font-weight: 750;
        cursor: pointer;
        transition: background .16s ease, transform .16s ease;
    }
    .auth-submit:hover:not(:disabled) { transform: translateY(-1px); background: #125788; }
    .auth-submit:disabled { cursor: wait; opacity: .7; }
    .auth-divider { display: flex; align-items: center; gap: 12px; color: #94a3b8; font-size: .78rem; }
    .auth-divider::before, .auth-divider::after { height: 1px; flex: 1; background: #e2e8f0; content: ""; }
    .auth-google-button {
        display: flex;
        min-height: 48px;
        align-items: center;
        justify-content: center;
        gap: 11px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: #27364a;
        font-size: .92rem;
        font-weight: 700;
        text-decoration: none;
        transition: border-color .16s ease, background .16s ease;
    }
    .auth-google-button:hover { border-color: #94a3b8; background: #f8fafc; color: #172b46; }
    .auth-google-button svg { width: 19px; height: 19px; flex: 0 0 auto; }
    .auth-google-note { margin: -8px 0 0; color: #7c6a37; font-size: .78rem; line-height: 1.5; text-align: center; }
    .auth-card-footer { margin: 22px 0 0; color: #64748b; font-size: .9rem; text-align: center; }
    .auth-message:empty { display: none; }
    .auth-message { margin-bottom: 16px; }
    .auth-form .alert { margin: 0; border-radius: 9px; font-size: .88rem; }
    @media (max-width: 480px) {
        .auth-page { padding: 28px 14px; }
        .auth-card { border-radius: 14px; }
    }
</style>
