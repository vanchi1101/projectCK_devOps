# Project CK - DevOps

Ứng dụng thương mại điện tử và quản lý tài khoản được xây dựng bằng PHP thuần, MySQL và Bootstrap. Dự án hỗ trợ cửa hàng laptop, giỏ hàng, thanh toán, nạp tiền, quản lý đơn hàng, tài khoản người dùng, đăng nhập Google và quy trình CI/CD tới cPanel.

## Tính năng

- Cửa hàng laptop với danh mục, tìm kiếm, phân trang và chi tiết sản phẩm.
- Giỏ hàng, đặt hàng, theo dõi trạng thái đơn hàng và thanh toán.
- Nạp tiền, nạp thẻ, lịch sử giao dịch và các luồng tích hợp ngân hàng.
- Đăng ký, đăng nhập, quên mật khẩu, đổi mật khẩu và đăng nhập Google OAuth 2.0.
- Khu vực quản trị: người dùng, sản phẩm, danh mục, đơn hàng, cấu hình và ngân hàng.
- Chương trình giới thiệu/hoa hồng và rút hoa hồng.
- Gửi email xác nhận đơn hàng qua PHPMailer.
- Pipeline GitHub Actions kiểm tra PHP, chạy test, đóng gói và triển khai bằng SSH tới cPanel.

## Công nghệ

| Thành phần | Công nghệ |
| --- | --- |
| Backend | PHP 8.2+, MySQL/MariaDB, mysqli |
| Frontend | HTML, CSS, JavaScript, Bootstrap |
| Thư viện PHP | PHPMailer, Mobile Detect, Gregwar Captcha |
| Dependency | Composer |
| Web server | Apache + mod_rewrite (khuyến nghị XAMPP/cPanel) |
| CI/CD | GitHub Actions, SSH/SFTP, Bash |

Các thư viện nằm trong `vendor/` và được quản lý bởi `composer.json`.

## Cấu trúc thư mục

```text
api/                  API bank và callback
assets/               Hình ảnh, AJAX, model và tài nguyên tĩnh
class/                Các class hỗ trợ
config/               Cấu hình database, URL và hàm chung
cron/                 Tiến trình cron kiểm tra giao dịch
database/migrations/  Migration và dữ liệu mẫu
includes/             Nghiệp vụ dùng chung
public/admin/         Giao diện và xử lý quản trị
public/client/        Giao diện và xử lý người dùng
template/             CSS, JavaScript và template dùng chung
tests/                Kiểm thử ứng dụng
.github/workflows/    Pipeline CI/CD
```

## Yêu cầu

- PHP 8.2 trở lên với các extension: `curl`, `gd`, `mbstring`, `mysqli`
- MySQL hoặc MariaDB
- Composer
- Apache có bật `mod_rewrite`
- XAMPP trên Windows hoặc môi trường PHP/MySQL tương đương

## Cài đặt local bằng XAMPP

### 1. Cài mã nguồn

Đặt mã nguồn tại thư mục web root của XAMPP:

```text
D:\xampp\htdocs
```

Bật Apache và MySQL trong XAMPP Control Panel.

### 2. Tạo database

Tạo database `shopvip` bằng phpMyAdmin hoặc MySQL CLI, sau đó import:

```text
shopvipntn.sql
```

Database mẫu đã gồm các bảng thương mại điện tử và dữ liệu cấu hình nền.

### 3. Cấu hình ứng dụng

Cấu hình mặc định được đặt trong `config/config.php`:

- Database: `localhost`
- Port: `3306`
- Database name: `shopvip`
- User: `root`
- Password: rỗng
- App URL: `http://localhost/`

Có thể ghi đè bằng biến môi trường Apache:

| Biến | Mặc định |
| --- | --- |
| `APP_URL` | `http://localhost/` |
| `DB_HOST` | `localhost` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `shopvip` |
| `DB_USER` | `root` |
| `DB_PASSWORD` | rỗng |

Nếu máy chủ hỗ trợ Apache environment, có thể dùng `SetEnv` trong virtual host hoặc cấu hình phù hợp. Không commit mật khẩu database thật.

### 4. Cài dependencies

Trong thư mục dự án:

```powershell
composer install
```

Nếu Composer chưa có sẵn, cài Composer trước rồi mới chạy lệnh trên.

### 5. Truy cập ứng dụng

- Cửa hàng: `http://localhost/`
- Đăng nhập: `http://localhost/Auth/Login`
- Đăng ký: `http://localhost/Auth/Register`
- Quản trị: `http://localhost/Admin`

Trang quản trị yêu cầu tài khoản có quyền `admin`.

## Cấu hình cửa hàng laptop

Các danh mục laptop được tạo từ `20261126_seed_laptop_categories.sql`. Migration sản phẩm sẽ thêm laptop mẫu và dữ liệu thông số; tồn kho ban đầu có thể được đặt là giá trị hiển thị và cần kiểm tra trước khi nhận đơn thật.

