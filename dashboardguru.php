<?php
session_start();
include 'config.php';

// Cek login guru
if (!isset($_SESSION['guru'])) {
    header("Location: loginguru.php");
    exit();
}

$nip = $_SESSION['guru'];

// Ambil data guru
$stmt = $conn->prepare("SELECT * FROM guru WHERE nip = ?");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $guru = $result->fetch_assoc();
    $guru_id = $guru['id'];
} else {
    echo "Data guru tidak ditemukan.";
    exit();
}

// Pengumuman (contoh, bisa diambil dari database)
$pengumuman = "Pengumuman: Rapat guru akan diadakan pada tanggal 1 Maret 2025.";

// Contoh data kalender akademik (simulasi)
$kalender_akademik = [
    ['tanggal' => '2025-03-01', 'keterangan' => 'Rapat Guru'],
    ['tanggal' => '2025-03-15', 'keterangan' => 'Ujian Tengah Semester'],
    ['tanggal' => '2025-04-01', 'keterangan' => 'Libur Paskah']
];

// Contoh data notifikasi (simulasi)
$notifikasi = [
    ['jenis' => 'Penting', 'pesan' => 'Batas akhir pengumpulan nilai adalah tanggal 10 Maret 2025.'],
    ['jenis' => 'Perhatian', 'pesan' => 'Siswa bernama Auriza Irhamnas sering absen. Mohon diperhatikan.']
];

// Contoh data tugas yang belum dinilai (simulasi)
$tugas_belum_dinilai = [
    ['nama_siswa' => 'Chaylarossa Suryana Putri', 'kelas' => 'XI-RPL', 'mata_pelajaran' => 'Pemograman Web'],
    ['nama_siswa' => 'Amyatul Janah', 'kelas' => 'XI-RPL', 'mata_pelajaran' => 'Pemograman Berorientasi Objek'],
    ['nama_siswa' => 'Melvin Pidiandra Putra', 'kelas' => 'XI-RPL', 'mata_pelajaran' => 'Pemograman Web']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
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
                        <span class="text-white font-bold">G</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Dashboard Guru
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                            <?= strtoupper(substr($guru['nama'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($guru['nama']) ?></span>
                    </div>
                    <a href="logout_guru.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg shadow-red-500/30">
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
                <a href="#" class="nav-link active flex items-center space-x-3 p-3 rounded-xl">
                    <i class="fas fa-home text-indigo-600"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="profil_guru.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-user"></i>
                    <span>Profil Guru</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                <!-- Welcome Section -->
                <div class="glass-card p-6 rounded-2xl animate-fadeIn gradient-border">
                    <h1 class="text-3xl font-bold text-gray-800">
                        <?php 
                        $hour = date('H');
                        $greeting = 'Selamat ';
                        if($hour < 11) {
                            $greeting .= 'Pagi';
                        } elseif($hour < 15) {
                            $greeting .= 'Siang';
                        } elseif($hour < 19) {
                            $greeting .= 'Sore';
                        } else {
                            $greeting .= 'Malam';
                        }
                        echo $greeting . ', ' . htmlspecialchars($guru['nama']) . '!';
                        ?>
                    </h1>
                    <p class="mt-2 text-gray-600">Selamat datang di dashboard guru. Anda dapat mengelola absensi siswa, melihat jadwal mengajar, dan memperbarui profil Anda.</p>
                </div>


                 <!-- Kalender Akademik -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-calendar mr-2 text-indigo-600"></i>
                        Kalender Akademik
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($kalender_akademik as $event): ?>
                            <div class="bg-white rounded-lg shadow-md p-4">
                                <h4 class="text-md font-semibold text-gray-800"><?= date('d M Y', strtotime($event['tanggal'])) ?></h4>
                                <p class="text-gray-600"><?= htmlspecialchars($event['keterangan']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Notifikasi -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-bell mr-2 text-indigo-600"></i>
                        Notifikasi
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($notifikasi as $notif): ?>
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-l-4 border-indigo-500 p-4 rounded-xl">
                                <div class="flex items-start space-x-4">
                                    <div>
                                        <h4 class="text-base font-semibold text-gray-800"><?= htmlspecialchars($notif['jenis']) ?></h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($notif['pesan']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tugas yang Belum Dinilai -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-tasks mr-2 text-indigo-600"></i>
                        Tugas yang Belum Dinilai
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($tugas_belum_dinilai as $tugas): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($tugas['nama_siswa']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($tugas['kelas']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($tugas['mata_pelajaran']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Announcement -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-bullhorn mr-2 text-indigo-600"></i>
                        Pengumuman Terbaru
                    </h3>
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-l-4 border-indigo-500 p-6 rounded-xl">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar text-indigo-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-gray-800">Informasi</h4>
                                <p class="mt-1 text-gray-600"><?= htmlspecialchars($pengumuman) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Chart Absensi
        const absensiCtx = document.getElementById('absensiChart').getContext('2d');
        new Chart(absensiCtx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
                datasets: [{
                    data: [
                        <?= $rekap_absensi['Hadir'] ?>,
                        <?= $rekap_absensi['Sakit'] ?>,
                        <?= $rekap_absensi['Izin'] ?>,
                        <?= $rekap_absensi['Alpha'] ?>
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(255, 205, 86, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>