<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Embun Cafe</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f3ed;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        h2 { color: #4c7c5f; margin: 0 0 10px 0; font-size: 24px; }
        p { color: #888; font-size: 13px; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; text-align: left; }
        label { display: block; font-size: 11px; font-weight: bold; color: #666; margin-bottom: 5px; }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: #4c7c5f; }
        
        .btn-submit {
            background-color: #4c7c5f;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover { background-color: #3d634c; }

        /* Styling untuk pesan error */
        .alert-error {
            background-color: #fce8e6;
            color: #dc3545;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid #fad2cf;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>☕ Embun Cafe</h2>
        <p>Silakan masuk ke portal operasional</p>

        @if (session('error'))
            <div class="alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            
            <div class="form-group">
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="form-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>

        <div style="margin-top: 25px; font-size: 11px; color: #aaa;">
            &copy; 2026 Embun Slow Bar & Library
        </div>
    </div>

</body>
</html>
