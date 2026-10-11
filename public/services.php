<?php
/**
 * Services Overview Page
 * MedTour Services
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Our Medical Tourism Services';
$metaDescription = 'Explore complete medical travel packages: hospital scheduling, recovery hotels, medical transportation, and visa processing.';
$heroTitle = 'Comprehensive Medical Tourism Care';
$heroSubtitle = 'Every service planned, vetted, and coordinated seamlessly for your international treatment journey.';

$pdo = getDbConnection();
$hospitals = $pdo->query("SELECT * FROM hospitals ORDER BY name ASC")->fetchAll();
$hotels = $pdo->query("SELECT * FROM hotels ORDER BY stars DESC, name ASC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Section: Hospital Appointments -->
<section id="hospitals" style="margin-bottom: 4rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge badge-danger">Medical Excellence</span>
            <h2 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem;">1. Hospital Appointments & Surgeries</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Accredited partner facilities featuring modern diagnostic equipment, intensive care units, and international patient wings.</p>
        </div>
        <a href="<?php echo url(isLoggedIn() ? 'patient/hospital.php' : 'public/login.php'); ?>" class="btn btn-primary">
            Schedule Appointment &rarr;
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        <?php foreach ($hospitals as $h): ?>
            <div class="card" style="padding: 1.25rem;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;"><?php echo e($h['name']); ?></h3>
                    <span style="font-size: 1.25rem;">🏥</span>
                </div>
                <p style="color: var(--primary); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem;">📍 <?php echo e($h['location'] ?? 'Cairo, Egypt'); ?></p>
                <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.5;">
                    <?php echo e($h['description'] ?: 'Fully equipped tertiary hospital providing multi-specialty surgeries, cardiology, oncology, and rehabilitation.'); ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Section: Hotel Lodging -->
<section id="hotels" style="margin-bottom: 4rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge badge-info">Comfort & Recovery</span>
            <h2 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem;">2. Partner Hotel Accommodations</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Hand-picked recuperation hotels with room-service dietary options, elevator accessibility, and quiet settings near clinics.</p>
        </div>
        <a href="<?php echo url(isLoggedIn() ? 'patient/hotel.php' : 'public/login.php'); ?>" class="btn btn-primary">
            Reserve Lodging &rarr;
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        <?php foreach ($hotels as $hotel): ?>
            <div class="card" style="padding: 1.25rem;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;"><?php echo e($hotel['name']); ?></h3>
                    <span style="font-size: 0.9rem; color: #f59e0b;"><?php echo str_repeat('★', (int)($hotel['stars'] ?? 4)); ?></span>
                </div>
                <p style="color: #0284c7; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem;">📍 <?php echo e($hotel['location']); ?></p>
                <p style="color: var(--text-muted); font-size: 0.825rem; line-height: 1.5;">
                    <?php echo e($hotel['description']); ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Section: Medical Transport -->
<section id="transport" style="margin-bottom: 4rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge badge-success">Safe Transit</span>
            <h2 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem;">3. Medical Transfers & Logistics</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Punctual, climate-controlled, and sanitized transit options for patient ease and caregiver convenience.</p>
        </div>
        <a href="<?php echo url(isLoggedIn() ? 'patient/transport.php' : 'public/login.php'); ?>" class="btn btn-primary">
            Book Transport &rarr;
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div class="card" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚕</div>
            <h4 style="font-weight: 700; margin-bottom: 0.25rem;">City Taxi</h4>
            <p style="color: var(--text-muted); font-size: 0.8rem;">Short-distance urban trips and clinic visits.</p>
        </div>
        <div class="card" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚗</div>
            <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Private Car</h4>
            <p style="color: var(--text-muted); font-size: 0.8rem;">Executive airport pickups and VIP private chauffeur.</p>
        </div>
        <div class="card" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚌</div>
            <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Group Shuttle</h4>
            <p style="color: var(--text-muted); font-size: 0.8rem;">Comfortable transport for larger families and escorts.</p>
        </div>
        <div class="card" style="text-align: center; padding: 1.5rem;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚑</div>
            <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Ambulance</h4>
            <p style="color: var(--text-muted); font-size: 0.8rem;">Stretcher and paramedic-supported emergency transfer.</p>
        </div>
    </div>
</section>

<!-- Section: Visa Assistance -->
<section id="visa" style="margin-bottom: 4rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <span class="badge badge-warning">Immigration Desk</span>
            <h2 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-top: 0.5rem;">4. Visa & Consular Documentation</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Liaison with national consulates, hospital invitation documentation, and expedited entry visas.</p>
        </div>
        <a href="<?php echo url(isLoggedIn() ? 'patient/visa.php' : 'public/login.php'); ?>" class="btn btn-primary">
            Apply for Visa &rarr;
        </a>
    </div>

    <div class="card" style="padding: 2rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Required Documentation Checklist</h3>
        <ul style="list-style: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; color: var(--text-muted); font-size: 0.875rem;">
            <li>✓ Valid passport (at least 6 months validity)</li>
            <li>✓ Official invitation letter issued by partner hospital</li>
            <li>✓ Preliminary medical diagnostic records / scans</li>
            <li>✓ Proof of accommodation reservation</li>
            <li>✓ Round-trip or onward travel confirmation</li>
            <li>✓ Verified personal identification photos</li>
        </ul>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
