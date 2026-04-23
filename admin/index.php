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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-indigo-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Admin Panel</span>
                </div>
                <div class="flex space-x-4">
                    <a href="index.php" class="bg-indigo-800 px-3 py-2 rounded-md text-sm font-medium">Siswa</a>
                    <a href="pengaturan.php" class="hover:bg-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Pengaturan</a>
                    <a href="logout.php" class="hover:bg-red-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Data Siswa</h1>
            <div class="flex flex-wrap gap-2">
                <button onclick="openModal('add')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Siswa
                </button>
                <button onclick="openModal('import')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow transition">
                    <i class="fas fa-file-excel mr-2"></i> Import Excel
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link SKL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form (Add/Edit) -->
    <div id="studentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Tambah Siswa</h3>
                <form id="studentForm" class="mt-4 space-y-4">
                    <input type="hidden" id="studentId" name="id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NISN</label>
                        <input type="text" name="nisn" id="formNisn" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="formNama" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="formStatus" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="LULUS">LULUS</option>
                            <option value="TIDAK LULUS">TIDAK LULUS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link Google Drive SKL</label>
                        <input type="url" name="link_skl" id="formLink" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" onclick="closeModal('studentModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Import Data Excel</h3>
                <p class="text-xs text-gray-500 mt-1">Format: NISN, Nama, Link SKL, Status (LULUS/TIDAK LULUS)</p>
                <form id="importForm" class="mt-4 space-y-4" enctype="multipart/form-data">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pilih File .xlsx</label>
                        <input type="file" name="file_excel" accept=".xlsx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" onclick="closeModal('importModal')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">Batal</button>
                        <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-700">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentMode = 'add';

        async function fetchStudents() {
            const res = await fetch('proses.php?action=fetch');
            const data = await res.json();
            const tbody = document.getElementById('studentTableBody');
            tbody.innerHTML = '';
            data.forEach((s, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${i + 1}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${s.nisn}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${s.nama}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${s.status === 'LULUS' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${s.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                            <a href="${s.link_skl}" target="_blank" class="hover:underline">Buka Link</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick='editStudent(${JSON.stringify(s)})' class="text-indigo-600 hover:text-indigo-900 mr-3"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteStudent(${s.id})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
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
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
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
