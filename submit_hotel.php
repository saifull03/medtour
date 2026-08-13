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

$message = ""; $msg_type = ""; $nights = 0;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_name = trim($_POST['hotel_name'] ?? '');
    $checkin    = trim($_POST['checkin_date'] ?? '');
    $checkout   = trim($_POST['checkout_date'] ?? '');
    $guests     = intval($_POST['num_guests'] ?? 1);
    $room_type  = trim($_POST['room_type'] ?? '');

    if ($hotel_name && $checkin && $checkout && $room_type) {
        if (strtotime($checkin) >= strtotime($checkout)) {
            $message = "Check-out date must be after check-in date."; $msg_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO hotel_bookings (patient_id,hotel_name,checkin_date,checkout_date,num_guests,room_type) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("isssis", $patient_id, $hotel_name, $checkin, $checkout, $guests, $room_type);
            if ($stmt->execute()) {
                $nights = (strtotime($checkout) - strtotime($checkin)) / 86400;
                $message = "Hotel booked! <strong>$nights night" . ($nights>1?'s':'') . "</strong> at <strong>" . htmlspecialchars($hotel_name) . "</strong> confirmed.";
                $msg_type = "success";
            } else { $message = "Booking error: " . $stmt->error; $msg_type = "error"; }
            $stmt->close();
        }
    } else { $message = "Please fill in all required fields."; $msg_type = "error"; }
}
$conn->close();

