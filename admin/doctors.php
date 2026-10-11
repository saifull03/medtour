<?php
/**
 * Admin Doctors Management
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
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'doctor'");
        $stmt->execute([$newStatus, $targetUserId]);

        adminLog($user['id'], "Doctor User ID {$targetUserId} set to {$action}d");
        setFlash('success', "Doctor account successfully " . ($action === 'activate' ? 'activated' : 'deactivated') . ".");
    }
    header('Location: ' . url('admin/doctors.php'));
    exit();
}

$stmt = $pdo->query("
    SELECT d.id as doctor_id, u.id as user_id, u.name, u.email, u.status as user_status,
           d.specialization, d.experience, d.consultation_fee, d.created_at,
           h.name as hospital_name
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    LEFT JOIN hospitals h ON d.hospital_id = h.id
    ORDER BY u.name ASC
");
$doctors = $stmt->fetchAll();

$pageTitle = 'Manage Doctors';
$heroTitle = 'Specialist Medical Doctors';
$heroSubtitle = 'Oversee practicing physicians, specialties, consultation rates, and credential statuses.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Registered Physicians (<?php echo count($doctors); ?>)</h3>
            <span class="card-subtitle">Active and registered medical providers</span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?php echo url('public/signup_doctor.php'); ?>" class="btn btn-success btn-sm">+ Register New Doctor</a>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
        </div>
    </div>

    <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Doctor ID</th>
                    <th>Doctor Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Affiliated Hospital</th>
                    <th>Experience</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doc): ?>
                    <tr>
                        <td><strong style="font-family: monospace; color: var(--primary);">#DOC-<?php echo $doc['doctor_id']; ?></strong></td>
                        <td><strong><?php echo e($doc['name']); ?></strong></td>
                        <td><?php echo e($doc['email']); ?></td>
                        <td><span class="badge badge-info"><?php echo e($doc['specialization'] ?? 'General'); ?></span></td>
                        <td><?php echo e($doc['hospital_name'] ?? 'Not Affiliated'); ?></td>
                        <td><?php echo e($doc['experience'] ?? 0); ?> yrs</td>
                        <td>$<?php echo number_format($doc['consultation_fee'] ?? 0, 2); ?></td>
                        <td>
                            <?php if ((int)$doc['user_status'] === 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Deactivated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?php echo url('admin/doctors.php'); ?>" method="POST" style="display: inline;">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="user_id" value="<?php echo (int)$doc['user_id']; ?>">
                                <?php if ((int)$doc['user_status'] === 1): ?>
                                    <input type="hidden" name="status_action" value="deactivate">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Deactivate this doctor account?');">Deactivate</button>
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
