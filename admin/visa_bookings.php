<?php
/**
 * Admin Visa Applications Management
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
    SELECT vb.id, vb.reference_no, vb.visa_type, vb.country, vb.passport_number,
           vb.application_date, vb.status, vb.created_at, vb.patient_id,
           u.name as patient_name, u.email as patient_email
    FROM visa_bookings vb
    JOIN patients p ON vb.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE 1=1
";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND vb.status = ?";
    $params[] = $statusFilter;
}

if ($searchTerm) {
    if (is_numeric($searchTerm)) {
        $sql .= " AND (vb.patient_id = ? OR vb.id = ?)";
        $params[] = (int)$searchTerm;
        $params[] = (int)$searchTerm;
    } else {
        $sql .= " AND (vb.reference_no LIKE ? OR u.name LIKE ? OR vb.country LIKE ? OR vb.passport_number LIKE ? OR vb.visa_type LIKE ?)";
        $like = "%$searchTerm%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
}

$sql .= match ($sort) {
    'date_asc' => " ORDER BY vb.application_date ASC",
    'status'   => " ORDER BY vb.status ASC, vb.application_date DESC",
    default    => " ORDER BY vb.application_date DESC",
};

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

$pageTitle = 'Manage Visa Applications';
$heroTitle = 'Medical Travel Visa Applications';
$heroSubtitle = 'Facilitate embassy clearance, medical visa invitations, and travel documents.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Search & Filter Toolbar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?php echo url('admin/visa_bookings.php'); ?>" class="toolbar" style="margin-bottom: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div>
                <input type="text" name="q" placeholder="Search patient, passport, country, ref..."
                    value="<?php echo e($searchTerm); ?>" class="form-control" style="width: 260px; padding: 0.5rem 0.75rem;">
            </div>

            <div>
                <select name="status" class="form-select" style="width: 150px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
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

            <button type="submit" class="btn btn-secondary btn-sm" style="background: #4338ca;">Filter</button>
            <?php if ($searchTerm || $statusFilter !== 'all' || $sort !== 'date_desc'): ?>
                <a href="<?php echo url('admin/visa_bookings.php'); ?>" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>

        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Showing <strong><?php echo count($bookings); ?></strong> of <strong><?php echo $totalRecords; ?></strong> applications
        </div>
    </form>
</div>

<!-- Visa Applications Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Visa Applications List</h3>
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <div class="empty-icon">📄</div>
            <h4 class="empty-title">No visa applications found</h4>
            <p class="empty-text">No visa application records matched your active filter parameters.</p>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Category</th>
                        <th>Country</th>
                        <th>Passport #</th>
                        <th>Travel Date</th>
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
                            <td><?php echo e($b['visa_type']); ?> Visa</td>
                            <td><strong><?php echo e($b['country']); ?></strong></td>
                            <td><code><?php echo e($b['passport_number']); ?></code></td>
                            <td><?php echo formatDate($b['application_date']); ?></td>
                            <td><?php echo renderStatusBadge($b['status']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.35rem; align-items: center;">
                                    <a href="<?php echo url('admin/update_booking.php?type=visa&id=' . $b['id']); ?>" class="btn btn-secondary btn-sm">
                                        Update
                                    </a>
                                    <form action="<?php echo url('actions/delete_booking.php'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this visa application?');">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="type" value="visa">
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
                        <a href="<?php echo url('admin/visa_bookings.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
