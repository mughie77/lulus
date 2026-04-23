<?php
require_once 'config.php';

if (isset($_GET['action']) && $_GET['action'] === 'search') {
    $keyword = $_GET['keyword'] ?? '';

    // Check by NISN first
    $stmt = $pdo->prepare("SELECT * FROM siswa WHERE nisn = ?");
    $stmt->execute([$keyword]);
    $siswa = $stmt->fetch();
    $found_by = 'nisn';

    if (!$siswa) {
        // If not found by NISN, check by Name
        $stmt = $pdo->prepare("SELECT * FROM siswa WHERE nama LIKE ?");
        $stmt->execute(["%$keyword%"]);
        $siswa = $stmt->fetch();
        $found_by = 'nama';
    }

    if ($siswa) {
        echo json_encode(['status' => 'success', 'data' => $siswa, 'found_by' => $found_by]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau Anda tidak terdaftar.']);
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
    <link rel="icon" type="image/png" href="uploads/logo/<?php echo $settings['logo']; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @keyframes gradient-animate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .bg-dynamic {
            background: linear-gradient(-45deg, #4f46e5, #7c3aed, #2563eb, #0891b2);
            background-size: 400% 400%;
            animation: gradient-animate 15s ease infinite;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-2 bg-indigo-50 rounded-2xl">
                    <img src="uploads/logo/<?php echo $settings['logo']; ?>" alt="Logo" class="h-10 w-10 object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-800 uppercase tracking-tighter leading-none"><?php echo $settings['nama_sekolah']; ?></h1>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1"><?php echo $settings['alamat_sekolah']; ?></p>
                </div>
            </div>
            <a href="admin/login.php" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                <i class="fas fa-shield-alt"></i>
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <main id="heroMain" class="flex-grow flex items-center justify-center p-4 md:p-6 bg-dynamic relative overflow-hidden transition-all duration-500">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-3xl w-full relative z-10">

            <!-- Countdown Section -->
            <div id="countdownSection" class="hidden text-center">
                <div class="inline-block px-6 py-2 bg-white/20 backdrop-blur-md rounded-full text-white text-sm font-bold uppercase tracking-widest mb-8 border border-white/20">
                    Pengumuman Akan Dibuka
                </div>
                <div class="grid grid-cols-4 gap-4 md:gap-8 mb-12">
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-[2rem] shadow-2xl">
                        <span id="days" class="block text-4xl md:text-6xl font-black text-white mb-2">00</span>
                        <span class="text-[10px] md:text-xs text-white/60 font-bold uppercase tracking-widest">Hari</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-[2rem] shadow-2xl">
                        <span id="hours" class="block text-4xl md:text-6xl font-black text-white mb-2">00</span>
                        <span class="text-[10px] md:text-xs text-white/60 font-bold uppercase tracking-widest">Jam</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-[2rem] shadow-2xl">
                        <span id="minutes" class="block text-4xl md:text-6xl font-black text-white mb-2">00</span>
                        <span class="text-[10px] md:text-xs text-white/60 font-bold uppercase tracking-widest">Menit</span>
                    </div>
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-[2rem] shadow-2xl">
                        <span id="seconds" class="block text-4xl md:text-6xl font-black text-white mb-2">00</span>
                        <span class="text-[10px] md:text-xs text-white/60 font-bold uppercase tracking-widest">Detik</span>
                    </div>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-white drop-shadow-lg">Persiapkan diri Anda untuk hasil terbaik!</h2>
            </div>

            <!-- Search Section -->
            <div id="searchSection" class="hidden glass-card rounded-[2rem] md:rounded-[3rem] shadow-2xl overflow-hidden p-6 md:p-12 animate-in fade-in slide-in-from-bottom-10 duration-700 w-full">
                <div class="text-center mb-8 md:mb-10">
                    <h2 class="text-3xl md:text-4xl font-black text-slate-800 mb-4 tracking-tighter">Cek Hasil Kelulusan</h2>
                    <p class="text-slate-500 font-medium text-sm md:text-base max-w-md mx-auto">Masukkan NISN atau Nama Anda untuk melihat status kelulusan.</p>
                </div>

                <form id="searchForm" class="relative max-w-lg mx-auto">
                    <div class="relative group">
                        <input type="text" id="keyword" name="keyword" placeholder="NISN atau Nama Lengkap"
                               class="w-full pl-6 md:pl-8 pr-16 md:pr-20 py-4 md:py-6 bg-slate-50 border-2 border-slate-100 rounded-2xl md:rounded-3xl focus:outline-none focus:border-indigo-500 focus:bg-white transition-all text-base md:text-xl font-bold text-slate-800 shadow-inner" required>
                        <button type="submit" class="absolute right-2 top-2 bottom-2 md:right-3 md:top-3 md:bottom-3 bg-indigo-600 text-white px-4 md:px-6 rounded-xl md:rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95">
                            <i class="fas fa-search md:text-xl"></i>
                        </button>
                    </div>
                </form>

                <!-- Result Area (Dynamic) -->
                <div id="resultCard" class="hidden mt-8 md:mt-12 pt-8 md:pt-12 border-t border-slate-100 animate-in fade-in zoom-in duration-500">
                    <div class="text-center">
                        <div id="statusIcon" class="mb-4 md:mb-6 scale-90 md:scale-110"></div>
                        <h3 class="text-[10px] uppercase tracking-[0.3em] text-slate-400 font-black mb-1 md:mb-2">Pernyataan Kelulusan</h3>
                        <h2 id="studentName" class="text-2xl md:text-4xl font-black text-slate-900 mb-1 md:mb-2 tracking-tight">NAMA SISWA</h2>
                        <p id="studentNisn" class="text-slate-400 mb-6 md:mb-8 font-bold text-xs md:text-sm tracking-widest">NISN: 0000000000</p>

                        <div id="statusBadge" class="inline-block px-8 py-3 md:px-12 md:py-4 rounded-2xl md:rounded-3xl text-xl md:text-2xl font-black mb-8 md:mb-10 shadow-lg">
                            LULUS
                        </div>

                        <div id="downloadArea" class="px-2">
                            <a id="downloadBtn" href="#" target="_blank" class="flex items-center justify-center space-x-3 w-full max-w-sm mx-auto bg-slate-900 hover:bg-indigo-600 text-white font-black py-4 md:py-5 rounded-2xl md:rounded-[2rem] transition-all duration-300 shadow-2xl hover:-translate-y-1">
                                <i class="fas fa-file-pdf text-xl"></i>
                                <span>DOWNLOAD SKL (PDF)</span>
                            </a>
                            <div id="downloadRestriction" class="hidden mt-4 p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                                <p class="text-amber-700 text-xs md:text-sm font-bold italic">
                                    <i class="fas fa-info-circle mr-1"></i> Untuk mengunduh SKL, silakan cari menggunakan NISN.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="py-10 bg-white text-center">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.2em]">&copy; <?php echo date('Y'); ?> <?php echo $settings['nama_sekolah']; ?></p>
        <div class="mt-4 flex justify-center space-x-6 text-slate-300">
            <i class="fab fa-facebook hover:text-indigo-500 cursor-pointer transition"></i>
            <i class="fab fa-instagram hover:text-pink-500 cursor-pointer transition"></i>
            <i class="fab fa-twitter hover:text-blue-400 cursor-pointer transition"></i>
        </div>
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
            const keyword = document.getElementById('keyword').value;

            Swal.fire({
                title: 'Memproses...',
                text: 'Mencari data di server',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const res = await fetch(`index.php?action=search&keyword=${keyword}`);
                const result = await res.json();

                Swal.close();

                if (result.status === 'success') {
                    const s = result.data;
                    const found_by = result.found_by;

                    document.getElementById('studentName').innerText = s.nama;
                    document.getElementById('studentNisn').innerText = `NISN: ${s.nisn}`;

                    const badge = document.getElementById('statusBadge');
                    const iconDiv = document.getElementById('statusIcon');
                    const downloadBtn = document.getElementById('downloadBtn');
                    const restriction = document.getElementById('downloadRestriction');
                    const resultCard = document.getElementById('resultCard');

                    if (s.status === 'LULUS') {
                        badge.innerText = 'LULUS';
                        badge.className = 'inline-block px-8 py-3 md:px-12 md:py-4 rounded-2xl md:rounded-3xl text-xl md:text-2xl font-black mb-8 md:mb-10 bg-emerald-100 text-emerald-700 shadow-emerald-100';
                        iconDiv.innerHTML = '<div class="w-20 h-20 md:w-24 md:h-24 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-4xl md:text-5xl shadow-xl shadow-emerald-200 animate-bounce"><i class="fas fa-check"></i></div>';

                        if (found_by === 'nisn') {
                            downloadBtn.classList.remove('hidden');
                            downloadBtn.href = s.link_skl;
                            restriction.classList.add('hidden');
                        } else {
                            downloadBtn.classList.add('hidden');
                            restriction.classList.remove('hidden');
                        }
                    } else {
                        badge.innerText = 'TIDAK LULUS';
                        badge.className = 'inline-block px-8 py-3 md:px-12 md:py-4 rounded-2xl md:rounded-3xl text-xl md:text-2xl font-black mb-8 md:mb-10 bg-rose-100 text-rose-700 shadow-rose-100';
                        iconDiv.innerHTML = '<div class="w-20 h-20 md:w-24 md:h-24 bg-rose-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-4xl md:text-5xl shadow-xl shadow-rose-200"><i class="fas fa-times"></i></div>';
                        downloadBtn.classList.add('hidden');
                        restriction.classList.add('hidden');
                    }

                    resultCard.classList.remove('hidden');

                    // Full screen effect on mobile
                    if (window.innerWidth < 768) {
                        document.getElementById('heroMain').classList.remove('p-4');
                        document.getElementById('heroMain').classList.add('p-0');
                        document.getElementById('searchSection').classList.remove('rounded-[2rem]');
                        document.getElementById('searchSection').classList.add('rounded-none', 'min-h-screen');
                    }

                    resultCard.scrollIntoView({ behavior: 'smooth' });
                } else {
                    document.getElementById('resultCard').classList.add('hidden');
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Ditemukan',
                        text: result.message,
                        confirmButtonColor: '#4F46E5',
                        customClass: {
                            popup: 'rounded-[2rem]',
                            confirmButton: 'rounded-xl px-8 py-3'
                        }
                    });
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memproses permintaan.', 'error');
            }
        };
    </script>
</body>
</html>
