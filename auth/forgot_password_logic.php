<?php
include __DIR__ . '/../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $code = rand(100000, 999999); // Tạo mã xác thực

    // Kiểm tra email có tồn tại không
    $checkStmt = $conn->prepare("SELECT * FROM accounts WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result && $result->num_rows === 1) {
        // Nếu có, thực hiện cập nhật mã xác thực
        $stmt = $conn->prepare("UPDATE accounts SET reset_code=? WHERE email=?");
        $stmt->bind_param("ss", $code, $email);

        if ($stmt->execute()) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = $code;

            echo "<script>
                alert('Mã xác thực đã được gửi (giả lập): $code');
                window.location.href = 'verify_code.php';
            </script>";
            exit();
        } else {
            $error = "Có lỗi xảy ra khi cập nhật mã xác thực.";
        }
    } else {
        // Email không tồn tại
        $error = "Email chưa được đăng ký trong hệ thống.";
    }
}
?>






