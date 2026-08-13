<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login_doctor.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];

// Get doctor record
$doctor_id = 0; $doctor_name = 'Doctor'; $specialization = '';
$stmt = $conn->prepare("SELECT d.id, u.name, d.specialization FROM doctors d JOIN users u ON d.user_id=u.id WHERE d.user_id=?");
$stmt->bind_param("i", $user_id); $stmt->execute();
$dr = $stmt->get_result()->fetch_assoc();
if ($dr) { $doctor_id = $dr['id']; $doctor_name = $dr['name']; $specialization = $dr['specialization']; }
$stmt->close();

// Status filter
$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

// Build query
$where = "a.doctor_id = ?";
$params = [$doctor_id];
$types  = "i";
if ($filter !== 'all') { $where .= " AND a.status = ?"; $params[] = $filter; $types .= "s"; }
if ($search)           { $where .= " AND (u.name LIKE ? OR h.name LIKE ?)"; $like = "%$search%"; $params[] = $like; $params[] = $like; $types .= "ss"; }

$sql = "SELECT a.id, a.appointment_date, a.status,
               u.name AS patient_name, u.email AS patient_email,
               h.name AS hospital_name
        FROM appointments a
        JOIN patients p   ON a.patient_id  = p.id
        JOIN users u      ON p.user_id     = u.id
        JOIN hospitals h  ON a.hospital_id = h.id
        WHERE $where
        ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count by status
