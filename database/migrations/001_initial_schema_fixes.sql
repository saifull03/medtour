-- Migration 001: Schema stabilization, constraints, and hotel seeding
-- Database: mt_db

-- 1. Create hotels table if not exists
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `stars` int(11) DEFAULT 4,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Seed hotels if empty
INSERT INTO `hotels` (`name`, `location`, `stars`, `description`)
SELECT * FROM (
    SELECT 'Cairo Marriott Hotel' AS name, 'Zamalek, Cairo' AS location, 5 AS stars, 'Luxury historic hotel in the heart of Zamalek overlooking the Nile.' AS description
    UNION ALL SELECT 'Kempinski Nile Hotel', 'Garden City, Cairo', 5, 'Boutique European luxury on the east bank of the Nile River.'
    UNION ALL SELECT 'Four Seasons Cairo', 'Giza, Cairo', 5, 'World-class hospitality with stunning views of the Great Pyramids.'
    UNION ALL SELECT 'Hilton Cairo Zamalek', 'Zamalek, Cairo', 4, 'Comfortable riverside stay close to premier medical centers.'
    UNION ALL SELECT 'InterContinental Semiramis', 'Corniche, Cairo', 5, 'Iconic luxury with premier wellness and accessible amenities.'
    UNION ALL SELECT 'Steigenberger Hotel', 'Tahrir Square, Cairo', 4, 'Modern centrally located hotel with exceptional connectivity.'
    UNION ALL SELECT 'Novotel Cairo Airport', 'Cairo Airport', 4, 'Convenient stay adjacent to airport with free shuttle services.'
    UNION ALL SELECT 'Al-Nile Residences', 'Giza, Cairo', 3, 'Serviced residential suites tailored for long-term recovery stays.'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `hotels` LIMIT 1);

-- 3. Modify appointments: change appointment_date to DATETIME, add notes column
ALTER TABLE `appointments`
  MODIFY `appointment_date` datetime NOT NULL;

-- Add notes column if not present
SET @col_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'appointments' AND column_name = 'notes');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE appointments ADD COLUMN notes TEXT NULL AFTER status', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Add hospital_id to doctors table if not present
SET @col_exists_doc = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'doctors' AND column_name = 'hospital_id');
SET @sql_doc = IF(@col_exists_doc = 0, 'ALTER TABLE doctors ADD COLUMN hospital_id INT(11) NULL AFTER user_id, ADD CONSTRAINT fk_doctor_hospital FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL', 'SELECT 1');
PREPARE stmt_doc FROM @sql_doc;
EXECUTE stmt_doc;
DEALLOCATE PREPARE stmt_doc;

-- Link existing doctor 1 to hospital 7 if not set
UPDATE `doctors` SET `hospital_id` = 7 WHERE `id` = 1 AND `hospital_id` IS NULL;

-- 5. Add UNIQUE constraint to patients(user_id) if not present
SET @uq_exists_pat = (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'patients' AND index_name = 'uq_patients_user_id');
SET @sql_pat = IF(@uq_exists_pat = 0, 'ALTER TABLE patients ADD UNIQUE KEY uq_patients_user_id (user_id)', 'SELECT 1');
PREPARE stmt_pat FROM @sql_pat;
EXECUTE stmt_pat;
DEALLOCATE PREPARE stmt_pat;

-- 6. Add UNIQUE constraint to doctors(user_id) if not present
SET @uq_exists_doc = (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'doctors' AND index_name = 'uq_doctors_user_id');
SET @sql_doc2 = IF(@uq_exists_doc = 0, 'ALTER TABLE doctors ADD UNIQUE KEY uq_doctors_user_id (user_id)', 'SELECT 1');
PREPARE stmt_doc2 FROM @sql_doc2;
EXECUTE stmt_doc2;
DEALLOCATE PREPARE stmt_doc2;

