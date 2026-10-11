<?php
/**
 * Doctor Appointments Management
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireDoctor();
$doctorId = currentDoctorId();
$pdo      = getDbConnection();

// Query parameters
$statusFilter = trim($_GET['status'] ?? 'all');
$searchTerm   = trim($_GET['q'] ?? '');
$sort         = trim($_GET['sort'] ?? 'date_desc');
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 10;

// Base query
$sql = "
    SELECT a.id, a.reference_no, a.appointment_date, a.status, a.notes, a.created_at,
           u.name AS patient_name, u.email AS patient_email, p.phone AS patient_phone,
           h.name AS hospital_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    WHERE a.doctor_id = ?
";
$params = [$doctorId];

if ($statusFilter !== 'all') {
    $sql .= " AND a.status = ?";
    $params[] = $statusFilter;
}

if ($searchTerm) {
    $sql .= " AND (a.reference_no LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
    $like = "%$searchTerm%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

// Order by
$sql .= match ($sort) {
    'date_asc' => " ORDER BY a.appointment_date ASC",
    'status'   => " ORDER BY a.status ASC, a.appointment_date DESC",
    default    => " ORDER BY a.appointment_date DESC",
};

// Count total matching
$stmtAll = $pdo->prepare($sql);
$stmtAll->execute($params);
$totalRecords = $stmtAll->rowCount();

$totalPages = max(1, (int)ceil($totalRecords / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$sql .= " LIMIT $perPage OFFSET $offset";
$stmtPaged = $pdo->prepare($sql);
$stmtPaged->execute($params);
$appointments = $stmtPaged->fetchAll();

$pageTitle = 'Manage Appointments';
$heroTitle = 'Appointments & Consultations';
$heroSubtitle = 'View, confirm, and update clinical consultations with international patients.';
$heroClass = 'hero-doctor';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Search & Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?php echo url('doctor/appointments.php'); ?>" class="toolbar" style="margin-bottom: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div>
                <input type="text" name="q" placeholder="Search patient name, email, ref..."
                    value="<?php echo e($searchTerm); ?>" class="form-control" style="width: 250px; padding: 0.5rem 0.75rem;">
            </div>

            <div>
                <select name="status" class="form-select" style="width: 160px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <select name="sort" class="form-select" style="width: 170px; padding: 0.5rem 0.75rem;">
                    <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Latest First)</option>
                    <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Earliest First)</option>
                    <option value="status" <?php echo $sort === 'status' ? 'selected' : ''; ?>>Group by Status</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success btn-sm">Filter Schedule</button>
            <?php if ($searchTerm || $statusFilter !== 'all' || $sort !== 'date_desc'): ?>
                <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>

        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Showing <strong><?php echo count($appointments); ?></strong> of <strong><?php echo $totalRecords; ?></strong> appointments
        </div>
    </form>
</div>

<!-- Appointments Table -->
<div class="card">
    <?php if (empty($appointments)): ?>
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3 class="empty-title">No appointments found</h3>
            <p class="empty-text">No consultation records matched your active filter criteria.</p>
            <a href="<?php echo url('doctor/appointments.php'); ?>" class="btn btn-outline btn-sm">Clear Filters</a>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Contact Email</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace; color: var(--primary);">
                                    <?php echo e($apt['reference_no'] ?: '#' . $apt['id']); ?>
                                </strong>
                            </td>
                            <td><strong><?php echo e($apt['patient_name']); ?></strong></td>
                            <td><?php echo e($apt['patient_email']); ?></td>
                            <td><?php echo formatDateTime($apt['appointment_date']); ?></td>
                            <td><?php echo renderStatusBadge($apt['status']); ?></td>
                            <td style="max-width: 220px; font-size: 0.8rem; color: var(--text-muted);">
                                <?php echo e($apt['notes'] ?: 'None provided'); ?>
                            </td>
                            <td>
                                <a href="<?php echo url('doctor/update_appointment.php?id=' . $apt['id']); ?>" class="btn btn-success btn-sm">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a href="<?php echo url('doctor/appointments.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