$hotels = [
    ['name'=>'Cairo Marriott Hotel','stars'=>5,'area'=>'Zamalek, Cairo'],
    ['name'=>'Kempinski Nile Hotel','stars'=>5,'area'=>'Garden City, Cairo'],
    ['name'=>'Four Seasons Cairo','stars'=>5,'area'=>'Giza, Cairo'],
    ['name'=>'Hilton Cairo Zamalek','stars'=>4,'area'=>'Zamalek, Cairo'],
    ['name'=>'InterContinental Semiramis','stars'=>5,'area'=>'Corniche, Cairo'],
    ['name'=>'Steigenberger Hotel','stars'=>4,'area'=>'Tahrir Square, Cairo'],
    ['name'=>'Novotel Cairo Airport','stars'=>4,'area'=>'Cairo Airport'],
    ['name'=>'Al-Nile Residences','stars'=>3,'area'=>'Giza, Cairo'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field { width:100%; border:2px solid #e2e8f0; border-radius:12px; padding:12px 16px; font-size:14px; color:#334155; background:#fff; transition:all 0.2s; outline:none; }
        .input-field:focus { border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,0.12); }
        .step-card { background:white; border-radius:16px; border:2px solid #f1f5f9; padding:20px; transition:border-color 0.2s; }
        .step-card:focus-within { border-color:#bae6fd; }
        .hotel-card { border:2px solid #e2e8f0; border-radius:12px; padding:14px 16px; cursor:pointer; transition:all 0.15s; }
        .hotel-card:hover { border-color:#7dd3fc; background:#f0f9ff; }
        .hotel-card.selected { border-color:#0ea5e9; background:#f0f9ff; }
        .guest-btn { width:36px; height:36px; border-radius:10px; border:2px solid #e2e8f0; font-size:18px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.15s; color:#475569; background:white; }
        .guest-btn:hover { border-color:#7dd3fc; color:#0ea5e9; }
        input[type="date"]::-webkit-calendar-picker-indicator { cursor:pointer; filter:invert(40%) sepia(80%) saturate(500%) hue-rotate(180deg); }
        select.input-field { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%230ea5e9' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; padding-right:40px; }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-slate-50 min-h-screen">

<header class="bg-white/80 backdrop-blur sticky top-0 z-50 shadow-sm border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-sky-100 p-1">
            <span class="font-bold text-slate-800">MedTour <span class="text-sky-500">Hotel</span></span>
        </a>
        <nav class="flex items-center gap-4">
            <a href="welcome.php" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition">← Dashboard</a>
            <a href="hotel_bookings.php" class="text-sky-600 hover:text-sky-700 text-sm font-medium transition">My Bookings</a>
            <a href="logout_user.php" class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-100 rounded-2xl mb-4 text-3xl">🏨</div>
        <h1 class="text-3xl font-bold text-slate-800">Book Hotel Accommodation</h1>
        <p class="text-slate-500 mt-2">Comfortable stays near top medical facilities</p>
    </div>

    <?php if (!empty($message)): ?>
    <div class="mb-6 rounded-2xl px-6 py-5 flex items-start gap-4 <?php echo $msg_type==='success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'; ?>">
        <div class="text-2xl"><?php echo $msg_type==='success' ? '✅' : '❌'; ?></div>
        <div>
            <p class="font-semibold"><?php echo $msg_type==='success' ? 'Booking Confirmed!' : 'Booking Failed'; ?></p>
            <p class="text-sm mt-1"><?php echo $message; ?></p>
            <?php if ($msg_type==='success'): ?>
            <div class="flex gap-3 mt-3">
                <a href="hotel_bookings.php" class="text-sm font-semibold underline">View My Bookings →</a>
                <a href="welcome.php" class="text-sm font-semibold underline ml-3">Back to Dashboard</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <!-- Hotel Selection -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-sky-500 text-white text-sm font-bold flex items-center justify-center">1</div>
                <h2 class="font-bold text-slate-700 text-lg">Select Hotel</h2>
            </div>
            <div class="grid grid-cols-1 gap-2" id="hotelList">
                <?php foreach ($hotels as $h): ?>
                <label class="hotel-card flex items-center justify-between cursor-pointer">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="hotel_name" value="<?php echo htmlspecialchars($h['name']); ?>" class="hidden hotel-radio" required>
                        <div class="w-9 h-9 bg-sky-100 rounded-xl flex items-center justify-center text-lg">🏨</div>
                        <div>
                            <p class="font-semibold text-slate-700 text-sm"><?php echo htmlspecialchars($h['name']); ?></p>
                            <p class="text-slate-400 text-xs">📍 <?php echo htmlspecialchars($h['area']); ?></p>
                        </div>
                    </div>
                    <div class="text-amber-400 text-sm"><?php echo str_repeat('★', $h['stars']); ?></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Dates -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-sky-500 text-white text-sm font-bold flex items-center justify-center">2</div>
                <h2 class="font-bold text-slate-700 text-lg">Select Dates</h2>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">📅 Check-in *</label>
                    <input type="date" name="checkin_date" id="checkin_date" required min="<?php echo date('Y-m-d'); ?>" class="input-field" onchange="updateDuration()">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">📅 Check-out *</label>
                    <input type="date" name="checkout_date" id="checkout_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="input-field" onchange="updateDuration()">
                </div>
            </div>
            <div id="durationDisplay" class="mt-3 hidden bg-sky-50 rounded-xl px-4 py-3 text-sky-700 text-sm font-medium text-center">
                🌙 <span id="durationText"></span>
            </div>
        </div>

        <!-- Guests & Room -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-sky-500 text-white text-sm font-bold flex items-center justify-center">3</div>
                <h2 class="font-bold text-slate-700 text-lg">Guests & Room Type</h2>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">👥 Number of Guests</label>
                    <div class="flex items-center gap-3">
                        <button type="button" class="guest-btn" onclick="changeGuests(-1)">−</button>
                        <span id="guestDisplay" class="text-2xl font-bold text-slate-700 w-8 text-center">1</span>
                        <button type="button" class="guest-btn" onclick="changeGuests(1)">+</button>
                        <input type="hidden" name="num_guests" id="num_guests" value="1">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">🛏️ Room Type *</label>
                    <select name="room_type" required class="input-field">
                        <option value="">— Choose room —</option>
                        <option value="Single">Single Room</option>
                        <option value="Double">Double Room</option>
                        <option value="Twin">Twin Room</option>
                        <option value="Suite">Suite</option>
                        <option value="Deluxe">Deluxe Room</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="bg-gradient-to-r from-sky-600 to-blue-600 rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-2">Ready to Book?</h3>
            <p class="text-sky-200 text-sm mb-5">Your reservation will be confirmed immediately after submission.</p>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-white text-sky-600 font-bold py-3 rounded-xl hover:bg-sky-50 transition shadow-lg">
                    🏨 Confirm Hotel Booking
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
// Hotel card selection
document.querySelectorAll('.hotel-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.hotel-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('.hotel-radio').checked = true;
    });
});

// Guest counter
let guests = 1;
function changeGuests(delta) {
    guests = Math.max(1, Math.min(10, guests + delta));
    document.getElementById('guestDisplay').textContent = guests;
    document.getElementById('num_guests').value = guests;
}

// Duration display
function updateDuration() {
    const ci = document.getElementById('checkin_date').value;
    const co = document.getElementById('checkout_date').value;
    if (ci && co) {
        const nights = Math.round((new Date(co) - new Date(ci)) / 86400000);
        const display = document.getElementById('durationDisplay');
        if (nights > 0) {
            display.classList.remove('hidden');
            document.getElementById('durationText').textContent = nights + ' night' + (nights>1?'s':'') + ' stay selected';
            // update checkout min
            document.getElementById('checkout_date').min = ci;
        } else {
            display.classList.add('hidden');
        }
    }
}
</script>
</body>
</html>
