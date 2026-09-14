<?php
include __DIR__ . '/../config/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $phone = $_POST["phone"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';
    $error = '';

    $email = trim($email);
    $phone = trim($phone);

    // 1. Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp.";
    }
    // 1.0 Kiểm tra định dạng email (@gmail.com)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email phải có định dạng hợp lệ.";
    }
    // 1.1 Kiểm tra định dạng số điện thoại (bắt đầu bằng 0, đủ 10 số)
    elseif (!preg_match('/^0\d{9}$/', $phone)) {
        $error = "Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.";
    }
    // 2. Kiểm tra định dạng mật khẩu
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/', $password)) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự, gồm ít nhất một chữ hoa, một chữ thường và một chữ số. Không chứa ký tự đặc biệt.";
    }
    else {
        // 3. Kiểm tra trùng email hoặc số điện thoại
        $check = $conn->prepare("SELECT * FROM accounts WHERE email = ? OR phone = ?");
        $check->bind_param("ss", $email, $phone);
        $check->execute();
        $result = $check->get_result();
        if ($result && $result->num_rows > 0) {
            $error = "Email hoặc số điện thoại đã tồn tại.";
        } else {
            // 4. Mã hóa mật khẩu và thêm vào DB
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO accounts (email, phone, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $phone, $hashed);

            if ($stmt->execute()) {
                header("Location: login.php?signup=1");
                exit();
            } else {
                $error = "Lỗi khi tạo tài khoản: " . $stmt->error;
            }
        }
    }

    // Hiển thị lỗi nếu có
   // if (!empty($error)) {
    //    echo "<div style='color:red; font-weight:bold; text-align:center;'>$error</div>";
   // } 
    
}
?>






