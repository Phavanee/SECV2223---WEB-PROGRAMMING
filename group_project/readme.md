# STUDEEWORK 💼 Job Hunt Portal

A web-based job application platform connecting students and employers with administrative moderation capabilities.

This documentation provides complete setup and usage instructions for running the project locally (using XAMPP) or deploying it online (using InfinityFree).

---

## 📦 Dependencies

Before running this project, ensure the following dependencies are installed:

### System Requirements
- PHP >= 8.0
- MySQL >= 5.7
- Web Server (e.g., Apache)

### Local Development Tools
- [XAMPP](https://www.apachefriends.org/) (includes Apache, PHP, and MySQL)
- Modern web browser (e.g., Chrome, Firefox)

---

## ⚙️ Local Setup (Using XAMPP)

### 1. Prerequisites
- Download and install [XAMPP](https://www.apachefriends.org/).
- Download the full project source code.

### 2. File Placement
- Move the project folder to the `htdocs` directory:
C:\xampp\htdocs\YourProjectFolder

### 3. Database Setup
- Start Apache and MySQL from the XAMPP Control Panel.
- Click **Admin** next to MySQL to open **phpMyAdmin**.
- Navigate to the **SQL** tab.
- Copy and paste the contents of `schema.sql` into the editor and click **Go**.
- Open `db.php` and configure database credentials (see [Database Configuration](#-database-configuration)).

### 4. Run the Application
- In your browser, visit:
http://localhost/YourProjectFolder

- If setup was successful, the login page should appear.

---

## 🌐 Web Deployment (Using InfinityFree)

### 1. Register for Hosting
- Create a free account at [InfinityFree](https://infinityfree.net/).
- Set up a domain or subdomain for your project.

### 2. Upload Project Files
- Log in to the control panel.
- Go to **File Manager** > `htdocs` directory.
- Upload all source code files into the `htdocs` folder.

### 3. Database Configuration
- In the control panel, create a new MySQL database named `jobhunt`.
- Note the following details:
- MySQL host
- Database name
- Username
- Password
- Open `db.php` and update the variables accordingly.
- Go to **phpMyAdmin**, select the database, and import the `schema.sql` file.

### 4. Access Your Site
- Visit your assigned domain, e.g.:
http://yourproject.infinityfreeapp.com/

- DNS propagation may take a few minutes to 24 hours.

---

## 🛠️ Database Configuration

To connect your project to a MySQL database:

### 1. Locate `db.php`
- Found in the project root. Open it with any text editor.

### 2. Update Database Credentials

#### For Localhost (XAMPP):
```php
$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'your_db_name';
```

#### For InfinityFree:

```php
$hostname = 'sqlXXX.epizy.com'; // Replace with actual host
$username = 'epiz_12345678';
$password = 'your_password';
$dbname   = 'epiz_12345678_jobhunt';
```

3. Test Connection

    Load the application in your browser.

    If successful, the system will connect to the database and function as expected.

🚀 Running the Application
🔐 All Users

    Register an Account
    Sign up with basic personal details.

    Sign In
    Login using registered credentials. A success message is shown.

    Edit Profile
    Modify name, email, contact number, skills, etc.

    Chatbot Assistance
    24/7 virtual assistant for help and job suggestions.

    Logout
    Clears session and redirects to login.

🎓 Students

    Filter Jobs by Job Title
    Use the search bar to find jobs by keyword.

    Sort Jobs
    Organize job listings by title, date, location, or tags.

    Apply for Jobs
    Applied jobs show “Applied” and disable the apply button.

    View Application History
    Displays all job applications and their statuses.

    Delete Job Applications
    Remove outdated or irrelevant applications.

    Rate Jobs
    Rate employers after job completion.

🏢 Employers

    Post Jobs
    Submit job listings with title, location, tags.

    View Jobs Posted
    See current and past job listings with application stats.

    Sort Jobs Posted
    Sort by date, title, or tags.

    Delete Jobs Posted
    Remove postings with confirmation message.

    View Applicants
    See student applicants and their details.

    Filter Applicants by Name
    Quick search to locate specific applicants.

    Sort Applicants
    Sort by name, date, or status.

    Update Applicant Status
    Mark progress (e.g., Applied → Approved → Completed).

    Rate Applicants
    Rate candidates based on performance.

🛡️ Admin Panel

    View Unverified Students/Employers
    Manage pending registrations.

    Verify Students/Employers
    Approve users via the “Verify” button.

    View All Students/Employers
    Access full user lists.

    Delete Students/Employers
    Remove users from the system after confirmation.

    View All Job Postings
    Monitor all jobs listed in the database.

    Delete Job Postings
    Remove inappropriate or outdated job listings.

📄 License

This project is intended for academic and educational use. You are free to modify or redistribute it for non-commercial purposes.
🤝 Contributing

Contributions are welcome! Please fork the repository and submit a pull request. For major changes, open an issue to propose your ideas first.
📫 Contact

For issues, suggestions, or questions, feel free to create a GitHub Issue or contact the project maintainer directly.
