<?php
$role = $_POST['role'];

if ($role === 'employee') {
    header("Location: check_attendance_form.php");
    exit;
} elseif ($role === 'admin') {
    header("Location: admin_select_employees.php");
    exit;
} else {
    echo "Invalid role selected.";
}
?>
