<?php
session_start();

if (!isset($_SESSION['siswa'])) {
    // Handle jika data siswa tidak ada dalam session
    echo "Sesi tidak valid. Silakan login kembali.";
    exit;
}

// Assume the session holds the student's ID
$student_id = $_SESSION['siswa'];

// Setup database connection (update credentials accordingly)
$mysqli = new mysqli("localhost", "root", "", "absensi_siswa");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit;
}

// Retrieve the student's name from table 'siswa', column 'nama'
$stmt = $mysqli->prepare("SELECT nama FROM siswa WHERE nis = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($nama);
$stmt->fetch();
$stmt->close();
$mysqli->close();

if (!$nama) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$siswa = ['nama' => $nama];

// Data dummy untuk jadwal pelajaran (SESUAI REQUEST)
$jadwal = [
    'Senin' => [
        ['jam' => '07:00 - 08:00', 'mata_pelajaran' => 'Bahasa Inggris', 'guru' => 'Mr. Sumarsono'],
        ['jam' => '08:00 - 09:00', 'mata_pelajaran' => 'RPL', 'guru' => 'Bu Nuraini Azizah']
    ],
    'Selasa' => [
        ['jam' => '07:00 - 08:00', 'mata_pelajaran' => 'Agama', 'guru' => 'Ibu Mukaromah'],
        ['jam' => '08:00 - 09:00', 'mata_pelajaran' => 'RPL', 'guru' => 'Pak Nugro'],
        ['jam' => '09:00 - 10:00', 'mata_pelajaran' => 'PKK', 'guru' => 'Bu Iin ']
    ],
    'Rabu' => [
        ['jam' => '07:00 - 08:00', 'mata_pelajaran' => 'Bahasa Inggris', 'guru' => 'Mr. Sumarsono'],
        ['jam' => '08:00 - 09:00', 'mata_pelajaran' => 'PKK', 'guru' => 'Ibu Iin'],
        ['jam' => '09:00 - 10:00', 'mata_pelajaran' => 'PJOK', 'guru' => 'Pak Wahid'],
        ['jam' => '10:00 - 11:00', 'mata_pelajaran' => 'Matematika', 'guru' => 'Bu Arty'],
        ['jam' => '11:00 - 12:00', 'mata_pelajaran' => 'BK', 'guru' => 'Bu Nurul']
    ],
    'Kamis' => [
        ['jam' => '07:00 - 08:00', 'mata_pelajaran' => 'PPKn', 'guru' => 'Bu Tinah'],
        ['jam' => '08:00 - 09:00', 'mata_pelajaran' => 'Bahasa Jepang', 'guru' => 'Sensei Tasya'],
        ['jam' => '09:00 - 10:00', 'mata_pelajaran' => 'Sejarah', 'guru' => 'Bu Titi'],
        ['jam' => '10:00 - 11:00', 'mata_pelajaran' => 'RPL', 'guru' => 'Pak Ardan']
    ],
    'Jumat' => [
        ['jam' => '07:00 - 08:00', 'mata_pelajaran' => 'RPL', 'guru' => 'Bu Nuraini Azizah'],
        ['jam' => '08:00 - 09:00', 'mata_pelajaran' => 'Bahasa Indonesia', 'guru' => 'Bu Henny']
    ],
];

// Data dummy untuk tugas (PR)
$tugas = [
    ['mata_pelajaran' => 'RPL', 'deskripsi_tugas' => 'Project Absensi', 'tanggal_pengumpulan' => '2025-02-21'],
    ['mata_pelajaran' => 'Bahasa Indonesia', 'deskripsi_tugas' => 'Mencari makalah, artikel dan laporan', 'tanggal_pengumpulan' => '2025-02-21'],
    ['mata_pelajaran' => 'PJOK', 'deskripsi_tugas' => 'Membuat Video Silat', 'tanggal_pengumpulan' => '2025-02-24']
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f6f8fc 0%, #f0f3f9 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }
        
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.2) 100%);
            border-right: 3px solid #6366f1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .gradient-border {
            position: relative;
            border-radius: 1rem;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) border-box;
            border: 2px solid transparent;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navbar -->
    <nav class="glass-card sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Dashboard Siswa
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                            <?= strtoupper(substr($siswa['nama'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?= isset($siswa['nama']) ? htmlspecialchars($siswa['nama']) : '' ?></span>
                    </div>
                    <a href="logout_siswa.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg shadow-red-500/30">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 glass-card min-h-screen p-4 border-r border-gray-200">
            <div class="space-y-2">
                <a href="dashboardsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-user"></i>
                    <span>Dashboard</span>
                </a>
                <a href="profilsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
                <a href="jadwalsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-calendar"></i>
                    <span>Jadwal</span>
                </a>
                
            </div>
        </aside>


        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800">Jadwal Pelajaran & Pengingat PR</h2>

            <!-- Jadwal Pelajaran -->
            <div class="bg-white p-6 rounded-2xl shadow-lg mb-8 border border-slate-100">
                <h3 class="text-xl font-semibold mb-4 text-slate-800">Jadwal Pelajaran</h3>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Hari</th>
                                <th class="px-4 py-2 text-left">Jadwal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwal as $hari => $pelajaran): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2 font-semibold"><?php echo $hari; ?></td>
                                    <td class="border px-4 py-2">
                                        <?php if (count($pelajaran) > 0): ?>
                                            <ul>
                                                <?php foreach ($pelajaran as $item): ?>
                                                    <li>
                                                        <?php echo $item['jam']; ?> - <?php echo $item['mata_pelajaran']; ?> (<?php echo $item['guru']; ?>)
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-gray-500">Tidak ada pelajaran hari ini.</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pengingat Tugas (PR) -->
            <div class="bg-white p-6 rounded-2xl shadow-lg mb-8 border border-slate-100">
                <h3 class="text-xl font-semibold mb-4 text-slate-800">Pengingat Tugas (PR)</h3>
                <div class="space-y-4">
                    <?php foreach ($tugas as $item): ?>
                        <div class="p-4 rounded-lg shadow-md bg-white border border-slate-200">
                            <h4 class="font-semibold text-lg text-slate-800"><?php echo $item['mata_pelajaran']; ?></h4>
                            <p class="text-slate-600 text-sm mt-1"><?php echo $item['deskripsi_tugas']; ?></p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-gray-500 text-xs">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?php echo $item['tanggal_pengumpulan']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
