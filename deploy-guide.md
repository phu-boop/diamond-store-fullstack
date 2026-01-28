# Hướng Dẫn Deploy Web Trang Sức lên Render.com

## 📋 Tổng quan

Hướng dẫn này sẽ giúp bạn deploy ứng dụng PHP Diamond Store lên Render.com kết hợp với Railway MySQL (miễn phí).

**Thời gian:** ~30-45 phút  
**Chi phí:** Free tier (cả Render và Railway)

---

## 🚀 Bước 1: Chuẩn bị Repository

### 1.1. Push code lên GitHub

```bash
cd /home/devphu/Documents/complete-project/diamond-store-fullstack

# Tạo .gitignore nếu chưa có
echo ".env" >> .gitignore
echo ".env.local" >> .gitignore

# Commit và push
git add .
git commit -m "Add Docker configuration for Render deployment"
git push origin main
```

> ⚠️ **Lưu ý:** Đảm bảo file `.env` không được push lên GitHub (đã có trong .gitignore)

---

## 💾 Bước 2: Setup Database trên Railway

### 2.1. Tạo account Railway
- Truy cập https://railway.app
- Sign up với GitHub account
- Verify email

### 2.2. Tạo MySQL Database
1. Click **"New Project"**
2. Chọn **"Provision MySQL"**
3. Đợi database khởi tạo (~1 phút)
4. Click vào **MySQL service** → Tab **"Variables"**
5. Copy các thông tin:
   - `MYSQL_HOST` (VD: `containers-us-west-xxx.railway.app`)
   - `MYSQL_USER` (thường là `root`)
   - `MYSQL_PASSWORD`
   - `MYSQL_DATABASE`

### 2.3. Import Database
1. Trong Railway MySQL service, click tab **"Data"**
2. Click **"Connect"** để mở MySQL client
3. Hoặc dùng MySQL Workbench/TablePlus:
   ```
   Host: [MYSQL_HOST từ bước trên]
   Port: [MYSQL_PORT từ Railway]
   User: root
   Password: [MYSQL_PASSWORD]
   Database: [MYSQL_DATABASE]
   ```
4. Import file `web_mysqli.sql`:
   ```bash
   mysql -h [MYSQL_HOST] -P [MYSQL_PORT] -u root -p[MYSQL_PASSWORD] [MYSQL_DATABASE] < web_mysqli.sql
   ```

> ✅ **Kiểm tra:** Vào Railway Data tab, bạn sẽ thấy tables: `tbl_admin`, `tbl_baiviet`, `tbl_danhmuc`, etc.

---

## 🌐 Bước 3: Deploy lên Render

### 3.1. Tạo account Render
- Truy cập https://render.com
- Sign up với GitHub account

### 3.2. Tạo Web Service
1. Click **"New +"** → **"Web Service"**
2. Chọn repository GitHub của bạn
3. Cấu hình như sau:

   **Basic Settings:**
   - Name: `diamond-store` (hoặc tên bạn muốn)
   - Region: `Singapore` (gần VN nhất)
   - Branch: `main`
   - Root Directory: (để trống)

   **Build Settings:**
   - Environment: `Docker`
   - Dockerfile Path: `./Dockerfile`

   **Plan:**
   - Chọn **"Free"**

### 3.3. Configure Environment Variables
Trong phần **Environment**, click **"Add Environment Variable"** và thêm:

```
DB_HOST=[MYSQL_HOST từ Railway]
DB_USER=root
DB_PASS=[MYSQL_PASSWORD từ Railway]
DB_NAME=[MYSQL_DATABASE từ Railway]
BASE_URL=https://diamond-store.onrender.com
```

(Thay `diamond-store` bằng tên service bạn đặt)

### 3.4. Deploy
- Click **"Create Web Service"**
- Đợi build (~5-10 phút)
- Xem logs để kiểm tra quá trình build

---

## ✅ Bước 4: Kiểm tra Deployment

### 4.1. Test Website
1. Truy cập URL Render của bạn: `https://[your-app-name].onrender.com`
2. Kiểm tra:
   - ✅ Trang chủ hiển thị đúng
   - ✅ Danh sách sản phẩm load được
   - ✅ Không có lỗi database connection

### 4.2. Test Admin Panel
1. Truy cập: `https://[your-app-name].onrender.com/admincp/login.php`
2. Login với:
   - Username: `admin`
   - Password: `123`
3. Kiểm tra admin features hoạt động

### 4.3. Test Payment Integration (Nếu cần)
> ⚠️ **Quan trọng:** Cần update callback URL tại MoMo/VNPay portal

**MoMo:**
- Vào MoMo Developer Portal
- Update IPN URL: `https://[your-app-name].onrender.com/pages/main/menu_cart/handle_momo.php`

**VNPay:**
- Vào VNPay Merchant Portal  
- Update Return URL tương tự

---

## 🐛 Xử lý sự cố thường gặp

### ❌ Lỗi: "Failed to connect to MySQL"
**Nguyên nhân:** Environment variables chưa đúng hoặc Railway database chưa allow external connections

**Giải pháp:**
1. Kiểm tra lại ENV vars trên Render
2. Verify Railway database đang running
3. Check Railway Settings → Allow external connections = ON

### ❌ Lỗi: "500 Internal Server Error"
**Nguyên nhân:** PHP errors hoặc missing extensions

**Giải pháp:**
1. Xem Render logs: Dashboard → Logs
2. Kiểm tra Dockerfile có đủ PHP extensions
3. Verify file permissions

### ❌ Website bị "sleep" sau 15 phút
**Nguyên nhân:** Render free tier auto-sleep

**Giải pháp:**
- Nâng cấp lên Render paid plan ($7/month)
- Hoặc dùng uptime monitoring service (UptimeRobot) để ping website mỗi 5 phút

---

## 🔄 Update Code sau khi Deploy

Sau khi deploy lần đầu, mỗi khi update code:

```bash
git add .
git commit -m "Update features"
git push origin main
```

Render sẽ **tự động rebuild và deploy** (auto-deploy).

---

## 📊 Monitoring & Maintenance

### Xem Logs
- **Render:** Dashboard → Service → Logs tab
- **Railway:** Dashboard → MySQL → Deployments

### Database Backup
Railway free tier không có auto backup. Nên:
```bash
# Backup manual hàng tuần
mysqldump -h [RAILWAY_HOST] -P [PORT] -u root -p[PASSWORD] [DATABASE] > backup_$(date +%Y%m%d).sql
```

---

## 💰 Chi phí dự kiến

| Service | Tier | Giới hạn | Chi phí |
|---------|------|----------|---------|
| Render Web | Free | 750 giờ/tháng, Sleep sau 15 phút | $0 |
| Railway MySQL | Free | 500MB storage, 5GB transfer | $0 |
| **Tổng** | | | **$0/tháng** |

> 💡 **Nâng cấp khi cần:**
> - Render Starter: $7/tháng (no sleep, custom domain)
> - Railway Pro: $5/tháng (more storage)

---

## 🎯 Checklist hoàn thành

- [ ] Code đã push lên GitHub
- [ ] Railway MySQL đã setup và import data
- [ ] Render Web Service đã deploy thành công
- [ ] Environment variables đã configure đúng
- [ ] Website truy cập được và hoạt động bình thường
- [ ] Admin panel login được
- [ ] Payment callback URLs đã update (nếu cần)

---

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Check Render logs
2. Check Railway database status
3. Verify environment variables
4. Test local với Docker trước: `docker-compose up`

**Happy Deploying! 🚀**
