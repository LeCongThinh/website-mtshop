# <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Shopping%20Cart.png" alt="Shopping Cart" width="35" height="35" /> MTShop - Premium Tech E-commerce

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777bb4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-Data-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952b3?style=for-the-badge&logo=bootstrap&logoColor=white" />
  <img src="https://img.shields.io/badge/Payment-VNPay%20%7C%20SePay-red?style=for-the-badge" />
  <img src="https://img.shields.io/badge/Maintained%3F-yes-green.svg?style=for-the-badge" />
</p>

<p align="center">
  <strong>MTShop</strong> là hệ thống website bán lẻ Laptop và Linh kiện máy tính chuyên nghiệp. 
  <br /><i>Giải pháp mua sắm hiện đại với trải nghiệm mượt mà, thanh toán tức thì.</i>
</p>

<p align="center">
  <a href="#-tính-năng-cốt-lõi">Tính năng</a> •
  <a href="#-cấu-trúc-thư-mục">Kiến trúc</a> •
  <a href="#-hướng-dẫn-cài-đặt">Cài đặt</a> •
  <a href="#-tác-giả">Liên hệ</a>
</p>

---

## 🎨 Giao diện & Trải nghiệm (UI/UX)

Dự án được thiết kế theo phong cách **High-Contrast Tech**, tập trung vào sự tối giản và chuyên nghiệp dành cho người dùng yêu công nghệ.

### 🖌 Bảng màu chủ đạo (Color Palette)

| Màu sắc | Mã Hex | Vai trò |
| :--- | :--- | :--- |
| <img src="https://singlecolorimage.com/get/212529/50x20" /> | `#212529` | **Primary Dark** - Nền chủ đạo & Text chính |
| <img src="https://singlecolorimage.com/get/0d6efd/50x20" /> | `#0D6EFD` | **Action Blue** - Nút bấm, Link & Trạng thái |
| <img src="https://singlecolorimage.com/get/f8f9fa/50x20" /> | `#F8F9FA` | **Clean Gray** - Nền phụ & Phân cách nội dung |

### 📱 Khả năng tương thích
* **Responsive Design:** Tối ưu hóa hiển thị trên đa thiết bị nhờ hệ thống Grid của **Bootstrap 5**.
* **Tech-Style UI:** Sử dụng các đường nét sắc sảo, độ tương phản cao giúp làm nổi bật sản phẩm laptop/linh kiện.
* **UX Focus:** Quy trình mua hàng tối giản (Minimalist Checkout) giúp tăng tỷ lệ chuyển đổi đơn hàng.

---

## 💎 Tính năng cốt lõi

### 🛒 Đối với Khách hàng
> Cung cấp quy trình mua sắm khép kín từ tìm kiếm đến nhận hàng.

- [x] **Live Search:** Tìm kiếm sản phẩm thời gian thực bằng AJAX (không load lại trang).
- [x] **Smart Filter:** Phân loại linh kiện theo danh mục.
- [x] **Checkout:** Tích hợp cổng thanh toán trực tuyến **VNPay API** (Sandbox), **SePay API** .
- [x] **User Portal:** Quản lý lịch sử đơn hàng, cập nhật thông tin cá nhân và Avatar.

### ⚙️ Đối với Quản trị viên
> Hệ thống quản lý (Back-office) mạnh mẽ cho doanh nghiệp.

- [x] **Inventory Management:** Quản lý kho, giá bán, và trạng thái khuyến mãi.
- [x] **Order Processing:** Quy trình duyệt đơn tự động hóa 3 trạng thái.
- [x] **Analytics:** Báo cáo doanh thu và thống kê tăng trưởng người dùng.

---

## 🛠 Tech Stack (Công nghệ sử dụng)

<table align="center">
  <tr>
    <td align="center" width="96">
      <img src="https://skillicons.dev/icons?i=php" width="48" height="48" alt="PHP" />
      <br />PHP 8.x
    </td>
    <td align="center" width="96">
      <img src="https://skillicons.dev/icons?i=mysql" width="48" height="48" alt="MySQL" />
      <br />MySQL
    </td>
    <td align="center" width="96">
      <img src="https://skillicons.dev/icons?i=bootstrap" width="48" height="48" alt="Bootstrap" />
      <br />Bootstrap 5
    </td>
    <td align="center" width="96">
      <img src="https://skillicons.dev/icons?i=jquery" width="48" height="48" alt="jQuery" />
      <br />jQuery
    </td>
    <td align="center" width="96">
      <img src="https://skillicons.dev/icons?i=html" width="48" height="48" alt="HTML5" />
      <br />HTML5/CSS3
    </td>
  </tr>
</table>

---

## 📂 Cấu trúc thư mục (Architecture)
Chúng tôi áp dụng mô hình điều hướng tập trung để dễ dàng bảo trì và mở rộng:

```bash
website-mtshop/
├── 📁 admin/               # Trang quản lý hệ thống (CMS)
├── 📁 assets/              # Tài nguyên tĩnh (CSS, JS, Media)
├── 📁 functions/           # Core Logic (Controller-based)
│   ├── 📁 user/            # Xử lý luồng Customer
│   └── 📁 admin/           # Xử lý luồng Admin
├── 📁 includes/            # Config & System Helpers
├── 📁 users_area/          # Trang giao diện người dùng
└── 📄 index.php            # Entry Point chính của Website