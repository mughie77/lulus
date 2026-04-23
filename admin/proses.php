<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

if (!isset($_SESSION['admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    $search = $_GET['search'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $where = "";
    $params = [];
    if (!empty($search)) {
        $where = "WHERE nama LIKE ? OR nisn LIKE ?";
        $params = ["%$search%", "%$search%"];
    }

    // Get total rows
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa $where");
    $count_stmt->execute($params);
    $total_rows = $count_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // Get data
    $stmt = $pdo->prepare("SELECT * FROM siswa $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    echo json_encode([
        'data' => $data,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'total_rows' => $total_rows
    ]);
}

elseif ($action === 'add') {
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $link_skl = $_POST['link_skl'];
    $status = $_POST['status'];

    // Validation for Google Drive or PDF
    if (!filter_var($link_skl, FILTER_VALIDATE_URL) ||
        !(strpos($link_skl, 'drive.google.com') !== false || strtolower(pathinfo($link_skl, PATHINFO_EXTENSION)) === 'pdf')) {
        echo json_encode(['status' => 'error', 'message' => 'Link SKL harus berupa link Google Drive atau file PDF.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO siswa (nisn, nama, link_skl, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nisn, $nama, $link_skl, $status]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'NISN sudah ada atau data tidak valid.']);
    }
}

elseif ($action === 'update') {
    $id = $_POST['id'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $link_skl = $_POST['link_skl'];
    $status = $_POST['status'];

    // Validation for Google Drive or PDF
    if (!filter_var($link_skl, FILTER_VALIDATE_URL) ||
        !(strpos($link_skl, 'drive.google.com') !== false || strtolower(pathinfo($link_skl, PATHINFO_EXTENSION)) === 'pdf')) {
        echo json_encode(['status' => 'error', 'message' => 'Link SKL harus berupa link Google Drive atau file PDF.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE siswa SET nisn = ?, nama = ?, link_skl = ?, status = ? WHERE id = ?");
        $stmt->execute([$nisn, $nama, $link_skl, $status, $id]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data.']);
    }
}

elseif ($action === 'delete') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success']);
}

elseif ($action === 'import') {
    if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] === UPLOAD_ERR_OK) {
        if ($xlsx = SimpleXLSX::parse($_FILES['file_excel']['tmp_name'])) {
            $rows = $xlsx->rows();
            array_shift($rows); // Remove header

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO siswa (nisn, nama, link_skl, status) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nama = VALUES(nama), link_skl = VALUES(link_skl), status = VALUES(status)");
                foreach ($rows as $row) {
                    if (count($row) >= 4) {
                        $stmt->execute([$row[0], $row[1], $row[2], $row[3]]);
                    }
                }
                $pdo->commit();
                echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengimpor data: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => SimpleXLSX::parseError()]);
        }
    }
}
?>
