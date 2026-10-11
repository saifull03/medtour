<?php
/**
 * Admin Patients Directory Management
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requireAdmin();
$user = currentUser();
$pdo  = getDbConnection();

// Handle Status Toggle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $targetUserId = filter_var($_POST['user_id'] ?? '', FILTER_VALIDATE_INT);
    $action       = trim($_POST['status_action'] ?? '');

    if ($targetUserId && in_array($action, ['activate', 'deactivate'], true)) {
        $newStatus = ($action === 'activate') ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'patient'");
        $stmt->execute([$newStatus, $targetUserId]);

        adminLog($user['id'], "Patient User ID {$targetUserId} set to {$action}d");
        setFlash('success', "Patient account successfully " . ($action === 'activate' ? 'activated' : 'deactivated') . ".");
    }
    header('Location: ' . url('admin/patients.php'));
    exit();
}

$stmt = $pdo->query("
    SELECT p.id as patient_id, u.id as user_id, u.name, u.email, u.status as user_status,
           p.phone, p.country, p.passport_no, u.created_at,
           (SELECT COUNT(*) FROM appointments WHERE patient_id = p.id) as apt_cnt,
           (SELECT COUNT(*) FROM hotel_bookings WHERE patient_id = p.id) as htl_cnt,
           (SELECT COUNT(*) FROM transport_bookings WHERE patient_id = p.id) as trn_cnt,
           (SELECT COUNT(*) FROM visa_bookings WHERE patient_id = p.id) as vsa_cnt
    FROM patients p
    JOIN users u ON p.user_id = u.id
    ORDER BY u.id DESC
");
$patients = $stmt->fetchAll();

$pageTitle = 'Manage Patients';
$heroTitle = 'Registered Patients Directory';
$heroSubtitle = 'View international patient profiles, passports, contact information, and booking activity.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Registered Patients (<?php echo count($patients); ?>)</h3>
            <span class="card-subtitle">Active and registered patient accounts</span>
        </div>
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
    </div>

    <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Passport</th>
                    <th>Total Bookings</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $pat): ?>
                    <?php $totalBookings = (int)$pat['apt_cnt'] + (int)$pat['htl_cnt'] + (int)$pat['trn_cnt'] + (int)$pat['vsa_cnt']; ?>
                    <tr>
                        <td><strong style="font-family: monospace; color: var(--primary);">#PAT-<?php echo $pat['patient_id']; ?></strong></td>
                        <td><strong><?php echo e($pat['name']); ?></strong></td>
                        <td><?php echo e($pat['email']); ?></td>
                        <td><?php echo e($pat['phone'] ?: '—'); ?></td>
                        <td><?php echo e($pat['country'] ?: '—'); ?></td>
                        <td><code style="font-size: 0.8rem;"><?php echo e($pat['passport_no'] ?: '—'); ?></code></td>
                        <td><span class="badge badge-info"><?php echo $totalBookings; ?> Bookings</span></td>
                        <td>
                            <?php if ((int)$pat['user_status'] === 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Deactivated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?php echo url('admin/patients.php'); ?>" method="POST" style="display: inline;">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="user_id" value="<?php echo (int)$pat['user_id']; ?>">
                                <?php if ((int)$pat['user_status'] === 1): ?>
                                    <input type="hidden" name="status_action" value="deactivate">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deactivate this patient account?');">Deactivate</button>
                                <?php else: ?>
                                    <input type="hidden" name="status_action" value="activate">
                                    <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
