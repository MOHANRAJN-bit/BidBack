# 🧾 BidBack – Smart Lost & Found with Bidding System

BidBack is a **web-based Lost & Found management system** that allows users to report, find, and claim lost items.  
It includes **bidding and approval mechanisms** managed through an admin panel.  
The system is built using **PHP, MySQL, HTML, CSS, and Bootstrap** for a clean, responsive, and secure experience.

---

## 🚀 Features

### 👤 User Features
- **Login / Register** to access the system.
- **Report Found Items** with details like item name, category, description, place, date, time, and optional photo.
- **Search Lost Items** posted by others.
- **Participate in Bidding** for unclaimed found items.
- **View Reports** of submitted and claimed items.

### 👑 Admin Features
- **Admin Dashboard** with quick navigation.
- **Approve or Reject** newly reported found items.
- **Manage Users**, Bidding Approvals, and Reports.
- **View All Items** and update their status or remarks.
- **Monitor System Activity**.

---

## 🗂 Folder Structure

```
BidBack/
│
├── db.php                     # Database connection
├── login.php                  # User login page
├── register.php               # User registration page
├── logout.php                 # Logout script
│
├── dashboard.php              # User dashboard (Report/Find/Bidding/Reports)
├── report_find_item.php       # User report found item page
├── find_lost_item.php         # Search for lost items
├── bidding.php                # User bidding module
├── reports.php                # View user item reports
│
├── admin_dashboard.php        # Admin dashboard
├── item_approvals.php         # Admin approve/reject found items
├── items.php                  # Admin manage all items
├── bidding_approvals.php      # Admin manage bidding approvals
├── users.php                  # Admin manage user accounts
│
├── uploads/                   # Folder to store uploaded item images
├── logo.png                   # Default logo image
│
└── README.md                  # Project documentation
```

---

## 💾 Database Setup

Run the following SQL commands in **phpMyAdmin** or your SQL console.

### Database Name
```sql
CREATE DATABASE bidback;
USE bidback;
```

### Users Table
```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user'
);
```

### Found Items Table
```sql
CREATE TABLE IF NOT EXISTS found_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255),
    description TEXT,
    category VARCHAR(100),
    found_place VARCHAR(255),
    found_date DATE,
    found_time VARCHAR(20),
    photo VARCHAR(255) DEFAULT 'logo.png',
    username VARCHAR(50),
    approval_status ENUM('approved','pending','rejected') DEFAULT 'pending',
    approval_remark TEXT,
    status ENUM('waiting approval','waiting for claim','bidding','claimed') DEFAULT 'waiting approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## ⚙️ Configuration

1. Copy all project files into your **htdocs** or **www** folder.
2. Open `db.php` and update database credentials:
   ```php
   $conn = new mysqli("localhost", "root", "", "bidback");
   ```
3. Ensure the folder `/uploads` has write permission:
   ```bash
   chmod 755 uploads
   ```
4. Access the system via browser:
   ```
   http://localhost/BidBack/
   ```

---

## 📋 report_find_item.php

### Functionality
- Allows users to report a found item.
- Input fields:
  - Item Name  
  - Description  
  - Category *(Stationary, Wood, Iron, Steel, Cloth, Bag, Other)*  
  - Found Place  
  - Found Date & Time  
  - Optional Photo Upload (stored in `/uploads/`, default: `logo.png`)
- Creates table if not exists (`found_items`).
- Displays preview of uploaded photo.
- “Back” button returns to the User Dashboard.
- Default values:
  - `approval_status = pending`
  - `status = waiting approval`

---

## 🧭 admin_dashboard.php

### Functionality
The admin dashboard includes five main sections:

1. **Item Approvals** – Approve or reject reported found items.  
2. **Items** – View and manage all found/lost items.  
3. **Bidding Approvals** – Monitor and approve bidding activities.  
4. **Reports** – Generate and view reports on items and user activity.  
5. **Users** – Manage user roles and remove unauthorized users.

---

## 🧩 Technologies Used

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript  
- **Backend:** PHP 8+  
- **Database:** MySQL  
- **Server:** MAMP / XAMPP

---

## 🧑‍💻 Developer Notes

- Default **MAMP credentials:**  
  - Username: `root`  
  - Password: `root`  
- Default **uploads path:** `uploads/`  
- Default image for items without photo: `logo.png`

---

## 🪪 License

This project is open-source and free to use for learning and academic purposes.

---

### 💡 Author
**Developed by:** Mohanraj  
**Location:** Coimbatore, Tamil Nadu, India  
**Email:** —  
**Project Type:** Academic / Hackathon  
