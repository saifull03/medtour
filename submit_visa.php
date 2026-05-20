<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php"); exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$pr = $conn->query("SELECT id FROM patients WHERE user_id=?");
$stmt = $conn->prepare("SELECT id FROM patients WHERE user_id=?");
$stmt->bind_param("i",$user_id); $stmt->execute();
$pr = $stmt->get_result();
if ($pr->num_rows === 0) {
    $stmt2 = $conn->prepare("INSERT INTO patients (user_id,phone,country,passport_no) VALUES (?,NULL,NULL,NULL)");
    $stmt2->bind_param("i",$user_id); $stmt2->execute(); $patient_id = $conn->insert_id; $stmt2->close();
} else { $patient_id = $pr->fetch_assoc()['id']; }
$stmt->close();

$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['visa_type']) && !empty($_POST['country']) && !empty($_POST['passport_number']) && !empty($_POST['application_date'])) {
        $stmt = $conn->prepare("INSERT INTO visa_bookings (patient_id,visa_type,country,passport_number,application_date,status) VALUES (?,?,?,?,?,'pending')");
        $stmt->bind_param("issss",$patient_id,$_POST['visa_type'],$_POST['country'],$_POST['passport_number'],$_POST['application_date']);
        if ($stmt->execute()) { $message = "✅ Visa application submitted successfully! We'll contact you soon."; $msg_type = "success"; }
        else { $message = "❌ Error: ".$stmt->error; $msg_type = "error"; }
        $stmt->close();
    } else { $message = "❌ All fields are required."; $msg_type = "error"; }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Application – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-violet-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-violet-300">Visa</span></span>
        </a>
        <nav class="flex items-center gap-5">
            <a href="welcome.php"     class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="logout_user.php" class="bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-violet-600 to-purple-700 p-7 text-white">
            <div class="text-3xl mb-2">📄</div>
            <h1 class="text-2xl font-bold">Visa Assistance Application</h1>
            <p class="text-violet-100 text-sm mt-1">Submit your visa request and our experts will guide you through the process</p>
        </div>

        <div class="p-7">
            <?php if (!empty($message)): ?>
            <div class="<?php echo $msg_type==='success' ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-rose-50 border-rose-300 text-rose-800'; ?> border rounded-xl px-4 py-3 mb-6 text-sm font-medium">
                <?php echo $message; ?>
                <?php if ($msg_type==='success'): ?><br><a href="welcome.php" class="underline mt-1 inline-block">← Back to Dashboard</a><?php endif; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-5">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Visa Type *</label>
                    <select name="visa_type" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 transition bg-white">
                        <option value="">— Select visa type —</option>
                        <option value="Tourist">🌍 Tourist</option>
                        <option value="Business">💼 Business</option>
                        <option value="Medical">🏥 Medical</option>
                        <option value="Student">🎓 Student</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Destination Country *</label>
                    <input type="text" name="country" placeholder="e.g., Thailand, Turkey, India" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 transition bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Passport Number *</label>
                    <input type="text" name="passport_number" placeholder="e.g., A12345678" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 transition bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Application Date *</label>
                    <input type="date" name="application_date" required min="<?php echo date('Y-m-d'); ?>"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-400 transition bg-white">
                </div>

                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4 text-violet-700 text-xs">
                    <strong>ℹ️ Note:</strong> After submission, our visa specialists will review your application and contact you within 2-3 business days with further instructions.
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-violet-500 hover:bg-violet-600 text-white font-bold py-3 rounded-xl transition shadow-lg">
                        📄 Submit Application
                    </button>
                    <a href="welcome.php" class="flex-1 text-center border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold py-3 rounded-xl transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-6">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>
</body>
</html>
