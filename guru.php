<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Array of available jabatan
$jabatan_list = ['Tim Manajemen', 'Tenaga Pendidik', 'Tenaga Kependidikan'];

// Tambah Guru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_guru'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    
    // Handle file upload for foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($foto);
        // You may want to add more checks on file type/size here
        move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file);
    } else {
        $foto = ""; // Set a default value or handle upload error
    }
    
    $sql = "INSERT INTO guru (nama, jabatan, foto) VALUES ('$nama', '$jabatan', '$foto')";
    $conn->query($sql);
    header("Location: guru.php");
    exit();
}

// Hapus Guru
if (isset($_GET['delete_guru'])) {
    $id = $_GET['delete_guru'];
    $sql = "DELETE FROM guru WHERE id=$id";
    $conn->query($sql);
    header("Location: guru.php");
    exit();
}

// Filter by Jabatan
$selected_jabatan = isset($_GET['filter_jabatan']) ? $_GET['filter_jabatan'] : 'all';

if ($selected_jabatan != 'all') {
    $sql = "SELECT * FROM guru WHERE jabatan='$selected_jabatan'";
} else {
    $sql = "SELECT * FROM guru";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Guru</title>
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
        .gradient-card-2 {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .gradient-card-3 {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        }
    </style>
</head>
<body class="bg-slate-50">
    <!-- Navbar -->
    <nav class="bg-[#534FDE] text-white p-4 shadow-sm flex justify-between items-center">
        <span class="text-xl font-bold tracking-tight">SMKN 40 Jakarta</span>
        <a href="#" onclick="confirm('Yakin ingin logout?')" 
           class="bg-red-500 px-6 py-2 rounded-lg hover:bg-red-600 text-white transition duration-200 text-sm font-semibold shadow-sm">
            Logout
        </a>
    </nav>
    
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#EFF3EA] text-slate-800 min-h-screen p-5">
            <ul class="space-y-2">
                <li>
                    <a href="dashboard.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="absensi.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Absensi Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="absensiguru.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Absensi Guru</span>
                    </a>
                </li>
                <li>
                    <a href="siswa.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Data Siswa</span>
                    </a>
                </li>
                <li>
                    <a href="guru.php" 
                       class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Data Guru</span>
                    </a>
                </li>
                <li>
                    <a href="manajemen_admin.php"
                        class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Manajemen Admin</span>
                    </a>
                </li>
            </ul>
        </aside>
        
        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800">Kelola Data Guru</h2>

            <!-- Form Tambah Guru -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-slate-800">Tambah Guru</h3>
                <form action="guru.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label for="nama" class="block text-slate-700">Nama Lengkap:</label>
                        <input type="text" name="nama" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label for="jabatan" class="block text-slate-700">Jabatan:</label>
                        <select name="jabatan" required class="w-full p-2 border rounded-lg">
                            <?php foreach ($jabatan_list as $jabatan): ?>
                                <option value="<?= htmlspecialchars($jabatan) ?>"><?= htmlspecialchars($jabatan) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="foto" class="block text-slate-700">Foto Guru:</label>
                        <input type="file" name="foto" accept="image/*" required class="w-full p-2 border rounded-lg">
                    </div>
                    <button type="submit" name="add_guru" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Tambah Guru
                    </button>
                </form>
            </div>

            <!-- Filter Jabatan -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 mb-6">
                <h3 class="text-xl font-semibold mb-4 text-slate-800">Filter Jabatan</h3>
                <form action="guru.php" method="GET" class="flex space-x-4">
                    <select name="filter_jabatan" class="p-2 border rounded-lg">
                        <option value="all">Semua Jabatan</option>
                        <?php foreach ($jabatan_list as $jabatan): ?>
                            <option value="<?= htmlspecialchars($jabatan) ?>" <?= ($selected_jabatan == $jabatan) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jabatan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Daftar Guru -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">Daftar Guru</h3>
                <table class="w-full text-left">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-3 font-medium text-slate-700">ID</th>
                            <th class="p-3 font-medium text-slate-700">Nama</th>
                            <th class="p-3 font-medium text-slate-700">Jabatan</th>
                            <th class="p-3 font-medium text-slate-700">Foto</th>
                            <th class="p-3 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b">
                                    <td class="p-3"><?= htmlspecialchars($row['id']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($row['jabatan']) ?></td>
                                    <td class="p-3">
                                        <?php if(!empty($row['foto'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" width="50" class="rounded-lg" alt="Foto Guru">
                                        <?php else: ?>
                                            Tidak ada foto
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <a href="?delete_guru=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php elseif ($selected_jabatan != 'all'): ?>
                            <tr>
                                <td colspan="5" class="p-3 text-center">Belum ada data guru untuk jabatan ini. Silakan tambah guru terlebih dahulu.</td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-3 text-center">Belum ada data guru. Silakan tambah guru terlebih dahulu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>