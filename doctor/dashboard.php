<?php
/**
 * Doctor Dashboard
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireDoctor();
$doctorId = currentDoctorId();
$user     = currentUser();
$pdo      = getDbConnection();

// Fetch doctor details and hospital affiliation
$stmtDoc = $pdo->prepare("
    SELECT d.id, d.specialization, d.experience, d.consultation_fee, h.name as hospital_name
    FROM doctors d
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    WHERE d.id = ?
");
$stmtDoc->execute([$doctorId]);
$doctorInfo = $stmtDoc->fetch();

// Status counts for this doctor
$stmtCounts = $pdo->prepare("
    SELECT status, COUNT(*) as cnt
    FROM appointments
    WHERE doctor_id = ?
    GROUP BY status
");
$stmtCounts->execute([$doctorId]);
$rawCounts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR);

$counts = [
    'pending'   => (int)($rawCounts['pending'] ?? 0),
    'approved'  => (int)($rawCounts['approved'] ?? 0),
    'completed' => (int)($rawCounts['completed'] ?? 0),
    'cancelled' => (int)($rawCounts['cancelled'] ?? 0),
];
$counts['total'] = array_sum($counts);

// Recent appointments for this doctor
$stmtRecent = $pdo->prepare("
    SELECT a.id, a.reference_no, a.appointment_date, a.status, a.notes, u.name as patient_name, u.email as patient_email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date DESC
    LIMIT 5
");
$stmtRecent->execute([$doctorId]);
$recentAppointments = $stmtRecent->fetchAll();

$pageTitle = 'Doctor Dashboard';
$heroTitle = 'Welcome, Dr. ' . $user['name'] . ' 🩺';
$heroSubtitle = ($doctorInfo['specialization'] ?? 'Specialist') . ' &middot; ' . ($doctorInfo['hospital_name'] ?? 'Partner Hospital');
$heroClass = 'hero-doctor';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Doctor Profile Summary Card -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-color: #a7f3d0;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-success" style="margin-bottom: 0.5rem;">Verified Medical Specialist</span>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: #064e3b; margin-bottom: 0.25rem;">
                <?php echo e($user['name']); ?>
            </h2>
            <p style="color: #047857; font-size: 0.875rem;">
                Specialization: <strong><?php echo e($doctorInfo['specialization'] ?? 'General Medicine'); ?></strong> &middot; 
                Experience: <strong><?php echo e($doctorInfo['experience'] ?? 0); ?> years</strong> &middot; 
                Consultation Fee: <strong>$<?php echo number_format($doctorInfo['consultation_fee'] ?? 0, 2); ?></strong>
            </p>
        </div>
        <div>
            <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-success">View All Appointments &rarr;</a>
        </div>
    </div>
</div>

<!-- Status Counts Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?php echo $counts['total']; ?></div>
        <div class="stat-label">Total Appointments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value" style="color: #d97706;"><?php echo $counts['pending']; ?></div>
        <div class="stat-label">Pending Approval</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value" style="color: #059669;"><?php echo $counts['approved']; ?></div>
        <div class="stat-label">Approved & Upcoming</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎉</div>
        <div class="stat-value" style="color: #0284c7;"><?php echo $counts['completed']; ?></div>
        <div class="stat-label">Completed Consults</div>
    </div>
</div>

<!-- Recent Appointments Table -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Latest Patient Appointments</h3>
            <span class="card-subtitle">Consultation requests assigned directly to you</span>
        </div>
        <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-outline btn-sm">Full Schedule &rarr;</a>
    </div>

    <?php if (empty($recentAppointments)): ?>
        <div class="empty-state">
            <div class="empty-icon">🗓️</div>
            <h4 class="empty-title">No appointments assigned yet</h4>
            <p class="empty-text">When patients select you for consultations or surgeries, their booking requests will appear here.</p>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient Name</th>
                        <th>Patient Email</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAppointments as $apt): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: var(--primary);"><?php echo e($apt['reference_no'] ?: '#' . $apt['id']); ?></strong></td>
                            <td><strong><?php echo e($apt['patient_name']); ?></strong></td>
                            <td><?php echo e($apt['patient_email']); ?></td>
                            <td><?php echo formatDateTime($apt['appointment_date']); ?></td>
                            <td><?php echo renderStatusBadge($apt['status']); ?></td>
                            <td>
                                <a href="<?php echo url('doctor/update_appointment.php?id=' . $apt['id']); ?>" class="btn btn-outline btn-sm">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
