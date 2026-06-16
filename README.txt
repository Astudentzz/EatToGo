=========================================
         EATTOGO – README
         Restaurant & Food Reservation System
         Final Year Project – UTM
=========================================

PROJECT DESCRIPTION
-------------------
EatToGo is a web-based restaurant reservation and food pre-ordering system.
It allows customers to search for restaurants, book tables, pre-order food,
and make online payments. Restaurant owners and staff have dedicated
dashboards to manage bookings, menu items, and verify payments.

The system supports multiple user roles:
  • Customer – browse restaurants, book tables, pre-order food, leave feedback
  • Staff – manage reservations, orders, menu items, verify payments
  • Owner – manage restaurants, staff, view reservations, handle listings
  • Admin – full system oversight (users, restaurants, applications)

TECHNOLOGY STACK
----------------
  • PHP (backend API)
  • MySQL / MariaDB (database)
  • HTML, CSS, Bootstrap 5 (frontend)
  • JavaScript (AJAX, dynamic UI)
  • PHPMailer (email notifications)
  • XAMPP (local development environment)

FOLDER STRUCTURE
----------------
  /EatToGo/
  ├── api/              – PHP backend endpoints
  │   ├── login.php
  │   ├── register.php
  │   ├── reservations.php
  │   ├── menu-items.php
  |   |──config/           – configuration files
  │   |  ├── database.php
  │   |  └── security.php
  │   └── ... (all API files)
  ├── assets/
  │   ├── css/          – stylesheets
  │   └── js/           – JavaScript files (api.js, app.js, mobile-nav.js)
  |
  ├── contact/          – SMTP configuration
  │   └── config.php
  ├── lib/              – external libraries
  │   └── PHPMailer/    – email library
  ├── uploads/          – uploaded files
  │   ├── certificates/
  │   ├── menu_items/
  │   ├── payment_proofs/
  │   ├── qrcodes/
  │   └── restaurants/
  └── *.html            – all frontend pages

INSTALLATION GUIDE (XAMPP)
--------------------------

1. Install XAMPP (Apache + MySQL + PHP)
   – Download from: https://www.apachefriends.org/
   – Install and start Apache & MySQL services.

2. Copy project files
   – Place the entire "EatToGo" folder into:
     C:\xampp\htdocs\EatToGo\

3. Create the database
   – Open phpMyAdmin (http://localhost/phpmyadmin)
   – Create a new database named: eattogo
   – Import the SQL file: if0_42158944_eattogo(4).sql

4. Configure database connection
   – Open: config/database.php
   – Change these lines:
       $host = 'localhost';
       $dbname = 'eattogo';
       $user = 'root';
       $pass = '';
   – Save the file.

5. Start the application
   – Open your browser and go to:
     http://localhost/EatToGo/index.html
   – You should see the homepage with restaurant listings.

DEFAULT TEST ACCOUNTS
---------------------
 ADMIN
eattogo.test@gmail.com
Admin123

Owners
groupokhciproject@gmail.com
Owner123

Owner
nazim06@graduate.utm.my
Eattogo

TianManStaff1
ngyueyang@graduate.utm.my
TianManStaff

MoonsCafeStaff1
chinming0210@gmail.com
MoonsCafeStaff1

GrumpyBear Staff
nazimaaabbb@gmail.com
John123

Gagahoho box Staff
theamazingemailz256@gmail.com
Staff12345

Customer
ngyueyang.316@gmail.com
customer123

Customer
tis.some.nonesense@gmail.com
Eatz123

Owner
hadifhazuan06@gmail.com
hadif123

Staff (Happy Dessert Cafe)
buddieshh@gmail.com
James123

Staff(Coffee House)
difqie06@gmail.com
Abu123



DEVELOPMENT NOTES
-----------------
  • All API endpoints are located in the /api/ folder.
  • The frontend uses fetch() with API_BASE from api.js.
  • Session and CSRF protection are handled by config/security.php.
  • File uploads are saved to the /uploads/ folder with unique filenames.
  • Email notifications use PHPMailer with Brevo SMTP.


SUPPORT & CONTACT
-----------------
  Project Team:
    • Navin Ramu
    • Muhammad Nazim
    • Hadif
    • Ng Yue Yang

  Email: eattogo.test@gmail.com
  Phone: +60 16-933 0771

  Universiti Teknologi Malaysia (UTM)
  Final Year Project – 2026

=========================================
              END OF README
=========================================