<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login_user.php"); exit();
}

$conn = new mysqli('localhost','root','','mt_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Seed sample doctors/hospitals if needed
$check = $conn->query("SELECT COUNT(*) as c FROM doctors")->fetch_assoc()['c'];
if ($check < 10) {
    $sample_doctors = [
        ['Dr. Ahmed Hassan','Cardiology','01012345678'],['Dr. Fatima Al-Rashid','Orthopedics','01023456789'],
        ['Dr. Mohamed Saleh','Neurology','01034567890'],['Dr. Layla Ibrahim','Dermatology','01045678901'],
        ['Dr. Karim Abdullah','Gastroenterology','01056789012'],['Dr. Aisha Mohammed','Pediatrics','01067890123'],
        ['Dr. Hassan Ali','Ophthalmology','01078901234'],['Dr. Noor Khalid','Gynecology','01089012345'],
        ['Dr. Samir Yousef','Psychiatry','01090123456'],['Dr. Dina Farraj','General Surgery','01001234567']
    ];
    foreach ($sample_doctors as $d) {
        $email = strtolower(str_replace([' ','.'],['.','.'],$d[0]))."@doctor.com";
        $check_u = $conn->query("SELECT id FROM users WHERE email='$email' AND role='doctor'");
        if ($check_u->num_rows === 0) {
            $hp = password_hash('doctor123', PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (name,email,password,role,status) VALUES ('{$d[0]}','$email','$hp','doctor','active')");
            $uid = $conn->insert_id;
            $conn->query("INSERT INTO doctors (user_id,specialization,phone) VALUES ($uid,'{$d[1]}','{$d[2]}')");
        }
    }
}
$check_h = $conn->query("SELECT COUNT(*) as c FROM hospitals")->fetch_assoc()['c'];
if ($check_h < 10) {
    $hs = [['Cairo Medical Center','Cairo, Egypt'],['Al-Nile Hospital','Giza, Egypt'],['Nasr City Hospital','Nasr City, Cairo'],['Maadi Hospital','Maadi, Cairo'],['Heliopolis Medical Center','Heliopolis, Cairo'],['Zamalek Hospital','Zamalek, Cairo'],['New Cairo Hospital','New Cairo'],['Sheikh Zayed Hospital','Sheikh Zayed City'],['Al-Obour Hospital','Obour City'],['Nile Badrawi Hospital','Giza, Egypt']];
    foreach ($hs as $h) {
        if ($conn->query("SELECT id FROM hospitals WHERE name='{$h[0]}'")->num_rows === 0)
            $conn->query("INSERT INTO hospitals (name,location) VALUES ('{$h[0]}','{$h[1]}')");
    }
}

// Ensure patient record exists
$user_id = $_SESSION['user_id'];
$pr = $conn->query("SELECT id FROM patients WHERE user_id=$user_id");
if ($pr->num_rows === 0) {
    $conn->query("INSERT INTO patients (user_id,phone,country,passport_no) VALUES ($user_id,NULL,NULL,NULL)");
    $patient_id = $conn->insert_id;
} else {
    $patient_id = $pr->fetch_assoc()['id'];
}

// Handle submission
$message = ""; $msg_type = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['appointment_date']) && !empty($_POST['doctor_id']) && !empty($_POST['hospital_id'])) {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id,doctor_id,appointment_date,hospital_id,status) VALUES (?,?,?,?,'pending')");
        $doctor_id = intval($_POST['doctor_id']); $hospital_id = intval($_POST['hospital_id']);
        $stmt->bind_param("iisi", $patient_id, $doctor_id, $_POST['appointment_date'], $hospital_id);
        if ($stmt->execute()) { $message = "✅ Appointment booked successfully! ID: #".$conn->insert_id; $msg_type = "success"; }
        else { $message = "❌ Error: ".$stmt->error; $msg_type = "error"; }
        $stmt->close();
    } else { $message = "❌ All fields are required."; $msg_type = "error"; }
}

$hospitals = $conn->query("SELECT id,name,location FROM hospitals ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$doctors   = $conn->query("SELECT d.id, u.name, d.specialization FROM doctors d JOIN users u ON d.user_id=u.id WHERE u.role='doctor' ORDER BY u.name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hospital Appointment – MedTour</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;}
        .field{@apply w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-rose-400 transition bg-white;}
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
<header class="bg-gradient-to-r from-slate-900 to-rose-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="index.php" class="flex items-center gap-3">
            <img src="logo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full bg-white p-1">
            <span class="font-bold text-lg">MedTour <span class="text-rose-300">Hospital</span></span>
        </a>
        <nav class="flex items-center gap-5">
            <a href="welcome.php"  class="text-slate-300 hover:text-white text-sm transition">Dashboard</a>
            <a href="logout_user.php" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-rose-600 to-red-700 p-7 text-white">
            <div class="text-3xl mb-2">🏥</div>
            <h1 class="text-2xl font-bold">Book Hospital Appointment</h1>
            <p class="text-rose-100 text-sm mt-1">Choose your doctor, hospital and preferred date</p>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Your User ID</label>
                    <input type="text" value="<?php echo $_SESSION['user_id']; ?>" readonly
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Select Doctor *</label>
                    <select name="doctor_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-rose-400 transition bg-white">
                        <option value="">— Choose a doctor —</option>
                        <?php foreach ($doctors as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name'])." — ".htmlspecialchars($d['specialization']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Select Hospital *</label>
                    <select name="hospital_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-rose-400 transition bg-white">
                        <option value="">— Choose a hospital —</option>
                        <?php foreach ($hospitals as $h): ?>
                        <option value="<?php echo $h['id']; ?>"><?php echo htmlspecialchars($h['name'])." (".htmlspecialchars($h['location']).")"; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Appointment Date *</label>
                    <input type="date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-rose-400 transition bg-white">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 bg-rose-500 hover:bg-rose-600 text-white font-bold py-3 rounded-xl transition shadow-lg">
                        📅 Book Appointment
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
<?php $conn->close(); ?>
</body>
</html>
