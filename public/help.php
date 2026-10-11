<?php
/**
 * Help & Knowledge Center Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Help & Medical Tourism Guide';
$metaDescription = 'Frequently asked questions, international patient guides, preparation checklists, and emergency support.';
$heroTitle = 'Help Center & Patient Guide';
$heroSubtitle = 'Clear answers and essential preparation tips for your medical travel abroad.';

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 860px; margin: 0 auto;">
    <!-- FAQ Section -->
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem;">Frequently Asked Questions</h2>

        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.35rem;">What is medical tourism?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                Medical tourism refers to traveling to another country to receive high-quality medical procedures, surgeries, or specialized healthcare. Patients travel to access internationally renowned surgeons, shorten wait times, or save substantially on treatment expenses.
            </p>
        </div>

        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.35rem;">How does MedTour coordinate my journey?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                We connect you directly with vetted doctors and partner hospitals. Simultaneously, our platform handles your local hotel accommodations, sanitized private transfers, and visa documentation so you can focus entirely on treatment and recovery.
            </p>
        </div>

        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.35rem;">Can a caregiver or family member accompany me?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                Yes, absolutely. Our hotel partners offer double rooms and multi-guest executive suites, and our transport fleet includes spacious private cars and shuttles suitable for family escorts.
            </p>
        </div>

        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.35rem;">How far in advance should I submit a visa application?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                We recommend submitting your medical visa application at least 2 to 3 weeks before your scheduled hospital appointment to allow adequate time for embassy processing and consular verification.
            </p>
        </div>

        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.35rem;">Can I cancel a booking if my travel plans change?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                Yes. Any booking with status <strong>Pending</strong> can be directly cancelled by the patient from their bookings dashboard at any time without fees.
            </p>
        </div>
    </div>

    <!-- Contact & Emergency Panel -->
    <div class="card" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border-color: #bae6fd;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">Need Immediate Help?</h3>
        <p style="color: #334155; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.25rem;">
            Our international patient concierge desk is on standby 24 hours a day to assist with medical queries, appointment modifications, or urgent hospital coordination.
        </p>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
            <a href="tel:+201002345678" class="btn btn-primary btn-sm">📞 Call +20 (0) 100 234 5678</a>
            <a href="mailto:support@medtourservices.com" class="btn btn-outline btn-sm">✉️ Email Support</a>
            <a href="<?php echo url('public/login.php'); ?>" class="btn btn-secondary btn-sm">Account Login</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
