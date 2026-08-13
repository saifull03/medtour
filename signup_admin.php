<?php
$message = ""; $message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username         = $_POST['username'];
    $email            = $_POST['email'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!"; $message_type = "error";
    } else {
        $conn = new mysqli('localhost', 'root', '', 'mt_db');
        if ($conn->connect_error) {
            $message = "Connection failed: " . $conn->connect_error; $message_type = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $message = "Admin account created! <a href='login_admin.php' class='underline font-semibold'>Login here →</a>";
                $message_type = "success";
            } else {
                $message = "Error: " . $stmt->error; $message_type = "error";
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up – Medical Tourism Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 60%, #6366f1 100%); }
    </style>
</head>
<body class="min-h-screen hero-bg flex flex-col">
<header class="bg-slate-900/80 backdrop-blur-sm text-white">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-base">MedTour <span class="text-indigo-300">Admin</span></span>
        </a>
        <nav class="flex items-center gap-4 text-sm">
            <a href="index.php"       class="text-slate-300 hover:text-white transition">Home</a>
            <a href="login_admin.php" class="text-slate-300 hover:text-white transition">Admin Login</a>
        </nav>
    </div>
</header>

<div class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 shadow-2xl">
            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl">🛡️</div>
            </div>
            <h1 class="text-2xl font-bold text-white text-center mb-1">Create Admin Account</h1>
            <p class="text-indigo-200 text-sm text-center mb-7">Register a new administrator account</p>

            <?php if (!empty($message)): ?>
            <div class="<?php echo $message_type === 'success' ? 'bg-emerald-500/20 border-emerald-400/40 text-emerald-200' : 'bg-rose-500/20 border-rose-400/40 text-rose-200'; ?> border rounded-xl px-4 py-3 mb-5 text-sm">
                <?php echo $message_type === 'success' ? '✅' : '⚠️'; ?> <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="#" class="space-y-4">
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" placeholder="Admin username" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" placeholder="admin@medtour.com" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="Create a strong password" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <div>
                    <label class="block text-indigo-200 text-xs font-semibold uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter your password" required
                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-bold py-3 rounded-xl transition shadow-lg mt-2">
                    Create Admin Account →
                </button>
            </form>

            <p class="text-center text-indigo-200 text-sm mt-6">
                Already have an account?
                <a href="login_admin.php" class="text-white font-semibold hover:underline">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<footer class="bg-slate-900/60 text-slate-400 py-4 text-center text-xs">
    &copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.
</footer>
</body>
</html>
