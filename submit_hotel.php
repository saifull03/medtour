<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php"); exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$pr = $conn->query("SELECT id FROM patients WHERE user_id=$user_id");
if ($pr->num_rows === 0) {
    $conn->query("INSERT INTO patients (user_id) VALUES ($user_id)");
    $patient_id = $conn->insert_id;
} else { $patient_id = $pr->fetch_assoc()['id']; }

$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['patient_id'])) {
    $pid        = intval($_POST['patient_id']);
    $hotel_name = $conn->real_escape_string($_POST['hotel_name']);
    $checkin    = $conn->real_escape_string($_POST['checkin_date']);
    $checkout   = $conn->real_escape_string($_POST['checkout_date']);
    $guests     = intval($_POST['num_guests']);
    $room_type  = $conn->real_escape_string($_POST['room_type']);

    if (!empty($hotel_name) && !empty($checkin) && !empty($checkout) && !empty($room_type)) {
        if (strtotime($checkin) >= strtotime($checkout)) {
            $message = "❌ Check-out date must be after check-in date."; $msg_type = "error";
        } else {
            $sql = "INSERT INTO hotel_bookings (patient_id,hotel_name,checkin_date,checkout_date,num_guests,room_type) VALUES ($pid,'$hotel_name','$checkin','$checkout',$guests,'$room_type')";
            if ($conn->query($sql) === TRUE) { $message = "✅ Hotel booked successfully! Booking confirmed."; $msg_type = "success"; }
            else { $message = "❌ Error: ".$conn->error; $msg_type = "error"; }
        }
    } else { $message = "❌ All fields are required."; $msg_type = "error"; }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-sky-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-sky-300">Hotel</span></span>
        </a>
        <nav class="flex items-center gap-5">
            <a href="welcome.php"     class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="logout_user.php" class="bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-blue-700 p-7 text-white">
            <div class="text-3xl mb-2">🏨</div>
            <h1 class="text-2xl font-bold">Book Hotel Accommodation</h1>
            <p class="text-sky-100 text-sm mt-1">Choose your preferred hotel, room type and stay duration</p>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Select Hotel *</label>
                    <select name="hotel_name" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-400 transition bg-white">
                        <option value="">— Choose a hotel —</option>
                        <option value="Cairo Marriott Hotel">Cairo Marriott Hotel</option>
                        <option value="Kempinski Nile Hotel">Kempinski Nile Hotel</option>
                        <option value="Four Seasons Cairo">Four Seasons Cairo</option>
                        <option value="Hilton Cairo Zamalek">Hilton Cairo Zamalek</option>
                        <option value="InterContinental Semiramis">InterContinental Semiramis</option>
                        <option value="Hotel A">Hotel A</option>
                        <option value="Hotel B">Hotel B</option>
                        <option value="Hotel C">Hotel C</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Check-in Date *</label>
                        <input type="date" name="checkin_date" required min="<?php echo date('Y-m-d'); ?>"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-400 transition bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Check-out Date *</label>
                        <input type="date" name="checkout_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-400 transition bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Number of Guests *</label>
                        <input type="number" name="num_guests" min="1" max="10" placeholder="e.g., 2" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-400 transition bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Room Type *</label>
                        <select name="room_type" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-400 transition bg-white">
                            <option value="">— Select room —</option>
                            <option value="Single">🛏️ Single</option>
                            <option value="Double">🛏️🛏️ Double</option>
                            <option value="Suite">🌟 Suite</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-xl transition shadow-lg">
                        🏨 Book Hotel
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
