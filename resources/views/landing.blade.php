<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embun Slow Bar & Library</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f3ed;
            color: #333;
            text-align: center;
        }
        h1 { color: #4c7c5f; margin-bottom: 10px; font-size: 2.5rem; }
        p { color: #666; margin-bottom: 30px; }
        
        .btn-container { display: flex; gap: 15px; justify-content: center; }
        .btn {
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            border: 2px solid #4c7c5f;
            transition: 0.2s;
        }
        .btn-primary { background-color: #4c7c5f; color: white; }
        .btn-outline { background-color: transparent; color: #4c7c5f; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>

    <h1>☕ Embun Cafe</h1>
    <p>Landing page under construction by Frontend Team 🚀</p>

    <div class="btn-container">
        <a href="/login" class="btn btn-outline">Login</a>
        
        <a href="/menu" class="btn btn-primary">Pesan Sekarang!</a>

        <a href="/reservasi" class="btn btn-primary">Reservasi Sekarang!</a>
    </div>

</body>
</html>
