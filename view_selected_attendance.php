<?php
$attendanceData = [];
$employeeNumbers = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSV EXPORT BLOCK
    if (isset($_POST['export_csv']) && isset($_POST['employees'])) {
        $conn = new mysqli("localhost", "root", "", "attendance_system");

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="selected_employees_attendance.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Employee Number', 'Name', 'Date', 'Status', 'In Time', 'Out Time']);

        foreach ($_POST['employees'] as $empNumber) {
            $empStmt = $conn->prepare("SELECT name FROM employees WHERE emp_number = ?");
            $empStmt->bind_param("s", $empNumber);
            $empStmt->execute();
            $empResult = $empStmt->get_result();
            $empRow = $empResult->fetch_assoc();
            $employeeName = $empRow['name'];

            $attStmt = $conn->prepare("SELECT date, status, in_time, out_time FROM attendance WHERE emp_number = ? ORDER BY date DESC LIMIT 30");
            $attStmt->bind_param("s", $empNumber);
            $attStmt->execute();
            $attResult = $attStmt->get_result();

            while ($row = $attResult->fetch_assoc()) {
                fputcsv($output, [
                    $empNumber,
                    $employeeName,
                    $row['date'],
                    $row['status'],
                    $row['in_time'],
                    $row['out_time']
                ]);
            }
        }

        fclose($output);
        exit;
    }

    // ATTENDANCE DISPLAY BLOCK
    if (isset($_POST['employees'])) {
        $conn = new mysqli("localhost", "root", "", "attendance_system");

        $employeeNumbers = $_POST['employees'];

        foreach ($employeeNumbers as $empNumber) {
            $empStmt = $conn->prepare("SELECT name FROM employees WHERE emp_number = ?");
            $empStmt->bind_param("s", $empNumber);
            $empStmt->execute();
            $empResult = $empStmt->get_result();
            $empRow = $empResult->fetch_assoc();
            $employeeName = $empRow['name'];

            $attStmt = $conn->prepare("SELECT date, status, in_time, out_time FROM attendance WHERE emp_number = ? ORDER BY date DESC LIMIT 30");
            $attStmt->bind_param("s", $empNumber);
            $attStmt->execute();
            $attResult = $attStmt->get_result();

            $attendance = [];
            while ($row = $attResult->fetch_assoc()) {
                $attendance[] = $row;
            }

            $attendanceData[] = [
                'emp_number' => $empNumber,
                'name' => $employeeName,
                'attendance' => $attendance
            ];
        }
    } else {
        echo "No employees selected.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Selected Employees Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 40px;
            font-size: 26px;
        }

        .employee-box {
            margin-bottom: 40px;
            border: 1px solid #d0dfe9;
            padding: 20px;
            border-radius: 12px;
            background-color: #f9fafd;
        }

        .employee-box h3 {
            margin-top: 0;
            color: #34495e;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #dde6f1;
            padding: 10px 14px;
            text-align: center;
            font-size: 15px;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        td {
            background-color: #fff;
        }

        .status-present {
            color: green;
            font-weight: bold;
        }

        .status-absent {
            color: red;
            font-weight: bold;
        }

        .icon {
            margin-right: 8px;
            color: #2980b9;
        }

        .back-button, .csv-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
            margin-right: 10px;
        }

        .back-button:hover, .csv-button:hover {
            background-color: #2c80b4;
        }

        .button-group {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-list icon"></i>Attendance of Selected Employees (Last 30 Days)</h2>

    <?php foreach ($attendanceData as $employee): ?>
        <div class="employee-box">
            <h3><i class="fas fa-user icon"></i><?= htmlspecialchars($employee['name']) ?> (<?= htmlspecialchars($employee['emp_number']) ?>)</h3>
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($employee['attendance'] as $att): ?>
                    <tr>
                        <td><?= htmlspecialchars($att['date']) ?></td>
                        <td class="<?= $att['status'] === 'Present' ? 'status-present' : 'status-absent' ?>">
                            <?= htmlspecialchars($att['status']) ?>
                        </td>
                        <td><?= htmlspecialchars($att['in_time'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($att['out_time'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <div class="button-group">
        <a class="back-button" href="admin_select_employees.php"><i class="fas fa-arrow-left icon"></i>Back to Selection</a>

        <form method="post" style="display: inline;">
            <?php foreach ($employeeNumbers as $emp): ?>
                <input type="hidden" name="employees[]" value="<?= htmlspecialchars($emp) ?>">
            <?php endforeach; ?>
            <input type="hidden" name="export_csv" value="1">
            <button type="submit" class="csv-button"><i class="fas fa-download icon"></i>Download CSV</button>
        </form>
    </div>
</div>
</body>
</html>
