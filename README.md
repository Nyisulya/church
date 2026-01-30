# Church Management System

A complete, modern, and secure church management system built with Laravel 11, TailwindCSS, and Livewire.

## 🌟 Features

### Core Modules
- ✅ **Member Management** - Comprehensive member profiles, QR codes, departments
- ✅ **Attendance Tracking** - QR code-based attendance scanner (Livewire)
- ✅ **Events Management** - Event creation, scheduling, and attendance tracking
- ✅ **Financial Management** - Income, expenses, pledges, and giving tracking
- ✅ **Birthday & Anniversary Automation** - Automated celebration tracking
- ✅ **Pastoral Care** - Visits, follow-up tasks, and prayer requests
- ✅ **Departments/Ministries** - Department management with member assignments
- ✅ **Comprehensive Reports** - Analytics and insights across all modules

### Key Capabilities
- 📱 **QR Code System** - Unique codes for each member
- 💰 **Financial Tracking** - Complete income/expense management
- 📊 **Reports & Analytics** - Detailed insights and statistics
- 🎂 **Automated Celebrations** - Birthday and anniversary reminders
- 🙏 **Pastoral Care Tracking** - Visit logs, tasks, and prayers
- 👥 **Role-Based Access** - Super Admin, Admin, Pastor, Treasurer, Leaders, Members
- 📈 **Visual Reports** - Charts and graphs with progress indicators

## 🛠️ Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Blade Templates, TailwindCSS
- **Interactive Components:** Livewire 3
- **Database:** MySQL / SQLite
- **Authentication:** Laravel Breeze
- **Authorization:** Spatie Laravel-Permission
- **QR Codes:** SimpleSoftwareIO/simple-qrcode

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0+ or SQLite
- Node.js 18+ (for assets)
- Web server (Apache/Nginx)

## 🚀 Quick Start

### Installation

```bash
# Clone repository
git clone https://github.com/your-church/cms.git church
cd church

# Install dependencies
composer install
npm install && npm run build

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_DATABASE=church_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed roles
php artisan db:seed --class=RoleSeeder

# Create admin user
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@church.com', 'password' => bcrypt('password')]);
>>> exit

# Start development server
php artisan serve
```

Visit `http://localhost:8000` and login with admin credentials.

## 📚 Documentation

- **[User Guide](USER_GUIDE.md)** - Complete guide for end users
- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production deployment instructions
- **[Walkthrough](walkthrough.md)** - Development walkthrough

## 🗂️ Project Structure

```
church/
├── app/
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Eloquent models
│   ├── Policies/              # Authorization policies
│   └── Livewire/              # Livewire components
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   └── views/                 # Blade templates
├── routes/
│   └── web.php                # Web routes
└── public/                    # Public assets
```

## 🔐 User Roles

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full system access |
| **Admin** | Administrative functions, cannot delete system data |
| **Pastor** | Member care, events, pastoral functions |
| **Treasurer** | Financial management and reports |
| **Department Leader** | Department-specific access |
| **Member** | View personal information |

## 📊 Module Overview

### 1. Members & Authentication
- Member registration and profiles
- QR code generation per member
- Department assignments
- Role-based access control

### 2. Attendance Scanner
- Real-time QR code scanning (Livewire)
- Event selection
- Automatic attendance logging
- Duplicate prevention

### 3. Events Management
- Create and schedule events
- Track event attendance
- Event types: Service, Meeting, Event
- Attendance statistics per event

### 4. Financial Management
- Income tracking (Tithes, Offerings, Donations)
- Expense management (Utilities, Salaries, etc.)
- Pledge creation and tracking
- Payment recording
- Financial dashboards and reports

### 5. Celebrations
- Birthday detection and reminders
- Wedding anniversary tracking
- Salvation/Baptism anniversaries
- Upcoming celebrations dashboard

### 6. Pastoral Care
- Visit logging (Home, Hospital, Office, Phone)
- Follow-up task management
- Prayer request tracking
- Member care history

### 7. Reports & Analytics
- Member reports (growth, demographics)
- Financial reports (income vs expense)
- Attendance analytics
- Pastoral care summaries
- Visual charts and indicators

## 🔒 Security Features

- ✅ Role-based access control
- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade templating)
- ✅ Password hashing (Bcrypt)
- ✅ Audit logging (Activity Log)
- ✅ Secure authentication (Laravel Breeze)

## 🎯 Roadmap

### Planned Features
- 📧 Communication System (SMS & Email)
- 📁 Document Management
- 📱 Mobile App (API ready)
- 📄 PDF/Excel Export
- 📊 Advanced Charts (Chart.js)
- 🔔 Notifications System
- 🌐 Multi-language Support

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is proprietary software for church use.

## 👏 Acknowledgments

- Laravel Framework
- TailwindCSS
- Livewire
- Spatie Packages

## 📞 Support

For support and questions, contact your system administrator.

---

**Version:** 1.0  
**Built with ❤️ for Church Ministry**
