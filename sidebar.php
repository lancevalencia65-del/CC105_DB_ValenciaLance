<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            width: 240px;
            background: rgba(255,255,255,0.13);
            border-radius: 0 18px 18px 0;
            box-shadow: 2px 0 32px 0 rgba(39,51,162,0.18);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-right: 2px solid #2733a2;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 2.5rem 1.2rem 1.2rem 1.2rem;
            z-index: 100;
        }
        .sidebar h3 {
            text-align: center;
            margin-bottom: 2.5rem;
            font-weight: bold;
            letter-spacing: 1px;
            color: #fff;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: block;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: linear-gradient(90deg, #2733a2 0%, #777eb4 100%);
            color: #ffd700;
        }
        .sidebar .logout-btn {
            margin-top: auto;
            width: 100%;
            background: #ffd700;
            color: #2733a2;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 0;
            font-weight: bold;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .logout-btn:hover {
            background: #fff;
            color: #2733a2;
        }
        .sidebar .user-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .sidebar .user-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0.5rem;
            border: 2px solid #fff;
            display: block;
            background: #fff;
            color: #2733a2;
            font-size: 2rem;
            align-items: center;
            justify-content: center;
        }
        .sidebar .username {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0;
            text-align: center;
            width: 100%;
            color: #ffd700;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Monitoring</h3>
        <div class="user-info">
            <?php if (!empty($_SESSION['pic'])): ?>
                <img src="<?php echo htmlspecialchars($_SESSION['pic']); ?>" class="user-pic" alt="Profile">
            <?php else: ?>
                <div class="user-pic d-flex align-items-center justify-content-center">
                    <span>👤</span>
                </div>
            <?php endif; ?>
            <div class="username">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
        </div>
        <a href="dashboard.php">Dashboard</a>
        <a href="student.php">Students</a>
        <a href="attendance.php">Attendance</a>
        <a href="report.php">Reports</a>
        <form method="post">
            <button class="logout-btn" type="submit" name="logout">LOGOUT</button>
        </form>
    </div>
</body>
</html>
