<?php
// koneksi.php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'absensi_siswa';

$koneksi = mysqli_connect($host, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// functions.php
function getRoles() {
    global $koneksi;
    $query = "SELECT * FROM roles";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getUsers() {
    global $koneksi;
    $query = "SELECT users.*, roles.role_name 
              FROM users 
              LEFT JOIN roles ON users.role_id = roles.id";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addUser($data) {
    global $koneksi;
    $username = mysqli_real_escape_string($koneksi, $data['username']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
    $role_id = (int)$data['role_id'];
    $status = 1; // default aktif

    $query = "INSERT INTO users (username, password, nama_lengkap, role_id, status) 
              VALUES ('$username', '$password', '$nama_lengkap', $role_id, $status)";
    return mysqli_query($koneksi, $query);
}

function updateUser($data) {
    global $koneksi;
    $id = (int)$data['id'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
    $role_id = (int)$data['role_id'];

    $query = "UPDATE users 
              SET nama_lengkap = '$nama_lengkap', 
                  role_id = $role_id 
              WHERE id = $id";
    return mysqli_query($koneksi, $query);
}

function resetPassword($user_id, $new_password) {
    global $koneksi;
    $id = (int)$user_id;
    $password = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = '$password' WHERE id = $id";
    return mysqli_query($koneksi, $query);
}

function toggleUserStatus($user_id) {
    global $koneksi;
    $id = (int)$user_id;
    
    $query = "UPDATE users SET status = NOT status WHERE id = $id";
    return mysqli_query($koneksi, $query);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .gradient-card-1 {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
    </style>
</head>
<body>
    <nav class="bg-[#534FDE] text-white p-4 shadow-sm flex justify-between items-center">
        <span class="text-xl font-bold tracking-tight">SMKN 40 JAKARTA</span>
        <a href="#" onclick="confirmLogout()" 
            class="bg-red-500 px-6 py-2 rounded-lg hover:bg-red-600 text-white transition duration-200 text-sm font-semibold shadow-sm hover-scale">
            Logout
        </a>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#EFF3EA] text-slate-800 min-h-screen p-5">
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="absensi.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Absensi Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="absensiguru.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Absensi Guru</span>
                    </a>
                </li>
                <li>
                    <a href="siswa.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Data Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="guru.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Data Guru</span>
                    </a>
                </li>
                <li>
                    <a href="manajemen_admin.php"
                        class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Manajemen Admin</span>
                    </a>
                </li>
                
            
                

                

            </ul>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Admin</h1>
                    <button onclick="document.getElementById('addAdminModal').classList.remove('hidden')"
                        class="bg-[#534FDE] hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 text-sm font-semibold shadow-sm">
                        Tambah Admin
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            $users = getUsers();
                            $no = 1;
                            foreach($users as $user):
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++ ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['username']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($user['role_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $user['status'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>
        <button onclick="document.getElementById('resetPasswordModal').classList.remove('hidden'); document.getElementById('reset_user_id').value='<?= $user['id'] ?>';" class='bg-[#534FDE] text-white px-4 py-2 rounded-lg hover: #4949C7 transition duration-200'>Reset Password</button>
        <button onclick="document.getElementById('changeStatusModal').classList.remove('hidden'); document.getElementById('status_user_id').value='<?= $user['id'] ?>';" class='bg-[#20BC59] text-white px-4 py-2 rounded-lg hover: #1A9F4B transition duration-200'>Ubah Status</button>
        <button onclick="if(confirm('Apakah Anda yakin ingin menghapus admin ini?')) { window.location.href='process.php?action=delete_user&id=<?= $user['id'] ?>' }" class='bg-[#D73232] text-white px-4 py-2 rounded-lg hover: #B02B2B transition duration-200'>Hapus Admin</button>
    </td>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Reset Password</h3>
            <button onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="process.php?action=reset_password" method="POST">
            <input type="hidden" name="user_id" id="reset_user_id">
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" id="new_password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">Reset Password</button>
        </form>
    </div>
</div>

<!-- Modal Change Status -->
<div id="changeStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Ubah Status</h3>
            <button onclick="document.getElementById('changeStatusModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="process.php?action=change_status" method="POST">
            <input type="hidden" name="user_id" id="status_user_id">
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">Ubah Status</button>
        </form>
    </div>
</div>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Admin -->
    <div id="addAdminModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Tambah Admin Baru</h3>
                <button onclick="document.getElementById('addAdminModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="process.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <?php
                            $roles = getRoles();
                            foreach($roles as $role):
                            ?>
                            <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('addAdminModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" name="action" value="add_user"
                        class="px-4 py-2 bg-[#534FDE] text-white rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editUser(userId) {
        // Implementasi fungsi edit
    }

    function resetPassword(userId) {
        if(confirm('Yakin ingin reset password user ini?')) {
            window.location.href = `process.php?action=reset_password&id=${userId}`;
        }
    }

    function toggleStatus(userId) {
        if(confirm('Yakin ingin mengubah status user ini?')) {
            window.location.href = `process.php?action=toggle_status&id=${userId}`;
        }
    }

    function confirmLogout() {
        if(confirm('Yakin ingin logout?')) {
            window.location.href = 'logout.php';
        }
    }
    </script>
</body>
</html>