$counts = ['all'=>0,'pending'=>0,'approved'=>0,'completed'=>0,'cancelled'=>0];
$cs = $conn->prepare("SELECT status, COUNT(*) as c FROM appointments WHERE doctor_id=? GROUP BY status");
$cs->bind_param("i",$doctor_id); $cs->execute();
$cr = $cs->get_result();
while ($c = $cr->fetch_assoc()) {
    $s = strtolower($c['status']);
    $counts[$s] = (int)$c['c'];
    $counts['all'] += (int)$c['c'];
}
$cs->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments – MedTour Doctor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print { header, .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-slate-900 to-blue-900 text-white shadow-lg no-print">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-blue-300">Doctor</span></span>
        </div>
        <nav class="hidden md:flex items-center gap-6">
            <a href="welcome_doctor.php"    class="text-slate-300 hover:text-white text-sm font-medium transition">Dashboard</a>
            <a href="view_appointments.php" class="text-white text-sm font-semibold border-b-2 border-blue-400 pb-0.5">Appointments</a>
            <a href="help.php"              class="text-slate-300 hover:text-white text-sm font-medium transition">Help</a>
        </nav>
        <a href="logout_doctor.php" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Title -->
    <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">📋 Appointment List</h1>
            <p class="text-slate-500 text-sm mt-1">
                Dr. <?php echo htmlspecialchars($doctor_name); ?>
                <?php if ($specialization): ?> &middot; <span class="text-blue-600 font-medium"><?php echo htmlspecialchars($specialization); ?></span><?php endif; ?>
            </p>
        </div>
        <div class="flex gap-2 no-print">
            <button onclick="window.print()" class="border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1.5">
                🖨️ Print
            </button>
            <a href="welcome_doctor.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6 no-print">
        <?php
        $stat_cards = [
            ['label'=>'Total',     'key'=>'all',       'color'=>'blue',   'icon'=>'📋'],
            ['label'=>'Pending',   'key'=>'pending',   'color'=>'amber',  'icon'=>'⏳'],
            ['label'=>'Approved',  'key'=>'approved',  'color'=>'emerald','icon'=>'✅'],
            ['label'=>'Completed', 'key'=>'completed', 'color'=>'sky',    'icon'=>'🏁'],
            ['label'=>'Cancelled', 'key'=>'cancelled', 'color'=>'rose',   'icon'=>'❌'],
        ];
        $color_map = [
            'blue'    => 'bg-blue-50 border-blue-100 text-blue-700',
            'amber'   => 'bg-amber-50 border-amber-100 text-amber-700',
            'emerald' => 'bg-emerald-50 border-emerald-100 text-emerald-700',
            'sky'     => 'bg-sky-50 border-sky-100 text-sky-700',
            'rose'    => 'bg-rose-50 border-rose-100 text-rose-700',
        ];
        foreach ($stat_cards as $sc):
            $cls = $color_map[$sc['color']];
            $active = ($filter === $sc['key']) ? 'ring-2 ring-offset-1 ring-' . $sc['color'] . '-400' : '';
        ?>
        <a href="?status=<?php echo $sc['key']; ?>&search=<?php echo urlencode($search); ?>"
            class="<?php echo $cls; ?> <?php echo $active; ?> border rounded-xl p-4 text-center hover:shadow-md transition cursor-pointer">
            <div class="text-xl mb-1"><?php echo $sc['icon']; ?></div>
            <div class="text-2xl font-bold"><?php echo $counts[$sc['key']]; ?></div>
            <div class="text-xs font-semibold mt-0.5"><?php echo $sc['label']; ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Search & Filters Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-5 no-print">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter); ?>">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search patient name or hospital..."
                    class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
            </div>
            <select name="status" onchange="this.form.submit()"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition bg-white">
                <option value="all"       <?php echo $filter==='all'       ? 'selected' : ''; ?>>All Statuses</option>
                <option value="pending"   <?php echo $filter==='pending'   ? 'selected' : ''; ?>>⏳ Pending</option>
                <option value="approved"  <?php echo $filter==='approved'  ? 'selected' : ''; ?>>✅ Approved</option>
                <option value="completed" <?php echo $filter==='completed' ? 'selected' : ''; ?>>🏁 Completed</option>
                <option value="cancelled" <?php echo $filter==='cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">Search</button>
            <?php if ($search || $filter !== 'all'): ?>
            <a href="view_appointments.php" class="border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-medium px-4 py-2.5 rounded-xl transition text-center">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <?php if (empty($rows)): ?>
        <div class="py-20 text-center text-slate-400">
            <div class="text-5xl mb-4">📭</div>
            <p class="font-semibold text-slate-500 text-lg">No appointments found</p>
            <p class="text-sm mt-2">
                <?php echo $search ? "No results matching \"<strong>$search</strong>\"." : "You have no " . ($filter !== 'all' ? $filter . " " : '') . "appointments yet."; ?>
            </p>
            <?php if ($search || $filter !== 'all'): ?>
            <a href="view_appointments.php" class="mt-4 inline-block text-blue-600 hover:underline text-sm font-medium">Clear filters</a>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <!-- Result count -->
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <p class="text-sm text-slate-500">Showing <strong class="text-slate-700"><?php echo count($rows); ?></strong> appointment<?php echo count($rows) !== 1 ? 's' : ''; ?></p>
            <?php if ($search): ?>
            <p class="text-sm text-blue-600">Results for "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
            <?php endif; ?>
        </div>

        <!-- Mobile Card View (sm and below) -->
        <div class="divide-y divide-slate-100 sm:hidden">
            <?php foreach ($rows as $row):
                $s = strtolower($row['status']);
                $badges = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','completed'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-rose-100 text-rose-700'];
                $badge = $badges[$s] ?? 'bg-slate-100 text-slate-600';
                $dt = $row['appointment_date'] ? date('D, d M Y', strtotime($row['appointment_date'])) : '—';
                $tm = $row['appointment_date'] && strpos($row['appointment_date'], ' ') !== false ? date('g:i A', strtotime($row['appointment_date'])) : '';
            ?>
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($row['patient_name']); ?></p>
                        <p class="text-xs text-slate-400"><?php echo htmlspecialchars($row['patient_email']); ?></p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full <?php echo $badge; ?>"><?php echo ucfirst($s); ?></span>
                </div>
                <div class="text-xs text-slate-500 space-y-1">
                    <p>🏥 <?php echo htmlspecialchars($row['hospital_name']); ?></p>
                    <p>📅 <?php echo $dt; ?><?php echo $tm ? " · ⏰ $tm" : ''; ?></p>
                    <p class="text-slate-400">Ref #<?php echo $row['id']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Ref #</th>
                        <th class="px-4 py-3 text-left font-semibold">Patient</th>
                        <th class="px-4 py-3 text-left font-semibold">Hospital</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Time</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($rows as $row):
                        $s = strtolower($row['status']);
                        $badges = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','completed'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-rose-100 text-rose-700'];
                        $badge = $badges[$s] ?? 'bg-slate-100 text-slate-600';
                        $has_time = strpos($row['appointment_date'], ' ') !== false;
                        $date_part = $has_time ? date('d M Y', strtotime($row['appointment_date'])) : date('d M Y', strtotime($row['appointment_date']));
                        $time_part = $has_time ? date('g:i A', strtotime($row['appointment_date'])) : '—';
                        // Determine if appointment is upcoming
                        $is_upcoming = strtotime($row['appointment_date']) >= strtotime('today');
                    ?>
                    <tr class="hover:bg-slate-50 transition <?php echo $is_upcoming && $s === 'approved' ? 'bg-blue-50/30' : ''; ?>">
                        <td class="px-4 py-4 font-mono text-slate-400 text-xs">#<?php echo $row['id']; ?></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm flex-shrink-0">
                                    <?php echo strtoupper(substr($row['patient_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($row['patient_name']); ?></p>
                                    <p class="text-xs text-slate-400"><?php echo htmlspecialchars($row['patient_email']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🏥</span>
                                <span class="text-slate-600 font-medium"><?php echo htmlspecialchars($row['hospital_name']); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-slate-700 font-medium"><?php echo $date_part; ?></div>
                            <?php if ($is_upcoming && $s !== 'cancelled'):
                                $days = (int)ceil((strtotime($row['appointment_date']) - time()) / 86400);
                            ?>
                            <div class="text-xs text-blue-500 font-medium mt-0.5">
                                <?php echo $days === 0 ? 'Today' : ($days === 1 ? 'Tomorrow' : "In $days days"); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-slate-500"><?php echo $time_part; ?></td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1 <?php echo $badge; ?> text-xs font-bold px-2.5 py-1.5 rounded-full">
                                <?php
                                $icons = ['pending'=>'⏳','approved'=>'✅','completed'=>'🏁','cancelled'=>'❌'];
                                echo ($icons[$s] ?? '') . ' ' . ucfirst($s);
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</main>

<footer class="bg-slate-900 text-slate-500 py-6 text-center text-xs mt-10 no-print">
    <p>&copy; <?php echo date('Y'); ?> MedTour Services. All rights reserved.</p>
</footer>

</body>
</html>
