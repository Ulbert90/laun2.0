<?php
require_once("../assets/dompdf/autoload.inc.php");
include "../koneksi.php";

$dari = isset($_GET['dari']) ? htmlspecialchars($_GET['dari']) : '';
$sampai = isset($_GET['sampai']) ? htmlspecialchars($_GET['sampai']) : '';

$html = '<!DOCTYPE html>';
$html .= '<html>';
$html .= '<head>';
$html .= '<title>SISTEM INFORMASI FAMILY LAUNDRY</title>';
$html .= '</head>';
$html .= '<body>';

$html .= '<h4>Data Laporan Laundry dari <b>' . $dari . '</b> sampai <b>' . $sampai . '</b></h4>';
$html .= '<table border="1" width="100%">';
$html .= '<tr>';
$html .= '<th width="1">No</th>';
$html .= '<th>Invoice</th>';
$html .= '<th>Tanggal</th>';
$html .= '<th>Pelanggan</th>';
$html .= '<th>Berat (Kg)</th>';
$html .= '<th>Tgl. Selesai</th>';
$html .= '<th>Harga</th>';
$html .= '<th>Status</th>';
$html .= '</tr>';

// Validate database connection
if (!$koneksi) {
    die('Error in connection: ' . mysqli_connect_error());
}

$data = mysqli_query($koneksi, "SELECT * FROM pelanggan, transaksi WHERE transaksi_pelanggan = pelanggan_id AND DATE(transaksi_tgl) >= '$dari' AND DATE(transaksi_tgl) <= '$sampai' ORDER BY transaksi_id DESC");

if (!$data) {
    die('Error in query: ' . mysqli_error($koneksi));
}

$no = 1;

while ($d = mysqli_fetch_array($data)) {
    $html .= '<tr>';
    $html .= '<td>' . $no++ . '</td>';
    $html .= '<td>INVOICE-' . $d['transaksi_id'] . '</td>';
    $html .= '<td>' . htmlspecialchars($d['transaksi_tgl']) . '</td>';
    $html .= '<td>' . htmlspecialchars($d['pelanggan_nama']) . '</td>';
    $html .= '<td>' . htmlspecialchars($d['transaksi_berat']) . '</td>';
    $html .= '<td>' . htmlspecialchars($d['transaksi_tgl_selesai']) . '</td>';
    $html .= '<td>Rp. ' . number_format($d['transaksi_harga']) . '</td>';

    if ($d['transaksi_status'] == "0") {
        $html .= '<td>PROSES</td>';
    } elseif ($d['transaksi_status'] == "1") {
        $html .= '<td>DICUCI</td>';
    } elseif ($d['transaksi_status'] == "2") {
        $html .= '<td>SELESAI</td>';
    }

    $html .= '</tr>';
}

$html .= '</table>';
$html .= '</body>';
$html .= '</html>';

$dompdf = new Dompdf\Dompdf();
$dompdf->set_paper('a4', 'landscape');
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream('laporan_dari_' . $dari . '_sampai_' . $sampai . '.pdf');
?>