-- 7. Add hotel_id to hotel_bookings if not present
SET @col_exists_hb = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'hotel_bookings' AND column_name = 'hotel_id');
SET @sql_hb = IF(@col_exists_hb = 0, 'ALTER TABLE hotel_bookings ADD COLUMN hotel_id INT(11) NULL AFTER patient_id, ADD CONSTRAINT fk_hotel_booking_hotel FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE SET NULL', 'SELECT 1');
PREPARE stmt_hb FROM @sql_hb;
EXECUTE stmt_hb;
DEALLOCATE PREPARE stmt_hb;

-- Link existing hotel booking 1 to hotel
UPDATE `hotel_bookings` SET `hotel_id` = 4 WHERE `id` = 1 AND `hotel_id` IS NULL;

-- 8. Add cancelled to visa_bookings status enum
ALTER TABLE `visa_bookings`
  MODIFY `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending';

-- 9. Add reference_no to booking tables for professional tracking
SET @col_exists_ref_a = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'appointments' AND column_name = 'reference_no');
SET @sql_ref_a = IF(@col_exists_ref_a = 0, 'ALTER TABLE appointments ADD COLUMN reference_no VARCHAR(20) NULL UNIQUE AFTER id', 'SELECT 1');
PREPARE stmt_ref_a FROM @sql_ref_a;
EXECUTE stmt_ref_a;
DEALLOCATE PREPARE stmt_ref_a;

SET @col_exists_ref_h = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'hotel_bookings' AND column_name = 'reference_no');
SET @sql_ref_h = IF(@col_exists_ref_h = 0, 'ALTER TABLE hotel_bookings ADD COLUMN reference_no VARCHAR(20) NULL UNIQUE AFTER id', 'SELECT 1');
PREPARE stmt_ref_h FROM @sql_ref_h;
EXECUTE stmt_ref_h;
DEALLOCATE PREPARE stmt_ref_h;

SET @col_exists_ref_t = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'transport_bookings' AND column_name = 'reference_no');
SET @sql_ref_t = IF(@col_exists_ref_t = 0, 'ALTER TABLE transport_bookings ADD COLUMN reference_no VARCHAR(20) NULL UNIQUE AFTER id', 'SELECT 1');
PREPARE stmt_ref_t FROM @sql_ref_t;
EXECUTE stmt_ref_t;
DEALLOCATE PREPARE stmt_ref_t;

SET @col_exists_ref_v = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'visa_bookings' AND column_name = 'reference_no');
SET @sql_ref_v = IF(@col_exists_ref_v = 0, 'ALTER TABLE visa_bookings ADD COLUMN reference_no VARCHAR(20) NULL UNIQUE AFTER id', 'SELECT 1');
PREPARE stmt_ref_v FROM @sql_ref_v;
EXECUTE stmt_ref_v;
DEALLOCATE PREPARE stmt_ref_v;

-- Populate reference numbers for existing records
UPDATE `appointments` SET `reference_no` = CONCAT('MED-APT-', LPAD(id, 5, '0')) WHERE `reference_no` IS NULL;
UPDATE `hotel_bookings` SET `reference_no` = CONCAT('MED-HTL-', LPAD(id, 5, '0')) WHERE `reference_no` IS NULL;
UPDATE `transport_bookings` SET `reference_no` = CONCAT('MED-TRN-', LPAD(id, 5, '0')) WHERE `reference_no` IS NULL;
UPDATE `visa_bookings` SET `reference_no` = CONCAT('MED-VSA-', LPAD(id, 5, '0')) WHERE `reference_no` IS NULL;

-- 10. Ensure admin user exists with strong password
-- Default admin credentials: admin@medtour.com / Admin@123456
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`)
SELECT 'System Administrator', 'admin@medtour.com', '$2y$10$U9X2r.4r1kXUoX9e6R1.4eKzN0T0Tz0N0uJ2y1X4R4P8A1r8R1.0.', 'admin', 1
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `role` = 'admin' LIMIT 1);
