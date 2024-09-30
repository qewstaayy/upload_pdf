<?php
// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root"; // Sesuaikan dengan username database kamu
$password = ""; // Sesuaikan dengan password database kamu
$dbname = "pdf_database"; // Nama database

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah form telah disubmit
if (isset($_POST['submit'])) {
    // Memeriksa apakah file telah diupload
    if (isset($_FILES['pdf']['name'][0]) && $_FILES['pdf']['error'][0] === UPLOAD_ERR_OK) {
        // Loop melalui semua file yang diupload
        $totalFiles = count($_FILES['pdf']['name']);
        for ($i = 0; $i < $totalFiles; $i++) {
            $fileTmpPath = $_FILES['pdf']['tmp_name'][$i];
            $fileName = $_FILES['pdf']['name'][$i];
            $fileSize = $_FILES['pdf']['size'][$i];
            $fileType = $_FILES['pdf']['type'][$i];

            // Cek apakah file yang diupload adalah file PDF
            $allowedFileTypes = ['application/pdf'];
            if (in_array($fileType, $allowedFileTypes)) {
                // Tentukan direktori penyimpanan file
                $uploadDir = 'uploads/';
                $filePath = $uploadDir . $fileName;

                // Pindahkan file ke direktori tujuan
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    // Simpan nama file ke database
                    $stmt = $conn->prepare("INSERT INTO pdf_files (file_name, file_path) VALUES (?, ?)");
                    $stmt->bind_param("ss", $fileName, $filePath);

                    if ($stmt->execute()) {
                        echo "File $fileName berhasil diupload dan disimpan ke database.<br>";
                    } else {
                        echo "Gagal menyimpan file $fileName ke database.<br>";
                    }

                    $stmt->close();
                } else {
                    echo "Terjadi kesalahan saat memindahkan file $fileName.<br>";
                }
            } else {
                echo "File $fileName bukan file PDF yang valid.<br>";
            }
        }
    } else {
        echo "Tidak ada file yang diupload.";
    }
}

// Tutup koneksi
$conn->close();
?>
