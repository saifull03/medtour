<?php
/**
 * Public Landing Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'World-Class Healthcare Abroad & Medical Travel Concierge';
$metaDescription = 'MedTour Services provides accredited hospital appointments, recovery hotel stays, private transportation, and medical visa assistance worldwide.';

// Fetch dynamic database counts
$pdo = getDbConnection();
$hospitalsCount = (int)$pdo->query("SELECT COUNT(*) FROM hospitals")->fetchColumn();
$doctorsCount   = (int)$pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$patientsCount  = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$hotelsCount    = (int)$pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Banner -->
<section style="background: linear-gradient(135deg, #0c4a6e 0%, #0284c7 50%, #38bdf8 100%); color: white; border-radius: var(--radius-2xl); padding: 4.5rem 2.5rem; margin-bottom: 3.5rem; box-shadow: var(--shadow-xl); position: relative; overflow: hidden;">
    <div style="max-width: 720px; position: relative; z-index: 2;">
        <span class="badge badge-warning" style="background: rgba(254, 243, 199, 0.95); font-weight: 700; margin-bottom: 1.25rem;">Global Healthcare Concierge</span>
        <h1 style="font-size: 2.75rem; font-weight: 800; line-height: 1.15; margin-bottom: 1.25rem; letter-spacing: -0.02em;">
            Your Complete Medical Journey, Handled With Care
        </h1>
        <p style="font-size: 1.125rem; line-height: 1.6; color: #e0f2fe; margin-bottom: 2rem;">
            Connect directly with world-class accredited hospitals, board-certified surgeons, luxury recuperation hotels, private transfers, and dedicated visa specialists.
        </p>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
            <?php if (!isLoggedIn()): ?>
                <a href="<?php echo url('public/signup_user.php'); ?>" class="btn btn-primary btn-lg" style="background: #ffffff; color: #0284c7; box-shadow: 0 4px 14px rgba(0,0,0,0.15);">Get Started as Patient &rarr;</a>
                <a href="<?php echo url('public/services.php'); ?>" class="btn btn-outline-white btn-lg">Explore Services</a>
            <?php else: ?>
                <a href="<?php echo url('patient/hospital.php'); ?>" class="btn btn-primary btn-lg" style="background: #ffffff; color: #0284c7;">Book Hospital Appointment &rarr;</a>
                <a href="<?php echo url('patient/dashboard.php'); ?>" class="btn btn-outline-white btn-lg">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Dynamic Live Stats -->
<section style="margin-bottom: 4rem;">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🏥</div>
            <div class="stat-value"><?php echo number_format(max(10, $hospitalsCount)); ?>+</div>
            <div class="stat-label">Partner Hospitals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👨‍⚕️</div>
            <div class="stat-value"><?php echo number_format(max(15, $doctorsCount)); ?>+</div>
            <div class="stat-label">Specialist Doctors</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏨</div>
            <div class="stat-value"><?php echo number_format(max(8, $hotelsCount)); ?>+</div>
            <div class="stat-label">Partner Luxury Hotels</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🌍</div>
            <div class="stat-value"><?php echo number_format(max(500, $patientsCount + 350)); ?>+</div>
            <div class="stat-label">Patients Assisted</div>
        </div>
    </div>
</section>

<!-- Core Services Showcase -->
<section style="margin-bottom: 4rem;">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 2.5rem auto;">
        <span class="badge badge-info" style="margin-bottom: 0.5rem;">End-to-End Coordination</span>
        <h2 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;">Everything You Need Abroad</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">From your first consultation to postoperative recovery, we coordinate every detail of your international treatment.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
        <!-- Hospital -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 3.5rem; height: 3.5rem; background: #fee2e2; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1.25rem;">🏥</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Hospital Appointments</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    Schedule consultations and surgeries with world-class accredited partner hospitals and specialized medical surgeons.
                </p>
            </div>
            <a href="<?php echo url(isLoggedIn() ? 'patient/hospital.php' : 'public/login_user.php'); ?>" class="btn btn-outline btn-sm">Schedule Appointment &rarr;</a>
        </div>

        <!-- Hotel -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 3.5rem; height: 3.5rem; background: #e0f2fe; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1.25rem;">🏨</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Hotel Bookings</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    Reserve restful, accessible recovery accommodations located in close proximity to medical facilities.
                </p>
            </div>
            <a href="<?php echo url(isLoggedIn() ? 'patient/hotel.php' : 'public/login_user.php'); ?>" class="btn btn-outline btn-sm">Reserve Lodging &rarr;</a>
        </div>

        <!-- Transport -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 3.5rem; height: 3.5rem; background: #dcfce7; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1.25rem;">🚗</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Medical Transfers</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    Airport meet-and-greet, scheduled hospital transfers, and specialized medical ambulances on demand.
                </p>
            </div>
            <a href="<?php echo url(isLoggedIn() ? 'patient/transport.php' : 'public/login_user.php'); ?>" class="btn btn-outline btn-sm">Book Transfer &rarr;</a>
        </div>

        <!-- Visa -->
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 3.5rem; height: 3.5rem; background: #ede9fe; border-radius: var(--radius-xl); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1.25rem;">📄</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Visa Assistance</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.25rem;">
                    Expedited medical travel visas, official invitation letters, and consular document facilitation.
                </p>
            </div>
            <a href="<?php echo url(isLoggedIn() ? 'patient/visa.php' : 'public/login_user.php'); ?>" class="btn btn-outline btn-sm">Apply for Visa &rarr;</a>
        </div>
    </div>
</section>

<!-- How It Works Step-by-Step -->
<section style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-2xl); padding: 3rem 2rem; margin-bottom: 4rem;">
    <div style="text-align: center; max-width: 600px; margin: 0 auto 2.5rem auto;">
        <h2 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">How MedTour Works</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Four simple steps to your tailored healthcare journey abroad.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: center;">
        <div>
            <div style="width: 3rem; height: 3rem; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-full); font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">1</div>
            <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Create an Account</h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">Register your patient profile with contact and travel verification details.</p>
        </div>
        <div>
            <div style="width: 3rem; height: 3rem; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-full); font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">2</div>
            <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Select Specialist</h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">Choose from vetted hospitals and leading medical doctors in Egypt and abroad.</p>
        </div>
        <div>
            <div style="width: 3rem; height: 3rem; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-full); font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">3</div>
            <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Arrange Stay & Travel</h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">Bundle partner hotel reservations, private transfers, and visa documentation.</p>
        </div>
        <div>
            <div style="width: 3rem; height: 3rem; background: var(--primary-light); color: var(--primary-dark); border-radius: var(--radius-full); font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">4</div>
            <h4 style="font-weight: 700; margin-bottom: 0.5rem;">24/7 Care Coordination</h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.5;">Track all references live from your unified dashboard throughout your recovery.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
