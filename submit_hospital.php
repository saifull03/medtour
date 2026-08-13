<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php"); exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Seed hospitals if empty
$check_h = $conn->query("SELECT COUNT(*) as c FROM hospitals")->fetch_assoc()['c'];
if ($check_h < 1) {
    $hs = [
        ['Cairo Medical Center','Cairo, Egypt'],['Al-Nile Hospital','Giza, Egypt'],
        ['Nasr City Hospital','Nasr City, Cairo'],['Maadi Hospital','Maadi, Cairo'],
        ['Heliopolis Medical Center','Heliopolis, Cairo'],['Zamalek Hospital','Zamalek, Cairo'],
        ['New Cairo Hospital','New Cairo'],['Sheikh Zayed Hospital','Sheikh Zayed City'],
        ['Al-Obour Hospital','Obour City'],['Nile Badrawi Hospital','Giza, Egypt']
    ];
    $stmt_h = $conn->prepare("INSERT IGNORE INTO hospitals (name,location) VALUES (?,?)");
    foreach ($hs as $h) { $stmt_h->bind_param("ss",$h[0],$h[1]); $stmt_h->execute(); }
    $stmt_h->close();
}

// Ensure patient record exists
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

// Fetch doctors and hospitals for dropdowns
$hospitals = $conn->query("SELECT id,name,location FROM hospitals ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$doctors   = $conn->query("SELECT d.id, u.name, d.specialization FROM doctors d JOIN users u ON d.user_id=u.id ORDER BY u.name")->fetch_all(MYSQLI_ASSOC);

// Handle form submission
$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doc_id  = intval($_POST['doctor_id'] ?? 0);
    $hosp_id = intval($_POST['hospital_id'] ?? 0);
    $appt_dt = trim($_POST['appointment_date'] ?? '');
    $appt_tm = trim($_POST['appointment_time'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');

    if ($doc_id && $hosp_id && $appt_dt) {
        $full_date = $appt_dt . ($appt_tm ? ' ' . $appt_tm : '');
        // Check if appointments table has a notes column
        $has_notes = false;
        $cols = $conn->query("SHOW COLUMNS FROM appointments LIKE 'notes'");
        if ($cols && $cols->num_rows > 0) $has_notes = true;

        if ($has_notes) {
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id,doctor_id,hospital_id,appointment_date,status,notes) VALUES (?,?,?,?,'pending',?)");
            $stmt->bind_param("iiiss", $patient_id, $doc_id, $hosp_id, $full_date, $notes);
        } else {
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id,doctor_id,hospital_id,appointment_date,status) VALUES (?,?,?,?,'pending')");
            $stmt->bind_param("iiis", $patient_id, $doc_id, $hosp_id, $full_date);
        }

        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            $message = "Appointment booked! Reference: <strong>#$new_id</strong>. We'll confirm within 24 hours.";
            $msg_type = "success";
        } else {
            $message = "Booking error: " . $stmt->error;
            $msg_type = "error";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all required fields.";
        $msg_type = "error";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hospital Appointment – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            color: #334155;
            background: #fff;
            transition: all 0.2s;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }
        .input-field:focus { border-color: #f43f5e; box-shadow: 0 0 0 3px rgba(244,63,94,0.12); }
        .step-card {
            background: white;
            border-radius: 16px;
            border: 2px solid #f1f5f9;
            padding: 20px;
            transition: border-color 0.2s;
        }
        .step-card:focus-within { border-color: #fda4af; }
        .doctor-option { cursor: pointer; border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; transition: all 0.15s; }
        .doctor-option:hover { border-color: #fda4af; background: #fff1f2; }
        .doctor-option.selected { border-color: #f43f5e; background: #fff1f2; }
        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator { cursor: pointer; filter: invert(40%) sepia(80%) saturate(400%) hue-rotate(300deg); }
        select.input-field { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23f43f5e' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }
        .time-btn { border: 2px solid #e2e8f0; border-radius: 10px; padding: 8px 14px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; color: #475569; }
        .time-btn:hover { border-color: #fda4af; color: #f43f5e; }
        .time-btn.active { border-color: #f43f5e; background: #f43f5e; color: white; }
    </style>
</head>
<body class="bg-gradient-to-br from-rose-50 via-white to-slate-50 min-h-screen">

<!-- Header -->
<header class="bg-white/80 backdrop-blur sticky top-0 z-50 shadow-sm border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-rose-100 p-1">
            <span class="font-bold text-slate-800">MedTour <span class="text-rose-500">Hospital</span></span>
        </a>
        <nav class="flex items-center gap-4">
            <a href="welcome.php" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition">← Dashboard</a>
            <a href="hospital_bookings.php" class="text-rose-600 hover:text-rose-700 text-sm font-medium transition">My Appointments</a>
            <a href="logout_user.php" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">

    <!-- Page Title -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-100 rounded-2xl mb-4 text-3xl">🏥</div>
        <h1 class="text-3xl font-bold text-slate-800">Book an Appointment</h1>
        <p class="text-slate-500 mt-2">Choose your doctor, hospital, and preferred time</p>
    </div>

    <?php if (!empty($message)): ?>
    <div class="mb-6 rounded-2xl px-6 py-5 flex items-start gap-4 <?php echo $msg_type==='success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'; ?>">
        <div class="text-2xl flex-shrink-0"><?php echo $msg_type==='success' ? '✅' : '❌'; ?></div>
        <div>
            <p class="font-semibold"><?php echo $msg_type==='success' ? 'Appointment Confirmed!' : 'Booking Failed'; ?></p>
            <p class="text-sm mt-1"><?php echo $message; ?></p>
            <?php if ($msg_type==='success'): ?>
            <div class="flex gap-3 mt-3">
                <a href="hospital_bookings.php" class="text-sm font-semibold underline">View My Appointments →</a>
                <a href="welcome.php" class="text-sm font-semibold underline ml-3">Back to Dashboard</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" id="apptForm" class="space-y-5">

        <!-- Step 1: Doctor -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-rose-500 text-white text-sm font-bold flex items-center justify-center">1</div>
                <h2 class="font-bold text-slate-700 text-lg">Select a Doctor</h2>
            </div>

            <?php if (empty($doctors)): ?>
            <div class="text-center py-6 text-slate-500">
                <div class="text-3xl mb-2">👨‍⚕️</div>
                <p class="font-medium">No registered doctors yet.</p>
                <p class="text-sm mt-1">Doctors will appear here once registered by an admin.</p>
            </div>
            <?php else: ?>
            <select name="doctor_id" id="doctor_id" required class="input-field">
                <option value="">— Choose a doctor —</option>
                <?php foreach ($doctors as $d): ?>
                <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']) . ' — ' . htmlspecialchars($d['specialization']); ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Doctor info preview -->
            <div id="doctorPreview" class="mt-3 hidden bg-rose-50 rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" id="doctorInitial">?</div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm" id="doctorName"></p>
                    <p class="text-rose-600 text-xs font-medium" id="doctorSpec"></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Step 2: Hospital -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-rose-500 text-white text-sm font-bold flex items-center justify-center">2</div>
                <h2 class="font-bold text-slate-700 text-lg">Select a Hospital</h2>
            </div>
            <select name="hospital_id" id="hospital_id" required class="input-field">
                <option value="">— Choose a hospital —</option>
                <?php foreach ($hospitals as $h): ?>
                <option value="<?php echo $h['id']; ?>" data-loc="<?php echo htmlspecialchars($h['location']); ?>">
                    <?php echo htmlspecialchars($h['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <div id="hospitalLocation" class="mt-2 text-xs text-slate-400 hidden">
                📍 <span id="hospitalLocText"></span>
            </div>
        </div>

        <!-- Step 3: Date & Time -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-rose-500 text-white text-sm font-bold flex items-center justify-center">3</div>
                <h2 class="font-bold text-slate-700 text-lg">Choose Date & Time</h2>
            </div>

            <!-- Date picker -->
            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">📅 Appointment Date *</label>
                <input type="date" name="appointment_date" id="appointment_date" required
                    min="<?php echo date('Y-m-d'); ?>"
                    value="<?php echo $_POST['appointment_date'] ?? ''; ?>"
                    class="input-field">
            </div>

            <!-- Quick time slots -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">🕐 Preferred Time Slot</label>
                <div class="grid grid-cols-4 gap-2 mb-3">
                    <?php
                    $slots = ['09:00','10:00','11:00','12:00','14:00','15:00','16:00','17:00'];
                    foreach ($slots as $s):
                    ?>
                    <button type="button" class="time-btn" onclick="selectTime('<?php echo $s; ?>', this)">
                        <?php echo date('g:i A', strtotime($s)); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="appointment_time" id="appointment_time" value="">
                <p class="text-xs text-slate-400">Click a slot above, or leave blank for morning</p>
            </div>
        </div>

        <!-- Step 4: Notes (optional) -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-slate-300 text-white text-sm font-bold flex items-center justify-center">4</div>
                <h2 class="font-bold text-slate-700 text-lg">Additional Notes <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
            </div>
            <textarea name="notes" rows="3" placeholder="Describe your symptoms or reason for visit..."
                class="input-field resize-none"></textarea>
        </div>

        <!-- Summary & Submit -->
        <div class="bg-gradient-to-r from-rose-600 to-red-600 rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-4">Booking Summary</h3>
            <div class="space-y-2 text-sm mb-5">
                <div class="flex justify-between"><span class="text-rose-200">Doctor</span><span id="sum_doctor" class="font-medium">—</span></div>
                <div class="flex justify-between"><span class="text-rose-200">Hospital</span><span id="sum_hospital" class="font-medium">—</span></div>
                <div class="flex justify-between"><span class="text-rose-200">Date</span><span id="sum_date" class="font-medium">—</span></div>
                <div class="flex justify-between"><span class="text-rose-200">Time</span><span id="sum_time" class="font-medium">Not specified</span></div>
                <div class="flex justify-between"><span class="text-rose-200">Status</span><span class="bg-amber-400 text-amber-900 text-xs font-bold px-2 py-0.5 rounded-full">Pending Confirmation</span></div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-white text-rose-600 font-bold py-3 rounded-xl hover:bg-rose-50 transition shadow-lg">
                    📅 Confirm Booking
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
// Doctor preview
const doctorData = <?php echo json_encode(array_column($doctors, null, 'id')); ?>;
const hospitalData = <?php echo json_encode(array_column($hospitals, null, 'id')); ?>;

document.getElementById('doctor_id')?.addEventListener('change', function() {
    const d = doctorData[this.value];
    if (d) {
        document.getElementById('doctorPreview').classList.remove('hidden');
        const name = d.name;
        document.getElementById('doctorInitial').textContent = name.charAt(0).toUpperCase();
        document.getElementById('doctorName').textContent = name;
        document.getElementById('doctorSpec').textContent = d.specialization;
        document.getElementById('sum_doctor').textContent = name;
    } else {
        document.getElementById('doctorPreview').classList.add('hidden');
        document.getElementById('sum_doctor').textContent = '—';
    }
});

document.getElementById('hospital_id')?.addEventListener('change', function() {
    const sel = this.options[this.selectedIndex];
    const loc = sel.dataset.loc;
    if (loc) {
        document.getElementById('hospitalLocation').classList.remove('hidden');
        document.getElementById('hospitalLocText').textContent = loc;
        document.getElementById('sum_hospital').textContent = sel.textContent.trim();
    } else {
        document.getElementById('hospitalLocation').classList.add('hidden');
        document.getElementById('sum_hospital').textContent = '—';
    }
});

document.getElementById('appointment_date')?.addEventListener('change', function() {
    const d = new Date(this.value);
    const opts = {weekday:'long',year:'numeric',month:'long',day:'numeric'};
    document.getElementById('sum_date').textContent = d.toLocaleDateString('en-US', opts);
});

function selectTime(time, btn) {
    document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('appointment_time').value = time;
    document.getElementById('sum_time').textContent = btn.textContent.trim();
}
</script>
</body>
</html>
