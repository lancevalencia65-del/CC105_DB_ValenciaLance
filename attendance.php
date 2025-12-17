<?php
include 'sidebar.php';
$filter_section = $_GET['section'] ?? '';
$filter_year = $_GET['year'] ?? '';
$current_date = $_GET['date'] ?? date('Y-m-d');
$sections = [];
$result = $conn->query("SELECT DISTINCT section FROM students WHERE section IS NOT NULL AND section != '' ORDER BY section ASC");
while ($row = $result->fetch_assoc()) {
    $sections[] = $row['section'];
}
$years = [];
$result_years = $conn->query("SELECT DISTINCT year FROM students WHERE year IS NOT NULL AND year != '' ORDER BY year ASC");
while ($row = $result_years->fetch_assoc()) {
    $years[] = $row['year'];
}
if ($filter_section && $filter_year) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE section = ? AND year = ? ORDER BY name ASC");
    $stmt->bind_param('ss', $filter_section, $filter_year);
    $stmt->execute();
    $students = $stmt->get_result();
} elseif ($filter_section) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE section = ? ORDER BY name ASC");
    $stmt->bind_param('s', $filter_section);
    $stmt->execute();
    $students = $stmt->get_result();
} elseif ($filter_year) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE year = ? ORDER BY name ASC");
    $stmt->bind_param('s', $filter_year);
    $stmt->execute();
    $students = $stmt->get_result();
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY section ASC, year ASC, name ASC");
}
$attendance_map = [];
$student_ids = [];
if ($students instanceof mysqli_result && $students->num_rows > 0) {
    $student_data = [];
    while ($row = $students->fetch_assoc()) {
        $student_ids[] = $row['id'];
        $student_data[$row['id']] = $row;
    }

    if (count($student_ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
        $types = str_repeat('i', count($student_ids));
        $sql = "SELECT student_id, status FROM attendance WHERE date = ? AND student_id IN ($placeholders)";
        $stmt_att = $conn->prepare($sql);
        $bind_params = array_merge([$current_date], $student_ids);
        $refs = [];
        foreach ($bind_params as $key => $value) {
            $refs[$key] = &$bind_params[$key];
        }
        $stmt_att->bind_param('s' . $types, ...$refs);
        $stmt_att->execute();
        $att_results = $stmt_att->get_result();
        while ($att_row = $att_results->fetch_assoc()) {
            $attendance_map[$att_row['student_id']] = $att_row['status'];
        }
    }
} else {
    $student_data = [];
}
if (isset($_POST['submit_attendance'])) {
    $date = $_POST['date'];
    $attendance_input = $_POST['attendance'] ?? [];
    $post_section = $_POST['section'] ?? '';
    $post_year = $_POST['year'] ?? '';
    $student_ids_post = array_keys($attendance_input);
    if (count($student_ids_post) > 0) {
        $placeholders = implode(',', array_fill(0, count($student_ids_post), '?'));
        $types = str_repeat('i', count($student_ids_post));
        $sql = "SELECT student_id FROM attendance WHERE date = ? AND student_id IN ($placeholders)";
        $stmt_check = $conn->prepare($sql);
        $bind_params_check = array_merge([$date], $student_ids_post);
        $refs_check = [];
        foreach ($bind_params_check as $key => $value) {
            $refs_check[$key] = &$bind_params_check[$key];
        }
        $stmt_check->bind_param('s' . $types, ...$refs_check);
        $stmt_check->execute();
        $check_results = $stmt_check->get_result();

        $existing_ids = [];
        while ($row = $check_results->fetch_assoc()) {
            $existing_ids[] = $row['student_id'];
        }

        $stmt_update = $conn->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND date = ?");
        $stmt_insert = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");

        foreach ($attendance_input as $student_id => $status) {
            if (in_array($student_id, $existing_ids)) {
                $stmt_update->bind_param("sis", $status, $student_id, $date);
                $stmt_update->execute();
            } else {
                $stmt_insert->bind_param("iss", $student_id, $date, $status);
                $stmt_insert->execute();
            }
        }
    }
    $params = [];
    if ($post_section) $params['section'] = $post_section;
    if ($post_year) $params['year'] = $post_year;
    $params['date'] = $date;
    $redirect_url = 'attendance.php?' . http_build_query($params);

    echo "<script>alert('Attendance saved!');window.location='" . $redirect_url . "';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Attendance - Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
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
        .btn-success {
            background: linear-gradient(90deg, #2733a2 0%, #777eb4 100%);
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-success:hover {
            background: linear-gradient(90deg, #777eb4 0%, #2733a2 100%);
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
            color: #000;
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
        select {
            color: black;
        }
    </style>
</head>
<body>
    <div class="bg-circle one"></div>
    <div class="bg-circle two"></div>
<div class="main-content">
    <div class="attendance-glass">
        <h1 class="attendance-title">ATTENDANCE</h1>

        <form method="get" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Year</label>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year) ?>" <?= $filter_year == $year ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter by Section</label>
                    <select name="section" class="form-select" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?= htmlspecialchars($section) ?>" <?= $filter_section == $section ? 'selected' : '' ?>>
                                <?= htmlspecialchars($section) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
        <form method="post">
            <input type="hidden" name="section" value="<?= htmlspecialchars($filter_section) ?>">
            <input type="hidden" name="year" value="<?= htmlspecialchars($filter_year) ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($current_date) ?>">

            <a href="show_attendance.php?<?= http_build_query(['section' => $filter_section, 'year' => $filter_year]) ?>" class="btn btn-primary mb-3">Show Attendance</a>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>YEAR</th>
                        <th>SECTION</th>
                        <th>ATTENDANCE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student_data as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['year']) ?></td>
                            <td><?= htmlspecialchars($student['section']) ?></td>
                            <td>
                                <select name="attendance[<?= $student['id'] ?>]" class="form-select">
                                    <option value="Present" <?= (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'Present') ? 'selected' : '' ?>>Present</option>
                                    <option value="Absent" <?= (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'Absent') ? 'selected' : '' ?>>Absent</option>
                                    <option value="Late" <?= (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'Late') ? 'selected' : '' ?>>Late</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-end mt-3">
                <button type="submit" name="submit_attendance" class="btn btn-success px-4 py-2">Save Attendance</button>
            </div>
        </form>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeDate(date) {
            const params = new URLSearchParams(window.location.search);
            if (date) {
                params.set('date', date);
            } else {
                params.delete('date');
            }
            window.location.search = params.toString();
        }
    </script>
</body>
</html>
