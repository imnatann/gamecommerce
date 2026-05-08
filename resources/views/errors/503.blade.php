<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Sedang Maintenance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #0f0f1a; color: #e1e1e6; min-height: 100vh; display: flex; align-items: center; justify-content: center; -webkit-font-smoothing: antialiased; }
        .container { text-align: center; padding: 40px 20px; max-width: 520px; }
        .error-code { font-size: 120px; font-weight: 800; background: linear-gradient(135deg, #06B6D4, #6C3FE8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 8px; letter-spacing: -4px; }
        .subtitle { font-size: 20px; font-weight: 600; color: #e1e1e6; margin-bottom: 12px; }
        .description { font-size: 14px; color: #71717a; line-height: 1.7; margin-bottom: 24px; }
        .maintenance-icon { font-size: 64px; margin-bottom: 16px; }
        .status-box { background-color: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 12px; padding: 16px 20px; margin-bottom: 32px; text-align: left; }
        .status-box p { color: #06B6D4; font-size: 13px; line-height: 1.6; }
        .countdown { font-size: 18px; font-weight: 600; color: #00E8A2; margin-bottom: 32px; }
        .btn-home { display: inline-block; background: linear-gradient(135deg, #6C3FE8, #4a2cbd); color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 32px; border-radius: 10px; transition: opacity 0.2s; }
        .btn-home:hover { opacity: 0.9; }
        .btn-retry { display: inline-block; background: transparent; border: 1px solid #262640; color: #a1a1aa; text-decoration: none; font-size: 14px; font-weight: 500; padding: 14px 32px; border-radius: 10px; margin-left: 12px; transition: all 0.2s; }
        .btn-retry:hover { border-color: #6C3FE8; color: #e1e1e6; }
        .links { margin-top: 32px; }
        .links a { color: #6C3FE8; text-decoration: none; font-size: 13px; margin: 0 8px; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="maintenance-icon">🔧</div>
        <div class="error-code">503</div>
        <h1 class="subtitle">Sedang Maintenance</h1>
        <p class="description">Kami sedang melakukan pemeliharaan server untuk memberikan pengalaman yang lebih baik. Kami akan segera kembali!</p>
        <div class="status-box">
            <p>🛠️ <strong>Sedang proses:</strong> Update server & optimasi performa. Estimasi selesai dalam beberapa menit.</p>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn-home">🎮 Kembali ke Beranda</a>
            <a href="javascript:location.reload()" class="btn-retry">🔄 Coba Lagi</a>
        </div>
        <div class="links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('search') }}">Cari Game</a>
        </div>
    </div>
</body>
</html>
