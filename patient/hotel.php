<?php
/**
 * Book Hotel Accommodation
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requirePatient();
$pdo = getDbConnection();

// Load partner hotels from database
$hotels = $pdo->query("SELECT id, name, location, stars, description FROM hotels ORDER BY stars DESC, name ASC")->fetchAll();

$pageTitle = 'Reserve Recovery Hotel';
$heroTitle = 'Partner Hotel Accommodations';
$heroSubtitle = 'Quiet, comfortable, and accessible recuperation lodging near partner hospitals.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header">
            <div>
                <h2 class="card-title">Hotel Reservation Request</h2>
                <span class="card-subtitle">Select hotel, check-in/out dates, and room arrangement</span>
            </div>
            <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">&larr; My Bookings</a>
        </div>

        <form action="<?php echo url('actions/create_hotel_booking.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="hotel_id" class="form-label">Select Partner Hotel <span class="required">*</span></label>
                <select id="hotel_id" name="hotel_id" class="form-select <?php echo fieldError('hotel_id') ? 'is-invalid' : ''; ?>" required>
                    <option value="">— Choose a Partner Hotel —</option>
                    <?php foreach ($hotels as $hotel): ?>
                        <option value="<?php echo e($hotel['id']); ?>" <?php echo old('hotel_id') == $hotel['id'] ? 'selected' : ''; ?>>
                            <?php echo e($hotel['name']); ?> (<?php echo str_repeat('★', (int)($hotel['stars'] ?? 4)); ?> &middot; <?php echo e($hotel['location']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (fieldError('hotel_id')): ?><span class="form-error"><?php echo e(fieldError('hotel_id')); ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="checkin_date" class="form-label">Check-in Date <span class="required">*</span></label>
                    <input type="date" id="checkin_date" name="checkin_date" min="<?php echo date('Y-m-d'); ?>"
                        class="form-control <?php echo fieldError('checkin_date') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('checkin_date')); ?>" required>
                    <?php if (fieldError('checkin_date')): ?><span class="form-error"><?php echo e(fieldError('checkin_date')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="checkout_date" class="form-label">Check-out Date <span class="required">*</span></label>
                    <input type="date" id="checkout_date" name="checkout_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                        class="form-control <?php echo fieldError('checkout_date') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('checkout_date')); ?>" required>
                    <?php if (fieldError('checkout_date')): ?><span class="form-error"><?php echo e(fieldError('checkout_date')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room_type" class="form-label">Room Type <span class="required">*</span></label>
                    <select id="room_type" name="room_type" class="form-select <?php echo fieldError('room_type') ? 'is-invalid' : ''; ?>" required>
                        <option value="Single Room" <?php echo old('room_type') === 'Single Room' ? 'selected' : ''; ?>>Single Room (1 Patient)</option>
                        <option value="Double Room" <?php echo old('room_type', 'Double Room') === 'Double Room' ? 'selected' : ''; ?>>Double Room (Patient + Escort)</option>
                        <option value="Deluxe Room" <?php echo old('room_type') === 'Deluxe Room' ? 'selected' : ''; ?>>Deluxe Accessible Room</option>
                        <option value="Executive Suite" <?php echo old('room_type') === 'Executive Suite' ? 'selected' : ''; ?>>Executive Family Suite</option>
                    </select>
                    <?php if (fieldError('room_type')): ?><span class="form-error"><?php echo e(fieldError('room_type')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="num_guests" class="form-label">Number of Guests <span class="required">*</span></label>
                    <input type="number" id="num_guests" name="num_guests" min="1" max="20"
                        class="form-control <?php echo fieldError('num_guests') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('num_guests', '2')); ?>" required>
                    <?php if (fieldError('num_guests')): ?><span class="form-error"><?php echo e(fieldError('num_guests')); ?></span><?php endif; ?>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem; margin: 1.5rem 0;">
                <p style="font-size: 0.85rem; color: #475569; line-height: 1.5;">
                    🏨 <strong>Booking Status:</strong> New hotel reservations start in <strong>Pending</strong> status until verified with hotel front desk.
                </p>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-primary btn-lg">Submit Hotel Reservation &rarr;</button>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
