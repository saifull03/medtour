<?php
/**
 * Admin Hotel Reservations Management
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
    SELECT hb.id, hb.reference_no, hb.hotel_name, hb.checkin_date, hb.checkout_date,
           hb.num_guests, hb.room_type, hb.status, hb.created_at, hb.patient_id,
           u.name as patient_name, u.email as patient_email
    FROM hotel_bookings hb
    JOIN patients p ON hb.patient_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE 1=1
";
$params = [];

if ($statusFilter !== 'all') {
    $sql .= " AND hb.status = ?";
    $params[] = $statusFilter;
}

if ($searchTerm) {
    if (is_numeric($searchTerm)) {
        $sql .= " AND (hb.patient_id = ? OR hb.id = ?)";
        $params[] = (int)$searchTerm;
        $params[] = (int)$searchTerm;
    } else {
        $sql .= " AND (hb.reference_no LIKE ? OR u.name LIKE ? OR hb.hotel_name LIKE ? OR hb.room_type LIKE ?)";
        $like = "%$searchTerm%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }
}

$sql .= match ($sort) {
    'date_asc' => " ORDER BY hb.checkin_date ASC",
    'status'   => " ORDER BY hb.status ASC, hb.checkin_date DESC",
    default    => " ORDER BY hb.checkin_date DESC",
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

$pageTitle = 'Manage Hotel Reservations';
$heroTitle = 'Partner Hotel Reservations';
$heroSubtitle = 'Oversee patient lodging bookings, check-in dates, and room arrangements.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Search & Filter Toolbar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?php echo url('admin/hotel_bookings.php'); ?>" class="toolbar" style="margin-bottom: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div>
                <input type="text" name="q" placeholder="Search patient, hotel, ref..."
                    value="<?php echo e($searchTerm); ?>" class="form-control" style="width: 250px; padding: 0.5rem 0.75rem;">
            </div>

            <div>
                <select name="status" class="form-select" style="width: 150px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
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
                <a href="<?php echo url('admin/hotel_bookings.php'); ?>" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>

        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Showing <strong><?php echo count($bookings); ?></strong> of <strong><?php echo $totalRecords; ?></strong> reservations
        </div>
    </form>
</div>

<!-- Hotel Bookings Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hotel Bookings List</h3>
        <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
    </div>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <div class="empty-icon">🏨</div>
            <h4 class="empty-title">No hotel reservations found</h4>
            <p class="empty-text">No hotel bookings matched your active search or filter criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Hotel</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Room & Guests</th>
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
                            <td><strong><?php echo e($b['hotel_name']); ?></strong></td>
                            <td><?php echo formatDate($b['checkin_date']); ?></td>
                            <td><?php echo formatDate($b['checkout_date']); ?></td>
                            <td><?php echo e($b['room_type']); ?> (<?php echo $b['num_guests']; ?>)</td>
                            <td><?php echo renderStatusBadge($b['status']); ?></td>
                            <td>
                                <div style="display: flex; gap: 0.35rem; align-items: center;">
                                    <a href="<?php echo url('admin/update_booking.php?type=hotel&id=' . $b['id']); ?>" class="btn btn-secondary btn-sm">
                                        Update
                                    </a>
                                    <form action="<?php echo url('actions/delete_booking.php'); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently delete this hotel booking?');">
                                        <?php echo csrfField(); ?>
                                        <input type="hidden" name="type" value="hotel">
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
                        <a href="<?php echo url('admin/hotel_bookings.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
