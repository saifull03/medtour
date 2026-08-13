<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php"); exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$pr = $conn->prepare("SELECT id FROM patients WHERE user_id=?");
$pr->bind_param("i",$user_id); $pr->execute();
$pr_res = $pr->get_result();
if ($pr_res->num_rows === 0) {
    $ins = $conn->prepare("INSERT INTO patients (user_id) VALUES (?)");
    $ins->bind_param("i",$user_id); $ins->execute();
    $patient_id = $conn->insert_id; $ins->close();
} else { $patient_id = $pr_res->fetch_assoc()['id']; }
$pr->close();

$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type   = trim($_POST['transport_type'] ?? '');
    $pickup = trim($_POST['pickup_location'] ?? '');
    $dest   = trim($_POST['destination'] ?? '');
    $date   = trim($_POST['date'] ?? '');
    $time   = trim($_POST['time'] ?? '');

    if ($type && $pickup && $dest && $date) {
        $stmt = $conn->prepare("INSERT INTO transport_bookings (patient_id,transport_type,pickup_location,destination,date,time) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isssss", $patient_id, $type, $pickup, $dest, $date, $time);
        if ($stmt->execute()) {
            $message = "Transport booked! Your <strong>" . htmlspecialchars($type) . "</strong> on <strong>" . htmlspecialchars($date) . "</strong>" . ($time ? " at " . htmlspecialchars($time) : "") . " is confirmed.";
            $msg_type = "success";
        } else { $message = "Booking error: " . $stmt->error; $msg_type = "error"; }
        $stmt->close();
    } else { $message = "Please fill in all required fields."; $msg_type = "error"; }
}
$conn->close();

