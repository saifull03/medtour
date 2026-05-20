<?php
session_start();
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'mt_db');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE name = ? AND role = 'patient'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name']    = $row['name'];
            $_SESSION['role']    = 'patient';
            header("Location: welcome.php");
            exit();
        } else {
            $error_msg = "Incorrect username or password.";
        }
    } else {
        $error_msg = "Patient account not found.";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 60%, #38bdf8 100%); }
    </style>
</head>
<body class="min-h-screen hero-bg flex flex-col">

<!-- Header -->
<header class="bg-slate-900/80 backdrop-blur-sm text-white">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-base tracking-tight">MedTour <span class="text-sky-300">Services</span></span>
        </a>
        <nav class="flex items-center gap-4 text-sm">
            <a href="index.php"        class="text-slate-300 hover:text-white transition">Home</a>
            <a href="help.php"         class="text-slate-300 hover:text-white transition">Help</a>
            <a href="login_admin.php"  class="text-slate-300 hover:text-white transition">Admin</a>
            <a href="login_doctor.php" class="text-slate-300 hover:text-white transition">Doctor</a>
        </nav>
    </div>
</header>

<!-- Login Card -->
<div class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 shadow-2xl">

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-white text-center mb-1">Patient Login</h1>
            <p class="text-sky-200 text-sm text-center mb-7">Access your medical tourism dashboard</p>

            <!-- Error -->
            <?php if (!empty($error_msg)): ?>
            <div class="bg-rose-500/20 border border-rose-400/40 text-rose-200 text-sm rounded-xl px-4 py-3 mb-5">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="#" class="space-y-4">
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" placeholder="Enter your username" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 transition">
                </div>
                <div>
                    <label class="block text-sky-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 transition">
                </div>
                <button type="submit"
                    class="w-full bg-sky-500 hover:bg-sky-400 text-white font-bold py-3 rounded-xl transition shadow-lg hover:shadow-sky-400/30 mt-2">
                    Sign In →
                </button>
            </form>

            <p class="text-center text-sky-200 text-sm mt-6">
                Don't have an account?
                <a href="signup_user.php" class="text-white font-semibold hover:underline">Sign up here</a>
            </p>
        </div>

        <!-- Other login options -->
        <div class="mt-6 flex justify-center gap-4 text-sm">
            <a href="login_admin.php"  class="text-sky-200 hover:text-white transition">Admin Login</a>
            <span class="text-sky-300/40">·</span>
            <a href="login_doctor.php" class="text-sky-200 hover:text-white transition">Doctor Login</a>
        </div>
    </div>
</div>

<footer class="bg-slate-900/60 text-slate-400 py-4 text-center text-xs">
    &copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.
</footer>

</body>
</html>
