<?php
include 'sidebar.php';

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE id = $delete_id");
    header("Location: student.php");
    exit();
}

if(isset($_POST['submit'])){
    $lrn = $_POST['lrn'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $date = $_POST['date'];

    $conn->query("INSERT INTO students (lrn, name, gender, year, section, date) VALUES ('$lrn','$name','$gender','$year', '$section', '$date')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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
        .students-glass {
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
        .students-title {
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
        .modal-content {
            background: #232526;
            color: #fff;
            border-radius: 16px;
            border: 2px solid #2733a2;
        }
        .modal-header {
            border-bottom: 1px solid #2733a2;
        }
        .modal-title {
            color: #fff;
        }
        .btn-close {
            filter: invert(1);
        }
        .modal-footer .btn {
            width: 100%;
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
        @media (max-width: 900px) {
            .main-content { margin-left: 0; padding: 1.5rem 0.5rem; }
            .students-glass { padding: 24px 8px; }
        }
    </style>
</head>
<body>
    <div class="bg-circle one"></div>
    <div class="bg-circle two"></div>
    <div class="main-content">
        <div class="students-glass">
            <div class="students-title">Manage Students</div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student's Name</th>
                        <th>Year and Section</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $result = $conn->query("SELECT * FROM students");
                while($row = $result->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['year']} - {$row['section']}</td>
                        <td>{$row['date']}</td>
                        <td>
                            <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='student.php?delete={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</a>
                        </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                Add Student
            </button>
        </div>
    </div>
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" action="student.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">LRN</label>
                        <input type="text" name="lrn" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Year</label>
                        <input type="text" name="year" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit" class="btn btn-primary" style="background:#2733a2;border:none;">Add</button>
                </div>
            </div>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