$transport_types = [
    ['value'=>'Taxi','label'=>'Taxi','icon'=>'🚕','desc'=>'City rides & short trips'],
    ['value'=>'Private Car','label'=>'Private Car','icon'=>'🚗','desc'=>'Comfortable private transfer'],
    ['value'=>'Bus','label'=>'Bus','icon'=>'🚌','desc'=>'Group travel & shuttles'],
    ['value'=>'Train','label'=>'Train','icon'=>'🚆','desc'=>'Inter-city rail travel'],
    ['value'=>'Ambulance','label'=>'Ambulance','icon'=>'🚑','desc'=>'Medical emergency transport'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Transport – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field { width:100%; border:2px solid #e2e8f0; border-radius:12px; padding:12px 16px; font-size:14px; color:#334155; background:#fff; transition:all 0.2s; outline:none; }
        .input-field:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
        .step-card { background:white; border-radius:16px; border:2px solid #f1f5f9; padding:20px; transition:border-color 0.2s; }
        .step-card:focus-within { border-color:#a7f3d0; }
        .transport-card { border:2px solid #e2e8f0; border-radius:14px; padding:14px; cursor:pointer; transition:all 0.15s; text-align:center; }
        .transport-card:hover { border-color:#6ee7b7; background:#f0fdf4; }
        .transport-card.selected { border-color:#10b981; background:#ecfdf5; }
        .time-btn { border:2px solid #e2e8f0; border-radius:10px; padding:8px 12px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.15s; color:#475569; background:white; }
        .time-btn:hover { border-color:#6ee7b7; color:#10b981; }
        .time-btn.active { border-color:#10b981; background:#10b981; color:white; }
        input[type="date"]::-webkit-calendar-picker-indicator { cursor:pointer; filter:invert(40%) sepia(80%) saturate(400%) hue-rotate(100deg); }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-slate-50 min-h-screen">

<header class="bg-white/80 backdrop-blur sticky top-0 z-50 shadow-sm border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-emerald-100 p-1">
            <span class="font-bold text-slate-800">MedTour <span class="text-emerald-500">Transport</span></span>
        </a>
        <nav class="flex items-center gap-4">
            <a href="welcome.php" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition">← Dashboard</a>
            <a href="transport_bookings.php" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium transition">My Rides</a>
            <a href="logout_user.php" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-2xl mb-4 text-3xl">🚗</div>
        <h1 class="text-3xl font-bold text-slate-800">Book Transportation</h1>
        <p class="text-slate-500 mt-2">Schedule your pickup, transfer, or city ride</p>
    </div>

    <?php if (!empty($message)): ?>
    <div class="mb-6 rounded-2xl px-6 py-5 flex items-start gap-4 <?php echo $msg_type==='success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'; ?>">
        <div class="text-2xl"><?php echo $msg_type==='success' ? '✅' : '❌'; ?></div>
        <div>
            <p class="font-semibold"><?php echo $msg_type==='success' ? 'Ride Confirmed!' : 'Booking Failed'; ?></p>
            <p class="text-sm mt-1"><?php echo $message; ?></p>
            <?php if ($msg_type==='success'): ?>
            <div class="flex gap-3 mt-3">
                <a href="transport_bookings.php" class="text-sm font-semibold underline">View My Rides →</a>
                <a href="welcome.php" class="text-sm font-semibold underline ml-3">Back to Dashboard</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <!-- Transport Type -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-bold flex items-center justify-center">1</div>
                <h2 class="font-bold text-slate-700 text-lg">Transport Type</h2>
            </div>
            <div class="grid grid-cols-5 gap-2">
                <?php foreach ($transport_types as $t): ?>
                <label class="transport-card">
                    <input type="radio" name="transport_type" value="<?php echo $t['value']; ?>" class="hidden transport-radio" required>
                    <div class="text-2xl mb-1"><?php echo $t['icon']; ?></div>
                    <div class="text-xs font-bold text-slate-700"><?php echo $t['label']; ?></div>
                    <div class="text-xs text-slate-400 mt-0.5 leading-tight hidden sm:block"><?php echo $t['desc']; ?></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pickup & Destination -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-bold flex items-center justify-center">2</div>
                <h2 class="font-bold text-slate-700 text-lg">Route Details</h2>
            </div>
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500 text-lg">📍</div>
                    <input type="text" name="pickup_location" placeholder="Pickup location (e.g., Cairo Airport)" required
                        class="input-field pl-10">
                </div>
                <!-- Arrow -->
                <div class="text-center text-slate-300 text-xl">↓</div>
                <div class="relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400 text-lg">🏁</div>
                    <input type="text" name="destination" placeholder="Destination (e.g., Cairo Medical Center)" required
                        class="input-field pl-10">
                </div>
            </div>
        </div>

        <!-- Date & Time -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-500 text-white text-sm font-bold flex items-center justify-center">3</div>
                <h2 class="font-bold text-slate-700 text-lg">When?</h2>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">📅 Date *</label>
                <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>" class="input-field">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">🕐 Time Slot</label>
                <div class="grid grid-cols-4 gap-2">
                    <?php foreach (['06:00','08:00','10:00','12:00','14:00','16:00','18:00','20:00'] as $s): ?>
                    <button type="button" class="time-btn" onclick="selectTime('<?php echo $s; ?>', this)">
                        <?php echo date('g:i A', strtotime($s)); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="time" id="time_val" value="">
            </div>
        </div>

        <!-- Submit -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-1">Confirm Your Ride</h3>
            <p class="text-emerald-200 text-sm mb-5">You'll receive confirmation instantly after booking.</p>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-white text-emerald-600 font-bold py-3 rounded-xl hover:bg-emerald-50 transition shadow-lg">
                    🚗 Book Transport
                </button>
                <a href="welcome.php" class="flex-1 text-center border border-white/40 text-white font-semibold py-3 rounded-xl hover:bg-white/10 transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>

<script>
document.querySelectorAll('.transport-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.transport-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('.transport-radio').checked = true;
    });
});

function selectTime(time, btn) {
    document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('time_val').value = time;
}
</script>
</body>
</html>
