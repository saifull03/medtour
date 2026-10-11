<?php
/**
 * Book Medical Transport
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

requirePatient();

$pageTitle = 'Book Medical Transport';
$heroTitle = 'Safe & Sanitized Medical Transfers';
$heroSubtitle = 'Airport meet-and-greet, scheduled hospital transfers, and accessible patient transit.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="card" style="box-shadow: var(--shadow-md);">
        <div class="card-header">
            <div>
                <h2 class="card-title">Medical Transfer Request</h2>
                <span class="card-subtitle">Choose transport type, route, pickup time, and destination</span>
            </div>
            <a href="<?php echo url('patient/bookings.php'); ?>" class="btn btn-outline btn-sm">&larr; My Bookings</a>
        </div>

        <form action="<?php echo url('actions/create_transport_booking.php'); ?>" method="POST">
            <?php echo csrfField(); ?>

            <div class="form-group">
                <label for="transport_type" class="form-label">Transport Vehicle / Service <span class="required">*</span></label>
                <select id="transport_type" name="transport_type" class="form-select <?php echo fieldError('transport_type') ? 'is-invalid' : ''; ?>" required>
                    <option value="">— Select Vehicle Category —</option>
                    <option value="Private Car" <?php echo old('transport_type', 'Private Car') === 'Private Car' ? 'selected' : ''; ?>>🚗 Private Car (Airport & Executive City Transfer)</option>
                    <option value="Taxi" <?php echo old('transport_type') === 'Taxi' ? 'selected' : ''; ?>>🚕 Standard City Taxi (Short-distance rides)</option>
                    <option value="Bus" <?php echo old('transport_type') === 'Bus' ? 'selected' : ''; ?>>🚌 Group Shuttle (Families & Escorts)</option>
                    <option value="Train" <?php echo old('transport_type') === 'Train' ? 'selected' : ''; ?>>🚆 Rail Transit (Inter-city medical transit)</option>
                    <option value="Ambulance" <?php echo old('transport_type') === 'Ambulance' ? 'selected' : ''; ?>>🚑 Emergency Ambulance (Paramedic & Stretcher supported)</option>
                </select>
                <?php if (fieldError('transport_type')): ?><span class="form-error"><?php echo e(fieldError('transport_type')); ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pickup_location" class="form-label">Pickup Location <span class="required">*</span></label>
                    <input type="text" id="pickup_location" name="pickup_location" class="form-control <?php echo fieldError('pickup_location') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('pickup_location')); ?>" placeholder="e.g. Cairo International Airport Terminal 3" required>
                    <?php if (fieldError('pickup_location')): ?><span class="form-error"><?php echo e(fieldError('pickup_location')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="destination" class="form-label">Destination Location <span class="required">*</span></label>
                    <input type="text" id="destination" name="destination" class="form-control <?php echo fieldError('destination') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('destination')); ?>" placeholder="e.g. Cairo Marriott Hotel, Zamalek" required>
                    <?php if (fieldError('destination')): ?><span class="form-error"><?php echo e(fieldError('destination')); ?></span><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date" class="form-label">Transfer Date <span class="required">*</span></label>
                    <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>"
                        class="form-control <?php echo fieldError('date') ? 'is-invalid' : ''; ?>"
                        value="<?php echo e(old('date')); ?>" required>
                    <?php if (fieldError('date')): ?><span class="form-error"><?php echo e(fieldError('date')); ?></span><?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="time" class="form-label">Pickup Time (Optional)</label>
                    <input type="time" id="time" name="time" class="form-control"
                        value="<?php echo e(old('time', '12:00')); ?>">
                    <span class="form-hint">Driver will monitor your arrival flight if applicable.</span>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem; margin: 1.5rem 0;">
                <p style="font-size: 0.85rem; color: #475569; line-height: 1.5;">
                    🚗 <strong>Note:</strong> Initial status will be <strong>Pending</strong>. You will receive dispatch and driver details once confirmed.
                </p>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button type="submit" class="btn btn-success btn-lg">Schedule Medical Transfer &rarr;</button>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
