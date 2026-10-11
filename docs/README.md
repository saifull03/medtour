# MedTour Services – Architecture & Operations Documentation

**MedTour Services** is a secure, native PHP 8.2 medical tourism platform connecting international patients with accredited partner hospitals, medical specialists, recovery hotel stays, private transportation, and medical travel visa assistance.

The application is built strictly using **native PHP, MySQL/MariaDB, semantic HTML5, and vanilla CSS** without external PHP frameworks, JavaScript frameworks, or client-side dependencies.

---

## 1. Directory Structure

```text
c:\xampp\htdocs\medtour\
├── config/
│   ├── constants.php            # Environment constants, paths, and role definitions
│   └── database.php             # Unified PDO database singleton with safe error handling
├── includes/
│   ├── auth.php                 # Role-based authorization & session guard helpers
│   ├── csrf.php                 # Cryptographic CSRF token generation & verification
│   ├── flash.php                # PRG session flash message & form preservation helper
│   ├── helpers.php              # XSS escaping (e), formatting, URL builder & app logger
│   ├── validation.php           # Server-side validation rules (email, password, bookings)
│   ├── header.php               # Shared semantic HTML header with meta tags & typography
│   ├── navigation.php           # Role-aware responsive navigation bar
│   └── footer.php               # Shared semantic HTML footer & session flash cleanup
├── public/
│   ├── index.php                # Public landing page with live dynamic statistics
│   ├── services.php             # Medical tourism services overview
│   ├── help.php                 # Knowledge center & FAQs
│   ├── login.php                # Unified authentication entry point (Admin, Doctor & Patient)
│   ├── signup_user.php          # Patient registration
│   ├── signup_doctor.php        # Physician onboarding
│   ├── signup_admin.php         # Restricted administrator provisioning
│   └── logout.php               # Unified session & cookie termination
├── patient/
│   ├── dashboard.php            # Patient overview, live counts & recent consultations
│   ├── profile.php              # Patient profile & travel passport manager
│   ├── hospital.php             # Hospital appointment booking form
│   ├── hotel.php                # Hotel accommodation reservation form
│   ├── transport.php            # Medical transit booking form
│   ├── visa.php                 # Consular visa application form
│   ├── bookings.php             # Unified bookings list with search, filter & pagination
│   └── cancel_booking.php       # Safe confirmation page for cancelling pending bookings
├── doctor/
│   ├── dashboard.php            # Physician overview, clinical rates & appointment counts
│   ├── appointments.php         # Assigned patient appointments schedule & filters
│   └── update_appointment.php   # Manage clinical status & add consultation notes
├── admin/
│   ├── dashboard.php            # Command center with system metrics & audit logs
│   ├── hospitals.php            # Partner hospital directory & facility registration
│   ├── doctors.php              # Physician credentials directory & status control
│   ├── patients.php             # Patient directory with booking counts & status control
│   ├── hospital_bookings.php    # Administrative hospital appointment management
│   ├── hotel_bookings.php       # Administrative hotel reservation management
│   ├── transport_bookings.php   # Administrative transport transfer management
│   ├── visa_bookings.php        # Administrative visa application management
│   └── update_booking.php       # Unified booking status & parameters editor
├── actions/
│   ├── login.php                # Unified authentication POST handler
│   ├── register.php             # Multi-role transactional registration handler
│   ├── create_hospital_booking.php # Hospital booking POST handler
│   ├── create_hotel_booking.php    # Hotel reservation POST handler
│   ├── create_transport_booking.php# Transport transfer POST handler
│   ├── create_visa_application.php # Visa application POST handler
│   ├── update_booking_status.php   # Status lifecycle transition handler
│   └── delete_booking.php          # Administrative permanent deletion handler
├── assets/
│   ├── css/style.css            # Pure Vanilla CSS design system
│   └── images/                  # Local project imagery & branding
├── database/
│   ├── mt_db.sql                # Complete baseline SQL dump
│   ├── migrate.php              # Safe PHP CLI migration runner
│   └── migrations/
│       └── 001_initial_schema_fixes.sql # Schema stabilization migration
├── logs/                        # Application & database error audit logs
└── docs/
    └── README.md                # System documentation
```

---

## 2. Security Implementations

1. **Prepared SQL Statements (PDO):**
   - Every database query containing dynamic parameters strictly utilizes prepared statements with bound parameters (`$stmt->prepare()`, `$stmt->execute([...])`).
   - Direct variable interpolation into SQL strings has been eliminated.
2. **Cross-Site Request Forgery (CSRF):**
   - Every state-changing form includes a cryptographically secure token (`bin2hex(random_bytes(32))`) validated via `validateCsrfToken()` before processing.
   - Deletions, status updates, and cancellations strictly reject GET requests and require POST with CSRF verification.
3. **Cross-Site Scripting (XSS) Prevention:**
   - All dynamic output is escaped using `e($value)` (`htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`).
4. **Session Fixation & Management:**
   - Sessions are protected via `session_regenerate_id(true)` upon successful authentication.
   - Consistent user session structure `$_SESSION['user']` combined with backwards-compatible aliases.
   - Logout clears all session memory and removes the session cookie with an expired timestamp.
5. **Password Security:**
   - Passwords are encrypted using bcrypt via `password_hash($password, PASSWORD_DEFAULT)` and verified via `password_verify()`. Plain-text passwords are never stored or logged.
   - Strong password policies enforced (minimum 8 characters, uppercase, lowercase, number).
6. **Authorization & RBAC:**
   - **Unified Login Entry Point:** All users (Patients, Doctors, and Administrators) authenticate through the single common portal at `public/login.php`. No separate admin or doctor login sections are exposed publicly. Upon credential verification, the system securely inspects the verified user's assigned role and automatically routes them to their corresponding dashboard (`admin/dashboard.php`, `doctor/dashboard.php`, or `patient/dashboard.php`).
   - Strict guards: `requireLogin()`, `requirePatient()`, `requireDoctor()`, `requireAdmin()`.
   - Patients can only view and cancel their own bookings.
   - Doctors can only view and update appointments assigned to their doctor ID.
   - Public registration of administrators is blocked; only existing administrators can provision new administrators.
7. **Post/Redirect/Get (PRG) Pattern:**
   - Form submissions process logic, set a session flash message (`setFlash()`), and issue an HTTP 302 redirect to prevent duplicate submissions on refresh.

---

## 3. Database Migration & Setup

To execute the schema updates and ensure all constraints are applied:

```powershell
php database/migrate.php
```

Default System Credentials:
- **Administrator:** `admin@medtour.com` / `Admin@123456`
- **Doctor:** `siddik@gmail.com`
- **Patient:** `saiful@gmail.com`

---

## 4. Backwards Compatibility & URL Routing

All legacy root URL entry points (such as `login.php`, `login_user.php`, `welcome.php`, `hospital.php`, `view_admin_hospital.php`, etc.) and root image assets are seamlessly forwarded to their organized counterparts via `.htaccess` Apache rewrite rules with non-breaking 302 HTTP redirects, keeping the root directory clean and clutter-free while preserving all GET query string parameters.
