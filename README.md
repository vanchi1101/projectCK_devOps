# Project CK - DevOps

Ứng dụng thương mại điện tử và quản lý tài khoản được xây dựng bằng PHP thuần, MySQL và Bootstrap. Dự án hỗ trợ cửa hàng laptop, giỏ hàng, thanh toán, nạp tiền, quản lý đơn hàng, tài khoản người dùng, đăng nhập Google và quy trình CI/CD tới cPanel.

## Tính năng

- Cửa hàng laptop với danh mục, tìm kiếm, phân trang và chi tiết sản phẩm.
- Giỏ hàng, đặt hàng, theo dõi trạng thái đơn hàng và thanh toán.
- Nạp tiền qua ngân hàng và theo dõi lịch sử giao dịch.
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


Các migration này có thể chạy với database `shopvip`. Migration seed danh mục/laptop được thiết kế để chạy lại an toàn.

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

## Thanh toán đơn hàng và tích hợp API ngân hàng

### Quy trình thanh toán hiện tại

Luồng thanh toán của hệ thống thương mại điện tử được triển khai theo các bước sau:

1. Khách hàng đăng nhập, thêm sản phẩm vào giỏ hàng và điều chỉnh số lượng.
2. Hệ thống kiểm tra sản phẩm còn được duyệt bán, số lượng có hợp lệ và không vượt quá tồn kho.
3. Khách hàng vào trang thanh toán, nhập họ tên, số điện thoại, email, địa chỉ nhận hàng và phương thức thanh toán.
4. Hệ thống hỗ trợ hai phương thức:
   - Chuyển khoản ngân hàng.
   - Thanh toán khi nhận hàng (COD).
5. Khi đặt hàng, hệ thống tạo một mã đơn duy nhất dạng `EC` cộng 24 ký tự hex, ví dụ `EC7A4C91D82B56F103E29C876`.
6. Hệ thống lưu đơn hàng, lưu các dòng sản phẩm và trừ tồn kho trong một database transaction.
7. Sau khi tạo đơn thành công, giỏ hàng được xóa và email xác nhận được gửi cho khách hàng cùng cửa hàng.

### Trạng thái đơn hàng

| Phương thức | Trạng thái đơn | Trạng thái thanh toán |
| --- | --- | --- |
| Chuyển khoản | `pending_payment` | `unpaid` |
| Chuyển khoản đã thanh toán | `paid` | `paid` |
| Cần đối soát | `payment_review` | `mismatch` |
| COD đang xử lý | `processing` | `unpaid` |
| COD đã hoàn tất | `completed` | `paid` |
| Hủy đơn chưa thanh toán | `cancelled` | `unpaid` |
| Hoàn tiền | `refunded` | `refunded` |

Đơn chuyển khoản chưa thanh toán trong 120 phút sẽ tự động bị hủy và tồn kho của các sản phẩm trong đơn được hoàn lại.

### Thanh toán chuyển khoản bằng mã QR

Trang thanh toán hiển thị:

- Tên ngân hàng.
- Số tài khoản.
- Chủ tài khoản.
- Số tiền chính xác.
- Nội dung chuyển khoản là mã đơn hàng.
- Mã QR VietQR được tạo từ dữ liệu tài khoản nhận tiền.

Trang Checkout tự động tải lại sau mỗi 60 giây để kiểm tra trạng thái đơn hàng.

### Quy trình đối soát tự động hiện tại

Cron ngân hàng đọc lịch sử giao dịch và chuyển dữ liệu sang lớp nghiệp vụ thương mại điện tử:

```text
Lịch sử giao dịch ngân hàng
        ↓
cron/bank.php
        ↓
ecom_process_bank_payment()
        ↓
Kiểm tra transaction ID
        ↓
Tách mã đơn EC...
        ↓
So sánh mã đơn và số tiền
        ↓
Cập nhật trạng thái đơn hàng
```

Hàm `ecom_process_bank_payment()` thực hiện các bước:

1. Tách mã đơn hàng có định dạng `EC[A-F0-9]{24}` từ nội dung giao dịch.
2. Kiểm tra mã giao dịch hợp lệ và chưa từng được xử lý.
3. Tìm đơn hàng bằng mã đơn trong database.
4. So sánh số tiền giao dịch với tổng thanh toán của đơn.
5. Kiểm tra đơn có đang ở trạng thái cho phép thanh toán không.
6. Ghi giao dịch vào bảng `ecom_bank_payments`.
7. Cập nhật đơn sang `paid / paid` nếu hợp lệ.
8. Tạo hoa hồng cho người giới thiệu hoặc cộng tác viên theo cấu hình.

