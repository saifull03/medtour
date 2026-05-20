<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login_admin.php"); exit();
}
$conn = new mysqli("localhost","root","","mt_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$search = isset($_GET["patient_id"]) ? $_GET["patient_id"] : "";
$sql = "SELECT * FROM hotel_bookings";
if (!empty($search)) $sql .= " WHERE patient_id = " . intval($search);
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);

$statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','completed'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-rose-100 text-rose-700'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Bookings – Admin Panel</title>
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
        <nav class="hidden md:flex items-center gap-5">
            <a href="welcome_admin.php"        class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="view_admin_transport.php"  class="text-slate-300 hover:text-white text-sm transition">Transport</a>
            <a href="view_admin_visa.php"       class="text-slate-300 hover:text-white text-sm transition">Visa</a>
            <a href="view_admin_hospital.php"   class="text-slate-300 hover:text-white text-sm transition">Hospital</a>
            <a href="view_admin_hotel.php"      class="text-indigo-300 text-sm font-semibold">Hotel</a>
        </nav>
        <a href="logout_admin.php" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">🏨 Hotel Bookings</h1>
            <p class="text-slate-500 text-sm">Monitor all patient hotel reservation records</p>
        </div>
        <a href="welcome_admin.php" class="text-slate-500 hover:text-slate-800 text-sm transition">← Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 mb-6">
        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="flex items-center gap-3">
            <input type="text" name="patient_id" placeholder="Search by Patient ID…"
                value="<?php echo htmlspecialchars($search); ?>"
                class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 w-56">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">🔍 Search</button>
            <?php if (!empty($search)): ?><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-slate-500 hover:text-slate-800 text-sm transition">Clear</a><?php endif; ?>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Patient ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Hotel Name</th>
                        <th class="px-4 py-3 text-left font-semibold">Check-in</th>
                        <th class="px-4 py-3 text-left font-semibold">Check-out</th>
                        <th class="px-4 py-3 text-left font-semibold">Guests</th>
                        <th class="px-4 py-3 text-left font-semibold">Room Type</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $s = strtolower($row['status'] ?? 'pending');
                            $badge = $statusColors[$s] ?? 'bg-slate-100 text-slate-600';
                            echo "<tr class='hover:bg-slate-50 transition'>";
                            echo "<td class='px-4 py-3 font-mono text-slate-500'>#{$row['id']}</td>";
                            echo "<td class='px-4 py-3'>{$row['patient_id']}</td>";
                            echo "<td class='px-4 py-3 font-medium'>".htmlspecialchars($row['hotel_name'])."</td>";
                            echo "<td class='px-4 py-3 text-slate-500'>{$row['checkin_date']}</td>";
                            echo "<td class='px-4 py-3 text-slate-500'>{$row['checkout_date']}</td>";
                            echo "<td class='px-4 py-3'>{$row['num_guests']}</td>";
                            echo "<td class='px-4 py-3'>".htmlspecialchars($row['room_type'])."</td>";
                            echo "<td class='px-4 py-3'><span class='inline-block $badge text-xs font-semibold px-2.5 py-1 rounded-full'>".ucfirst($s)."</span></td>";
                            echo "<td class='px-4 py-3'><button onclick=\"location.href='update.php?id={$row['id']}&type=hotel'\" class='bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition'>Update</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='px-4 py-8 text-center text-slate-400'>No hotel bookings found.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services Administration. All rights reserved.</p>
</footer>
</body>
</html>
