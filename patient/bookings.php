<?php
/**
 * Patient Unified Bookings Management
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requirePatient();
$patientId = currentPatientId();
$pdo = getDbConnection();

// Filters from GET
$serviceFilter = trim($_GET['service'] ?? 'all');
$statusFilter  = trim($_GET['status'] ?? 'all');
$searchTerm    = trim($_GET['q'] ?? '');
$sort          = trim($_GET['sort'] ?? 'date_desc');
$page          = max(1, (int)($_GET['page'] ?? 1));
$perPage       = 10;

// Query collections based on service filter
$items = [];

// 1. Appointments
if ($serviceFilter === 'all' || $serviceFilter === 'hospital') {
    $sql = "
        SELECT 
            'hospital' AS service_type,
            '🏥 Hospital' AS service_label,
            a.id,
            a.reference_no,
            a.status,
            a.appointment_date AS item_date,
            CONCAT(h.name, ' — Dr. ', u.name) AS details,
            a.created_at
        FROM appointments a
        LEFT JOIN hospitals h ON a.hospital_id = h.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE a.patient_id = ?
    ";
    $params = [$patientId];
    if ($statusFilter !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $statusFilter;
    }
    if ($searchTerm) {
        $sql .= " AND (a.reference_no LIKE ? OR h.name LIKE ? OR u.name LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = array_merge($items, $stmt->fetchAll());
}

// 2. Hotel Bookings
if ($serviceFilter === 'all' || $serviceFilter === 'hotel') {
    $sql = "
        SELECT 
            'hotel' AS service_type,
            '🏨 Hotel' AS service_label,
            hb.id,
            hb.reference_no,
            hb.status,
            hb.checkin_date AS item_date,
            CONCAT(hb.hotel_name, ' (', hb.room_type, ', ', hb.num_guests, ' guests, to ', hb.checkout_date, ')') AS details,
            hb.created_at
        FROM hotel_bookings hb
        WHERE hb.patient_id = ?
    ";
    $params = [$patientId];
    if ($statusFilter !== 'all') {
        $sql .= " AND hb.status = ?";
        $params[] = $statusFilter;
    }
    if ($searchTerm) {
        $sql .= " AND (hb.reference_no LIKE ? OR hb.hotel_name LIKE ? OR hb.room_type LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = array_merge($items, $stmt->fetchAll());
}

// 3. Transport Bookings
if ($serviceFilter === 'all' || $serviceFilter === 'transport') {
    $sql = "
        SELECT 
            'transport' AS service_type,
            '🚗 Transport' AS service_label,
            tb.id,
            tb.reference_no,
            tb.status,
            tb.date AS item_date,
            CONCAT(tb.transport_type, ': ', tb.pickup_location, ' &rarr; ', tb.destination, IF(tb.time IS NOT NULL, CONCAT(' @ ', tb.time), '')) AS details,
            tb.created_at
        FROM transport_bookings tb
        WHERE tb.patient_id = ?
    ";
    $params = [$patientId];
    if ($statusFilter !== 'all') {
        $sql .= " AND tb.status = ?";
        $params[] = $statusFilter;
    }
    if ($searchTerm) {
        $sql .= " AND (tb.reference_no LIKE ? OR tb.transport_type LIKE ? OR tb.pickup_location LIKE ? OR tb.destination LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = array_merge($items, $stmt->fetchAll());
}

// 4. Visa Bookings
if ($serviceFilter === 'all' || $serviceFilter === 'visa') {
    $sql = "
        SELECT 
            'visa' AS service_type,
            '📄 Visa' AS service_label,
            vb.id,
            vb.reference_no,
            vb.status,
            vb.application_date AS item_date,
            CONCAT(vb.visa_type, ' Visa for ', vb.country, ' (Passport: ', vb.passport_number, ')') AS details,
            vb.created_at
        FROM visa_bookings vb
        WHERE vb.patient_id = ?
    ";
    $params = [$patientId];
    if ($statusFilter !== 'all') {
        $sql .= " AND vb.status = ?";
        $params[] = $statusFilter;
    }
    if ($searchTerm) {
        $sql .= " AND (vb.reference_no LIKE ? OR vb.visa_type LIKE ? OR vb.country LIKE ? OR vb.passport_number LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = array_merge($items, $stmt->fetchAll());
}

// In-memory Sorting of combined records
usort($items, function ($a, $b) use ($sort) {
    if ($sort === 'date_asc') {
        return strtotime($a['item_date']) <=> strtotime($b['item_date']);
    }
    if ($sort === 'status') {
        return strcmp($a['status'], $b['status']);
    }
    // Default: date_desc
    return strtotime($b['item_date']) <=> strtotime($a['item_date']);
});

$totalRecords = count($items);
$totalPages   = max(1, (int)ceil($totalRecords / $perPage));
$page         = min($page, $totalPages);
$offset       = ($page - 1) * $perPage;
$pagedItems   = array_slice($items, $offset, $perPage);

$pageTitle = 'My Bookings & Reservations';
$heroTitle = 'My Medical Travel Bookings';
$heroSubtitle = 'Track appointments, hotel stays, private transfers, and visa documentation in one place.';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Search & Filter Toolbar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?php echo url('patient/bookings.php'); ?>" class="toolbar" style="margin-bottom: 0;">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div>
                <input type="text" name="q" placeholder="Search reference, hospital, hotel..."
                    value="<?php echo e($searchTerm); ?>" class="form-control" style="width: 240px; padding: 0.5rem 0.75rem;">
            </div>

            <div>
                <select name="service" class="form-select" style="width: 160px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $serviceFilter === 'all' ? 'selected' : ''; ?>>All Services</option>
                    <option value="hospital" <?php echo $serviceFilter === 'hospital' ? 'selected' : ''; ?>>🏥 Hospital Only</option>
                    <option value="hotel" <?php echo $serviceFilter === 'hotel' ? 'selected' : ''; ?>>🏨 Hotels Only</option>
                    <option value="transport" <?php echo $serviceFilter === 'transport' ? 'selected' : ''; ?>>🚗 Transport Only</option>
                    <option value="visa" <?php echo $serviceFilter === 'visa' ? 'selected' : ''; ?>>📄 Visa Only</option>
                </select>
            </div>

            <div>
                <select name="status" class="form-select" style="width: 150px; padding: 0.5rem 0.75rem;">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <select name="sort" class="form-select" style="width: 160px; padding: 0.5rem 0.75rem;">
                    <option value="date_desc" <?php echo $sort === 'date_desc' ? 'selected' : ''; ?>>Date (Newest)</option>
                    <option value="date_asc" <?php echo $sort === 'date_asc' ? 'selected' : ''; ?>>Date (Oldest)</option>
                    <option value="status" <?php echo $sort === 'status' ? 'selected' : ''; ?>>Status</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($searchTerm || $serviceFilter !== 'all' || $statusFilter !== 'all' || $sort !== 'date_desc'): ?>
                <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">Reset</a>
            <?php endif; ?>
        </div>

        <div style="font-size: 0.85rem; color: var(--text-muted);">
            Showing <strong><?php echo count($pagedItems); ?></strong> of <strong><?php echo $totalRecords; ?></strong> bookings
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card">
    <?php if (empty($pagedItems)): ?>
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <h3 class="empty-title">No bookings found</h3>
            <p class="empty-text">There are no records matching your selected filter criteria. You can create a new booking using the buttons below.</p>
            <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo url('patient/hospital.php'); ?>" class="btn btn-primary btn-sm">🏥 Book Hospital</a>
                <a href="<?php echo url('patient/hotel.php'); ?>" class="btn btn-secondary btn-sm">🏨 Book Hotel</a>
                <a href="<?php echo url('patient/transport.php'); ?>" class="btn btn-success btn-sm">🚗 Book Transport</a>
                <a href="<?php echo url('patient/visa.php'); ?>" class="btn btn-outline btn-sm">📄 Apply Visa</a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Service</th>
                        <th>Details</th>
                        <th>Scheduled Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagedItems as $b): ?>
                        <tr>
                            <td>
                                <strong style="font-family: monospace; color: var(--primary);">
                                    <?php echo e($b['reference_no'] ?: generateReferenceNumber(substr($b['service_type'], 0, 3), $b['id'])); ?>
                                </strong>
                            </td>
                            <td><span style="font-weight: 600;"><?php echo e($b['service_label']); ?></span></td>
                            <td style="max-width: 320px;"><?php echo e($b['details']); ?></td>
                            <td><?php echo formatDate($b['item_date']); ?></td>
                            <td><?php echo renderStatusBadge($b['status']); ?></td>
                            <td>
                                <?php if (strtolower($b['status']) === 'pending'): ?>
                                    <a href="<?php echo url('patient/cancel_booking.php?type=' . $b['service_type'] . '&id=' . $b['id']); ?>"
                                       class="btn btn-danger btn-sm" title="Cancel this pending booking">
                                        Cancel
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
                                <?php endif; ?>
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
                        <a href="<?php echo url('patient/bookings.php?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>" class="page-link">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
