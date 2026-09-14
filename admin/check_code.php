<?php
session_start();

$enteredCode = $_POST['code'] ?? '';
$actualCode = $_SESSION['code'] ?? '';

if ($enteredCode == $actualCode) {
    echo "<script>
        alert('Mã xác nhận đúng. Bạn sẽ được chuyển sang trang đặt lại mật khẩu.');
        window.location.href = 'reset.php';
    </script>";
} else {
    echo "<script>
        alert('Mã xác nhận không đúng. Vui lòng thử lại.');
        window.location.href = 'confirm.php';
    </script>";
}
?>





