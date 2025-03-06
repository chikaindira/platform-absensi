<?php
session_start();
include 'config.php';

// Cek apakah guru sudah login
if (!isset($_SESSION['guru'])) {
    header("Location: login_guru.php");
    exit();
}

// Ambil data guru dari database
$nip = $_SESSION['guru'];
$guru_result = $conn->query("SELECT * FROM guru WHERE nip = '$nip'");

// Pastikan query berhasil
if (!$guru_result) {
    die("Query error: " . $conn->error);
}

$guru = $guru_result->fetch_assoc();

// Pastikan data guru ditemukan
if (!$guru) {
    echo "<div class='text-red-500'>Data guru tidak ditemukan.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="dashboardguru.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="#" class="nav-link active flex items-center space-x-3 p-3 rounded-xl">
                    <i class="fas fa-user text-indigo-600"></i>
                    <span class="font-medium">Profil Guru</span>
                </a>
            </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800 animate-fadeIn">Profil Guru</h2>

            <!-- Modern Profile Card -->
            <div class="glass-card rounded-2xl shadow-xl p-8 border border-slate-100 animate-fadeIn">
                <!-- Header dengan gradien -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-2xl h-24"></div>

                <div class="relative">
                    <div class="flex items-center space-x-6 mb-6 pt-4 pl-4">
                        <!-- Foto Profil -->
                        <!-- Foto Profil -->
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        <?php
                            $foto_profil = "uploads/bu aini.jpg";
                            if (file_exists($foto_profil)) {
                                echo '<img src="' . $foto_profil . '" alt="Foto Profil" class="w-full h-full object-cover">';
                            } else {
                                echo '<i class="fas fa-user text-4xl text-gray-400"></i>';
                            }
                        ?>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($guru['nama']); ?></h3>
                            <p class="text-gray-600">NIP: <?php echo htmlspecialchars($guru['nip']); ?></p>
                        </div>
                    </div>

                    <!-- Informasi Profil dengan Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4">
                        <div>
                            <i class="fas fa-chalkboard-teacher mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Mata Pelajaran: </strong>
                            <p class="text-slate-600"><?php echo htmlspecialchars($guru['mapel'] ?? 'Pemograman Web dan Pemograman Berorientasi Objek'); ?></p>
                        </div>
                        <div>
                            <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Email: </strong>
                            <p class="text-slate-600">
                                <?php
                                if (isset($guru['email']) && !empty($guru['email'])) {
                                    echo htmlspecialchars($guru['email']);
                                } else {
                                    echo "<span class='text-gray-500'>nuarainiazizah@gmail.com</span>";
                                }
                                ?>
                            </p>
                        </div>
                        <div>
                            <i class="fas fa-phone mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">No. Telepon: </strong>
                            <p class="text-slate-600"><?php echo htmlspecialchars($guru['no_telp'] ?? '081238291223'); ?></p>
                        </div>
                        <div>
                            <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Alamat: Utan Kayu Utara, Matraman</strong>
                            <p class="text-slate-600"><?php echo htmlspecialchars($guru['alamat'] ?? 'Utan Kayu Utara, Matraman'); ?></p>
                        </div>
                    </div>

                    
                </div>
            </div>
        </main>
    </div>
</body>
</html>