Các giao dịch sai số tiền, sai đơn hàng hoặc không hợp lệ được ghi lại để đối soát thay vì cộng tiền tự động sai.

### Quy trình khi tích hợp API ngân hàng thật

Về lý thuyết và để triển khai với API ngân hàng/cổng thanh toán thật, luồng khuyến nghị là:

```text
Khách hàng
    ↓
Website PHP tạo đơn và trừ tồn kho
    ↓
Gọi API của ngân hàng/cổng thanh toán
    ↓
Nhận mã QR hoặc link thanh toán
    ↓
Khách thanh toán
    ↓
Ngân hàng/cổng thanh toán gọi Webhook
    ↓
Website xác thực chữ ký và dữ liệu
    ↓
Kiểm tra mã đơn, số tiền và transaction ID
    ↓
Cập nhật đơn hàng trong database transaction
```

Endpoint webhook dự kiến nhận dữ liệu gồm:

```json
{
  "transaction_id": "TXN202611270001",
  "order_no": "EC7A4C91D82B56F103E29C876",
  "amount": 500000,
  "status": "success",
  "signature": "chuoi_chu_ky_bao_mat"
}
```

Server khi nhận callback phải thực hiện:

1. Kiểm tra request đến từ phương thức POST.
2. Xác thực chữ ký hoặc callback secret do nhà cung cấp cấp.
3. Kiểm tra timestamp để tránh callback quá thời hạn.
4. Kiểm tra mã giao dịch đã tồn tại chưa.
5. Tìm đơn hàng theo mã đơn hoặc transaction ID.
6. So sánh số tiền thực tế với tổng thanh toán của đơn.
7. Kiểm tra đơn có đang chờ thanh toán hoặc đang đối soát không.
8. Cập nhật trạng thái trong database transaction.
9. Trả HTTP `200 OK` sau khi xử lý thành công.

Không nên tin dữ liệu callback nếu chưa xác thực chữ ký. Trạng thái thanh toán không được thay đổi dựa trên dữ liệu do trình duyệt gửi lên.

### Hai phương án đối soát

| Phương án | Ưu điểm | Nhược điểm |
| --- | --- | --- |
| Polling lịch sử giao dịch | Dễ demo, không bắt buộc endpoint công khai | Chậm, tốn request, phụ thuộc giới hạn API |
| Webhook | Gần thời gian thực, ít request | Cần HTTPS, endpoint công khai và cơ chế xác thực |

Kiến nghị triển khai thực tế: dùng **Webhook làm luồng chính**, kết hợp **Polling/Cron đối soát định kỳ** để phát hiện giao dịch bị mất callback.

### Bảo mật khi tích hợp API thật

- Sử dụng HTTPS cho callback.
- Không commit API Key, Access Token hoặc callback secret vào Git.
- Lưu credential trong biến môi trường hoặc cấu hình ngoài mã nguồn.
- Kiểm tra chữ ký callback và mã giao dịch.
- Sử dụng ràng buộc `UNIQUE` cho `transaction_id` để tránh xử lý trùng.
- Ghi log transaction để đối soát thủ công.
- Dùng cơ chế idempotency để callback gửi nhiều lần không cập nhật đơn hoặc tạo hoa hồng hai lần.
- Ghi từng thay đổi trạng thái để truy vết.
- Cần sandbox và tài khoản merchant từ ngân hàng/cổng thanh toán trước khi chạy tài khoản thật.

### Các trường hợp cần kiểm thử

| Trường hợp | Kết quả mong đợi |
| --- | --- |
| Đúng mã đơn, đúng số tiền | Đơn chuyển `paid / paid` |
| Sai số tiền | Đơn chuyển `payment_review / mismatch` |
| Giao dịch lặp lại | Giao dịch bị đánh dấu duplicate và không xử lý lại |
| Callback sai chữ ký | Từ chối request và ghi log |
| Đơn không tồn tại | Ghi nhận unknown order để đối soát |
| Đơn chưa thanh toán quá 120 phút | Hủy đơn và hoàn tồn kho |
| Thanh toán COD | Chỉ ghi nhận đã thanh toán khi quản trị xác nhận đơn hoàn tất |

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
