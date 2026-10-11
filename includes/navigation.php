<?php
/**
 * Navigation Component
 * MedTour Services
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

$user = currentUser();
$role = $user['role'] ?? null;
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<header class="site-header">
    <div class="container navbar">
        <a href="<?php echo url('public/index.php'); ?>" class="brand" aria-label="MedTour Services Home">
            <img src="<?php echo url('assets/images/logo.png'); ?>" alt="MedTour Logo" class="brand-logo">
            <span class="brand-text">MedTour <span class="brand-accent">Services</span></span>
        </a>

        <!-- Mobile Menu Toggle (Pure CSS) -->
        <input type="checkbox" id="nav-toggle" class="nav-toggle-input" aria-label="Toggle Navigation">
        <label for="nav-toggle" class="nav-toggle-label" aria-hidden="true">&#9776;</label>

        <ul class="nav-menu">
            <?php if (!$user): ?>
                <!-- Public / Guest Navigation (Unified: No separate section for Admin) -->
                <li><a href="<?php echo url('public/index.php'); ?>" class="nav-link <?php echo $currentScript === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo url('public/services.php'); ?>" class="nav-link <?php echo $currentScript === 'services.php' ? 'active' : ''; ?>">Services</a></li>
                <li><a href="<?php echo url('public/help.php'); ?>" class="nav-link <?php echo $currentScript === 'help.php' ? 'active' : ''; ?>">Help & FAQ</a></li>
            <?php elseif ($role === ROLE_PATIENT): ?>
                <!-- Patient Navigation -->
                <li><a href="<?php echo url('patient/dashboard.php'); ?>" class="nav-link <?php echo $currentScript === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo url('patient/bookings.php'); ?>" class="nav-link <?php echo $currentScript === 'bookings.php' ? 'active' : ''; ?>">My Bookings</a></li>
                <li><a href="<?php echo url('patient/hospital.php'); ?>" class="nav-link <?php echo $currentScript === 'hospital.php' ? 'active' : ''; ?>">Hospitals</a></li>
                <li><a href="<?php echo url('patient/hotel.php'); ?>" class="nav-link <?php echo $currentScript === 'hotel.php' ? 'active' : ''; ?>">Hotels</a></li>
                <li><a href="<?php echo url('patient/transport.php'); ?>" class="nav-link <?php echo $currentScript === 'transport.php' ? 'active' : ''; ?>">Transport</a></li>
                <li><a href="<?php echo url('patient/visa.php'); ?>" class="nav-link <?php echo $currentScript === 'visa.php' ? 'active' : ''; ?>">Visa</a></li>
                <li><a href="<?php echo url('patient/profile.php'); ?>" class="nav-link <?php echo $currentScript === 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
            <?php elseif ($role === ROLE_DOCTOR): ?>
                <!-- Doctor Navigation -->
                <li><a href="<?php echo url('doctor/dashboard.php'); ?>" class="nav-link <?php echo $currentScript === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo url('doctor/appointments.php'); ?>" class="nav-link <?php echo $currentScript === 'appointments.php' ? 'active' : ''; ?>">Appointments</a></li>
                <li><a href="<?php echo url('public/help.php'); ?>" class="nav-link <?php echo $currentScript === 'help.php' ? 'active' : ''; ?>">Help</a></li>
            <?php elseif ($role === ROLE_ADMIN): ?>
                <!-- Administrator Navigation -->
                <li><a href="<?php echo url('admin/dashboard.php'); ?>" class="nav-link <?php echo $currentScript === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo url('admin/hospital_bookings.php'); ?>" class="nav-link <?php echo $currentScript === 'hospital_bookings.php' ? 'active' : ''; ?>">Hospital Bookings</a></li>
                <li><a href="<?php echo url('admin/hotel_bookings.php'); ?>" class="nav-link <?php echo $currentScript === 'hotel_bookings.php' ? 'active' : ''; ?>">Hotels</a></li>
                <li><a href="<?php echo url('admin/transport_bookings.php'); ?>" class="nav-link <?php echo $currentScript === 'transport_bookings.php' ? 'active' : ''; ?>">Transport</a></li>
                <li><a href="<?php echo url('admin/visa_bookings.php'); ?>" class="nav-link <?php echo $currentScript === 'visa_bookings.php' ? 'active' : ''; ?>">Visa</a></li>
                <li><a href="<?php echo url('admin/patients.php'); ?>" class="nav-link <?php echo $currentScript === 'patients.php' ? 'active' : ''; ?>">Patients</a></li>
                <li><a href="<?php echo url('admin/doctors.php'); ?>" class="nav-link <?php echo $currentScript === 'doctors.php' ? 'active' : ''; ?>">Doctors</a></li>
                <li><a href="<?php echo url('admin/hospitals.php'); ?>" class="nav-link <?php echo $currentScript === 'hospitals.php' ? 'active' : ''; ?>">Hospitals</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <?php if (!$user): ?>
                <!-- Common Unified Login for All Users -->
                <a href="<?php echo url('public/login.php'); ?>" class="btn btn-outline-white btn-sm">Login</a>
                <a href="<?php echo url('public/signup_user.php'); ?>" class="btn btn-primary btn-sm">Sign Up</a>
            <?php else: ?>
                <span class="user-greeting" style="color: #cbd5e1; font-size: 0.85rem; font-weight: 500;">
                    Hello, <strong><?php echo e($user['name']); ?></strong> (<?php echo e(ucfirst($user['role'])); ?>)
                </span>
                <form action="<?php echo url('public/logout.php'); ?>" method="POST" style="display: inline; margin: 0;">
                    <?php echo csrfField(); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>
