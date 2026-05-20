<?php
session_start();
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'mt_db');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE name = ? AND role = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_role'] = $row['role'];
            header("Location: welcome_admin.php");
            exit();
        } else {
            $error_message = "Incorrect username or password.";
        }
    } else {
        $error_message = "Admin account not found.";
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
    <title>Admin Login – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 60%, #6366f1 100%); }
    </style>
</head>
<body class="min-h-screen hero-bg flex flex-col">

<!-- Header -->
<header class="bg-slate-900/80 backdrop-blur-sm text-white">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-base tracking-tight">MedTour <span class="text-indigo-300">Admin</span></span>
        </a>
        <nav class="flex items-center gap-4 text-sm">
            <a href="index.php"       class="text-slate-300 hover:text-white transition">Home</a>
            <a href="help.php"        class="text-slate-300 hover:text-white transition">Help</a>
            <a href="login_user.php"  class="text-slate-300 hover:text-white transition">Patient Login</a>
        </nav>
    </div>
</header>

<!-- Login Card -->
<div class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 shadow-2xl">

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-white text-center mb-1">Admin Login</h1>
            <p class="text-indigo-200 text-sm text-center mb-7">Access the administration control panel</p>

            <?php if (!empty($error_message)): ?>
            <div class="bg-rose-500/20 border border-rose-400/40 text-rose-200 text-sm rounded-xl px-4 py-3 mb-5">
                ⚠️ <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Admin Username</label>
                    <input type="text" name="username" placeholder="Enter admin username" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <button type="submit"
                    class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-bold py-3 rounded-xl transition shadow-lg hover:shadow-indigo-400/30 mt-2">
                    Sign In →
                </button>
            </form>

            <p class="text-center text-indigo-200 text-sm mt-6">
                Don't have an admin account?
                <a href="signup_admin.php" class="text-white font-semibold hover:underline">Register here</a>
            </p>
        </div>

        <div class="mt-6 flex justify-center gap-4 text-sm">
            <a href="login_user.php"   class="text-indigo-200 hover:text-white transition">Patient Login</a>
            <span class="text-indigo-300/40">·</span>
            <a href="login_doctor.php" class="text-indigo-200 hover:text-white transition">Doctor Login</a>
        </div>
    </div>
</div>

<footer class="bg-slate-900/60 text-slate-400 py-4 text-center text-xs">
    &copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.
</footer>

</body>
</html>
