Ứng dụng hỗ trợ quản lý media Googlopoly
link: (đang phát triển)

Công cụ và nền tảng sử dụng: MySQL, PHP Laravel, Railway, Render, Github

1. Giao diện đăng nhập
   ![image](https://github.com/user-attachments/assets/9cff1231-2aae-4100-bfc3-825072e24f57)
-Header
•	Logo “Googlopoly” nổi bật góc trái trên cùng, tạo độ nhận diện thương hiệu.
•	Các mục điều hướng bên góc phải, cung cấp thêm nhiều thông tin về trang web cho người dùng (tính năng đang phát triển).
-Main
•	Hình nền: tạo độ sâu và sinh động cho trang web.
•	Tiêu đề lớn: khái quát ngắn gọn nội dung trang web.
•	Tiêu đề nhỏ: bổ sung nghĩa cho ‘Tiêu đề lớn’ và kích thích người dùng.
•	‘Đăng nhập’ và ‘Đăng ký’: người dùng tạo tài khoản, điều hướng về trang chủ.

2. Đăng ký, đăng nhập
   ![image](https://github.com/user-attachments/assets/ba78acdd-98ea-4e74-9960-45721584b5d1)
   ![image](https://github.com/user-attachments/assets/e08d0b39-c4d7-47a1-8bce-52297134f3f2)
-Cấu trúc đăng nhập
•	Email: Người dùng nhập Email
•	Mật khẩu: Dữ liệu được mã hóa dưới dạng ký tự *
•	Nút ‘Đăng Nhập’: Thiết kế màu sắc chuyển từ #0A0607 (Xanh đen) -> #5A8B43 (Xanh nõn chuối) -> #B09D17 (Vàng nâu), đồng bộ màu sắc với ‘Hình nền’
-Khi đăng nhập nếu người dùng nhập sai mật khẩu hoặc email thì sẽ được thông báo là “Thông tin không chính xác”. Nếu không tìm thấy tài khoản thì hiển thị thông báo '‘không tồn tại tài khoản'’.

-Cấu trúc đăng kí
•	Tên người dùng: Nhập tên người dùng
•	Email: Người dùng nhập Email
•	Mật khẩu: Dữ liệu được mã hóa dưới dạng ký tự *
•	Nhập lại mật khẩu: Xác thực tránh người dùng sai sót
•	Nút ‘Đăng Ký’: Thiết kế màu sắc chuyển từ #0A0607 (Xanh đen) -> #5A8B43 (Xanh nõn chuối) -> #B09D17 (Vàng nâu), đồng bộ màu sắc với ‘Hình nền’
-Trong phần đăng ký tài khoản cũng hoạt động tương tự với đăng nhập. Khi người dùng nhập 1 username hoặc email đã được dùng thì sẽ nhận 1 thông báo '‘trùng username hoặc email'’ và khi nhập mật khẩu không khớp cũng sẽ nhận lại 1 thông báo là '‘mật khẩu không trùng khớp'’. Điều này giúp người dùng có trải nghiệm tốt hơn và không bị trùng tài khoản với người khác.

3. Giao diện trang chủ
   ![image](https://github.com/user-attachments/assets/218e57e6-f324-4e03-afb9-1d6de31d3736)
   Giao diện trang chủ được chia bố cục riêng biệt:
-Header
•	Logo: ‘Googlopoly Photos’
•	Search-bar: Thanh tìm kiếm, cho phép tìm kiếm ảnh theo tên (Tính năng đang được phát triển)
•	Nav-bar: gồm 3 icon khác nhau với chức năng riêng biệt
    o	Thêm ảnh, video: Ấn vào icon sẽ hiện ra popup để người dùng có thể đăng tải ảnh, video. Khi tải ảnh thành công/ thất bại, popup sẽ tự động đóng và thông báo tải ảnh thành công/ thất bại.
   ![image](https://github.com/user-attachments/assets/d10499f1-6052-4be9-9e95-4898c530c799)
    o	Cài đặt: Giao diện tối, khi ấn vào sẽ chuyển giao diện sang màu âm bản, giúp người dùng tránh hiện tượng mỏi mắt, phù hợp với người dùng vào ban đêm
    o	Tài khoản: Hiển thị thông tin tài khoản và nút đăng xuất
        	Đăng xuất: Sẽ thoát tài khoản hiện tại và đưa người dùng trực tiếp đến giao diện đăng nhập

-Main
Gồm 2 mục Left-side và Right-side
•	Left-side: là thanh Sidebar, gồm các mục lục để phân định media: Tất cả ảnh và video, Ảnh, Video và Yêu thích
    o	Tất cả ảnh và video: Hiển thị tất cả các media đã và sẽ được đăng tải
    o	Ảnh: Chỉ hiển thị tất cả các media có định dạng : JPG, PNG
    o	Video: Chỉ hiển thị tất cả các media có định dạng: GIF, MP4
    o	Yêu thích: Chỉ hiển thị những media được đánh dấu Yêu thích
•	Right-side: nằm bên phải, hiển thị những media thuộc sở hữu của các mục bên Left-side, bố cục sắp xếp rõ ràng hợp lý, hiển thị media dưới dạng xem trước (Media-review)
    o	Media-review: hiển thị thông tin của media đó, gồm nội dung và tên, trên góc có ô tích (Tickbox), nếu người dùng ấn vào media sẽ mở ra popup hiển thị chi tiết ảnh với        độ phân giải tốt nhất (Popup-Media).
        	Tickbox: khi click vào, ô sẽ hiện dấu tích màu xanh dương và Media-review sẽ vào trạng thái đang được tích. Đồng thời, dưới góc phải sẽ hiện ra 2 nút với chức năng         Yêu thích và Xóa.
        ![image](https://github.com/user-attachments/assets/e7a49c5c-fdb9-40aa-add1-778e5abc8a45)
            -	Yêu thích: Thêm tất cả các Media-review đang được tích xanh vào trong mục Yêu thích.
            -	Xóa: Xóa tất cả các Media-review đang được tích xanh.
        	Popup-Media: Hiển thị chi tiết ảnh cùng độ phân giải tốt nhất, bên dưới là 3 nút chức năng: Yêu thích, Xóa và Đóng popup






   

   
