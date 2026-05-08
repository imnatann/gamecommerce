@props(['subject'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #0f0f1a; color: #e1e1e6; -webkit-font-smoothing: antialiased; }
        .email-wrapper { width: 100%; max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .email-card { background-color: #1a1a2e; border: 1px solid #262640; border-radius: 16px; overflow: hidden; }
        .email-header { background: linear-gradient(135deg, #6C3FE8 0%, #4a2cbd 100%); padding: 32px 24px; text-align: center; }
        .email-header h1 { color: #fff; font-size: 20px; font-weight: 700; margin: 0; }
        .email-header p { color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 4px; }
        .email-body { padding: 32px 24px; }
        .email-body h2 { color: #e1e1e6; font-size: 18px; font-weight: 600; margin-bottom: 16px; }
        .email-body p { color: #a1a1aa; font-size: 14px; line-height: 1.7; margin-bottom: 12px; }
        .email-body ul { list-style: none; padding: 0; margin: 12px 0; }
        .email-body ul li { padding: 8px 0; border-bottom: 1px solid #262640; color: #a1a1aa; font-size: 14px; }
        .email-body ul li:last-child { border-bottom: none; }
        .email-body ul li strong { color: #e1e1e6; }
        .order-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .order-table th { background-color: #0f0f1a; color: #71717a; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 12px; text-align: left; border-bottom: 1px solid #262640; }
        .order-table td { padding: 12px; border-bottom: 1px solid #262640; color: #e1e1e6; font-size: 14px; }
        .order-table .text-right { text-align: right; }
        .order-table .text-muted { color: #71717a; }
        .order-table .text-accent { color: #00E8A2; font-weight: 600; }
        .order-table .text-primary { color: #6C3FE8; font-weight: 600; }
        .divider { height: 1px; background-color: #262640; margin: 24px 0; }
        .code-box { background-color: #0f0f1a; border: 1px dashed #6C3FE8; border-radius: 8px; padding: 16px; text-align: center; margin: 16px 0; }
        .code-box code { font-family: 'Courier New', monospace; font-size: 18px; font-weight: 700; color: #00E8A2; letter-spacing: 3px; word-break: break-all; }
        .code-box small { display: block; color: #71717a; font-size: 11px; margin-top: 8px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #6C3FE8, #4a2cbd); color: #fff !important; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 32px; border-radius: 10px; text-align: center; }
        .btn:hover { opacity: 0.9; }
        .btn-accent { background: linear-gradient(135deg, #00E8A2, #00c48c); color: #0f0f1a !important; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-badge-pending { background-color: rgba(234, 179, 8, 0.15); color: #EAB308; }
        .status-badge-paid { background-color: rgba(6, 182, 212, 0.15); color: #06B6D4; }
        .status-badge-processing { background-color: rgba(108, 63, 232, 0.15); color: #6C3FE8; }
        .status-badge-delivered { background-color: rgba(0, 232, 162, 0.15); color: #00E8A2; }
        .status-badge-completed { background-color: rgba(0, 232, 162, 0.15); color: #00E8A2; }
        .total-row td { padding-top: 16px; border-bottom: none; }
        .total-row .total-price { font-size: 20px; color: #00E8A2; font-weight: 700; }
        .email-footer { padding: 24px; text-align: center; }
        .email-footer p { color: #52525b; font-size: 12px; line-height: 1.6; }
        .email-footer a { color: #6C3FE8; text-decoration: none; }
        .email-footer a:hover { text-decoration: underline; }
        .social-links { margin-top: 16px; }
        .social-links a { display: inline-block; margin: 0 8px; color: #71717a; text-decoration: none; }
        .social-links a:hover { color: #6C3FE8; }
        .warning-box { background-color: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3); border-radius: 8px; padding: 12px 16px; margin: 16px 0; }
        .warning-box p { color: #EAB308; font-size: 13px; margin: 0; }
        .info-box { background-color: rgba(108, 63, 232, 0.1); border: 1px solid rgba(108, 63, 232, 0.3); border-radius: 8px; padding: 12px 16px; margin: 16px 0; }
        .info-box p { color: #a78bfa; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <h1>🎮 GameCommerce</h1>
                <p>{{ $subject }}</p>
            </div>
            <div class="email-body">
                {{ $slot }}
            </div>
        </div>
        <div class="email-footer">
            <p>GameCommerce &mdash; Top Up Game & Voucher Terlengkap</p>
            <p>Jika Anda tidak membuat pesanan ini, mohon abaikan email ini.</p>
            <div class="social-links">
                <a href="#">Instagram</a> &bull; <a href="#">Twitter</a> &bull; <a href="#">Discord</a>
            </div>
            <p style="margin-top: 12px;">&copy; {{ date('Y') }} GameCommerce. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>