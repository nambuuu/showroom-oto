# 🚗 Website Showroom Ô Tô

> Ứng dụng web full-stack để trưng bày các mẫu xe, so sánh thông số kỹ thuật, đặt lịch lái thử và quản lý hệ thống.

---

## 📋 Mục Lục

- [Tổng Quan](#tổng-quan)
- [Công Nghệ Sử Dụng](#công-nghệ-sử-dụng)
- [Tính Năng](#tính-năng)
- [Cấu Trúc Dự Án](#cấu-trúc-dự-án)
- [Cơ Sở Dữ Liệu](#cơ-sở-dữ-liệu)
- [Hướng Dẫn Cài Đặt](#hướng-dẫn-cài-đặt)
- [Cấu Hình](#cấu-hình)
- [Phân Công Nhóm](#phân-công-nhóm)

---

## 📌 Tổng Quan

**Website Showroom Ô Tô** là một ứng dụng web động, full-stack cho phép khách hàng duyệt các mẫu xe, so sánh thông số, gửi yêu cầu tư vấn và đặt lịch lái thử. Quản trị viên có thể quản lý danh sách xe, xử lý lịch đặt và xem các yêu cầu liên hệ thông qua bảng điều khiển backend bảo mật.

---

## 🛠️ Công Nghệ Sử Dụng

| Tầng          | Công Nghệ               |
|---------------|-------------------------|
| Giao diện     | HTML5, CSS3, JavaScript |
| Máy chủ       | PHP 8.x                 |
| Cơ sở dữ liệu | MySQL 8.x               |
| Web Server    | Apache (XAMPP/LAMP)     |
| Thư viện      | jQuery, Bootstrap 5     |

---

## ✨ Tính Năng

### 👤 Dành Cho Khách Hàng
- Duyệt tất cả mẫu xe với thông số chi tiết và thư viện ảnh
- So sánh tối đa 3 xe cùng lúc
- Tìm kiếm và lọc theo hãng xe, loại xe, khoảng giá
- Gửi biểu mẫu liên hệ / yêu cầu tư vấn
- Đặt lịch lái thử với ngày và giờ mong muốn

### 🔐 Dành Cho Quản Trị Viên
- Hệ thống đăng nhập / đăng xuất bảo mật
- Bảng điều khiển thống kê tổng quan
- Thêm, sửa, xóa các mẫu xe và ảnh
- Quản lý lịch lái thử (duyệt, từ chối, hoàn thành)
- Xem và trả lời yêu cầu liên hệ
- Quản lý tài khoản quản trị viên

---

## 📁 Cấu Trúc Dự Án

```
car-showroom/
├── config/
│   ├── db.php              # Kết nối database (PDO)
│   └── auth_guard.php      # Kiểm tra session & phân quyền
│
├── admin/                  # 🔐 Chỉ dành cho Admin
│   ├── dashboard.php       # Bảng điều khiển – thống kê tổng quan
│   ├── cars.php            # Quản lý danh sách xe (CRUD)
│   ├── cars_add.php        # Thêm mẫu xe mới + tải ảnh
│   ├── cars_edit.php       # Chỉnh sửa thông tin xe
│   ├── brands.php          # Quản lý hãng xe
│   ├── bookings.php        # Danh sách lịch lái thử
│   ├── booking_detail.php  # Chi tiết & duyệt lịch lái thử
│   ├── contacts.php        # Danh sách yêu cầu liên hệ
│   ├── contact_view.php    # Xem & trả lời yêu cầu liên hệ
│   └── users.php           # Quản lý tài khoản admin
│
├── customer/               # 👤 Dành cho Khách Hàng
│   ├── index.php           # Trang chủ – xe nổi bật, banner
│   ├── cars.php            # Danh sách xe (lọc, tìm kiếm)
│   ├── car_detail.php      # Chi tiết xe + thư viện ảnh
│   ├── compare.php         # So sánh tối đa 3 xe
│   ├── booking.php         # Biểu mẫu đặt lịch lái thử
│   ├── contact.php         # Biểu mẫu liên hệ & tư vấn
│   ├── about.php           # Giới thiệu về showroom
│   └── search.php          # Trang kết quả tìm kiếm
│
├── api/                    # 🔌 AJAX Endpoints (trả về JSON)
│   ├── get_cars.php        # Lấy danh sách xe (lọc/tìm kiếm)
│   ├── get_car.php         # Lấy dữ liệu một xe (dùng cho so sánh)
│   ├── post_booking.php    # Nhận & lưu yêu cầu đặt lịch
│   ├── post_contact.php    # Nhận & lưu yêu cầu liên hệ
│   └── get_brands.php      # Lấy danh sách hãng cho dropdown
│
├── assets/
│   ├── css/style.css       # Stylesheet chính + responsive
│   ├── js/main.js            # JavaScript toàn cục
│   └── images/
│       ├── cars/           # Ảnh các mẫu xe (upload từ admin)
│       └── brands/         # Logo các hãng xe
│
├── database/
│   ├── schema.sql          # Cấu trúc CSDL (CREATE TABLE)
│   └── seed.sql            # Dữ liệu mẫu / demo
│
├── login.php               # Trang đăng nhập admin
├── logout.php              # Đăng xuất & hủy session
└── README.md               # Tài liệu dự án
```

---

## 🗄️ Cơ Sở Dữ Liệu

### Tổng Quan Các Bảng

```
car_showroom_db
├── brands              # Hãng xe / nhà sản xuất
├── cars                # Danh mục mẫu xe
├── car_images          # Nhiều ảnh cho mỗi xe
├── car_specifications  # Thông số kỹ thuật chi tiết
├── bookings            # Yêu cầu đặt lịch lái thử
├── contacts            # Yêu cầu từ biểu mẫu liên hệ
└── admin_users         # Tài khoản quản trị
```

### Chi Tiết Từng Bảng

#### `brands` – Hãng Xe
| Cột     | Kiểu Dữ Liệu | Mô Tả                     |
|---------|--------------|---------------------------|
| id      | INT PK       | Khóa chính tự tăng        |
| name    | VARCHAR(100) | Tên hãng xe (Toyota, ...) |
| logo    | VARCHAR(255) | Đường dẫn logo hãng       |
| country | VARCHAR(100) | Quốc gia xuất xứ          |

#### `cars` – Danh Sách Xe
| Cột           | Kiểu Dữ Liệu  | Mô Tả                             |
|---------------|---------------|-----------------------------------|
| id            | INT PK        | Khóa chính tự tăng                |
| brand_id      | INT FK        | Liên kết tới `brands.id`          |
| model_name    | VARCHAR(100)  | Tên mẫu xe                        |
| year          | YEAR          | Năm sản xuất                      |
| price         | DECIMAL(12,2) | Giá niêm yết (VNĐ)                |
| category      | ENUM          | sedan, suv, hatchback, truck, ... |
| color_options | TEXT          | Mảng JSON các màu có sẵn          |
| description   | TEXT          | Mô tả đầy đủ mẫu xe               |
| status        | ENUM          | available, sold_out, coming_soon  |
| is_featured   | TINYINT       | Hiển thị trang chủ (0/1)          |
| created_at    | TIMESTAMP     | Thời gian tạo bản ghi             |

#### `car_images` – Ảnh Xe
| Cột     | Kiểu Dữ Liệu | Mô Tả                    |
|---------|--------------|--------------------------|
| id      | INT PK       | Khóa chính tự tăng       |
| car_id  | INT FK       | Liên kết tới `cars.id`   |
| image   | VARCHAR(255) | Đường dẫn file ảnh       |
| is_main | TINYINT      | Ảnh đại diện chính (0/1) |

#### `car_specifications` – Thông Số Kỹ Thuật
| Cột             | Kiểu Dữ Liệu | Mô Tả                      |
|-----------------|--------------|----------------------------|
| id              | INT PK       | Khóa chính tự tăng         |
| car_id          | INT FK       | Liên kết tới `cars.id`     |
| engine          | VARCHAR(100) | Loại / dung tích động cơ   |
| horsepower      | INT          | Công suất (HP)             |
| torque          | VARCHAR(50)  | Mô-men xoắn (Nm)           |
| transmission    | VARCHAR(50)  | Tự động / Số sàn / CVT     |
| fuel_type       | VARCHAR(50)  | Xăng / Diesel / Điện       |
| fuel_efficiency | VARCHAR(50)  | Mức tiêu hao nhiên liệu    |
| seating         | INT          | Số chỗ ngồi                |
| drive_type      | VARCHAR(50)  | Dẫn động FWD/RWD/AWD/4WD  |
| top_speed       | INT          | Tốc độ tối đa (km/h)       |
| acceleration    | DECIMAL(4,1) | Tăng tốc 0-100 km/h (giây) |

#### `bookings` – Lịch Lái Thử
| Cột            | Kiểu Dữ Liệu | Mô Tả                             |
|----------------|--------------|-----------------------------------|
| id             | INT PK       | Khóa chính tự tăng                |
| car_id         | INT FK       | Liên kết tới `cars.id`            |
| full_name      | VARCHAR(100) | Họ tên đầy đủ khách hàng          |
| email          | VARCHAR(100) | Email khách hàng                  |
| phone          | VARCHAR(20)  | Số điện thoại khách hàng          |
| preferred_date | DATE         | Ngày lái thử mong muốn            |
| preferred_time | TIME         | Giờ lái thử mong muốn             |
| message        | TEXT         | Ghi chú thêm từ khách hàng        |
| status         | ENUM         | pending, approved, rejected, done |
| created_at     | TIMESTAMP    | Thời gian gửi yêu cầu             |

#### `contacts` – Liên Hệ
| Cột        | Kiểu Dữ Liệu | Mô Tả                    |
|------------|--------------|--------------------------|
| id         | INT PK       | Khóa chính tự tăng       |
| full_name  | VARCHAR(100) | Họ tên người gửi         |
| email      | VARCHAR(100) | Email người gửi          |
| phone      | VARCHAR(20)  | Số điện thoại (tùy chọn) |
| subject    | VARCHAR(200) | Chủ đề yêu cầu           |
| message    | TEXT         | Nội dung yêu cầu         |
| is_read    | TINYINT      | Trạng thái đã đọc (0/1)  |
| created_at | TIMESTAMP    | Thời gian gửi            |

#### `admin_users` – Tài Khoản Quản Trị
| Cột        | Kiểu Dữ Liệu | Mô Tả                       |
|------------|--------------|-----------------------------|
| id         | INT PK       | Khóa chính tự tăng          |
| username   | VARCHAR(50)  | Tên đăng nhập               |
| password   | VARCHAR(255) | Mật khẩu đã mã hóa (Bcrypt) |
| full_name  | VARCHAR(100) | Tên hiển thị                |
| email      | VARCHAR(100) | Email quản trị viên         |
| role       | ENUM         | superadmin, customer          |
| last_login | TIMESTAMP    | Lần đăng nhập gần nhất      |
| created_at | TIMESTAMP    | Thời gian tạo tài khoản     |

---

## ⚙️ Hướng Dẫn Cài Đặt

### Yêu Cầu Hệ Thống
- XAMPP / LAMP / WAMP
- PHP >= 8.0
- MySQL >= 8.0
- Trình duyệt web (Chrome / Firefox khuyến nghị)

### Các Bước Thực Hiện
- Tải git về máy, kết nối đến repo
 sau đó bật terminal trong vscode bằng cách gõ(ctrl + ~)
 sau đó gõ lệnh sau:
    git config --global user.name "gõ tên của bạn"
    git config --global user.email "gõ email của bạn" 
sau đó clone repo bằng cách gõ câu lệnh sau:
    git clone https://github.com/nambuuu/showroom-oto.git
sau khi clone về máy xong mọi người sẽ code các file được giao nhiệm vụ sau đó commit và push lên github

## 🔧 Cấu Hình

Chỉnh sửa file **`config/db.php`**:

```php
<?php
define('SITE_NAME', 'AutoElite Showroom');
define('BASE_URL',  'http://localhost/car-showroom/');

define('DB_HOST', 'localhost');
define('DB_NAME', 'car_showroom_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('UPLOAD_PATH', 'assets/images/cars/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
?>
```

### Tài Khoản Admin Mặc Định *(đổi ngay sau khi cài đặt)*
```
Tên đăng nhập : admin
Mật khẩu      : Admin@123
```

---

## 👥 Phân Công Nhóm

| Thành Viên   | Vai Trò & Nhiệm Vụ                                          |
|--------------|-------------------------------------------------------------|
| Thành viên 1 | Giao diện – Trang chủ, Danh sách xe, Chi tiết xe            |
| Thành viên 2 | Giao diện – So sánh xe, Liên hệ, Đặt lịch lái thử          |
| Thành viên 3 | Backend – Quản lý xe (CRUD), Tải ảnh lên                    |
| Thành viên 4 | Backend – Xử lý lịch đặt & liên hệ, Bảng điều khiển admin  |
| Thành viên 5 | Thiết kế CSDL, API Endpoints, Triển khai                    |

---

## 📄 Giấy Phép

Dự án được phát triển phục vụ mục đích học tập trong khuôn khổ môn học Lập Trình Web.

---

> 💡 **Lưu ý:** Luôn đổi mật khẩu admin mặc định trước khi triển khai. Không đưa `config/db.php` chứa thông tin thật lên kho lưu trữ công khai.
