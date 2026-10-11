<?php
/**
 * Patient Dashboard
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requirePatient();
$patientId = currentPatientId();
$user = currentUser();

$pdo = getDbConnection();

// Dynamic patient counts
$hStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ?");
$hStmt->execute([$patientId]);
$hCount = (int)$hStmt->fetchColumn();

$htStmt = $pdo->prepare("SELECT COUNT(*) FROM hotel_bookings WHERE patient_id = ?");
$htStmt->execute([$patientId]);
$htCount = (int)$htStmt->fetchColumn();

$tStmt = $pdo->prepare("SELECT COUNT(*) FROM transport_bookings WHERE patient_id = ?");
$tStmt->execute([$patientId]);
$tCount = (int)$tStmt->fetchColumn();

$vStmt = $pdo->prepare("SELECT COUNT(*) FROM visa_bookings WHERE patient_id = ?");
$vStmt->execute([$patientId]);
$vCount = (int)$vStmt->fetchColumn();

// Recent appointments
$stmtRecent = $pdo->prepare("
    SELECT a.id, a.reference_no, a.appointment_date, a.status, h.name as hospital_name, u.name as doctor_name
    FROM appointments a
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    LEFT JOIN users u ON d.user_id = u.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC
    LIMIT 5
");
$stmtRecent->execute([$patientId]);
$recentAppointments = $stmtRecent->fetchAll();

$pageTitle = 'Patient Dashboard';
$heroTitle = 'Welcome back, ' . $user['name'] . ' 👋';
$heroSubtitle = 'Monitor and coordinate all your medical consultations, hotel lodgings, transfers, and visa records.';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏥</div>
        <div class="stat-value"><?php echo $hCount; ?></div>
        <div class="stat-label">Hospital Appointments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏨</div>
        <div class="stat-value"><?php echo $htCount; ?></div>
        <div class="stat-label">Hotel Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚗</div>
        <div class="stat-value"><?php echo $tCount; ?></div>
        <div class="stat-label">Medical Transfers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value"><?php echo $vCount; ?></div>
        <div class="stat-label">Visa Applications</div>
    </div>
</div>

<!-- Quick Actions Banner -->
<div class="card" style="margin-bottom: 2.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border-color: #bae6fd;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Book a New Healthcare Service</h2>
            <p style="color: #475569; font-size: 0.875rem;">Coordinate your hospital consult, hotel reservation, private transfer, or visa paperwork.</p>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="<?php echo url('patient/hospital.php'); ?>" class="btn btn-primary btn-sm">🏥 Hospital Appointment</a>
            <a href="<?php echo url('patient/hotel.php'); ?>" class="btn btn-secondary btn-sm">🏨 Hotel Reservation</a>
            <a href="<?php echo url('patient/transport.php'); ?>" class="btn btn-success btn-sm">🚗 Medical Transfer</a>
            <a href="<?php echo url('patient/visa.php'); ?>" class="btn btn-outline btn-sm">📄 Visa Application</a>
        </div>
    </div>
</div>

<!-- Recent Appointments Table -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Recent Hospital Appointments</h3>
            <span class="card-subtitle">Your latest upcoming or past hospital visits</span>
        </div>
        <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">View All Bookings &rarr;</a>
    </div>

    <?php if (empty($recentAppointments)): ?>
        <div class="empty-state">
            <div class="empty-icon">🗓️</div>
            <h4 class="empty-title">No appointments scheduled yet</h4>
            <p class="empty-text">You haven't scheduled any medical consultations or surgeries yet.</p>
            <a href="<?php echo url('patient/hospital.php'); ?>" class="btn btn-primary btn-sm">Book Your First Appointment &rarr;</a>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Hospital</th>
                        <th>Doctor</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAppointments as $apt): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: var(--primary);"><?php echo e($apt['reference_no'] ?: '#' . $apt['id']); ?></strong></td>
                            <td><?php echo e($apt['hospital_name'] ?? 'General Partner Hospital'); ?></td>
                            <td><?php echo e($apt['doctor_name'] ?? 'Attending Specialist'); ?></td>
                            <td><?php echo formatDateTime($apt['appointment_date']); ?></td>
                            <td><?php echo renderStatusBadge($apt['status']); ?></td>
                            <td>
                                <?php if (strtolower($apt['status']) === 'pending'): ?>
                                    <a href="<?php echo url('patient/cancel_booking.php?type=hospital&id=' . $apt['id']); ?>" class="btn btn-danger btn-sm">Cancel</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
