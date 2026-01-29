# 💎 Diamond Store - Full-Stack E-Commerce Platform

[![Live Demo](https://img.shields.io/badge/demo-live-success)](https://diamond-store-web.onrender.com)
> **⚠️ Note:** The application is hosted on Render's free tier. The server may go to sleep after inactivity. Please **wait ~60 seconds** for the initial load if the site is slow.
[![Tech Stack](https://img.shields.io/badge/stack-PHP%20%7C%20MySQL%20%7C%20Docker-blue)](#tech-stack)
[![Deployment](https://img.shields.io/badge/deployment-Render%20%2B%20Railway-orange)](#deployment)

> A production-ready jewelry e-commerce platform with modern DevOps practices, payment integration, and cloud deployment.

## 🎯 Project Overview

Diamond Store is a comprehensive full-stack e-commerce application developed as a **University Course Project (3 months)**. It simulates a real-world luxury jewelry store, demonstrating end-to-end development capabilities from system design to production deployment using modern cloud infrastructure.

**[📹 Live Demo Video](https://drive.google.com/file/d/1UId2zdR7i_wm7qoQku8QGzttTL9Wgixb/view?usp=drive_link)**

---

## 🚀 Key Features

### Customer-Facing
- 🛍️ **Product Catalog** - Browse, search, and filter jewelry by category, price, and material
- 🛒 **Shopping Cart** - Complete cart management with real-time updates
- 💳 **Payment Integration** - Multiple payment methods (VNPay, MoMo, Cash on Delivery)
- 📧 **Email Notifications** - Automated order confirmation emails via PHPMailer

### Admin Dashboard
- 📊 **Order Management** - Track and update order status
- 📦 **Inventory Control** - CRUD operations for products and categories
- 👥 **Customer Management** - View customer data and order history
- 📈 **Analytics** - Sales reports and business insights

### 🔐 Test Credentials (For HR/Reviewers)

**Admin Dashboard:**
- **URL:** [diamond-store-web.onrender.com/admincp/login.php](https://diamond-store-web.onrender.com/admincp/login.php)
- **Username:** `admin`
- **Password:** `123`

**Customer Account:**
- **Email:** `demo@gmail.com`
- **Password:** `password123!`
- *(Or you can register a new account freely)*

---

## 🛠️ Tech Stack

### Backend
- **PHP 8.0** - Core application logic
- **MySQL 8.0** - Relational database with Railway cloud hosting
- **Apache 2.4** - Web server with mod_rewrite
- **Composer** - Dependency management (PHPMailer)

### Frontend
- **HTML5/CSS3** - Responsive UI design
- **JavaScript (Vanilla)** - Interactive features
- **Font Awesome** - Icon library

### DevOps & Deployment
- **Docker** - Containerization with multi-stage builds
- **Render.com** - Web application hosting (free tier)
- **Railway** - MySQL database hosting (free tier)
- **Git/GitHub** - Version control with CI/CD auto-deployment

### Payment APIs
- **VNPay** - Vietnamese payment gateway
- **MoMo** - Mobile wallet integration

---

## 📦 Deployment Architecture

```
┌─────────────────┐
│   GitHub Repo   │
│  (Source Code)  │
└────────┬────────┘
         │ Auto-deploy on push
         ▼
┌─────────────────┐      ┌──────────────────┐
│   Render.com    │◄────►│  Railway MySQL   │
│   (Docker App)  │      │  (Database Host) │
│   Port: 80      │      │  Port: 28967     │
└─────────────────┘      └──────────────────┘
         │
         ▼
┌─────────────────┐
│   End Users     │
│ (Public Access) │
└─────────────────┘
```

---

## 🔧 Local Development Setup

### Prerequisites
- Docker & Docker Compose
- Git

### Quick Start

```bash
# 1. Clone repository
git clone https://github.com/phu-boop/diamond-store-fullstack.git
cd diamond-store-fullstack

# 2. Start with Docker Compose
docker-compose up -d

# 3. Import database
docker exec -i diamond-store-db mysql -uroot -prootpassword web_mysqli < web_mysqli.sql

# 4. Access application
open http://localhost:8080
```

### Admin Access
- URL: `http://localhost:8080/admincp/login.php`
- Username: `admin`
- Password: `123`

---

## 🌐 Production Deployment

### Infrastructure Setup

**Database (Railway)**
```bash
# Connection URL format
mysql://root:password@host:28967/railway
```

**Web Application (Render)**
- Runtime: Docker
- Auto-deploy: Enabled on main branch
- Environment Variables: 6 configured (DB credentials, BASE_URL)

### Deployment Process

1. **Code Changes** → Push to GitHub
2. **Automatic Build** → Render detects commit and triggers Docker build
3. **Container Deploy** → New container replaces old one (zero-downtime)
4. **Health Check** → Render verifies HTTP port 80 is accessible

**Build Time:** ~2-3 minutes  
**Deployment:** Fully automated via Git hooks

---

## 💡 Technical Highlights

### Problem-Solving & DevOps Skills

1. **Containerization**
   - Custom Dockerfile with optimized layer caching
   - Multi-stage builds for dependency management
   - **Challenge:** Configuring Apache to serve properly behind Render's load balancer and handling variable port bindings.
   - **Solution:** Reconfigured `000-default.conf` to bind to `0.0.0.0` and optimized the Dockerfile with multi-stage builds to reduce image size and build time.

2. **Database Migration**
   - Migrated from localhost MySQL to cloud Railway
   - Dynamic port configuration (28967 instead of default 3306)
   - **Challenge:** Handling secure database connections across different environments (Dev vs Prod).
   - **Solution:** Implemented robust environment variable management for DB credentials and successfully migrated data from local MySQL to Railway's cloud instance.

3. **Dependency Management**
   - Composer integration for PHPMailer library
   - Proper autoloading instead of manual requires
   - `.gitignore` for vendor directory exclusion

4. **Network Configuration**
   - Fixed Apache VirtualHost binding issues
   - Custom `000-default.conf` for wildcard interfaces
   - `Listen 0.0.0.0:80` for external accessibility

---

## 📂 Project Structure

```
diamond-store-fullstack/
├── admincp/              # Admin panel
│   ├── config/          # Database configuration
│   └── modules/         # Admin features (products, orders, etc.)
├── pages/               # Customer-facing pages
│   ├── main/           # Main shopping flow
│   └── header.php      # Navigation
├── assets/             # Static resources (CSS, JS, images)
├── mail/               # Email functionality (PHPMailer)
├── Dockerfile          # Container configuration
├── docker-compose.yml  # Local development stack
├── render.yaml         # Render.com deployment config
├── composer.json       # PHP dependencies
└── web_mysqli.sql      # Database schema & seed data
```

---

## 🎓 Skills Demonstrated

### Backend Development
✅ PHP object-oriented programming  
✅ MySQL database design & optimization  
✅ RESTful API integration (VNPay, MoMo IPN handling)  
✅ Email automation (PHPMailer/SMTP)  
✅ Session management & authentication  

### DevOps & Infrastructure
✅ Docker containerization  
✅ Cloud deployment (Render + Railway)  
✅ CI/CD pipeline setup  
✅ Environment variable management  
✅ Web server configuration (Apache)  

### Software Engineering
✅ Git version control  
✅ Dependency management (Composer)  
✅ Configuration file management  
✅ Documentation writing  
✅ Problem-solving & debugging  

---

## 📊 Database Schema

- **tbl_sanpham** - Product catalog (130+ items)
- **tbl_danhmuc** - Product categories
- **tbl_giohang** - Shopping cart & orders
- **tbl_khachhang** - Customer accounts
- **tbl_vanchuyen** - Shipping information
- **tbl_khuyenmai** - Promotional campaigns
- **tbl_admin** - Admin users

**Total Records:** 200+ customers, 127+ orders, 130+ products

---

## 🔐 Security Features

- Password hashing (MD5 - *Note: Should upgrade to bcrypt for production*)
- SQL injection prevention via prepared statements
- Environment variable-based secrets (no hardcoded credentials)
- `.gitignore` for sensitive files

---

## 📈 Performance Optimizations

- Docker layer caching for faster builds
- Composer autoloader optimization (`--optimize-autoloader`)
- Apache `mod_rewrite` for clean URLs
- Database indexing on foreign keys

---

## 🚀 Future Enhancements

- [ ] Upgrade password hashing to bcrypt/Argon2
- [ ] Implement Redis for session storage
- [ ] Add unit tests (PHPUnit)
- [ ] Migrate to Laravel/Symfony framework
- [ ] Implement CI/CD testing pipeline
- [ ] Add monitoring (Sentry/New Relic)

---

## 👨‍💻 Developer

**Nguyễn Lê Anh Phú**

- 📧 Email: phudz25022005@gmail.com
- 🔗 GitHub: [@phu-boop](https://github.com/phu-boop)
- 💼 LinkedIn: [Nguyễn Lê Anh Phú](https://www.linkedin.com/in/nguyễn-lê-anh-phú-8392393a9)

---

## 📝 License

This project is created for educational and portfolio purposes.

---

## 🙏 Acknowledgments

- Payment integration: VNPay, MoMo APIs
- Icons: Font Awesome
- Hosting: Render.com (Web), Railway (Database)
- Email: PHPMailer library

---

**⭐ If you find this project interesting, please give it a star!**
