<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; color: #333; margin: 0; padding: 20px; font-size: 14px; }
        .receipt-container { width: 100%; max-width: 600px; margin: 0 auto; border: 2px dashed #ccc; padding: 20px; border-radius: 10px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .content { width: 100%; }
        .content table { width: 100%; border-collapse: collapse; }
        .content th, .content td { padding: 8px 0; text-align: left; vertical-align: top; }
        .content th { width: 35%; font-weight: bold; }
        .amount { font-size: 18px; font-weight: bold; margin-top: 20px; border-top: 2px dashed #ccc; padding-top: 20px; text-align: center; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            body { padding: 0; }
            .receipt-container { border: none; max-width: 100%; }
            .btn-print { display: none; }
        }
        
        .btn-print { display: block; width: 200px; margin: 20px auto; padding: 10px; background-color: #1e40af; color: white; text-align: center; text-decoration: none; border-radius: 5px; font-family: Arial, sans-serif; cursor: pointer; border: none; font-weight: bold; }
        .btn-print:hover { background-color: #1e3a8a; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h2>Kwitansi Setoran Iuran</h2>
            <p>Tanggal Cetak: <?= date('d F Y H:i', strtotime($print_date)) ?></p>
        </div>
        
        <div class="content">
            <table>
                <tr>
                    <th>No. Referensi</th>
                    <td>: SET-<?= date('Ymd', strtotime($setoran['tanggal_setoran'])) ?>-<?= str_pad($setoran['id'], 4, '0', STR_PAD_LEFT) ?></td>
                </tr>
                <tr>
                    <th>Tanggal Setoran</th>
                    <td>: <?= date('d F Y H:i', strtotime($setoran['tanggal_setoran'])) ?></td>
                </tr>
                <tr>
                    <th>Nama Penyetor</th>
                    <td>: <?= esc($user['nama'] ?? 'User') ?></td>
                </tr>
                <tr>
                    <th>Periode Iuran</th>
                    <td>: <?= $periode ? esc($periode['nama_periode']) : '-' ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>: <?= strtoupper($setoran['status_setoran']) ?></td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>: <?= empty($setoran['keterangan']) ? '-' : esc($setoran['keterangan']) ?></td>
                </tr>
            </table>
        </div>
        
        <div class="amount">
            TOTAL: Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?>
        </div>
        
        <div class="footer">
            <p>Terima kasih atas partisipasi Anda.</p>
            <p>Kwitansi ini adalah bukti pembayaran yang sah dan diterbitkan secara otomatis oleh sistem.</p>
        </div>
    </div>
    
    <button class="btn-print" onclick="window.print()">
        Cetak Kwitansi
    </button>
</body>
</html>
