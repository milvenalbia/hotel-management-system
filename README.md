# Hotel Management System

A comprehensive web application designed to provide hospitality management students with a realistic simulation of hotel operations. This system offers hands-on experience with modern hotel management processes, from reservations to checkout and reporting.

![Hotel Management System Banner](https://i.imgur.com/MPNUafi.jpeg)
<p align="center">
  <img src="https://i.imgur.com/ohtqh81.jpeg" width="48%" />
    &nbsp;
    &nbsp;
  <img src="https://i.imgur.com/7ymwpT4.jpeg" width="48%" />
</p>

## 🏨 Overview

The Hotel Management System is an educational tool that mimics real-world hotel operations, allowing students to practice and learn essential hospitality management skills in a controlled environment. Each admin account represents an individual hotel, providing students with their own management space.

## 🛠️ Tech Stack

- **Backend:** Laravel (PHP Framework)
- **Frontend:** Livewire (Real-time UI components)
- **Styling:** Bootstrap (Responsive CSS Framework)
- **Database:** MySQL
- **Email:** Laravel Mail (for reservation confirmations)

## ✨ Features

### Core Functionality
- **📊 Dashboard** - Comprehensive overview of hotel operations with key metrics
- **📅 Reservation System** - Complete booking management with automated email confirmations
- **👥 Guest Management** - Customer information and history tracking
- **🏠 Room Management** - Room inventory, status, and maintenance tracking
- **👨‍💼 User Management** - Staff and account administration (Super Admin only)
- **💳 Checkout/Payment Management** - Billing and payment processing
- **📈 Generate Reports** - Detailed analytics and operational reports

### User Roles

#### Super Admin (Teacher Role)
- Full system access
- Manage all hotels and users
- Access to all features and reports
- User management capabilities
- System-wide oversight

#### Admin (Student Role)
- Individual hotel management
- Access to all features except user management
- Own hotel operations and data
- Learning-focused environment

## 📋 Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL >= 5.7
- Apache/Nginx web server

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/hotel-management-system.git
cd hotel-management-system
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
Configure your database settings in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database:
```bash
# Create database (MySQL)
mysql -u root -p
CREATE DATABASE hotel_management;
exit
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Configure Email Settings
Update your `.env` file with email configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 8. Build Frontend Assets
```bash
npm run build
```

### 9. Storage Link
```bash
php artisan storage:link
```

### 10. Start the Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Getting Started
1. Login with your assigned role
2. Explore the dashboard for system overview
3. Set up rooms and room types
4. Configure hotel settings
5. Start managing reservations and guests

## 🎯 Educational Objectives

This system helps students learn:
- Hotel reservation systems
- Guest relationship management
- Room inventory management
- Financial reporting and analytics
- Customer service workflows
- Multi-user system administration

## 🛡️ Security Features

- Role-based access control
- Secure authentication
- Data encryption
- SQL injection protection
- XSS prevention

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the GitHub repository
- Contact the development team

## 📞 Contact

Project Link: [https://github.com/milvenalbia/hotel-management-system](https://github.com/milvenalbia/hotel-management-system)

---

**Note:** This is an educational project designed for learning purposes. Always follow your institution's guidelines and policies when using this system.
