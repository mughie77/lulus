<?php
require_once 'auth.php';

$output = '';
$git_url = "https://github.com/mughie77/lulus.git";

if (isset($_POST['update'])) {
    // Comprehensive Git Update Command
    // 1. Navigate to root
    // 2. Initialize if needed
    // 3. Set/Update remote
    // 4. Clean local changes that might block reset
    // 5. Fetch and Reset
    $cmd = "cd .. && ";
    if (!is_dir('../.git')) {
        $cmd .= "git init && git remote add origin $git_url && ";
    } else {
        $cmd .= "git remote set-url origin $git_url && ";
    }
    $cmd .= "git fetch --all && git clean -fd && (git reset --hard origin/main || git reset --hard origin/master) 2>&1";

    $git_output = shell_exec($cmd);

    // Check if update was actually successful
    if (strpos($git_output, 'HEAD is now at') !== false || strpos($git_output, 'Already up to date') !== false) {
        $_SESSION['git_output'] = $git_output;
        header("Location: system_update.php?updated=true");
    } else {
        $_SESSION['git_error'] = $git_output;
        header("Location: system_update.php?error=true");
    }
    exit;
}

// DB Sync Logic
$db_message = '';
if (isset($_POST['sync_db'])) {
    require_once 'db_sync.php';
    $db_message = syncDatabase($pdo);
}

$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Update - Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../uploads/logo/<?php echo $settings['logo']; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 min-h-screen flex">

    <?php include 'components/sidebar.php'; ?>

    <main class="flex-1 md:ml-64 p-4 md:p-10 pt-20 md:pt-10">
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">System Update</h1>
            <p class="text-slate-500 mt-1 font-medium">Perbarui kode aplikasi dan sinkronisasi database.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Git Update Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 flex flex-col justify-between">
                <div>
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 text-2xl">
                        <i class="fab fa-github"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Update Kode (Git)</h2>
                    <p class="text-slate-500 mb-6">Mengambil kode terbaru dari repository GitHub dan menimpa perubahan lokal.</p>
                    <p class="text-xs font-mono bg-slate-50 p-3 rounded-lg text-slate-400 break-all"><?php echo $git_url; ?></p>
                </div>
                <form method="POST" class="mt-8">
                    <button type="submit" name="update" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i> Jalankan Update
                    </button>
                </form>
            </div>

            <!-- DB Sync Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 flex flex-col justify-between">
                <div>
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 text-2xl">
                        <i class="fas fa-database"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-2">Sinkronisasi Database</h2>
                    <p class="text-slate-500 mb-6">Mengecek dan memperbarui struktur tabel serta field agar sesuai dengan versi terbaru.</p>
                </div>
                <form method="POST" class="mt-8">
                    <button type="submit" name="sync_db" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all flex items-center justify-center">
                        <i class="fas fa-sync mr-2"></i> Sync Database
                    </button>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <script>
                Swal.fire({
                    title: 'Update Berhasil!',
                    text: 'Kode sistem telah diperbarui ke versi terbaru.',
                    icon: 'success',
                    footer: '<div class="w-full text-left"><p class="text-xs font-bold mb-1">Terminal Output:</p><pre class="text-[10px] bg-slate-900 text-slate-300 p-3 rounded-lg w-full max-h-40 overflow-auto">' + `<?php echo addslashes($_SESSION['git_output'] ?? ""); ?>` + '</pre></div>'
                });
            </script>
            <?php unset($_SESSION['git_output']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <script>
                Swal.fire({
                    title: 'Update Gagal!',
                    text: 'Terjadi kesalahan saat melakukan update dari Git.',
                    icon: 'error',
                    footer: '<div class="w-full text-left"><p class="text-xs font-bold mb-1 text-red-600">Error Output:</p><pre class="text-[10px] bg-red-50 text-red-800 p-3 rounded-lg border border-red-100 w-full max-h-40 overflow-auto">' + `<?php echo addslashes($_SESSION['git_error'] ?? ""); ?>` + '</pre></div>'
                });
            </script>
            <?php unset($_SESSION['git_error']); ?>
        <?php endif; ?>

        <?php if ($db_message): ?>
            <div class="mt-8 bg-slate-800 text-slate-200 p-6 rounded-3xl font-mono text-sm shadow-xl overflow-x-auto">
                <h3 class="text-emerald-400 mb-4 font-bold">Log Sinkronisasi Database:</h3>
                <?php echo nl2br(htmlspecialchars($db_message)); ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
