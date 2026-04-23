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
        $settings = getSettings($pdo);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../uploads/logo/<?php echo $settings['logo']; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 min-h-screen flex">

    <?php include 'components/sidebar.php'; ?>

    <main class="flex-1 md:ml-64 p-4 md:p-10 pt-20 md:pt-10">
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sekolah</h1>
            <p class="text-slate-500 mt-1 font-medium">Konfigurasi identitas sekolah dan waktu pengumuman.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden p-8">
            <?php if ($message): ?>
                <script>
                    Swal.fire('Berhasil!', '<?php echo $message; ?>', 'success');
                </script>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" value="<?php echo htmlspecialchars($settings['nama_sekolah']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Waktu Pengumuman Buka</label>
                        <input type="datetime-local" name="tgl_pengumuman" value="<?php echo date('Y-m-d\TH:i', strtotime($settings['tgl_pengumuman'])); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Sekolah</label>
                    <textarea name="alamat_sekolah" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required><?php echo htmlspecialchars($settings['alamat_sekolah']); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Logo Sekolah (.png)</label>
                    <div class="flex items-center space-x-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                        <img src="../uploads/logo/<?php echo $settings['logo']; ?>" alt="Logo" class="h-24 w-24 object-contain bg-white p-2 rounded-xl shadow-sm">
                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/png" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                            <p class="text-xs text-slate-400 mt-2">Format PNG, ukuran maksimal 2MB disarankan.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-indigo-200 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
