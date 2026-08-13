<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php");
    exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];
$sql = "SELECT vb.* FROM visa_bookings vb JOIN patients p ON vb.patient_id = p.id WHERE p.user_id = $user_id ORDER BY vb.id DESC";
$result = $conn->query($sql);
$statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','completed'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-rose-100 text-rose-700'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Visa Applications – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif;}@media print{header,footer,.no-print{display:none;}}</style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-violet-900 text-white shadow-lg no-print">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-violet-300">Visa</span></span>
        </a>
        <nav class="flex items-center gap-5">
            <a href="welcome.php"          class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="submit_visa.php"      class="text-slate-300 hover:text-white text-sm transition">Apply New</a>
            <a href="logout_user.php"      class="bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">📄 My Visa Applications</h1>
            <p class="text-slate-500 text-sm">All your medical visa requests</p>
        </div>
        <div class="flex gap-3 no-print">
            <button onclick="window.print()" class="border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-semibold px-4 py-2 rounded-lg transition">🖨️ Print</button>
            <a href="submit_visa.php" class="bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">+ Apply New</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Visa Type</th>
                        <th class="px-4 py-3 text-left font-semibold">Country</th>
                        <th class="px-4 py-3 text-left font-semibold">Passport #</th>
                        <th class="px-4 py-3 text-left font-semibold">Application Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $s = strtolower($row['status']);
                            $badge = $statusColors[$s] ?? 'bg-slate-100 text-slate-600';
                            echo "<tr class='hover:bg-slate-50 transition'>";
                            echo "<td class='px-4 py-3 font-mono text-slate-500'>#{$row['id']}</td>";
                            echo "<td class='px-4 py-3 font-medium'>".htmlspecialchars($row['visa_type'])."</td>";
                            echo "<td class='px-4 py-3'>".htmlspecialchars($row['country'])."</td>";
                            echo "<td class='px-4 py-3 text-slate-500'>".htmlspecialchars($row['passport_number'])."</td>";
                            echo "<td class='px-4 py-3 text-slate-500'>{$row['application_date']}</td>";
                            echo "<td class='px-4 py-3'><span class='inline-block $badge text-xs font-semibold px-2.5 py-1 rounded-full'>".ucfirst($s)."</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-4 py-12 text-center text-slate-400'>
                            <div class='text-4xl mb-3'>📄</div>
                            <p class='font-medium text-slate-500'>No visa applications found.</p>
                            <a href='submit_visa.php' class='text-violet-600 hover:underline text-sm'>Apply for your first visa →</a>
                        </td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10 no-print">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>
</body>
</html>
