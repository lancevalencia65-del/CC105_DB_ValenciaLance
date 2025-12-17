<?php
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            z-index: 1;
        }
        .bg-circle.one {
            width: 320px; height: 320px;
            background: #2733a2;
            top: -80px; left: -100px;
        }
        .bg-circle.two {
            width: 220px; height: 220px;
            background: #2733a2;
            bottom: -60px; right: -60px;
        }
        .main-content {
            margin-left: 240px;
            padding: 2.5rem 2rem;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }
        .dashboard-glass {
            background: rgba(255,255,255,0.13);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 40px 32px 32px 32px;
            color: #fff;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .welcome-msg {
            font-size: 1.2rem;
            color: #e0e0e0;
            margin-bottom: 2rem;
        }
        .dashboard-cards {
            margin-top: 2rem;
        }
        .dashboard-card {
            background: rgba(255,255,255,0.18);
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(39,51,162,0.07);
            border: none;
            color: #fff;
            padding: 2rem 1.5rem;
            margin-bottom: 1.5rem;
            transition: background 0.2s, transform 0.2s;
        }
        .dashboard-card:hover {
            background: rgba(119,126,180,0.25);
            transform: translateY(-4px) scale(1.03);
        }
        .dashboard-card .card-title {
            color: #ffd700;
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 0.7rem;
        }
        .dashboard-card .card-text {
            color: #f8f9fa;
            font-size: 1.05rem;
        }
        .dashboard-card .btn {
            background: linear-gradient(90deg, #2733a2 0%, #777eb4 100%);
            border: none;
            color: #fff;
            font-weight: bold;
            margin-top: 1.2rem;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .dashboard-card .btn:hover {
            background: linear-gradient(90deg, #777eb4 0%, #2733a2 100%);
            color: #ffd700;
        }
        @media (max-width: 900px) {
            .main-content { margin-left: 0; padding: 1.5rem 0.5rem; }
            .dashboard-glass { padding: 24px 8px; }
        }
    </style>
</head>
<body>
    <div class="bg-circle one"></div>
    <div class="bg-circle two"></div>
    <div class="main-content">
        <div class="dashboard-glass">
            <div class="dashboard-title">Dashboard</div>
            <div class="welcome-msg">
                Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!
            </div>
            <div class="row dashboard-cards">
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <div class="card-title">Students</div>
                        <a href="students.php" class="btn w-100">View Students</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <div class="card-title">Attendance</div>
                        <a href="attendance.php" class="btn w-100">View Attendance</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card h-100">
                        <div class="card-title">Reports</div>
                        <a href="report.php" class="btn w-100">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>