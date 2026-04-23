<?php
require_once 'auth.php';
$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../uploads/logo/<?php echo $settings['logo']; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 min-h-screen flex">

    <?php include 'components/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 md:ml-64 p-4 md:p-10 pt-20 md:pt-10">
        <div class="mb-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Manajemen Siswa</h1>
                <p class="text-slate-500 mt-1 font-medium">Kelola data kelulusan siswa dengan mudah dan cepat.</p>
            </div>
            <div class="flex flex-wrap gap-3 items-center">
                <div class="relative">
                    <input type="text" id="adminSearch" placeholder="Cari Nama/NISN..." class="bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-64 shadow-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
                <button id="btnBulkDelete" onclick="deleteSelected()" class="hidden bg-rose-100 text-rose-600 hover:bg-rose-600 hover:text-white px-6 py-3 rounded-2xl transition-all font-bold flex items-center border border-rose-200">
                    <i class="fas fa-trash-alt mr-2"></i> Hapus Terpilih
                </button>
                <button onclick="openModal('add')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-indigo-200 transition-all font-bold flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Siswa
                </button>
                <button onclick="openModal('import')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-emerald-200 transition-all font-bold flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Import Excel
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <p class="text-slate-500 text-sm font-bold"><i class="fas fa-list-ul mr-2"></i>Total <span id="totalRows">0</span> Siswa</p>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-slate-400">Tampilkan:</span>
                    <select id="limitSelect" class="bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-600 focus:outline-none">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-5 text-left">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">No</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">NISN</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Nama</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">Link SKL</th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="divide-y divide-slate-100">
                        <!-- Data loaded via AJAX -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500 font-medium">Halaman <span id="currentPageNum" class="font-bold text-slate-900">1</span> dari <span id="totalPagesNum" class="font-bold text-slate-900">1</span></p>
                <div id="paginationButtons" class="flex items-center space-x-2">
                    <!-- Pagination buttons dynamic -->
                </div>
            </div>
        </div>
    </main>

    <!-- Modal forms remains largely the same but updated style -->
    <!-- (Modal content follows...) -->
    <div id="studentModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative mx-auto p-8 w-full max-w-md shadow-2xl rounded-3xl bg-white animate-in fade-in zoom-in duration-300">
            <h3 class="text-2xl font-bold text-slate-800 mb-6" id="modalTitle">Tambah Siswa</h3>
            <form id="studentForm" class="space-y-5">
                <input type="hidden" id="studentId" name="id">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" id="formNisn" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="formNama" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" id="formStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="LULUS">LULUS</option>
                        <option value="TIDAK LULUS">TIDAK LULUS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Link Google Drive SKL</label>
                    <input type="url" name="link_skl" id="formLink" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" required>
                </div>
                <div class="flex justify-end space-x-3 pt-6">
                    <button type="button" onclick="closeModal('studentModal')" class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative mx-auto p-8 w-full max-w-md shadow-2xl rounded-3xl bg-white animate-in fade-in zoom-in duration-300">
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Import Data Excel</h3>
            <p class="text-sm text-slate-500 mb-4">Pastikan urutan kolom: NISN, Nama, Link SKL, Status</p>
            <a href="proses.php?action=download_template" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center mb-6">
                <i class="fas fa-file-download mr-2"></i> Download Template Excel
            </a>
            <form id="importForm" class="space-y-5" enctype="multipart/form-data">
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-indigo-300 transition-colors">
                    <input type="file" name="file_excel" accept=".xlsx" class="hidden" id="excelInput" required>
                    <label for="excelInput" class="cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-600 font-medium">Klik untuk pilih file .xlsx</p>
                    </label>
                </div>
                <div class="flex justify-end space-x-3 pt-6">
                    <button type="button" onclick="closeModal('importModal')" class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition">Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentMode = 'add';
        let currentPage = 1;
        let currentSearch = '';
        let currentLimit = 10;

        async function fetchStudents() {
            const res = await fetch(`proses.php?action=fetch&page=${currentPage}&search=${currentSearch}&limit=${currentLimit}`);
            const result = await res.json();
            const tbody = document.getElementById('studentTableBody');
            const data = result.data;

            tbody.innerHTML = '';
            document.getElementById('selectAll').checked = false;
            toggleBulkDeleteBtn();

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-8 py-10 text-center text-slate-400 font-medium italic">Tidak ada data ditemukan.</td></tr>`;
            }

            data.forEach((s, i) => {
                const globalIndex = (currentPage - 1) * currentLimit + i + 1;
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <input type="checkbox" class="student-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" value="${s.id}" onchange="toggleBulkDeleteBtn()">
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-slate-400">${globalIndex}</td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-900">${s.nisn}</td>
                        <td class="px-8 py-5 text-sm font-semibold text-slate-700">${s.nama}</td>
                        <td class="px-8 py-5 text-sm">
                            <span class="px-4 py-1 inline-flex text-xs font-bold leading-5 rounded-full ${s.status === 'LULUS' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                                ${s.status}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-sm">
                            <a href="${s.link_skl}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-bold flex items-center">
                                <i class="fas fa-external-link-alt mr-2"></i> Link
                            </a>
                        </td>
                        <td class="px-8 py-5 text-sm text-center">
                            <button onclick='editStudent(${JSON.stringify(s)})' class="text-slate-400 hover:text-indigo-600 p-2 transition-colors"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteStudent(${s.id})" class="text-slate-400 hover:text-red-600 p-2 transition-colors"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            // Update Pagination UI
            document.getElementById('totalRows').innerText = result.total_rows;
            document.getElementById('currentPageNum').innerText = result.current_page;
            document.getElementById('totalPagesNum').innerText = result.total_pages;

            const paginationDiv = document.getElementById('paginationButtons');
            paginationDiv.innerHTML = '';

            // Prev Button
            const prevBtn = document.createElement('button');
            prevBtn.className = `p-2 w-10 h-10 rounded-xl flex items-center justify-center transition ${currentPage === 1 ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}`;
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevBtn.onclick = () => { if(currentPage > 1) { currentPage--; fetchStudents(); } };
            paginationDiv.appendChild(prevBtn);

            // Page numbers (simplified)
            for (let i = 1; i <= result.total_pages; i++) {
                if (i === 1 || i === result.total_pages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `w-10 h-10 rounded-xl font-bold transition ${i === currentPage ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}`;
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => { currentPage = i; fetchStudents(); };
                    paginationDiv.appendChild(pageBtn);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    const dots = document.createElement('span');
                    dots.className = 'w-10 h-10 flex items-center justify-center text-slate-400';
                    dots.innerText = '...';
                    paginationDiv.appendChild(dots);
                }
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = `p-2 w-10 h-10 rounded-xl flex items-center justify-center transition ${currentPage === result.total_pages || result.total_pages === 0 ? 'bg-slate-100 text-slate-300 cursor-not-allowed' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'}`;
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextBtn.onclick = () => { if(currentPage < result.total_pages) { currentPage++; fetchStudents(); } };
            paginationDiv.appendChild(nextBtn);
        }

        // Search listener
        let searchTimeout;
        document.getElementById('adminSearch').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = e.target.value;
                currentPage = 1;
                fetchStudents();
            }, 500);
        });

        // Limit listener
        document.getElementById('limitSelect').addEventListener('change', (e) => {
            currentLimit = e.target.value;
            currentPage = 1;
            fetchStudents();
        });

        // Select All listener
        document.getElementById('selectAll').addEventListener('change', (e) => {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            toggleBulkDeleteBtn();
        });

        function toggleBulkDeleteBtn() {
            const selected = document.querySelectorAll('.student-checkbox:checked').length;
            const btn = document.getElementById('btnBulkDelete');
            if (selected > 0) {
                btn.classList.remove('hidden');
                btn.innerHTML = `<i class="fas fa-trash-alt mr-2"></i> Hapus (${selected})`;
            } else {
                btn.classList.add('hidden');
            }
        }

        async function deleteSelected() {
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            const confirm = await Swal.fire({
                title: 'Hapus data terpilih?',
                text: `${ids.length} data siswa akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Semua!'
            });

            if (confirm.isConfirmed) {
                const formData = new FormData();
                ids.forEach(id => formData.append('ids[]', id));

                const res = await fetch('proses.php?action=delete_selected', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.status === 'success') {
                    Swal.fire('Terhapus!', 'Data terpilih berhasil dihapus.', 'success');
                    fetchStudents();
                }
            }
        }

        function openModal(mode) {
            currentMode = mode;
            if (mode === 'add') {
                document.getElementById('modalTitle').innerText = 'Tambah Siswa';
                document.getElementById('studentForm').reset();
                document.getElementById('studentId').value = '';
                document.getElementById('studentModal').classList.remove('hidden');
            } else if (mode === 'import') {
                document.getElementById('importModal').classList.remove('hidden');
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function editStudent(s) {
            currentMode = 'edit';
            document.getElementById('modalTitle').innerText = 'Edit Siswa';
            document.getElementById('studentId').value = s.id;
            document.getElementById('formNisn').value = s.nisn;
            document.getElementById('formNama').value = s.nama;
            document.getElementById('formStatus').value = s.status;
            document.getElementById('formLink').value = s.link_skl;
            document.getElementById('studentModal').classList.remove('hidden');
        }

        document.getElementById('studentForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const action = currentMode === 'add' ? 'add' : 'update';
            const res = await fetch('proses.php?action=' + action, {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.status === 'success') {
                Swal.fire('Berhasil!', 'Data siswa berhasil disimpan.', 'success');
                closeModal('studentModal');
                fetchStudents();
            } else {
                Swal.fire('Error!', result.message, 'error');
            }
        };

        document.getElementById('importForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const res = await fetch('proses.php?action=import', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.status === 'success') {
                Swal.fire('Berhasil!', 'Data berhasil diimport.', 'success');
                closeModal('importModal');
                fetchStudents();
            } else {
                Swal.fire('Error!', result.message, 'error');
            }
        };

        async function deleteStudent(id) {
            const confirm = await Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!'
            });

            if (confirm.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);
                const res = await fetch('proses.php?action=delete', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();
                if (result.status === 'success') {
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                    fetchStudents();
                }
            }
        }

        window.onload = fetchStudents;
    </script>
</body>
</html>
