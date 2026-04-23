<?php
require_once 'auth.php';
$settings = getSettings($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_sekolah = $_POST['nama_sekolah'];
    $alamat_sekolah = $_POST['alamat_sekolah'];
    $tgl_pengumuman = $_POST['tgl_pengumuman'];
    $logo_name = $settings['logo'];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext === 'png') {
            $new_name = 'logo_' . time() . '.png';
            if (move_uploaded_file($file_tmp, '../uploads/logo/' . $new_name)) {
                // Delete old logo if it's not the default
                if ($settings['logo'] !== 'default-logo.png' && file_exists('../uploads/logo/' . $settings['logo'])) {
                    unlink('../uploads/logo/' . $settings['logo']);
                }
                $logo_name = $new_name;
            }
        } else {
            $message = "Hanya file PNG yang diperbolehkan.";
        }
    }

    if (empty($message)) {
        $stmt = $pdo->prepare("UPDATE pengaturan SET nama_sekolah = ?, alamat_sekolah = ?, logo = ?, tgl_pengumuman = ? WHERE id = ?");
        $stmt->execute([$nama_sekolah, $alamat_sekolah, $logo_name, $tgl_pengumuman, $settings['id']]);
        $message = "Pengaturan berhasil diperbarui!";
        $settings = getSettings($pdo); // Refresh settings
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Admin Panel</span>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php" class="hover:bg-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Siswa</a>
                    <a href="pengaturan.php" class="bg-indigo-800 px-3 py-2 rounded-md text-sm font-medium">Pengaturan</a>
                    <a href="logout.php" class="hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto py-10 px-4">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Pengaturan Umum</h2>

            <?php if ($message): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" value="<?php echo htmlspecialchars($settings['nama_sekolah']); ?>" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Waktu Pengumuman Buka</label>
                        <input type="datetime-local" name="tgl_pengumuman" value="<?php echo date('Y-m-d\TH:i', strtotime($settings['tgl_pengumuman'])); ?>" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Sekolah</label>
                    <textarea name="alamat_sekolah" rows="3" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required><?php echo htmlspecialchars($settings['alamat_sekolah']); ?></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Logo Sekolah (.png)</label>
                    <div class="flex items-center space-x-4">
                        <img src="../uploads/logo/<?php echo $settings['logo']; ?>" alt="Logo" class="h-20 w-20 object-contain border p-1 rounded">
                        <input type="file" name="logo" accept="image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md transition duration-300">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
