<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-end { text-align: right; }
        .footer { margin-top: 30px; text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 15px; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 8px 15px; cursor: pointer;">Tutup</button>
    </div>

    <div class="header">
        <h2>LAPORAN REKAPITULASI SETORAN IURAN</h2>
        <p>Tanggal Cetak: <?= esc($tanggal) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Pengguna</th>
                <th>Periode</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($setoranList)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data setoran.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; $total = 0; foreach ($setoranList as $row): ?>
                    <?php if ($row['status_setoran'] !== 'dibatalkan') $total += (float)$row['nominal']; ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_setoran'])) ?></td>
                        <td><?= esc($row['user_name']) ?></td>
                        <td><?= esc($row['nama_periode']) ?></td>
                        <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                        <td><?= ucfirst($row['status_setoran']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL KESELURUHAN</td>
                    <td colspan="2" style="font-weight: bold;">Rp <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak Oleh: Administrator</p>
    </div>
</body>
</html>
