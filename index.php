<?php
require_once 'config.php';

if (isset($_GET['action']) && $_GET['action'] === 'search') {
    $nisn = $_GET['nisn'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE nisn = ?");
    $stmt->execute([$nisn]);
    $siswa = $stmt->fetch();

    if ($siswa) {
        echo json_encode(['status' => 'success', 'data' => $siswa]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'NISN tidak ditemukan atau Anda tidak terdaftar.']);
    }
    exit;
}

$settings = getSettings($pdo);
$waktu_buka = $settings['tgl_pengumuman'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Kelulusan - <?php echo $settings['nama_sekolah']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm py-4">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="uploads/logo/<?php echo $settings['logo']; ?>" alt="Logo" class="h-12 w-12 object-contain">
                <div>
                    <h1 class="text-xl font-bold text-gray-800 leading-tight"><?php echo $settings['nama_sekolah']; ?></h1>
                    <p class="text-xs text-gray-500"><?php echo $settings['alamat_sekolah']; ?></p>
                </div>
            </div>
            <a href="admin/login.php" class="text-gray-400 hover:text-indigo-600 transition">
                <i class="fas fa-user-shield text-xl"></i>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">

            <!-- Countdown Section -->
            <div id="countdownSection" class="hidden text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Pengumuman Akan Dibuka Dalam:</h2>
                <div class="flex justify-center space-x-4">
                    <div class="bg-white p-4 rounded-xl shadow-md w-20 md:w-24">
                        <span id="days" class="block text-3xl md:text-4xl font-bold text-indigo-600">00</span>
                        <span class="text-xs text-gray-500 uppercase">Hari</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md w-20 md:w-24">
                        <span id="hours" class="block text-3xl md:text-4xl font-bold text-indigo-600">00</span>
                        <span class="text-xs text-gray-500 uppercase">Jam</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md w-20 md:w-24">
                        <span id="minutes" class="block text-3xl md:text-4xl font-bold text-indigo-600">00</span>
                        <span class="text-xs text-gray-500 uppercase">Menit</span>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md w-20 md:w-24">
                        <span id="seconds" class="block text-3xl md:text-4xl font-bold text-indigo-600">00</span>
                        <span class="text-xs text-gray-500 uppercase">Detik</span>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div id="searchSection" class="hidden glass rounded-3xl shadow-2xl overflow-hidden">
                <div class="bg-gradient p-8 text-white text-center">
                    <h2 class="text-3xl font-extrabold mb-2">Cek Kelulusan</h2>
                    <p class="opacity-90">Masukkan NISN Anda untuk melihat hasil pengumuman</p>
                </div>
                <div class="p-8">
                    <form id="searchForm" class="space-y-4">
                        <div class="relative">
                            <input type="text" id="nisn" name="nisn" placeholder="Masukkan NISN"
                                   class="w-full pl-5 pr-12 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:outline-none focus:border-indigo-500 transition-all text-lg font-medium" required>
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-indigo-600 text-white p-3 rounded-xl hover:bg-indigo-700 transition shadow-lg">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Result Section (Dynamic) -->
            <div id="resultCard" class="hidden mt-8 glass rounded-3xl shadow-2xl p-8 transform transition-all duration-500 border-t-8 border-indigo-500">
                <div class="text-center">
                    <div id="statusIcon" class="mb-4">
                        <!-- Icon will be inserted here -->
                    </div>
                    <h3 class="text-sm uppercase tracking-widest text-gray-500 font-bold mb-1">Hasil Pengumuman</h3>
                    <h2 id="studentName" class="text-3xl font-black text-gray-800 mb-2">NAMA SISWA</h2>
                    <p id="studentNisn" class="text-gray-500 mb-6 font-medium">NISN: 0000000000</p>

                    <div id="statusBadge" class="inline-block px-8 py-3 rounded-full text-xl font-bold mb-8">
                        LULUS
                    </div>

                    <div id="downloadArea">
                        <a id="downloadBtn" href="#" target="_blank" class="flex items-center justify-center space-x-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl transition shadow-xl">
                            <i class="fas fa-file-download"></i>
                            <span>Download SKL</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="py-6 text-center text-gray-500 text-sm">
        &copy; <?php echo date('Y'); ?> <?php echo $settings['nama_sekolah']; ?>. All rights reserved.
    </footer>

    <script>
        const targetDate = new Date("<?php echo $waktu_buka; ?>").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance > 0) {
                document.getElementById('countdownSection').classList.remove('hidden');
                document.getElementById('searchSection').classList.add('hidden');

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').innerText = days.toString().padStart(2, '0');
                document.getElementById('hours').innerText = hours.toString().padStart(2, '0');
                document.getElementById('minutes').innerText = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').innerText = seconds.toString().padStart(2, '0');
            } else {
                document.getElementById('countdownSection').classList.add('hidden');
                document.getElementById('searchSection').classList.remove('hidden');
                clearInterval(countdownInterval);
            }
        }

        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();

        document.getElementById('searchForm').onsubmit = async (e) => {
            e.preventDefault();
            const nisn = document.getElementById('nisn').value;

            Swal.fire({
                title: 'Mencari Data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const res = await fetch(`index.php?action=search&nisn=${nisn}`);
                const result = await res.json();

                Swal.close();

                if (result.status === 'success') {
                    const s = result.data;
                    document.getElementById('studentName').innerText = s.nama;
                    document.getElementById('studentNisn').innerText = `NISN: ${s.nisn}`;

                    const badge = document.getElementById('statusBadge');
                    const iconDiv = document.getElementById('statusIcon');
                    const downloadArea = document.getElementById('downloadArea');
                    const resultCard = document.getElementById('resultCard');

                    if (s.status === 'LULUS') {
                        badge.innerText = 'LULUS';
                        badge.className = 'inline-block px-8 py-3 rounded-full text-xl font-bold mb-8 bg-green-100 text-green-700 border-2 border-green-200';
                        iconDiv.innerHTML = '<div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fas fa-check-circle"></i></div>';
                        downloadArea.classList.remove('hidden');
                        document.getElementById('downloadBtn').href = s.link_skl;
                        resultCard.style.borderColor = '#10B981';
                    } else {
                        badge.innerText = 'TIDAK LULUS';
                        badge.className = 'inline-block px-8 py-3 rounded-full text-xl font-bold mb-8 bg-red-100 text-red-700 border-2 border-red-200';
                        iconDiv.innerHTML = '<div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl"><i class="fas fa-times-circle"></i></div>';
                        downloadArea.classList.add('hidden');
                        resultCard.style.borderColor = '#EF4444';
                    }

                    resultCard.classList.remove('hidden');
                    resultCard.scrollIntoView({ behavior: 'smooth' });
                } else {
                    document.getElementById('resultCard').classList.add('hidden');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.message,
                        confirmButtonColor: '#4F46E5'
                    });
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memproses permintaan.', 'error');
            }
        };
    </script>
</body>
</html>
