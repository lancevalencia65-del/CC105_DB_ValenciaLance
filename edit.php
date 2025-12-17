<?php
include 'sidebar.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    if (!$student) {
        echo "<script>alert('Student not found.');window.location='student.php';</script>";
        exit();
    }
} else {
    echo "<script>window.location='student.php';</script>";
    exit();
}
if (isset($_POST['update'])) {
    $lrn = $_POST['lrn'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE students SET lrn=?, name=?, gender=?, year=?, section=?, date=? WHERE id=?");
    $stmt->bind_param("ssssssi", $lrn, $name, $gender, $year, $section, $date, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Student updated successfully.');window.location='student.php';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
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
        .students-glass {
            background: rgba(255,255,255,0.13);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 40px 32px 32px 32px;
            color: #fff;
            max-width: 600px;
            margin: 0 auto;
        }
        .students-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
            text-align: center;
        }
        .form-label {
            color: #ffd700;
            font-weight: 500;
        }
        .form-control {
            background: rgba(255,255,255,0.18);
            border: none;
            border-radius: 8px;
            color: #fff;
            margin-bottom: 12px;
            padding: 12px;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.28);
            color: #232526;
            box-shadow: 0 0 0 2px  #2733a2;
        }
        .btn-primary {
            background: linear-gradient(90deg, #2733a2 0%, #777eb4 100%);
            border: none;
            color: #fff;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #777eb4 0%, #2733a2 100%);
            color: #ffd700;
        }
    </style>
</head>
<body>
    <div class="bg-circle one"></div>
    <div class="bg-circle two"></div>
    <div class="main-content">
        <div class="students-glass">
            <div class="students-title">Edit Student</div>
            <form method="POST">
                <div class="mb-2">
                    <label class="form-label">LRN</label>
                    <input type="text" name="lrn" class="form-control" value="<?php echo htmlspecialchars($student['lrn']); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="Male" <?php if($student['gender']=='Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if($student['gender']=='Female') echo 'selected'; ?>>Female</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Year</label>
                    <input type="text" name="year" class="form-control" value="<?php echo htmlspecialchars($student['year']); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Section</label>
                    <input type="text" name="section" class="form-control" value="<?php echo htmlspecialchars($student['section']); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($student['date']); ?>" required>
                </div>
                <div class="mt-4">
                    <button type="submit" name="update" class="btn btn-primary w-100">Update</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>