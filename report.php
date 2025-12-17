<?php
include 'sidebar.php';

$filter_section = $_GET['section'] ?? '';
$filter_year = $_GET['year'] ?? '';
$filter_date = $_GET['date'] ?? date('Y-m-d');

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

$query = "SELECT s.name, s.year, s.section, a.date, a.status
          FROM students s
          LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
          WHERE 1";
$params = [$filter_date];
$types = "s";

if ($filter_section) {
    $query .= " AND s.section = ?";
    $params[] = $filter_section;
    $types .= "s";
}
if ($filter_year) {
    $query .= " AND s.year = ?";
    $params[] = $filter_year;
    $types .= "s";
}
$query .= " ORDER BY s.section, s.year, s.name ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$records = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - Monitoring System</title>
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
        .main-content {
            margin-left: 240px;
            padding: 2.5rem 2rem;
            min-height: 100vh;
            position: relative;
            z-index: 2;
        }
        .report-glass {
            background: rgba(255,255,255,0.13);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 40px 32px 32px 32px;
            color: #fff;
            max-width: 1100px;
            margin: 0 auto;
        }
        .report-title {
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
        .btn-print {
            background: #ffd700;
            color: #2733a2;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            margin-bottom: 1.5rem;
        }
        .btn-print:hover {
            background: #fff;
            color: #2733a2;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .report-glass, .main-content { background: #fff !important; color: #000 !important; box-shadow: none !important; }
            .table { color: #000 !important; }
            .table thead { background: #eee !important; color: #000 !important; }
            .report-title, form, .btn-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="report-glass">
            <div class="report-title no-print">Attendance Report</div>
            <form method="get" class="row g-3 align-items-end mb-4 no-print">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section</label>
                    <select name="section" class="form-select">
                        <option value="">All Sections</option>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?= htmlspecialchars($section) ?>" <?= $filter_section == $section ? 'selected' : '' ?>>
                                <?= htmlspecialchars($section) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year) ?>" <?= $filter_year == $year ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-9 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
            <a
                href="print_report.php?date=<?= urlencode($filter_date) ?>&section=<?= urlencode($filter_section) ?>&year=<?= urlencode($filter_year) ?>"
                target="_blank"
                class="btn btn-print no-print mb-3"
            >
                <span class="bi bi-printer"></span> Print Report
            </a>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>YEAR</th>
                        <th>SECTION</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records->num_rows > 0): ?>
                        <?php while ($row = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['year']) ?></td>
                                <td><?= htmlspecialchars($row['section']) ?></td>
                                <td><?= htmlspecialchars($row['date'] ?? $filter_date) ?></td>
                                <td><?= htmlspecialchars($row['status'] ?? 'No Record') ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-warning">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>