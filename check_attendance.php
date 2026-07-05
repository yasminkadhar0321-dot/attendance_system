<?php
$conn = new mysqli("localhost", "root", "", "attendance_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$greeting_message = "";
$attendance_table = "";
$summary_boxes = "";
$extra_comment = "";
$employee_details = "";

$present_count = 0;
$absent_count = 0;
$last30_present = 0;
$last30_absent = 0;
$from_date_display = "";
$to_date_display = "";

$emp_number = $_POST['emp_number'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

if (!empty($emp_number)) {
    $name_sql = "SELECT name FROM employees WHERE emp_number = '$emp_number'";
    $name_result = $conn->query($name_sql);

    if ($name_result && $name_result->num_rows > 0) {
        $employee_name = $name_result->fetch_assoc()['name'];
        $current_date = date("l, d F Y");

        $greeting_message = "<h2>👋 Welcome, <span class='emp-name'>$employee_name</span>!</h2>
                             <p class='current-date'>📅 $current_date</p>";

        $employee_details = "
            <div class='employee-card'>
                <p><strong>Name:</strong> $employee_name</p>
                <p><strong>Emp Number:</strong> $emp_number</p>
                <p><strong>Designation:</strong> CASUAL</p>
                <p><strong>Joining Date:</strong> 12 Jan 2022</p>
            </div>
        ";

        
        $today = date("Y-m-d");
        $last30 = date("Y-m-d", strtotime("-30 days"));
        $last30_sql = "SELECT status FROM attendance WHERE emp_number = '$emp_number' AND date BETWEEN '$last30' AND '$today'";
        $last30_result = $conn->query($last30_sql);
        if ($last30_result) {
            while ($row = $last30_result->fetch_assoc()) {
                if ($row['status'] === 'Present') $last30_present++;
                if ($row['status'] === 'Absent') $last30_absent++;
            }
        }

        if (!empty($from_date) && !empty($to_date)) {
            $from_date_display = date("d M Y", strtotime($from_date));
            $to_date_display = date("d M Y", strtotime($to_date));

            $sql = "SELECT date, status, in_time, out_time, total_hours FROM attendance 
                    WHERE emp_number = '$emp_number' 
                    AND date BETWEEN '$from_date' AND '$to_date' 
                    ORDER BY date DESC";
            $result = $conn->query($sql);

            $attendance_table = "<h3>📋 Attendance for <span class='emp-name'>$employee_name</span></h3>
                <button onclick='exportTableToCSV(\"attendance_$emp_number.csv\")' class='export-btn'>⬇️ Export CSV</button>
                <table id='attendanceTable'>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Total Hours</th>
                    </tr>";

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = $row['status'];
                    $color = ($status === 'Present') ? 'green' : (($status === 'Absent') ? 'red' : 'black');
                    if ($status === 'Present') $present_count++;
                    if ($status === 'Absent') $absent_count++;

                    $attendance_table .= "<tr>
                        <td>{$row['date']}</td>
                        <td style='color:$color;font-weight:bold'>{$row['status']}</td>
                        <td>" . ($row['in_time'] ?? '-') . "</td>
                        <td>" . ($row['out_time'] ?? '-') . "</td>
                        <td>" . ($row['total_hours'] ?? '-') . "</td>
                    </tr>";
                }
            } else {
                $attendance_table .= "<tr><td colspan='5'>No attendance records found.</td></tr>";
            }

            $attendance_table .= "</table>";

            $summary_boxes = "
                <div class='summary-boxes'>
                    <div class='box green'><h4>✅ Present</h4><p>$present_count</p></div>
                    <div class='box red'><h4>❌ Absent</h4><p>$absent_count</p></div>
                    <div class='box blue'><h4>📆 Range</h4><p>$from_date_display<br>to<br>$to_date_display</p></div>
                    <div class='box gray'><h4>🧑 Employee</h4><p>$employee_name<br>($emp_number)</p></div>
                    <div class='box orange'><h4>📅 Last 30 Days</h4><p>$last30_present Present<br>$last30_absent Absent</p></div>
                </div>
            ";

            $extra_comment = "<div class='extra-comment'>";
            if ($present_count > $absent_count) {
                $extra_comment .= "🌟Well done! Your dedication is inspiring. Great job, $employee_name!";
            } elseif ($absent_count > $present_count) {
                $extra_comment .= "⚠️ Try to reduce your absences, $employee_name.";
            } else {
                $extra_comment .= "👍 Balanced attendance. Keep it up!";
            }
            $extra_comment .= "</div>";
        }
    } else {
        $greeting_message = "<h2 style='color:red;'>❌ Employee not found.</h2>";
    }
}
$conn->close();
?>

<html>
<head>
    <title>Employee Attendance Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }

        .greeting {
            text-align: center;
        }

        .emp-name {
            color: #1976d2;
        }

        .current-date {
            color: #444;
            font-size: 16px;
        }

        .employee-card {
            background: #f1f1f1;
            border-left: 5px solid #1976d2;
            padding: 15px;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
        }

        .date-section {
            background-color: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }

        .date-section input[type="date"],
        .date-section input[type="submit"] {
            padding: 10px 14px;
            margin: 10px;
            border-radius: 8px;
            border: 1px solid #aaa;
            font-size: 16px;
        }

        .date-section input[type="submit"] {
            background: #1976d2;
            color: white;
            cursor: pointer;
        }

        .summary-boxes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 30px 0;
        }

        .box {
            flex: 1 1 180px;
            margin: 10px;
            padding: 20px;
            border-radius: 12px;
            color: white;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .green { background: #4caf50; }
        .red { background: #f44336; }
        .blue { background: #2196f3; }
        .gray { background: #757575; }
        .orange { background: #ff9800; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #1976d2;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .extra-comment {
            margin-top: 25px;
            font-size: 18px;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .export-btn {
            float: right;
            margin-bottom: 10px;
            padding: 8px 12px;
            border: none;
            background: #43a047;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .export-btn:hover {
            background: #388e3c;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 22px;
            cursor: pointer;
            display: none;
        }

        @media screen and (max-width: 768px) {
            .summary-boxes {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="greeting"><?php echo $greeting_message; ?></div>
    <?php echo $employee_details; ?>
    <div class="date-section">
        <form method="POST" action="">
            <input type="hidden" name="emp_number" value="<?php echo htmlspecialchars($emp_number); ?>">
            <label for="from_date">From:</label>
            <input type="date" id="from_date" name="from_date" required>
            <label for="to_date">To:</label>
            <input type="date" id="to_date" name="to_date" required>
            <input type="submit" value="Get Attendance">
        </form>
    </div>
    <?php echo $summary_boxes; ?>
    <?php echo $attendance_table; ?>
    <?php echo $extra_comment; ?>
</div>

<button class="back-to-top" onclick="scrollToTop()">↑</button>

<script>
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    window.onscroll = function () {
        document.querySelector('.back-to-top').style.display = window.scrollY > 300 ? 'block' : 'none';
    };

    function exportTableToCSV(filename) {
        var csv = [];
        var rows = document.querySelectorAll("table tr");

        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            for (var j = 0; j < cols.length; j++)
                row.push(cols[j].innerText);
            csv.push(row.join(","));
        }

        var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        var downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }
</script>
</body>
</html>
