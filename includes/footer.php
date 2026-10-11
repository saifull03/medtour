<?php
/**
 * Shared Footer Component
 * MedTour Services
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/flash.php';

// Clear form flash after output
clearFormFlash();
?>
    </div><!-- /.container -->
</main><!-- /.main-content -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="brand" style="margin-bottom: 1rem;">
                    <img src="<?php echo url('assets/images/logo.png'); ?>" alt="MedTour Logo" class="brand-logo" style="height: 2rem; width: 2rem;">
                    <span class="brand-text" style="font-size: 1.1rem;">MedTour <span class="brand-accent">Services</span></span>
                </div>
                <p style="color: #94a3b8; font-size: 0.85rem; line-height: 1.6;">
                    Leading healthcare concierge connecting patients globally with accredited partner hospitals, premier hotel lodging, medical transportation, and expedited visa facilitation.
                </p>
            </div>

            <div>
                <h4 class="footer-heading">Services</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo url('public/services.php'); ?>#hospitals">Hospital Appointments</a></li>
                    <li><a href="<?php echo url('public/services.php'); ?>#hotels">Hotel Reservations</a></li>
                    <li><a href="<?php echo url('public/services.php'); ?>#transport">Medical Transfers</a></li>
                    <li><a href="<?php echo url('public/services.php'); ?>#visa">Visa Documentation</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-heading">Access & Support</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo url('public/login.php'); ?>">Account Sign In</a></li>
                    <li><a href="<?php echo url('public/signup_user.php'); ?>">Patient Registration</a></li>
                    <li><a href="<?php echo url('public/signup_doctor.php'); ?>">Doctor Registration</a></li>
                    <li><a href="<?php echo url('public/help.php'); ?>">Help & FAQs</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-heading">Contact & Support</h4>
                <ul class="footer-links">
                    <li><span>📍 Cairo & International Concierge</span></li>
                    <li><span>📞 24/7 Helpline: +20 (0) 100 234 5678</span></li>
                    <li><span>✉️ support@medtourservices.com</span></li>
                    <li><span>🕒 Patient Desk: 24/7 Available</span></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(APP_NAME); ?>. All rights reserved. World-Class Healthcare Concierge.</p>
        </div>
    </div>
</footer>

</body>
</html>
