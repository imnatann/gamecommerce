<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #0f0f1a; color: #e1e1e6; min-height: 100vh; display: flex; align-items: center; justify-content: center; -webkit-font-smoothing: antialiased; }
        .container { text-align: center; padding: 40px 20px; max-width: 520px; }
        .error-code { font-size: 120px; font-weight: 800; background: linear-gradient(135deg, #EAB308, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 8px; letter-spacing: -4px; }
        .subtitle { font-size: 20px; font-weight: 600; color: #e1e1e6; margin-bottom: 12px; }
        .description { font-size: 14px; color: #71717a; line-height: 1.7; margin-bottom: 32px; }
        .info-box { background-color: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.3); border-radius: 12px; padding: 16px 20px; margin-bottom: 32px; text-align: left; }
        .info-box p { color: #EAB308; font-size: 13px; line-height: 1.6; }
        .btn-home { display: inline-block; background: linear-gradient(135deg, #6C3FE8, #4a2cbd); color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 14px 32px; border-radius: 10px; transition: opacity 0.2s; }
        .btn-home:hover { opacity: 0.9; }
        .btn-ghost { display: inline-block; background: transparent; border: 1px solid #262640; color: #a1a1aa; text-decoration: none; font-size: 14px; font-weight: 500; padding: 14px 32px; border-radius: 10px; margin-left: 12px; transition: all 0.2s; }
        .btn-ghost:hover { border-color: #6C3FE8; color: #e1e1e6; }
        .links { margin-top: 32px; }
        .links a { color: #6C3FE8; text-decoration: none; font-size: 13px; margin: 0 8px; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">403</div>
        <h1 class="subtitle">Akses Ditolak</h1>
        <p class="description">Kamu tidak memiliki izin untuk mengakses halaman ini. Jika menurutmu ini adalah kesalahan, silakan hubungi customer service kami.</p>
        <div class="info-box">
            <p>🔒 <strong>Kemungkinan penyebab:</strong> Akun belum login, role tidak cukup, atau resource ini bersifat privat.</p>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn-home">🎮 Kembali ke Beranda</a>
            <a href="javascript:history.back()" class="btn-ghost">← Kembali</a>
        </div>
        <div class="links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('search') }}">Cari Game</a>
        </div>
    </div>
</body>
</html>
