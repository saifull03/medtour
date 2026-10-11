<?php
/**
 * Admin Hospital Appointments Management
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();
$pdo = getDbConnection();

$statusFilter = trim($_GET['status'] ?? 'all');
$searchTerm   = trim($_GET['q'] ?? '');
$sort         = trim($_GET['sort'] ?? 'date_desc');
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 10;

$sql = "
    SELECT a.id, a.reference_no, a.appointment_date, a.status, a.notes, a.created_at,
           u.name as patient_name, u.email as patient_email, a.patient_id,
           h.name as hospital_name, doc_u.name as doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    LEFT JOIN hospitals h ON a.hospital_id = h.id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    LEFT JOIN users doc_u ON d.user_id = doc_u.id
    WHERE 1=1
";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND a.status = ?";
    $params[] = $statusFilter;
}

if ($searchTerm) {
    if (is_numeric($searchTerm)) {
        $sql .= " AND (a.patient_id = ? OR a.id = ?)";
        $params[] = (int)$searchTerm;
        $params[] = (int)$searchTerm;
    } else {
        $sql .= " AND (a.reference_no LIKE ? OR u.name LIKE ? OR doc_u.name LIKE ? OR h.name LIKE ?)";
        $like = "%$searchTerm%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
}

$sql .= match ($sort) {
    'date_asc' => " ORDER BY a.appointment_date ASC",
    'status'   => " ORDER BY a.status ASC, a.appointment_date DESC",
    default    => " ORDER BY a.appointment_date DESC",
};

// Count total
$stmtAll = $pdo->prepare($sql);
$stmtAll->execute($params);
$totalRecords = $stmtAll->rowCount();

$totalPages = max(1, (int)ceil($totalRecords / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$sql .= " LIMIT $perPage OFFSET $offset";
$stmtPaged = $pdo->prepare($sql);
$stmtPaged->execute($params);
$bookings = $stmtPaged->fetchAll();

$pageTitle = 'Manage Hospital Appointments';
$heroTitle = 'Hospital Consultations & Surgeries';
$heroSubtitle = 'Oversee patient appointment schedules, affiliated hospitals, and clinical specialists.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Search & Filter Toolbar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?php echo url('admin/hospital_bookings.php'); ?>" class="toolbar" style="margin-bottom: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div>
                <input type="text" name="q" placeholder="Search patient, doctor, hospital, ref..."
                    value="<?php echo e($searchTerm); ?>" class="form-control" style="width: 250px; padding: 0.5rem 0.75rem;">
            </div>

            <div>
                <select name="status" class="form-select" style="width: 150px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <select name="sort" class="form-select" style="width: 160px; padding: 0.5rem 0.75rem;">
                    <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Latest)</option>
                    <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Earliest)</option>
                    <option value="status" <?php echo $sort === 'status' ? 'selected' : ''; ?>>By Status</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($searchTerm || $statusFilter !== 'all' || $sort !== 'date_desc'): ?>
                <a href="<?php echo url('admin/hospital_bookings.php'); ?>" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>

        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Showing <strong><?php echo count($bookings); ?></strong> of <strong><?php echo $totalRecords; ?></strong> appointments
        </div>
    </form>
</div>

<!-- Appointments Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hospital Bookings List</h3>
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <div class="empty-icon">🏥</div>
            <h4 class="empty-title">No appointments found</h4>
            <p class="empty-text">No hospital appointments matched your active filter parameters.</p>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Hospital</th>
                        <th>Doctor</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: var(--primary);"><?php echo e($b['reference_no'] ?: '#' . $b['id']); ?></strong></td>
                            <td>
                                <strong><?php echo e($b['patient_name']); ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: #PAT-<?php echo $b['patient_id']; ?></div>
                            </td>
                            <td><?php echo e($b['hospital_name'] ?? '—'); ?></td>
                            <td><?php echo e($b['doctor_name'] ? 'Dr. ' . $b['doctor_name'] : '—'); ?></td>
                            <td><?php echo formatDateTime($b['appointment_date']); ?></td>
                            <td><?php echo renderStatusBadge($b['status']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.35rem; align-items: center;">
                                    <a href="<?php echo url('admin/update_booking.php?type=hospital&id=' . $b['id']); ?>" class="btn btn-secondary btn-sm">
                                        Update
                                    </a>
                                    <form action="<?php echo url('actions/delete_booking.php'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this appointment?');">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="type" value="hospital">
                                        <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
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
                        <a href="<?php echo url('admin/hospital_bookings.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
