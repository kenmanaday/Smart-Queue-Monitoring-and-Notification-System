SMART QUEUE MONITORING AND NOTIFICATION SYSTEM — PHP VERSION

FILES INCLUDED
- front.php: role selection (Student / Administrator)
- system.php and signupsystem.php: student login and registration
- adminlogin.php and adminsignup.php: administrator login and registration
- interface.php: student queue dashboard, connected to the database/API
- admin.php: administrator dashboard, connected to the database/API
- db.php: creates the MySQL database/tables automatically
- auth.php: registration, login, session and logout logic
- queue_api.php: live queue, 3 registrar windows, queue actions
- *.css: external CSS files used by the pages

SETUP (XAMPP)
1. Copy the entire smart_queue_php folder to C:\xampp\htdocs\
2. Ensure these image files are also in the same folder if your design uses them:
   02.png and csu.png
3. Open XAMPP Control Panel and start Apache and MySQL.
4. Visit http://localhost/smart_queue_php/front.php
5. Register a student using Student ID format 00-00000, then log in.
6. Create an administrator account from Administrator > Sign up, then log in.

DATABASE
db.php creates the smart_queue_db database and the required tables automatically on first use.
Default XAMPP credentials are MySQL user root with an empty password. Change DB_USER/DB_PASS in db.php if yours differ.

QUEUE BEHAVIOR
- Queue numbers increment independently of Student ID, from 1 through 10,000.
- Each student may have up to 2 active queue numbers.
- There are 3 registrar windows.
- Dashboards refresh every 3 seconds to show shared database state.
- Administrator can advance a window, open/close windows, and reset the queue.

IMPORTANT SECURITY NOTE
For a real deployment, administrator sign-up should not be public. Restrict admin account creation to a trusted owner or manually create admin users. This demo currently allows admin registration through the Administrator sign-up page.

Run with Apache/PHP and MySQL; do not double-click PHP files or open them as file:// URLs.
