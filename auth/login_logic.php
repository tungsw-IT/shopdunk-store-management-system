<?php
include __DIR__ . '/../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Email phải có định dạng hợp lệ.";
        header("Location: login.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION["account_id"] = $row["account_id"];
            $_SESSION["accounts_name"] = $row["accounts_name"];
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Sai mật khẩu.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "Tài khoản không tồn tại.";
        header("Location: login.php");
        exit;
    }
}






