<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Array of available jurusan
$jurusan_list = ['RPL', 'AKL', 'MP', 'BR', 'DKV 1', 'DKV 2'];
 
// Tambah Absensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_absensi'])) {
    $siswa_id = $_POST['siswa_id'];
    $status = $_POST['status'];
    $sql = "INSERT INTO absensi (siswa_id, status) VALUES ('$siswa_id', '$status')";
    $conn->query($sql);
    header("Location: absensi.php");
}

// Hapus Absensi
if (isset($_GET['delete_absensi'])) {
    $id = $_GET['delete_absensi'];
    $sql = "DELETE FROM absensi WHERE id=$id";
    $conn->query($sql);
    header("Location: absensi.php");
}

// Update Absensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_absensi'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $sql = "UPDATE absensi SET status='$status' WHERE id=$id";
    $conn->query($sql);
    header("Location: absensi.php");
}

// Filter by Jurusan
$selected_jurusan = isset($_GET['filter_jurusan']) ? $_GET['filter_jurusan'] : 'all';

// Ambil Data Siswa Berdasarkan Filter Jurusan
if ($selected_jurusan != 'all') {
    $siswa_sql = "SELECT * FROM siswa WHERE jurusan='$selected_jurusan'";
} else {
    $siswa_sql = "SELECT * FROM siswa";
}
$siswa_result = $conn->query($siswa_sql);

// Ambil Data Absensi dengan Join ke Tabel Siswa
if ($selected_jurusan != 'all') {
    $absensi_sql = "
        SELECT absensi.id, siswa.nama, siswa.jurusan, absensi.status, absensi.tanggal 
        FROM absensi 
        JOIN siswa ON absensi.siswa_id = siswa.id
        WHERE siswa.jurusan='$selected_jurusan'
    ";
} else {
    $absensi_sql = "
        SELECT absensi.id, siswa.nama, siswa.jurusan, absensi.status, absensi.tanggal 
        FROM absensi 
        JOIN siswa ON absensi.siswa_id = siswa.id
    ";
}

// Update the Tambah Absensi section (replace the existing one)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_absensi'])) {
    $siswa_id = $_POST['siswa_id'];
    $status = $_POST['status'];
    
    // Check if student already has attendance record for today
    $today = date('Y-m-d');
    $check_sql = "SELECT COUNT(*) as count FROM absensi 
                  WHERE siswa_id = '$siswa_id' 
                  AND DATE(tanggal) = '$today'";
    
    $check_result = $conn->query($check_sql);
    $check_data = $check_result->fetch_assoc();
    
    if ($check_data['count'] > 0) {
        // Student already has attendance record for today
        echo "<script>
                alert('Siswa ini sudah memiliki data absensi untuk hari ini!');
                window.location.href = 'absensi.php';
              </script>";
        exit();
    } else {
        // Insert new attendance record
        $sql = "INSERT INTO absensi (siswa_id, status) VALUES ('$siswa_id', '$status')";
        if ($conn->query($sql)) {
            echo "<script>
                    alert('Data absensi berhasil ditambahkan!');
                    window.location.href = 'absensi.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menambahkan data absensi!');
                    window.location.href = 'absensi.php';
                  </script>";
        }
    }
}
$absensi_result = $conn->query($absensi_sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Siswa</title>
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
        <span class="text-xl font-bold tracking-tight">SMKN 40 JAKARTA</span>
        <a href="#" onclick="confirmLogout()" 
           class="bg-red-500 px-6 py-2 rounded-lg hover:bg-red-600 text-white transition duration-200 text-sm font-semibold shadow-sm hover-scale ">
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
        
        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800">Kelola Absensi Siswa</h2>

            <!-- Form Tambah Absensi -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 mb-8">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">Tambah Absensi Baru</h3>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="filter_jurusan" class="block text-sm font-medium text-slate-700 mb-2">Filter Jurusan</label>
                        <select name="filter_jurusan" id="filter_jurusan" onchange="this.form.submit()" class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="all" <?php echo ($selected_jurusan == 'all') ? 'selected' : ''; ?>>Semua Jurusan</option>
                            <option value="RPL" <?php echo ($selected_jurusan == 'RPL') ? 'selected' : ''; ?>>RPL</option>
                            <option value="AKL" <?php echo ($selected_jurusan == 'AKL') ? 'selected' : ''; ?>>AKL</option>
                            <option value="DKV" <?php echo ($selected_jurusan == 'DKV') ? 'selected' : ''; ?>>DKV</option>
                            <option value="MP" <?php echo ($selected_jurusan == 'MP') ? 'selected' : ''; ?>>MP</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="siswa_id" class="block text-sm font-medium text-slate-700 mb-2">Pilih Siswa</label>
                        <select name="siswa_id" id="siswa_id" class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <?php while ($row = $siswa_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-2">Status Kehadiran</label>
                        <select name="status" id="status" class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="Hadir">Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                    <button type="submit" name="add_absensi" class="px-6 py-3 rounded-lg text-white transition duration-200 text-sm font-semibold shadow-sm" style="background-color: #534FDE;">
                        Simpan Absensi
                    </button>
                </form>
            </div>
 <!-- Filter Jurusan -->
 <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 mb-8">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">Filter Jurusan</h3>
                <form method="GET" action="" class="flex gap-4">
                    <select name="filter_jurusan" 
                            class="p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="all">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jur): ?>
                            <option value="<?php echo $jur; ?>" <?php echo ($selected_jurusan == $jur) ? 'selected' : ''; ?>>
                                <?php echo $jur; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" 
                            class="px-6 py-3 rounded-lg text-white transition duration-200 text-sm font-semibold shadow-sm" 
                            style="background-color: #534FDE;">
                        Filter
                    </button>
                </form>
            </div>
            <!-- Rekap Absensi -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">Rekap Absensi</h3>
                <table class="w-full text-left">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-3 font-medium text-slate-700">Nama Siswa</th>
                            <th class="p-3 font-medium text-slate-700">Jurusan</th>
                            <th class="p-3 font-medium text-slate-700">Status</th>
                            <th class="p-3 font-medium text-slate-700">Tanggal</th>
                            <th class="p-3 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($absensi_result->num_rows > 0): ?>
                            <?php while ($row = $absensi_result->fetch_assoc()): ?>
                                <tr class="border-b border-slate-200">
                                    <td class="p-3"><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['jurusan']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                    <td class="p-3">
                                        <a href="?delete_absensi=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php elseif ($selected_jurusan != 'all'): ?>
                            <tr>
                                <td colspan="5" class="p-3 text-center">Belum ada data absensi untuk jurusan ini.</td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-3 text-center">Belum ada data absensi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <script>
        function confirmLogout() {
            if (confirm("Apakah Anda yakin ingin logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>