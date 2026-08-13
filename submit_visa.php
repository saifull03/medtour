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
    $visa_type   = trim($_POST['visa_type'] ?? '');
    $country     = trim($_POST['country'] ?? '');
    $passport    = trim($_POST['passport_number'] ?? '');
    $appt_date   = trim($_POST['application_date'] ?? '');

    if ($visa_type && $country && $passport && $appt_date) {
        $stmt = $conn->prepare("INSERT INTO visa_bookings (patient_id,visa_type,country,passport_number,application_date,status) VALUES (?,?,?,?,?,'pending')");
        $stmt->bind_param("issss",$patient_id,$visa_type,$country,$passport,$appt_date);
        if ($stmt->execute()) {
            $message = "Visa application submitted! Our specialists will contact you within <strong>2-3 business days</strong>.";
            $msg_type = "success";
        } else { $message = "Submission error: " . $stmt->error; $msg_type = "error"; }
        $stmt->close();
    } else { $message = "Please fill in all required fields."; $msg_type = "error"; }
}
$conn->close();

$popular_countries = ['Thailand','Turkey','India','Germany','Malaysia','South Korea','UAE','Singapore','Egypt','Jordan'];
$visa_types = [
    ['value'=>'Medical','label'=>'Medical Visa','icon'=>'🏥','desc'=>'For medical treatment abroad'],
    ['value'=>'Tourist','label'=>'Tourist Visa','icon'=>'🌍','desc'=>'Tourism and travel'],
    ['value'=>'Business','label'=>'Business Visa','icon'=>'💼','desc'=>'Business purposes'],
    ['value'=>'Student','label'=>'Student Visa','icon'=>'🎓','desc'=>'Study programs'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa Application – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .input-field { width:100%; border:2px solid #e2e8f0; border-radius:12px; padding:12px 16px; font-size:14px; color:#334155; background:#fff; transition:all 0.2s; outline:none; }
        .input-field:focus { border-color:#8b5cf6; box-shadow:0 0 0 3px rgba(139,92,246,0.12); }
        .step-card { background:white; border-radius:16px; border:2px solid #f1f5f9; padding:20px; transition:border-color 0.2s; }
        .step-card:focus-within { border-color:#ddd6fe; }
        .visa-card { border:2px solid #e2e8f0; border-radius:14px; padding:14px; cursor:pointer; transition:all 0.15s; text-align:center; }
        .visa-card:hover { border-color:#c4b5fd; background:#faf5ff; }
        .visa-card.selected { border-color:#8b5cf6; background:#faf5ff; }
        .country-chip { border:2px solid #e2e8f0; border-radius:999px; padding:6px 14px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.15s; color:#475569; white-space:nowrap; }
        .country-chip:hover { border-color:#c4b5fd; color:#8b5cf6; }
        .country-chip.active { border-color:#8b5cf6; background:#8b5cf6; color:white; }
        input[type="date"]::-webkit-calendar-picker-indicator { cursor:pointer; filter:invert(40%) sepia(80%) saturate(400%) hue-rotate(220deg); }
    </style>
</head>
<body class="bg-gradient-to-br from-violet-50 via-white to-slate-50 min-h-screen">

<header class="bg-white/80 backdrop-blur sticky top-0 z-50 shadow-sm border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-9 w-9 object-contain rounded-full bg-violet-100 p-1">
            <span class="font-bold text-slate-800">MedTour <span class="text-violet-500">Visa</span></span>
        </a>
        <nav class="flex items-center gap-4">
            <a href="welcome.php" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition">← Dashboard</a>
            <a href="visa_bookings.php" class="text-violet-600 hover:text-violet-700 text-sm font-medium transition">My Applications</a>
            <a href="logout_user.php" class="bg-violet-500 hover:bg-violet-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-10">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-violet-100 rounded-2xl mb-4 text-3xl">📄</div>
        <h1 class="text-3xl font-bold text-slate-800">Visa Assistance</h1>
        <p class="text-slate-500 mt-2">Submit your visa request — our experts handle the rest</p>
    </div>

    <?php if (!empty($message)): ?>
    <div class="mb-6 rounded-2xl px-6 py-5 flex items-start gap-4 <?php echo $msg_type==='success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'; ?>">
        <div class="text-2xl"><?php echo $msg_type==='success' ? '✅' : '❌'; ?></div>
        <div>
            <p class="font-semibold"><?php echo $msg_type==='success' ? 'Application Submitted!' : 'Submission Failed'; ?></p>
            <p class="text-sm mt-1"><?php echo $message; ?></p>
            <?php if ($msg_type==='success'): ?>
            <div class="flex gap-3 mt-3">
                <a href="visa_bookings.php" class="text-sm font-semibold underline">Track Applications →</a>
                <a href="welcome.php" class="text-sm font-semibold underline ml-3">Back to Dashboard</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">

        <!-- Visa Type -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-violet-500 text-white text-sm font-bold flex items-center justify-center">1</div>
                <h2 class="font-bold text-slate-700 text-lg">Visa Type</h2>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <?php foreach ($visa_types as $v): ?>
                <label class="visa-card">
                    <input type="radio" name="visa_type" value="<?php echo $v['value']; ?>" class="hidden visa-radio" required>
                    <div class="text-2xl mb-2"><?php echo $v['icon']; ?></div>
                    <div class="text-xs font-bold text-slate-700"><?php echo $v['label']; ?></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Destination Country -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-violet-500 text-white text-sm font-bold flex items-center justify-center">2</div>
                <h2 class="font-bold text-slate-700 text-lg">Destination Country</h2>
            </div>
            <p class="text-xs text-slate-400 mb-3">Quick select popular destinations or type below</p>
            <div class="flex flex-wrap gap-2 mb-3">
                <?php foreach ($popular_countries as $c): ?>
                <span class="country-chip" onclick="selectCountry('<?php echo $c; ?>', this)"><?php echo $c; ?></span>
                <?php endforeach; ?>
            </div>
            <input type="text" name="country" id="country_input" placeholder="Or type a country name..." required class="input-field">
        </div>

        <!-- Passport & Date -->
        <div class="step-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-violet-500 text-white text-sm font-bold flex items-center justify-center">3</div>
                <h2 class="font-bold text-slate-700 text-lg">Passport & Timeline</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">🛂 Passport Number *</label>
                    <input type="text" name="passport_number" placeholder="e.g., A12345678" required
                        class="input-field" style="letter-spacing:1px">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">📅 Requested Application Date *</label>
                    <input type="date" name="application_date" required
                        min="<?php echo date('Y-m-d'); ?>"
                        class="input-field">
                    <p class="text-xs text-slate-400 mt-1.5">This is your preferred start date. We'll confirm availability.</p>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-violet-50 border border-violet-200 rounded-2xl p-4 flex gap-3">
            <div class="text-violet-500 text-xl flex-shrink-0">ℹ️</div>
            <div class="text-sm text-violet-700">
                <p class="font-semibold mb-1">How it works</p>
                <p>After submission, our visa specialists will review your application and contact you within <strong>2-3 business days</strong> with document requirements and next steps.</p>
            </div>
        </div>

        <!-- Submit -->
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-1">Submit Application</h3>
            <p class="text-violet-200 text-sm mb-5">Our team will reach out with guidance within 2-3 business days.</p>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-white text-violet-600 font-bold py-3 rounded-xl hover:bg-violet-50 transition shadow-lg">
                    📄 Submit Application
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
document.querySelectorAll('.visa-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.visa-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        card.querySelector('.visa-radio').checked = true;
    });
});

function selectCountry(name, el) {
    document.querySelectorAll('.country-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('country_input').value = name;
}
</script>
</body>
</html>
