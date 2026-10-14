# Clinic Manage System

A professional clinic management system built with Laravel, designed to manage appointments, patients, staff, billing, and clinic operations.

## 🚀 Features

- **Appointment Management**: Schedule and manage patient appointments with doctors.
- **Patient Management**: Track patient records, medical history, and treatments.
- **Staff Management**: Manage clinic staff, roles, and access permissions.
- **Billing & Payments**: Generate invoices and manage payments for clinic services.
- **Reporting**:
    - Appointment and patient reports.
    - Revenue and billing summaries.
    - Staff activity reports.
- **User Management**: Advanced Role-Based Access Control (RBAC) with clinic-specific roles.
- **Exports**: One-click Export to Excel and PDF for all major records.
- **Responsive UI**: Fully optimized for Mobile, Tablet, and Desktop views.
- **API Support**: Integrated API for seamless connectivity with external devices or apps.

## 🛠️ Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Blade Templates, Bootstrap 5, jQuery, AJAX
- **Database**: MySQL
- **Libraries**:
    - DataTables (Interactive tables)
    - DomPDF (PDF generation)
    - PhpSpreadsheet (Excel generation)
    - Select2 (Advanced dropdowns)
    
## 📦 Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/rajsingh10/clinic-manage-sys.git
   cd clinic-manage-sys
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Environment Setup**:
   Copy `.env.example` to `.env` and configure your database credentials.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration & Seeding**:
   ```bash
   php artisan migrate --seed
   ```

   This will create the database tables and seed the default roles and admin user.

   > **Default Admin Credentials**
   > - **Email**: `admin@gmail.com`
   > - **Password**: `12345678`
   >
   > ⚠️ Change the password immediately after your first login in a production environment.

   **Available Roles**

   | Role | Description |
   |------|-------------|
   | `Admin` | Admin access to clinic operations. |
   | `Doctor` | Doctor access with assigned permissions. |
   | `Receptionist` | Receptionist access for front desk operations. |
   | `Patient` | Patient portal access. |

5. **Link Storage**:
   ```bash
   php artisan storage:link
   ```

6. **Run the application**:
   ```bash
   php artisan serve
   ```

## 📱 Mobile Responsiveness

The system features a dedicated mobile bottom navigation bar and card-based layouts for tables on smaller screens, ensuring that your business can be managed on the go.

## 📄 License

This project is proprietary software developed by **Fablead Developers Technolab**. All rights reserved.
