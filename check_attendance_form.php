<html>
<head>
  <title>Employee Attendance Check</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome Icons -->
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: #e0e0e0; /* Soft gray background */
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    h2 {
      color: #333;
      margin-bottom: 25px;
      font-size: 24px;
      font-weight: 600;
    }

    .input-group {
      text-align: left;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      font-size: 14px;
      color: #555;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
      box-sizing: border-box;
      transition: 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #4CAF50;
      box-shadow: 0 0 8px rgba(76, 175, 80, 0.5);
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      background: #4CAF50;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #45a049;
    }

    .icon {
      margin-right: 10px;
    }

    .emoji {
      font-size: 1.5em;
    }

    
    @media (max-width: 500px) {
      .container {
        padding: 20px;
      }

      h2 {
        font-size: 20px;
      }

      input[type="text"], input[type="password"] {
        font-size: 14px;
      }

      button {
        font-size: 14px;
      }
    }

  </style>
</head>
<body>
  <div class="container">
    <h2><span class="emoji">📋</span> Check Your Attendance</h2>
    <form action="check_attendance.php" method="POST">
      <div class="input-group">
        <label for="username"><i class="fas fa-user icon"></i>Your Name</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="input-group">
        <label for="emp_number"><i class="fas fa-id-badge icon"></i>Employee Number</label>
        <input type="password" id="emp_number" name="emp_number" required>
      </div>

      <button type="submit"><span class="emoji">👀</span> View Attendance</button>
    </form>
  </div>
</body>
</html>
