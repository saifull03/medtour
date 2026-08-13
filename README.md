# 🏥 MedTour Services — Medical Tourism Management System

[![PHP Version](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

MedTour Services is a comprehensive, multi-role web-based application designed to bridge the gap between world-class medical healthcare and international travel support. The system manages the complex lifecycle of medical tourism, combining patient registrations, specialist consultations, hospital bookings, accommodation reservations, transportation scheduling, and visa processing under a unified portal.

---

## 📌 Project Overview

When patients travel abroad for medical treatment, they face logistical hurdles beyond clinical care. MedTour Services solves this by providing:
1. **🏥 Premium Healthcare Coordination:** Seamless appointment booking with specialized doctors at JCI-accredited partner hospitals.
2. **🌍 End-to-End Travel Logistics:** Integrated booking systems for hotel stays, airport/hospital transport, and medical visa processing.
3. **🛡️ Dedicated Portals:** Tailored interfaces for Patients, Doctor Specialists, and System Administrators.

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.x (Session-based auth, prepared SQL statements)
- **Database:** MySQL / MariaDB (Relational structure with cascade triggers)
- **Frontend:** HTML5, Modern Vanilla JavaScript, CSS3
- **CSS Framework:** Tailwind CSS (CDN-based custom theme integration)
- **Typography:** Inter (via Google Fonts)
- **Icons:** SVG icons & Emojis

---

## 🔑 Key Features by User Role

### 1. 🙋‍♂️ Patient Portal
- **Dashboard:** At-a-glance booking metrics for appointments, transport, visas, and hotels.
- **Service Bookings:**
  - **Hospital Booking:** Schedule clinical consultations with specialist doctors.
  - **Hotel Booking:** Find and reserve accommodation close to partner hospitals.
  - **Transportation:** Book airport pickups, private cabs, or medical transport.
  - **Visa Assistance:** Request processing support for medical entry visas.
- **Booking Overview:** Access, review, and track status (`pending`, `confirmed`, `completed`, `cancelled`) for all active bookings.

### 2. 👨‍⚕️ Doctor Portal
- **Professional Overview:** Highlight doctor specialization, experience, and custom consultation fee structures.
- **Appointment Management:** View patient bookings, check patient details, and process appointment statuses.
- **Schedule Tracker:** Keep track of total appointments and upcoming patient check-ups.

### 3. 🛡️ Administrative Portal
- **Logistics Control Center:** Complete oversight of all patient transport requests, hotel bookings, hospital visits, and visa applications.
- **Status Approvals:** Power to change bookings status and process actions.
- **Account Registration:** Provision and register new admin accounts or verify medical doctor profiles.

---

## 📊 Database Schema (ER Diagram)

The system is built on a highly normalized relational database model designed to guarantee data integrity through foreign keys and cascading operations.

```mermaid
erDiagram
    USERS {
        int id PK
        varchar name
        varchar email UK
        varchar password
        enum role
        tinyint status
        timestamp created_at
    }
    PATIENTS {
        int id PK
        int user_id FK
        varchar phone
        varchar country
        varchar passport_no
        timestamp created_at
    }
    DOCTORS {
        int id PK
        int user_id FK
        varchar specialization
        int experience
        decimal consultation_fee
        timestamp created_at
    }
    HOSPITALS {
        int id PK
        varchar name
        varchar location
        text description
        timestamp created_at
    }
    TREATMENTS {
        int id PK
        int hospital_id FK
        varchar treatment_name
        decimal cost
        varchar duration
        timestamp created_at
    }
    APPOINTMENTS {
        int id PK
        int patient_id FK
        int doctor_id FK
        int hospital_id FK
        date appointment_date
        enum status
        timestamp created_at
    }
    BOOKINGS {
        int id PK
        int patient_id FK
        int treatment_id FK
        date booking_date
        enum status
        timestamp created_at
    }
    PAYMENTS {
        int id PK
        int booking_id FK
        decimal amount
        enum payment_method
        enum payment_status
        timestamp paid_at
        timestamp created_at
    }
    HOTEL_BOOKINGS {
        int id PK
        int patient_id FK
        varchar hotel_name
        date checkin_date
        date checkout_date
        int num_guests
        varchar room_type
        enum status
        timestamp created_at
    }
    TRANSPORT_BOOKINGS {
        int id PK
        int patient_id FK
        varchar transport_type
        varchar pickup_location
        varchar destination
        date date
        time time
        enum status
        timestamp created_at
    }
    VISA_BOOKINGS {
        int id PK
        int patient_id FK
        varchar visa_type
        varchar country
        varchar passport_number
        date application_date
        enum status
        timestamp created_at
    }
    REVIEWS {
        int id PK
        int patient_id FK
        int doctor_id FK
        int rating
        text comment
        timestamp created_at
    }

    USERS ||--o| PATIENTS : "registers as"
    USERS ||--o| DOCTORS : "registers as"
    PATIENTS ||--o{ APPOINTMENTS : "books"
    PATIENTS ||--o{ BOOKINGS : "submits"
    PATIENTS ||--o{ HOTEL_BOOKINGS : "reserves"
    PATIENTS ||--o{ TRANSPORT_BOOKINGS : "arranges"
    PATIENTS ||--o{ VISA_BOOKINGS : "applies"
    PATIENTS ||--o{ REVIEWS : "writes"
    DOCTORS ||--o{ APPOINTMENTS : "attends"
    DOCTORS ||--o{ REVIEWS : "receives"
    HOSPITALS ||--o{ TREATMENTS : "offers"
    HOSPITALS ||--o{ APPOINTMENTS : "hosts"
    TREATMENTS ||--o{ BOOKINGS : "associated_with"
    BOOKINGS ||--o| PAYMENTS : "billed_by"
```

---

## 📂 Project Structure

Key files and folders in this repository:

```text
├── index.php                 # Application Main Landing Page
├── services.php              # Public services information page
├── help.php                  # Interactive Help, FAQs, and medical tourism guide
├── nav_bar.php               # Shared navigation layout component
├── mt_db.sql                 # Comprehensive Database schema & sample seed data
│
├── login_user.php            # Patient login portal
├── signup_user.php           # Patient registration portal
├── welcome.php               # Patient dashboard
│
├── login_doctor.php          # Doctor login portal
├── signup_doctor.php         # Doctor registration portal
├── welcome_doctor.php        # Doctor dashboard
├── view_appointments.php     # Doctor appointment tracker list
│
├── login_admin.php           # Admin login portal
├── signup_admin.php          # Admin registration portal
├── welcome_admin.php         # Admin dashboard control center
│
├── hotel.php                 # Hotel booking form
├── transport.php             # Transport booking form
├── hospital.php              # Hospital booking form
├── visa.php                  # Visa assistance form
│
├── submit_hotel.php          # Hotel form submission handler
├── submit_transport.php      # Transport form submission handler
├── submit_hospital.php       # Hospital/Appointment form handler
├── submit_visa.php           # Visa form submission handler
│
├── hotel_bookings.php        # Patient's hotel booking management
├── transport_bookings.php    # Patient's transport booking management
├── visa_bookings.php         # Patient's visa booking management
├── hospital_bookings.php     # Patient's hospital booking management
│
├── view_admin_hotel.php      # Admin view for hotel reservations
├── view_admin_transport.php  # Admin view for transport bookings
├── view_admin_hospital.php   # Admin view for hospital appointments
├── view_admin_visa.php       # Admin view for visa applications
│
├── update.php                # Booking details editor page
├── update_process.php        # Process updates from editor forms
├── delete.php                # Booking cancellation & deletion handler
├── logout_user.php           # Patient session termination
├── logout_doctor.php         # Doctor session termination
└── logout_admin.php          # Admin session termination
```

---

## 🚀 Installation & Setup Guide

To run MedTour Services locally, follow these steps:

### Prerequisites
Make sure you have a local web server environment installed:
- **Windows:** XAMPP or WAMP
- **macOS:** MAMP
- **Linux:** LAMP Stack (Apache, MySQL, PHP)

### Step 1: Clone or Place the Project
Clone this repository or download the source code and place it inside the document root of your local server:
- For XAMPP / LAMPP: `/opt/lampp/htdocs/medtour` or `C:\xampp\htdocs\medtour`

### Step 2: Set Up MySQL Database
1. Start **Apache** and **MySQL** services from your XAMPP/LAMPP control panel.
2. Open your web browser and navigate to **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Click on the **Databases** tab, enter `mt_db` in the Database name field, and click **Create**.
4. Select the newly created `mt_db` database, click on the **Import** tab.
5. Click **Choose File** and select the database file: `mt_db.sql` located inside the project root folder.
6. Click **Import** (or **Go**) at the bottom to build the schema tables and insert initial rows.

### Step 3: Run the Application
1. Open your web browser.
2. Navigate to: `http://localhost/medtour/index.php`
3. You should see the homepage running with statistics read dynamically from the database.

---

## 💡 Quick Test Guide

1. **Sign Up:** Go to the home page, select "Sign Up" or "Login" to register as a **Patient**, **Doctor**, or **Admin** (via the respective login page links).
2. **Bookings Flow:** Once logged in as a patient, select any service (e.g. transport) and fill in the booking details.
3. **Approval Flow:** Log in as an Administrator (`login_admin.php`) to see the newly submitted booking, update its status, or delete records.

---

## 📄 License

This project is licensed under the MIT License. Feel free to use and adapt this system for educational or real-world project scenarios.
