<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login_user.php"); exit(); }

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$pr = $conn->query("SELECT id FROM patients WHERE user_id=$user_id");
if ($pr->num_rows === 0) { $conn->query("INSERT INTO patients (user_id) VALUES ($user_id)"); $patient_id = $conn->insert_id; }
else { $patient_id = $pr->fetch_assoc()['id']; }

$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['patient_id'])) {
    $pid      = intval($_POST['patient_id']);
    $type     = $conn->real_escape_string($_POST['transport_type']);
    $pickup   = $conn->real_escape_string($_POST['pickup_location']);
    $dest     = $conn->real_escape_string($_POST['destination']);
    $date     = $conn->real_escape_string($_POST['date']);
    $time     = $conn->real_escape_string($_POST['time']);

    if (!empty($type) && !empty($pickup) && !empty($dest) && !empty($date) && !empty($time)) {
        $sql = "INSERT INTO transport_bookings (patient_id,transport_type,pickup_location,destination,date,time) VALUES ($pid,'$type','$pickup','$dest','$date','$time')";
        if ($conn->query($sql) === TRUE) { $message = "✅ Transport booked successfully!"; $msg_type = "success"; }
        else { $message = "❌ Error: ".$conn->error; $msg_type = "error"; }
    } else { $message = "❌ All fields are required."; $msg_type = "error"; }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Transport – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-emerald-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-emerald-300">Transport</span></span>
        </a>
        <nav class="flex items-center gap-5">
            <a href="welcome.php"     class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="logout_user.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-7 text-white">
            <div class="text-3xl mb-2">🚗</div>
            <h1 class="text-2xl font-bold">Book Transportation</h1>
            <p class="text-emerald-100 text-sm mt-1">Schedule your pickup, hospital transfer or city ride</p>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Transport Type *</label>
                    <select name="transport_type" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition bg-white">
                        <option value="">— Select type —</option>
                        <option value="Taxi">🚕 Taxi</option>
                        <option value="Bus">🚌 Bus</option>
                        <option value="Train">🚆 Train</option>
                        <option value="Private Car">🚗 Private Car</option>
                        <option value="Ambulance">🚑 Ambulance</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pickup Location *</label>
                    <input type="text" name="pickup_location" placeholder="e.g., Cairo Airport, Nile Hotel" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Destination *</label>
                    <input type="text" name="destination" placeholder="e.g., Cairo Medical Center, Maadi Hospital" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition bg-white">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Date *</label>
                        <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Time *</label>
                        <input type="time" name="time" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition bg-white">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 rounded-xl transition shadow-lg">
                        🚗 Book Transport
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
