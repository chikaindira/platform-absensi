<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Array of available jurusan
$jurusan_list = ['RPL', 'AKL', 'MP', 'BR', 'DKV 1', 'DKV 2'];

// Tambah Siswa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_siswa'])) {
    // Pastikan field nis dan password juga dikirim dari form
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $jurusan = $_POST['jurusan'];
    $password = $_POST['password'];

    // Validate NIS and Password
    if (empty($nis) || empty($password)) {
        echo "<script>alert('NIS dan Password harus diisi!'); window.location.href='siswa.php';</script>"; // Display error and redirect
        exit(); // Stop further execution
    }

    // Upload foto
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto);
    move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);

    // Hash password before disimpan (opsional namun direkomendasikan)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO siswa (nis, password, nama, kelas, jurusan, foto) VALUES ('$nis', '$hashed_password', '$nama', '$kelas', '$jurusan', '$foto')";
    $conn->query($sql);
    header("Location: siswa.php");
}


// Hapus Siswa
if (isset($_GET['delete_siswa'])) {
    $id = $_GET['delete_siswa'];
    $sql = "DELETE FROM siswa WHERE id=$id";
    $conn->query($sql);
    header("Location: siswa.php");
}

// Filter by jurusan
$selected_jurusan = isset($_GET['filter_jurusan']) ? $_GET['filter_jurusan'] : 'all';
if ($selected_jurusan != 'all') {
    $sql = "SELECT * FROM siswa WHERE jurusan='$selected_jurusan'";
} else {
    $sql = "SELECT * FROM siswa";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
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
        
        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800">Kelola Data Siswa</h2>

            <!-- Form Tambah Siswa -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 mb-8">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">Tambah Siswa Baru</h3>
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
    <div>
        <label for="nis" class="block text-sm font-medium text-slate-700 mb-2">NIS</label>
        <input type="text" name="nis" id="nis" placeholder="Masukkan NIS" required 
               class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
        <input type="password" name="password" id="password" placeholder="Masukkan Password" required 
               class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>
    <div>
        <label for="nama" class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
        <input type="text" name="nama" id="nama" placeholder="Masukkan Nama Lengkap" required 
               class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>
    <div>
        <label for="kelas" class="block text-sm font-medium text-slate-700 mb-2">Kelas</label>
        <input type="text" name="kelas" id="kelas" placeholder="Masukkan Kelas" required 
               class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>
    <div>
        <label for="jurusan" class="block text-sm font-medium text-slate-700 mb-2">Jurusan</label>
        <select name="jurusan" id="jurusan" required 
                class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
            <?php foreach ($jurusan_list as $jur): ?>
                <option value="<?php echo $jur; ?>"><?php echo $jur; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="foto" class="block text-sm font-medium text-slate-700 mb-2">Foto Siswa</label>
        <input type="file" name="foto" id="foto" accept="image/*" required 
               class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500">
    </div>
    <button type="submit" name="add_siswa" 
            class="px-6 py-3 rounded-lg text-white transition duration-200 text-sm font-semibold shadow-sm" 
            style="background-color: #534FDE;">
        Tambah Siswa
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

            <!-- Daftar Siswa -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
                <h3 class="text-xl font-semibold mb-6 text-slate-800">
                    Daftar Siswa <?php echo ($selected_jurusan != 'all') ? "- $selected_jurusan" : ''; ?>
                </h3>
                <table class="w-full text-left">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="p-3 font-medium text-slate-700">ID</th>
                            <th class="p-3 font-medium text-slate-700">Nama</th>
                            <th class="p-3 font-medium text-slate-700">Kelas</th>
                            <th class="p-3 font-medium text-slate-700">Jurusan</th>
                            <th class="p-3 font-medium text-slate-700">Foto</th>
                            <th class="p-3 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['jurusan']) . "</td>";
                            echo "<td><img src='uploads/" . htmlspecialchars($row['foto']) . "' width='50'></td>";
                            echo "<td><a href='?delete_siswa=" . $row['id'] . "' class='text-red-500 hover:text-red-700'>Hapus</a></td>";
                            echo "</tr>";
                        }
                    } elseif ($selected_jurusan != 'all') {
                        echo "<tr><td colspan='6' class='text-center p-4 text-slate-600'>Belum ada data siswa untuk jurusan ini. Silakan tambah siswa terlebih dahulu.</td></tr>";
                    } else {
                        echo "<tr><td colspan='6' class='text-center p-4 text-slate-600'>Belum ada data siswa. Silakan tambah siswa terlebih dahulu.</td></tr>";
                    }
                    ?>
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