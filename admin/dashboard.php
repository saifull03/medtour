<?php
/**
 * Administrator Dashboard
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();
$user = currentUser();
$pdo  = getDbConnection();

// Dynamic database metrics
$patientsCount  = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$doctorsCount   = (int)$pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$hospitalsCount = (int)$pdo->query("SELECT COUNT(*) FROM hospitals")->fetchColumn();
$hotelsCount    = (int)$pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();

// Booking metrics
$aptCount = (int)$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$htlCount = (int)$pdo->query("SELECT COUNT(*) FROM hotel_bookings")->fetchColumn();
$trnCount = (int)$pdo->query("SELECT COUNT(*) FROM transport_bookings")->fetchColumn();
$vsaCount = (int)$pdo->query("SELECT COUNT(*) FROM visa_bookings")->fetchColumn();

// Pending counts requiring action
$pendingApt = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
$pendingHtl = (int)$pdo->query("SELECT COUNT(*) FROM hotel_bookings WHERE status = 'pending'")->fetchColumn();
$pendingTrn = (int)$pdo->query("SELECT COUNT(*) FROM transport_bookings WHERE status = 'pending'")->fetchColumn();
$pendingVsa = (int)$pdo->query("SELECT COUNT(*) FROM visa_bookings WHERE status = 'pending'")->fetchColumn();
$totalPending = $pendingApt + $pendingHtl + $pendingTrn + $pendingVsa;

// Recent admin logs
$stmtLogs = $pdo->query("
    SELECT l.id, l.action, l.created_at, u.name as admin_name
    FROM admin_logs l
    LEFT JOIN users u ON l.admin_id = u.id
    ORDER BY l.id DESC
    LIMIT 6
");
$recentLogs = $stmtLogs->fetchAll();

$pageTitle = 'Administrator Dashboard';
$heroTitle = 'Administration Command Center 🛡️';
$heroSubtitle = 'Real-time overview of medical bookings, partner hospitals, physicians, and system audit logs.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Attention Banner if Pending Items Exist -->
<?php if ($totalPending > 0): ?>
    <div class="card" style="margin-bottom: 2rem; background: #fffbeb; border-color: #fde68a; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-warning" style="margin-bottom: 0.25rem;">Action Required</span>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #92400e; margin-top: 0.25rem;">
                <?php echo $totalPending; ?> New Booking Request<?php echo $totalPending > 1 ? 's' : ''; ?> Awaiting Review
            </h3>
            <p style="color: #b45309; font-size: 0.85rem;">
                Appointments: <strong><?php echo $pendingApt; ?></strong> &middot; 
                Hotels: <strong><?php echo $pendingHtl; ?></strong> &middot; 
                Transport: <strong><?php echo $pendingTrn; ?></strong> &middot; 
                Visas: <strong><?php echo $pendingVsa; ?></strong>
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="<?php echo url('admin/hospital_bookings.php?status=pending'); ?>" class="btn btn-primary btn-sm">Review Hospitals</a>
            <a href="<?php echo url('admin/hotel_bookings.php?status=pending'); ?>" class="btn btn-secondary btn-sm">Review Hotels</a>
            <a href="<?php echo url('admin/transport_bookings.php?status=pending'); ?>" class="btn btn-success btn-sm">Review Transport</a>
            <a href="<?php echo url('admin/visa_bookings.php?status=pending'); ?>" class="btn btn-outline btn-sm">Review Visas</a>
        </div>
    </div>
<?php endif; ?>

<!-- Core Metrics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏥</div>
        <div class="stat-value"><?php echo $aptCount; ?></div>
        <div class="stat-label">Hospital Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏨</div>
        <div class="stat-value"><?php echo $htlCount; ?></div>
        <div class="stat-label">Hotel Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚗</div>
        <div class="stat-value"><?php echo $trnCount; ?></div>
        <div class="stat-label">Transport Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value"><?php echo $vsaCount; ?></div>
        <div class="stat-label">Visa Applications</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?php echo $patientsCount; ?></div>
        <div class="stat-label">Registered Patients</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👨‍⚕️</div>
        <div class="stat-value"><?php echo $doctorsCount; ?></div>
        <div class="stat-label">Specialist Doctors</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-value"><?php echo $hospitalsCount; ?></div>
        <div class="stat-label">Partner Hospitals</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🛏️</div>
        <div class="stat-value"><?php echo $hotelsCount; ?></div>
        <div class="stat-label">Partner Hotels</div>
    </div>
</div>

<!-- Admin Quick Links Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Hospital Appointments</h4>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Manage, approve, or cancel patient medical visits.</p>
        <a href="<?php echo url('admin/hospital_bookings.php'); ?>" class="btn btn-primary btn-sm btn-block">Manage Appointments &rarr;</a>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Hotel Lodging</h4>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Verify room availability and confirm guest reservations.</p>
        <a href="<?php echo url('admin/hotel_bookings.php'); ?>" class="btn btn-secondary btn-sm btn-block">Manage Hotels &rarr;</a>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Medical Transfers</h4>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Dispatch airport transfers, city taxis, and ambulances.</p>
        <a href="<?php echo url('admin/transport_bookings.php'); ?>" class="btn btn-success btn-sm btn-block">Manage Transfers &rarr;</a>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Visa Documentation</h4>
        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Process consular requests and medical entry approvals.</p>
        <a href="<?php echo url('admin/visa_bookings.php'); ?>" class="btn btn-outline btn-sm btn-block">Manage Visas &rarr;</a>
    </div>
</div>

<!-- System Audit Logs -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Recent Administrative Actions</h3>
            <span class="card-subtitle">Audited record changes and administrator activity</span>
        </div>
        <a href="<?php echo url('public/signup_admin.php'); ?>" class="btn btn-secondary btn-sm">+ Provision Admin</a>
    </div>

    <?php if (empty($recentLogs)): ?>
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h4 class="empty-title">No administrative logs recorded yet</h4>
            <p class="empty-text">Future status updates, cancellations, and deletions will be logged here automatically.</p>
        </div>
    <?php else: ?>
        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Administrator</th>
                        <th>Action Description</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: var(--primary);">#<?php echo $log['id']; ?></strong></td>
                            <td><strong><?php echo e($log['admin_name'] ?? 'System Admin'); ?></strong></td>
                            <td><?php echo e($log['action']); ?></td>
                            <td><?php echo formatDateTime($log['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
