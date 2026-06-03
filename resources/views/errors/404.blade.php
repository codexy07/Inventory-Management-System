<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Not Found — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0b0d14;
            background-image:
                radial-gradient(ellipse 70% 50% at 50% 0%, rgba(217, 119, 6, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 30% 100%, rgba(59, 130, 246, 0.04) 0%, transparent 50%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background:
                repeating-linear-gradient(0deg, transparent, transparent 80px, rgba(255,255,255,0.006) 80px, rgba(255,255,255,0.006) 81px),
                repeating-linear-gradient(90deg, transparent, transparent 80px, rgba(255,255,255,0.006) 80px, rgba(255,255,255,0.006) 81px);
            pointer-events: none;
            z-index: 0;
        }
        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 40px 24px;
            max-width: 480px;
        }
        .error-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 120px;
            font-weight: 500;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -6px;
        }
        .error-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(217,119,6,0.15), rgba(245,158,11,0.08));
            border: 1px solid rgba(217,119,6,0.2);
        }
        .error-icon i {
            font-size: 30px;
            color: #f59e0b;
        }
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }
        .error-message {
            color: rgba(255,255,255,0.45);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .error-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            box-shadow: 0 4px 20px rgba(217,119,6,0.3);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 28px rgba(217,119,6,0.4);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            font-weight: 500;
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.04);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border-color: rgba(255,255,255,0.2);
        }
        .error-footer {
            margin-top: 40px;
            color: rgba(255,255,255,0.15);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-compass"></i>
        </div>
        <div class="error-code">404</div>
        <div class="error-title">Page Not Found</div>
        <div class="error-message">
            The page you're looking for doesn't exist or has been moved.<br>
            Let's get you back on track.
        </div>
        <div class="error-actions">
            <a href="{{ url()->previous() }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
            <a href="{{ route('dashboard') }}" class="btn-primary">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </div>
        <div class="error-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
