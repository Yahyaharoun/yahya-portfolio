<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Votre Code OTP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; padding: 40px; }
        .container { background-color: #ffffff; padding: 40px; border-radius: 12px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        h1 { color: #111827; font-size: 24px; margin-bottom: 20px; text-align: center; }
        p { color: #4b5563; font-size: 16px; line-height: 1.5; text-align: center; }
        .code-box { background-color: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; padding: 20px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1f2937; text-align: center; margin: 30px 0; }
        .footer { margin-top: 30px; font-size: 14px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Demande d'authentification</h1>
        <p>Bonjour,</p>
        <p>Vous avez demandé à accéder à des documents sécurisés (CV) sur le portfolio. Veuillez utiliser le code ci-dessous pour vérifier votre identité :</p>
        
        <div class="code-box">
            {{ $code }}
        </div>
        
        <p>Ce code expirera dans 10 minutes. S'il n'a pas été demandé par vous, vous pouvez ignorer cet email.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} Yahya Haroun - Portfolio
        </div>
    </div>
</body>
</html>
