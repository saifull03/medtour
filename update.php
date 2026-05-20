<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login_admin.php"); exit();
}

$conn = new mysqli("localhost","root","","mt_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id   = isset($_GET['id'])   ? intval($_GET['id'])   : 0;
$type = isset($_GET['type']) ? $_GET['type']          : '';
$record = []; $error = "";

if ($type === 'hospital' && $id > 0) {
    $stmt = $conn->prepare("SELECT a.id,a.patient_id,a.doctor_id,a.hospital_id,a.appointment_date,a.status,u.name as patient_name FROM appointments a JOIN patients p ON a.patient_id=p.id JOIN users u ON p.user_id=u.id WHERE a.id=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $result = $stmt->get_result();
    if ($result->num_rows > 0) $record = $result->fetch_assoc(); else $error = "Appointment not found.";
    $stmt->close();
} elseif ($type === 'hotel' && $id > 0) {
    $stmt = $conn->prepare("SELECT h.*,u.name as patient_name FROM hotel_bookings h JOIN patients p ON h.patient_id=p.id JOIN users u ON p.user_id=u.id WHERE h.id=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $result = $stmt->get_result();
    if ($result->num_rows > 0) $record = $result->fetch_assoc(); else $error = "Hotel booking not found.";
    $stmt->close();
} elseif ($type === 'transport' && $id > 0) {
    $stmt = $conn->prepare("SELECT t.*,u.name as patient_name FROM transport_bookings t JOIN patients p ON t.patient_id=p.id JOIN users u ON p.user_id=u.id WHERE t.id=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $result = $stmt->get_result();
    if ($result->num_rows > 0) $record = $result->fetch_assoc(); else $error = "Transport booking not found.";
    $stmt->close();
} elseif ($type === 'visa' && $id > 0) {
    $stmt = $conn->prepare("SELECT v.*,u.name as patient_name FROM visa_bookings v JOIN patients p ON v.patient_id=p.id JOIN users u ON p.user_id=u.id WHERE v.id=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $result = $stmt->get_result();
    if ($result->num_rows > 0) $record = $result->fetch_assoc(); else $error = "Visa application not found.";
    $stmt->close();
} else { $error = "Invalid type or ID."; }
$conn->close();

$typeLabels = ['hospital'=>'Hospital Appointment','hotel'=>'Hotel Booking','transport'=>'Transport Booking','visa'=>'Visa Application'];
$typeIcons  = ['hospital'=>'🏥','hotel'=>'🏨','transport'=>'🚗','visa'=>'📄'];
$backLinks  = ['hospital'=>'view_admin_hospital.php','hotel'=>'view_admin_hotel.php','transport'=>'view_admin_transport.php','visa'=>'view_admin_visa.php'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking – Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-indigo-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="welcome_admin.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-indigo-300">Admin</span></span>
        </a>
        <div class="flex items-center gap-4">
            <a href="welcome_admin.php"  class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="logout_admin.php"   class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </div>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-12">
    <?php if ($error): ?>
    <div class="bg-rose-50 border border-rose-300 text-rose-800 rounded-2xl p-6 text-center">
        <div class="text-4xl mb-3">❌</div>
        <p class="font-semibold"><?php echo htmlspecialchars($error); ?></p>
        <a href="welcome_admin.php" class="text-indigo-600 hover:underline text-sm mt-3 inline-block">← Back to Dashboard</a>
    </div>
    <?php elseif ($record): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-slate-800 p-7 text-white">
            <div class="text-3xl mb-2"><?php echo $typeIcons[$type] ?? '📋'; ?></div>
            <h1 class="text-2xl font-bold">Update <?php echo $typeLabels[$type] ?? 'Booking'; ?></h1>
            <p class="text-indigo-200 text-sm mt-1">Record #<?php echo $record['id']; ?> · Patient: <?php echo htmlspecialchars($record['patient_name']); ?></p>
        </div>

        <div class="p-7">
            <form action="update_process.php" method="POST" class="space-y-5">
                <input type="hidden" name="id"   value="<?php echo $record['id']; ?>">
                <input type="hidden" name="type" value="<?php echo $type; ?>">

                <!-- Readonly patient info -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Patient Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($record['patient_name']); ?>" disabled
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                </div>

                <!-- Status always editable -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Status *</label>
                    <select name="status" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                        <?php foreach (['pending','approved','completed','cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php if(($record['status']??'') == $s) echo 'selected'; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Type-specific fields -->
                <?php if ($type === 'hospital'): ?>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Appointment Date</label>
                    <input type="date" value="<?php echo $record['appointment_date']; ?>" disabled
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                </div>
                <?php elseif ($type === 'hotel'): ?>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Hotel Name</label>
                    <input type="text" name="hotel_name" value="<?php echo htmlspecialchars($record['hotel_name']); ?>" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Check-in</label>
                        <input type="date" value="<?php echo $record['checkin_date']; ?>" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Check-out</label>
                        <input type="date" value="<?php echo $record['checkout_date']; ?>" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Room Type</label>
                    <select name="room_type" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                        <?php foreach (['Single','Double','Suite'] as $r): ?>
                        <option value="<?php echo $r; ?>" <?php if($record['room_type']==$r) echo 'selected'; ?>><?php echo $r; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif ($type === 'transport'): ?>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Transport Type</label>
                    <select name="transport_type" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                        <?php foreach (['Taxi','Bus','Train','Private Car','Ambulance'] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php if($record['transport_type']==$t) echo 'selected'; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pickup Location</label>
                    <input type="text" name="pickup_location" value="<?php echo htmlspecialchars($record['pickup_location']); ?>" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Destination</label>
                    <input type="text" name="destination" value="<?php echo htmlspecialchars($record['destination']); ?>" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition bg-white">
                </div>
                <?php elseif ($type === 'visa'): ?>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Country</label>
                        <input type="text" value="<?php echo htmlspecialchars($record['country']); ?>" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Passport No.</label>
                        <input type="text" value="<?php echo htmlspecialchars($record['passport_number']); ?>" disabled
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    </div>
                </div>
                <?php endif; ?>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 rounded-xl transition shadow-lg">
                        ✅ Update Booking
                    </button>
                    <a href="<?php echo $backLinks[$type] ?? 'welcome_admin.php'; ?>" class="flex-1 text-center border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold py-3 rounded-xl transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services Administration. All rights reserved.</p>
</footer>
</body>
</html>