Trong khu vực quản trị, mở **Quản lý cửa hàng** để:

- Tạo, sửa hoặc đổi trạng thái danh mục và sản phẩm.
- Cập nhật giá, mã SKU, tồn kho và thông số kỹ thuật.
- Sử dụng URL hình ảnh được phép.
- Duyệt hoặc từ chối sản phẩm.
- Quản lý đơn hàng và trạng thái thanh toán.

## Đăng nhập Google

Trước khi bật Google OAuth:

1. Chạy migration `20261126_add_google_login.sql`.
2. Tạo OAuth 2.0 Web Client trong Google Cloud Console.
3. Thêm Authorized redirect URI:

   ```text
   http://localhost/Auth/Google/Callback
   ```

4. Cung cấp `GOOGLE_CLIENT_ID` và `GOOGLE_CLIENT_SECRET` qua môi trường Apache.
5. Khởi động lại Apache.

`GOOGLE_REDIRECT_URI` có thể được đặt khi callback khác URL mặc định. Không commit hoặc chia sẻ client secret.

## Khôi phục mật khẩu

- Gửi qua SMTP đã cấu hình trong bảng `options`.
- Lưu dưới dạng hash.
- Hết hạn sau 10 phút.
- Yêu cầu tài khoản có email hợp lệ.

Tài khoản cũ chưa có email cần được quản trị viên cập nhật trong **Thành viên → Chỉnh sửa**.

## Test

Chạy bộ kiểm tra ứng dụng:

```powershell
php tests/run.php
```

Các test hiện tại kiểm tra trạng thái đơn hàng, escape output, CSRF và một số logic nghiệp vụ. Kiểm tra syntax toàn bộ mã PHP:

```powershell
Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch '\\vendor\\' } | ForEach-Object { php -l $_.FullName }
```

## CI/CD

Workflow `.github/workflows/deploy.yml` thực hiện 6 job theo thứ tự:

1. Checkout và tạo source artifact.
2. Validate syntax PHP 8.2 và `composer.json`.
3. Chạy `tests/run.php`.
4. Cài dependency production và tạo deployment package.
5. Triển khai bằng SSH tới document root cPanel.
6. Kiểm tra HTTP của website sau triển khai.

Push lên nhánh `main` hoặc chạy workflow thủ công bằng GitHub Actions.

### GitHub Secrets

Thêm các secret sau trong **Settings → Secrets and variables → Actions**:

| Secret | Mô tả |
| --- | --- |
| `CPANEL_SSH_HOST` | Hostname hoặc IPv4 của cPanel |
| `CPANEL_SSH_USER` | Tài khoản SSH cPanel |
| `CPANEL_SSH_PORT` | Port SSH, mặc định `22` |
| `CPANEL_SSH_PRIVATE_KEY` | Private key đã được cPanel authorize |
| `CPANEL_SSH_KEY_PASSPHRASE` | Passphrase của private key |
| `CPANEL_SSH_KNOWN_HOSTS` | Host key đã xác minh |
| `CPANEL_DEPLOY_PATH` | Đường dẫn tuyệt đối document root |
| `SITE_URL` | URL HTTPS công khai để health check |

Lấy host key bằng `ssh-keyscan`, xác minh fingerprint với nhà cung cấp hosting trước khi lưu. Không tắt SSH host-key checking.

## Bảo vệ dữ liệu triển khai

Deployment package không chứa dữ liệu hosting:

- `assets/storage/`
- `cron/LogBank.txt` và cron lock
- File SQL
- File error log
- File môi trường
- File runtime `.azcode-runtime.php`

`config/config.php` được đóng gói làm mã cấu hình ứng dụng. Khi deploy, thông tin database thực tế nên được đặt trong file `.azcode-runtime.php` ở thư mục cha của document root hoặc qua biến môi trường Apache, không đặt trực tiếp trong Git. Workflow không import SQL hoặc chạy migration production. Luôn sao lưu database và file trước khi triển khai hoặc áp dụng migration thủ công.

## Khắc phục sự cố

| Vấn đề | Cách kiểm tra |
| --- | --- |
| Không kết nối database | Kiểm tra MySQL, tên database, user, password và port |
| 404 hoặc route không hoạt động | Bật `mod_rewrite` và kiểm tra `.htaccess` |
| Không gửi được email | Kiểm tra SMTP trong bảng `options` |
| Google OAuth bị lỗi callback | Kiểm tra redirect URI, client ID và client secret |
| Deploy SSH thất bại | Kiểm tra host, port, private key, known hosts và quyền document root |
| Verify sau deploy thất bại | Kiểm tra `SITE_URL`, HTTPS và file application |

Sau khi rollback mã nguồn, chạy lại workflow đã kiểm tra thay vì khôi phục trực tiếp `config/config.php` hoặc dữ liệu database hosting.
