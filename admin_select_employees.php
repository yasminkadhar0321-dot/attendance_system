<?php
$conn = new mysqli("localhost", "root", "", "attendance_system");
$result = $conn->query("SELECT emp_number, name FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Employees</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid #3498db;
        }

        h2 {
            text-align: center;
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .employee-list {
            list-style-type: none;
            padding: 0;
        }

        .employee-item {
            background-color: #f9fafd;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.3s ease;
            border: 1px solid #d0dfe9;
            cursor: pointer; /* Make the row clickable */
        }

        .employee-item:hover {
            background-color: #dbe9f7;
        }

        .employee-item input[type="checkbox"] {
            display: none; /* Hide the actual checkbox */
        }

        .employee-item label {
            font-size: 18px;
            color: #34495e;
            display: flex;
            align-items: center;
            width: 100%; /* Make label span the entire row */
        }

        .employee-item label input {
            margin-right: 12px;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #3498db;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            margin-top: 30px;
        }

        .submit-btn:hover {
            background-color: #2c80b4;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #2c80b4;
        }

        .icon {
            margin-right: 8px;
        }

        .emoji {
            font-size: 20px;
            margin-right: 8px;
        }

        /* Make the whole row clickable */
        .employee-item:hover input[type="checkbox"] {
            display: inline-block; /* Show the checkbox when the row is hovered */
        }

        .employee-item input[type="checkbox"]:checked + label {
            background-color: #dbe9f7; /* Change background color when checked */
        }
    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fas fa-users icon"></i>Select Employees to View Attendance</h2>
        <form method="POST" action="view_selected_attendance.php">
            <ul class="employee-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="employee-item">
                        <input type="checkbox" name="employees[]" value="<?= $row['emp_number'] ?>" id="emp_<?= $row['emp_number'] ?>">
                        <label for="emp_<?= $row['emp_number'] ?>">
                            <span class="emoji">👤</span>
                            <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['emp_number']) ?>)
                        </label>
                    </li>
                <?php endwhile; ?>
            </ul>

            <input type="submit" value="View Attendance" class="submit-btn">
        </form>

        <div style="text-align: center;">
            <a class="back-button" href="admin_login.php"><i class="fas fa-arrow-left icon"></i>Back to Login</a>
        </div>
    </div>

</body>
</html>
