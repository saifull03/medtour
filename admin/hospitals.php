<?php
/**
 * Admin Hospital Directory Management
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

// Handle New Hospital POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();

    $name     = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $desc     = trim($_POST['description'] ?? '');

    $errors = [];
    if (empty($name)) {
        $errors['name'] = 'Hospital name is required.';
    }
    if (empty($location)) {
        $errors['location'] = 'Hospital location is required.';
    }

    if (!empty($errors)) {
        setFormFlash($_POST, $errors);
        setFlash('error', 'Please resolve the errors below.');
        header('Location: ' . url('admin/hospitals.php'));
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO hospitals (name, location, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $location, $desc ?: null]);
        $hospId = (int)$pdo->lastInsertId();

        adminLog($user['id'], "Added new partner hospital '{$name}' (ID: {$hospId})");
        setFlash('success', "Partner hospital '<strong>" . e($name) . "</strong>' added successfully.");
        header('Location: ' . url('admin/hospitals.php'));
        exit();
    } catch (Exception $e) {
        appLog('HOSPITAL_ADD_ERROR', ['error' => $e->getMessage()]);
        setFlash('error', 'Database error creating hospital record.');
        header('Location: ' . url('admin/hospitals.php'));
        exit();
    }
}

// Fetch all hospitals with affiliated doctor count
$stmt = $pdo->query("
    SELECT h.*, COUNT(d.id) as doctor_count
    FROM hospitals h
    LEFT JOIN doctors d ON h.id = d.hospital_id
    GROUP BY h.id
    ORDER BY h.name ASC
");
$hospitals = $stmt->fetchAll();

$pageTitle = 'Partner Hospitals Directory';
$heroTitle = 'Partner Hospital Facilities';
$heroSubtitle = 'Manage accredited clinics, diagnostic centers, and hospital affiliations.';
$heroClass = 'hero-admin';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
    <!-- Hospitals List Table -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Hospital Directory (<?php echo count($hospitals); ?>)</h3>
                <span class="card-subtitle">All active accredited partner facilities</span>
            </div>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline btn-sm">&larr; Dashboard</a>
        </div>

        <div class="table-container" style="box-shadow: none; border: none; margin-bottom: 0;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hospital Name</th>
                        <th>Location</th>
                        <th>Affiliated Doctors</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hospitals as $h): ?>
                        <tr>
                            <td><strong style="font-family: monospace; color: var(--primary);">#<?php echo $h['id']; ?></strong></td>
                            <td><strong><?php echo e($h['name']); ?></strong></td>
                            <td>📍 <?php echo e($h['location']); ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo (int)$h['doctor_count']; ?> Doctors</span>
                            </td>
                            <td><?php echo formatDate($h['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Hospital Form -->
    <div class="card" style="box-shadow: var(--shadow-md);">
        <h3 class="card-title" style="margin-bottom: 0.25rem;">Add Partner Hospital</h3>
        <p style="color: var(--text-muted); font-size: 0.825rem; margin-bottom: 1.25rem;">Register a new healthcare facility in the network</p>

        <form action="<?php echo url('admin/hospitals.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="name" class="form-label">Hospital Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control <?php echo fieldError('name') ? 'is-invalid' : ''; ?>"
                    value="<?php echo e(old('name')); ?>" placeholder="e.g. Al-Salam International Hospital" required>
                <?php if (fieldError('name')): ?><span class="form-error"><?php echo e(fieldError('name')); ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Location / City <span class="required">*</span></label>
                <input type="text" id="location" name="location" class="form-control <?php echo fieldError('location') ? 'is-invalid' : ''; ?>"
                    value="<?php echo e(old('location')); ?>" placeholder="e.g. Maadi, Cairo, Egypt" required>
                <?php if (fieldError('location')): ?><span class="form-error"><?php echo e(fieldError('location')); ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Facility Description</label>
                <textarea id="description" name="description" rows="3" class="form-control"
                    placeholder="Key specialties, accreditations (JCI), technology..."><?php echo e(old('description')); ?></textarea>
            </div>

            <button type="submit" class="btn btn-secondary btn-block" style="background: #4338ca;">+ Add Hospital Facility</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
