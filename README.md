# 🏥 WSU Employee Leave Management System
> **A Web-based platform for Wolaita Sodo University to streamline staff leave requests and approvals.**

---

## 🔗 Live Demo
You can explore the live version of the system here:
👉 **[WSU Leave System Live](http://ethio-online-market.infinityfreeapp.com/login.php)**

---

## 📌 Overview
This system is designed to digitize and simplify the manual leave process at **Wolaita Sodo University (WSU)**. It allows employees to request leave online, while administrators can track, approve, or reject requests efficiently.

## ✨ Key Features
* 🔐 **Secure Authentication:** Role-based login for Employees and Administrators.
* 📝 **Leave Application:** Employees can submit various leave types (Annual, Sick, Maternity, etc.).
* 📊 **Admin Dashboard:** Real-time management of pending requests.
* 🕒 **Leave History:** Users can track the status and history of their past requests.
* 📱 **Responsive Design:** Optimized for both Desktop and Mobile users.



## 🛠️ Technologies Used
| Component | Technology |
| :--- | :--- |
| **Backend** | PHP 8.x |
| **Database** | MySQL |
| **Frontend** | HTML5, CSS3 (Bootstrap/Custom), JavaScript |
| **Server** | XAMPP / InfinityFree |

---

## 🚀 Getting Started

### Prerequisites
* [XAMPP](https://www.apachefriends.org/index.html) installed for local development.
* Basic knowledge of PHP and MySQL.

### Local Installation Steps
1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/chacha-it-deve-2018/WSU-Leave-Systems.git](https://github.com/chacha-it-deve-2018/WSU-Leave-Systems.git)
    ```
2.  **Move to Web Directory:**
    Copy the project folder to your XAMPP `htdocs` directory.
3.  **Database Setup:**
    * Open **PHPMyAdmin**.
    * Create a new database named `wsu_leave_db`.
    * Import the provided `database.sql` file.
4.  **Configuration:**
    * Update `db_config.php` with your database credentials.
5.  **Run:**
    * Start Apache & MySQL and visit `http://localhost/WSU-Leave-Systems`.

---

## 📁 Project Structure
```text
├── assets/             # CSS, Images, and JS files
├── includes/           # Header, Footer, and DB connection
├── admin/              # Admin panel pages
├── user/               # Employee dashboard pages
├── database.sql        # Database schema for import
└── index.php           # Landing and Login page
------

👨‍💻 Developer
Chalachew Belay 🎓 Wolaita Sodo University (WSU)

🌐 GitHub: @chacha-it-deve-2018

💻 Project Link: WSU Leave Systems
