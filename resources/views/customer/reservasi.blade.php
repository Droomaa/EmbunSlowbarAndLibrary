<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Booking Tempat - Embun Cafe</title>
    <style>
        body { margin: 0; font-family: sans-serif; background-color: #f0f2f0; color: #333; }
        .app-container { max-width: 480px; margin: 0 auto; background: white; min-height: 100vh; position: relative; padding-bottom: 30px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        
        .header { background: #4c7c5f; color: white; padding: 30px 20px; text-align: center; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; position: relative; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0 0 0; font-size: 13px; opacity: 0.9; }
        
        .form-container { padding: 25px 20px; margin-top: -15px; position: relative; z-index: 10; }
        
        .alert-success { background: #e6f4ea; color: #1e8e3e; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; text-align: center; border: 1px solid #cce8d6; box-shadow: 0 4px 10px rgba(30,142,62,0.1); }
        
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #555; margin-bottom: 8px; text-transform: uppercase; }
        .form-control { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; font-size: 14px; background: #fdfdfa; transition: 0.2s; }
        .form-control:focus { outline: none; border-color: #4c7c5f; box-shadow: 0 0 0 3px rgba(76,124,95,0.1); background: white; }
        textarea.form-control { resize: none; height: 80px; }
        
        .btn-submit { background: #4c7c5f; color: white; width: 100%; padding: 16px; border: none; border-radius: 10px; font-weight: bold; font-size: 16px; cursor: pointer; margin-top: 10px; box-shadow: 0 4px 15px rgba(76,124,95,0.3); transition: 0.2s; }
        .btn-submit:active { transform: scale(0.98); }
        
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <div class="header">
            <h1>📅 Booking Tempat</h1>
            <p>Reservasi meja untuk pengalaman slow bar terbaikmu</p>
        </div>

        <div class="form-container">
            
            @if(session('success'))
                <div class="alert-success">
                    {!! session('success') !!}
                </div>
            @endif

            <form action="/reservasi" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Rara" required>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="tel" name="telepon" class="form-control" placeholder="0812xxxxxxx" required>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="col form-group">
                        <label>Jam</label>
                        <input type="time" name="jam" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jumlah Orang (Pax)</label>
                    <input type="number" name="pax" class="form-control" placeholder="Contoh: 2" min="1" required>
                </div>

                <div class="form-group">
                    <label>Catatan Khusus (Opsional)</label>
                    <textarea name="notes" class="form-control" placeholder="Misal: Minta tempat di dekat jendela..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Kirim Permintaan</button>
            </form>
            
            <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #aaa;">
                Dengan melakukan reservasi, kamu menyetujui kebijakan Embun Cafe.
            </div>
        </div>

    </div>

</body>
</html>
