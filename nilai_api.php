<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Headers: Content-Type');
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

require_once 'koneksi.php'; // File untuk membuat koneksi ke database
switch ($method) {
    case 'GET':
        if (isset($_GET['nim'])) {
            // Menampilkan nilai mahasiswa tertentu (berdasarkan parameter nim)
            $nim = $_GET['nim'];
            $sql = "SELECT * FROM data_kampus WHERE nim = '$nim'";
            $result = $conn->query($sql);
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            // Menampilkan semua nilai mahasiswa
            $sql = "SELECT * FROM data_kampus";
            $result = $conn->query($sql);
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;

    case 'POST':
        // Memasukkan nilai baru untuk mahasiswa tertentu
        $nim = $data['nim'];
        $kode_mk = $data['kode_mk'];
        $nilai = $data['nilai'];
        $sql = "INSERT INTO perkuliahan (nim, kode_mk, nilai) VALUES ('$nim', '$kode_mk', $nilai)";
        if ($conn->query($sql) === TRUE) {
            $response = array('message' => 'Data nilai berhasil ditambahkan');
        } else {
            $response = array('message' => 'Gagal menambahkan data nilai');
        }
        echo json_encode($response);
        break;

    case 'PUT':
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405); // Method Not Allowed
        exit();
    }
    
    // Mendapatkan data dari body permintaan
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Memastikan bahwa data yang dibutuhkan tersedia
    if (!isset($data['nim']) || !isset($data['kode_mk']) || !isset($data['nilai'])) {
        http_response_code(400); // Bad Request
        echo json_encode(array('message' => 'Parameter yang diperlukan tidak lengkap.'));
        exit();
    }
    
    $nim = $conn->real_escape_string($data['nim']);
    $kode_mk = $conn->real_escape_string($data['kode_mk']);
    $nilai = $conn->real_escape_string($data['nilai']);
    
    // Query untuk melakukan update nilai
    $sql = "UPDATE perkuliahan SET nilai = '$nilai' WHERE nim = '$nim' AND kode_mk = '$kode_mk'";
    
    if ($conn->query($sql) === TRUE) {
        http_response_code(200); // OK
        echo json_encode(array('message' => 'Data nilai berhasil diupdate'));
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(array('message' => 'Gagal mengupdate data nilai: ' . $conn->error));
    }
    break;



    case 'DELETE':
        // Mendapatkan data dari body permintaan
        $deleteData = json_decode(file_get_contents('php://input'), true);
        
        // Memastikan bahwa data yang dibutuhkan tersedia
        if (!isset($deleteData['nim']) || !isset($deleteData['kode_mk'])) {
            http_response_code(400); // Bad Request
            echo json_encode(array('message' => 'Parameter yang diperlukan tidak lengkap.'));
            exit();
        }
        
        $nim = $conn->real_escape_string($deleteData['nim']);
        $kode_mk = $conn->real_escape_string($deleteData['kode_mk']);
        
        // Query untuk menghapus nilai berdasarkan nim dan kode_mk
        $sql = "DELETE FROM perkuliahan WHERE nim = '$nim' AND kode_mk = '$kode_mk'";
        
        if ($conn->query($sql) === TRUE) {
            http_response_code(200); // OK
            echo json_encode(array('message' => 'Data nilai berhasil dihapus'));
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(array('message' => 'Gagal menghapus data nilai: ' . $conn->error));
        }
        break;
    
}

$conn->close();