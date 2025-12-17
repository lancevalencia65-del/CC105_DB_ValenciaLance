<?php
include 'sidebar.php';
$filter_section = isset($_GET['section']) ? $_GET['section'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$sections = [];
$section_result = $conn->query("SELECT DISTINCT section FROM students ORDER BY section ASC");
while ($row = $section_result->fetch_assoc()) {
    if (!empty($row['section'])) {
        $sections[] = $row['section'];
    }
}
$attendance_records = [];
if ($filter_section) {
    $stmt = $conn->prepare(
        "SELECT s.name, s.year, s.section, a.status 
         FROM students s 
         LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
         WHERE s.section = ?
         ORDER BY s.name ASC"
    );
    $stmt->bind_param("ss", $filter_date, $filter_section);
    $stmt->execute();
    $attendance_records = $stmt->get_result();
} else {
    $stmt = $conn->prepare(
        "SELECT s.name, s.year, s.section, a.status 
         FROM students s 
         LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
         ORDER BY s.section, s.name ASC"
    );
    $stmt->bind_param("s", $filter_date);
    $stmt->execute();
    $attendance_records = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Attendance - Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .attendance-glass {
            background: rgba(255,255,255,0.13);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 40px 32px 32px 32px;
            color: #fff;
            max-width: 1000px;
            margin: 0 auto;
        }
        .attendance-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
            text-align: center;
        }
        .table {
            background: rgba(255,255,255,0.18);
            border-radius: 12px;
            overflow: hidden;
            color: #fff;
        }
        .table thead {
            background: rgba(39,51,162,0.7);
            color: #ffd700;
        }
        .form-label {
            color: #ffd700;
            font-weight: 500;
        }
        .form-control, .form-select {
            background: rgba(255,255,255,0.18);
            border: none;
            border-radius: 8px;
            color: #fff;
            margin-bottom: 12px;
            padding: 12px;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.28);
            color: #232526;
            box-shadow: 0 0 0 2px  #2733a2;
        }
        @media (max-width: 900px) {
            .main-content { margin-left: 0; padding: 1.5rem 0.5rem; }
            .attendance-glass { padding: 24px 8px; }
        }
    </style>
</head>
<body>
    <div class="bg-circle one"></div>
    <div class="bg-circle two"></div>
    <div class="main-content">
        <div class="attendance-glass">
            <div class="attendance-title">Attendance Records</div>
            <form method="get" class="mb-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Section</label>
                        <select name="section" class="form-select" onchange="this.form.submit()">
                            <option value="">All Sections</option>
                            <?php foreach($sections as $section): ?>
                                <option value="<?php echo htmlspecialchars($section); ?>" <?php if($filter_section==$section) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($section); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>" onchange="this.form.submit()">
                    </div>
                </div>
            </form>
            <table class="table table-bordered" style="background: rgba(255,255,255,0.18); border-radius: 12px; overflow: hidden; color: #fff;">
                <thead style="background: rgba(39,51,162,0.7); color: #ffd700;">
                    <tr>
                        <th>Student's Name</th>
                        <th>Year & Section</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($attendance_records instanceof mysqli_result && $attendance_records->num_rows > 0) {
                    while($row = $attendance_records->fetch_assoc()){
                        echo "<tr>
                            <td>".htmlspecialchars($row['name'])."</td>
                            <td>".htmlspecialchars($row['year'].' - '.$row['section'])."</td>
                            <td>".htmlspecialchars($row['status'] ? $row['status'] : 'No Record')."</td>
                        </tr>";
                    }
                } else {
                    echo '<tr><td colspan="3" class="text-center text-warning">No attendance records found.</td></tr>';
                }
                ?>
                </tbody>
            </table>
            <a href="attendance.php<?php echo $filter_section ? '?section=' . urlencode($filter_section) : ''; ?>" class="btn btn-secondary mt-3">Back to Attendance</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>