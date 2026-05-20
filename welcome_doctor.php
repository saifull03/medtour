<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login_doctor.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$sql = "SELECT u.*, d.specialization, d.experience, d.consultation_fee, d.id as doctor_id
        FROM users u LEFT JOIN doctors d ON u.id = d.user_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Count appointments for this doctor
$doctor_id   = $row['doctor_id'] ?? 0;
$apt_count   = 0;
if ($doctor_id) {
    $c = $conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE doctor_id = $doctor_id");
    $apt_count = $c->fetch_assoc()['cnt'] ?? 0;
}
$conn->close();

$name             = htmlspecialchars($row['name'] ?? 'Doctor');
$email            = htmlspecialchars($row['email'] ?? '');
$specialization   = htmlspecialchars($row['specialization'] ?? 'General');
$experience       = htmlspecialchars($row['experience'] ?? '0');
$consultation_fee = number_format($row['consultation_fee'] ?? 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-slate-900 to-teal-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg tracking-tight">MedTour <span class="text-teal-300">Doctor</span></span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a href="welcome_doctor.php"   class="text-slate-300 hover:text-white text-sm font-medium transition">Dashboard</a>
            <a href="view_appointments.php" class="text-slate-300 hover:text-white text-sm font-medium transition">Appointments</a>
            <a href="help.php"             class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
        </nav>
        <form action="logout_doctor.php" method="post">
            <button class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</button>
        </form>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-10">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-teal-600 to-cyan-700 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-72 h-72 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/2 blur-3xl"></div>
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0 text-4xl font-bold">
                <?php echo mb_strtoupper(mb_substr($name, 0, 1)); ?>
            </div>
            <div>
                <p class="text-teal-200 text-sm font-medium mb-1">Doctor Portal 👨‍⚕️</p>
                <h1 class="text-3xl font-bold mb-1">Welcome, Dr. <?php echo $name; ?></h1>
                <p class="text-teal-100 text-sm"><?php echo $specialization; ?> · <?php echo $experience; ?> years experience</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Total Appointments</p>
            <p class="text-3xl font-bold text-teal-600 mt-1"><?php echo $apt_count; ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Specialization</p>
            <p class="text-base font-bold text-slate-700 mt-1"><?php echo $specialization; ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Experience</p>
            <p class="text-3xl font-bold text-cyan-600 mt-1"><?php echo $experience; ?><span class="text-base font-medium text-slate-400"> yrs</span></p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-400 uppercase tracking-wider font-medium">Consultation Fee</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">$<?php echo $consultation_fee; ?></p>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">

        <!-- Doctor Profile Card -->
        <div class="md:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Profile Details</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <span class="text-slate-400 w-5 flex-shrink-0 mt-0.5">✉️</span>
                    <div>
                        <p class="text-slate-400 text-xs">Email</p>
                        <p class="text-slate-700 font-medium"><?php echo $email; ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-slate-400 w-5 flex-shrink-0 mt-0.5">🩺</span>
                    <div>
                        <p class="text-slate-400 text-xs">Specialization</p>
                        <p class="text-slate-700 font-medium"><?php echo $specialization; ?></p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-slate-400 w-5 flex-shrink-0 mt-0.5">📅</span>
                    <div>
                        <p class="text-slate-400 text-xs">Experience</p>
                        <p class="text-slate-700 font-medium"><?php echo $experience; ?> years</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-slate-400 w-5 flex-shrink-0 mt-0.5">💰</span>
                    <div>
                        <p class="text-slate-400 text-xs">Consultation Fee</p>
                        <p class="text-slate-700 font-medium">$<?php echo $consultation_fee; ?> per session</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-slate-400 w-5 flex-shrink-0 mt-0.5">🆔</span>
                    <div>
                        <p class="text-slate-400 text-xs">Doctor ID</p>
                        <p class="text-slate-700 font-mono">#<?php echo $doctor_id; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="md:col-span-2 flex flex-col gap-5">

            <!-- View Appointments -->
            <a href="view_appointments.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-teal-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
                <div class="w-14 h-14 bg-teal-100 group-hover:bg-teal-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-teal-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">View Appointments</h3>
                    <p class="text-slate-500 text-sm">Review all your scheduled patient appointments</p>
                    <span class="text-teal-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Calendar →</span>
                </div>
            </a>

            <!-- Manage Schedule -->
            <a href="view_appointments.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-cyan-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
                <div class="w-14 h-14 bg-cyan-100 group-hover:bg-cyan-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-cyan-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Manage Schedule</h3>
                    <p class="text-slate-500 text-sm">Update your availability and appointment times</p>
                    <span class="text-cyan-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Open Schedule →</span>
                </div>
            </a>

            <!-- Help -->
            <a href="help.php" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:border-amber-300 hover:shadow-md transition-all duration-300 flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-100 group-hover:bg-amber-500 rounded-2xl flex items-center justify-center transition-colors duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Help & Support</h3>
                    <p class="text-slate-500 text-sm">Access documentation and support resources</p>
                    <span class="text-amber-600 text-xs font-semibold mt-1 inline-block group-hover:underline">Get Help →</span>
                </div>
            </a>

        </div>
    </div>

</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>

</body>
</html>
