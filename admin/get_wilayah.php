<?php
header('Content-Type: application/json');
include '../koneksi.php';

$tipe = $_GET['tipe'] ?? '';
$id = $_GET['id'] ?? '';

if (!$tipe || !$id) {
    echo json_encode(["error" => "Parameter tidak lengkap"]);
    exit;
}

$data = [];

switch ($tipe) {
    case 'kabupaten':
        $stmt = $conn->prepare("SELECT kode_kabupaten, nama_kabupaten FROM wilayah_kabupaten WHERE kode_provinsi = ?");
        $stmt->bind_param("s", $id);
        break;
    case 'kecamatan':
        $stmt = $conn->prepare("SELECT kode_kecamatan, nama_kecamatan FROM wilayah_kecamatan WHERE kode_kabupaten = ?");
        $stmt->bind_param("s", $id);
        break;
    case 'kelurahan':
        $stmt = $conn->prepare("SELECT kode_kelurahan, nama_kelurahan FROM wilayah_kelurahan WHERE kode_kecamatan = ?");
        $stmt->bind_param("s", $id);
        break;
    default:
        echo json_encode(["error" => "Tipe tidak valid"]);
        exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
