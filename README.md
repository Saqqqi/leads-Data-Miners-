# Leads Data Miners

## Overview
Leads Data Miners is a web application built with HTML, JavaScript, Tailwind CSS, PHP, and MySQL/MariaDB to manage owner data and employee lead records. Employees can add owner information (name, email, primary/secondary phone numbers, source link) to a SQL database, with validation to prevent duplicate phone numbers. Employees can view their daily and monthly lead counts without accessing individual lead details. Admins have full access to view all leads, filter by employee or date, export records to Excel (daily or monthly), and search employees by phone number or other fields.

## Features

### Employee Features
- **Add Owner Data**: Employees submit owner details (name, email, primary phone, secondary phone, source link). The system checks for duplicate phone numbers and displays an error if found: "Phone number already exists."
- **View Lead Counts**: Employees can see their total lead counts for each day and month, without access to individual lead details.
- **Access Restrictions**: Employees are limited to adding owner data and viewing their own lead counts.

### Admin Features
- **View All Leads**: Admins can view all leads across all employees, with filters for specific employees or daily/monthly periods.
- **Export Leads**: Admins can export lead records to Excel, filtered by day, month, or specific employee.
- **Search Employees**: Admins can search employees by phone number, name, or other database fields.
- **Full Access**: Admins have unrestricted access to manage all owner data and employee records.

## Technologies Used
- **Frontend**: HTML, JavaScript, Tailwind CSS
- **Backend**: PHP
- **Database**: MySQL/MariaDB (SQL)

## Database Structure
The application uses a MySQL/MariaDB database (`leads_data_miners`) with the following key tables:
- **owners**: Stores owner data (name, email, primary/secondary phone numbers, source link, creation timestamp). Phone numbers are unique to prevent duplicates.
- **employee_records**: Tracks employee lead records, linked to owners and users, with record type (daily/monthly) and data.
- **users**: Stores employee and admin information (ID, name, email, password, role, status, timestamps).

### Where to Add the Database
- Create the database in MySQL/MariaDB and import the SQL schema file (`database.sql`) located in the repository’s root directory (`C:\Users\Saqlain\Documents\leads-Data-Miners-\database.sql`).
- The database schema includes tables for `owners`, `employee_records`, and `users`, with appropriate foreign key constraints and collations.

## Setup Instructions

### Prerequisites
- **Git**: Installed locally (`git --version` to check).
- **PHP**: Version 8.2 or higher.
- **MySQL/MariaDB**: Version 10.4 or higher.
- **Web Server**: Apache/Nginx (e.g., via XAMPP, WAMP, or Docker).
- **Composer**: For PHP dependencies (optional).
- **Node.js**: For Tailwind CSS setup (if not using CDN).
- **Browser**: For accessing the frontend.

### Installation
1. **Clone the Repository**:
   Clone the repository to your local machine:
   ```bash
   git clone https://github.com/Saqqqi/leads-Data-Miners-.git
   cd leads-Data-Miners-
   ```

2. **Set Up Tailwind CSS**:
   - If using Tailwind CSS via CDN, no additional setup is needed (included in HTML files).
   - If using Tailwind CSS locally, install dependencies and build:
     ```bash
     npm install
     npx tailwindcss -i ./src/input.css -o ./public/css/output.css --watch
     ```
     Ensure `input.css` is configured with Tailwind directives and output to `public/css/output.css`.

3. **Set Up the Database**:
   - Create a MySQL/MariaDB database:
     ```sql
     CREATE DATABASE leads_data_miners;
     ```
   - Import the SQL schema from the repository’s root directory:
     ```sql
     SOURCE database.sql;
     ```
   - The `database.sql` file is located in the root of the `leads-Data-Miners-` repository (e.g., `C:\Users\Saqlain\Documents\leads-Data-Miners-\database.sql`).

4. **Configure the Application**:
   - Copy `config.example.php` to `config.php` in the repository’s root directory.
   - Update `config.php` with your database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'leads_data_miners');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

5. **Start the Web Server**:
   - Use XAMPP/WAMP or start a PHP development server:
     ```bash
     php -S localhost:8000 -t public
     ```
   - Access the application at `http://localhost:8000`.

## Usage
- **Employees**:
  - Log in with employee credentials.
  - Use the "Add Owner" form to submit owner data.
  - View daily and monthly lead counts on the dashboard.
- **Admins**:
  - Log in with admin credentials (role: `admin`).
  - Use the admin dashboard to:
    - View all leads, filtered by employee or date.
    - Export leads to Excel (daily/monthly).
    - Search employees by phone number, name, or email.
- **Search**:
  - Admins can use the search feature to find employees by phone number or other fields.

## Development Workflow
- **Branches**:
  - Use the `development` branch for main development.
  - Create feature branches for specific tasks:
    ```bash
    git checkout development
    git checkout -b feature/<feature-name>
    ```
    Example: `feature/add-excel-export`
- **Commits**:
  - Commit changes with clear, descriptive messages:
    ```bash
    git add .
    git commit -m "Add feature or fix description"
    ```
- **Push to GitHub**:
  - Push branches to the remote repository:
    ```bash
    git push --set-upstream origin feature/<feature-name>
    ```
- **Pull Requests**:
  - Create pull requests on GitHub to merge feature branches into `development`.
- **Testing**:
  - Test changes locally using a web server and MySQL/MariaDB before pushing.

## Troubleshooting
- **SQL Errors**:
  - If foreign key errors occur (e.g., for `employee_records`), verify table collations and constraints:
    ```sql
    SHOW CREATE TABLE users;
    SHOW CREATE TABLE owners;
    SHOW ENGINE INNODB STATUS;
    ```
  - Ensure `ENGINE=InnoDB` and `CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci` for all tables.
- **Git Issues**:
  - Ensure write access to `https://github.com/Saqqqi/leads-Data-Miners-`.
  - Verify authentication (HTTPS with Personal Access Token or SSH key):
    ```bash
    ssh -T git@github.com
    ```
- **Application Errors**:
  - Check `config.php` for correct database credentials.
  - Ensure Tailwind CSS is properly linked (via CDN or local build).
  - Test PHP scripts and JavaScript functionality locally.
