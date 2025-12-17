<?php
include 'conn.php';
$filter_section = $_GET['section'] ?? '';
$filter_year = $_GET['year'] ?? '';
$filter_date = $_GET['date'] ?? date('Y-m-d');

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
    <title>Print Attendance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background: #fff; color: #000; }
        .table { color: #000; }
        .table thead { background: #eee; color: #000; }
        @media print {
            body { background: #fff !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <h2 class="mb-3">Attendance Report</h2>
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
</body>
</html>