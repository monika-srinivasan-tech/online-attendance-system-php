<?php
// ✅ Fix timezone issue
date_default_timezone_set("Asia/Kolkata");

session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if(!isset($_GET['class_id'])){
    header("Location: dashboard.php");
    exit();
}

$class_id = intval($_GET['class_id']);
$date = date('Y-m-d'); // ✅ now this will always match Indian date

// ✅ Add Student
if(isset($_POST['add_student'])){
    $student_name = trim($conn->real_escape_string($_POST['student_name']));
    if(!empty($student_name)){
        $insert_student = "INSERT INTO students (name, class_id) VALUES ('$student_name', '$class_id')";
        $conn->query($insert_student);
        $success = "New student added successfully!";
    } else {
        $error = "Please enter student name!";
    }
}

// ✅ Delete Student
if(isset($_GET['delete_student'])){
    $del_id = intval($_GET['delete_student']);
    $conn->query("DELETE FROM students WHERE id='$del_id' AND class_id='$class_id'");
    $conn->query("DELETE FROM attendance WHERE student_id='$del_id' AND class_id='$class_id'");
    header("Location: ?class_id=$class_id");
    exit();
}

// ✅ Update student names
if(isset($_POST['update_students'])){
    foreach($_POST['student_name'] as $id => $name){
        $safe_name = trim($conn->real_escape_string($name));
        if(!empty($safe_name)){
            $conn->query("UPDATE students SET name='$safe_name' WHERE id='$id' AND class_id='$class_id'");
            $conn->query("UPDATE attendance SET student_name='$safe_name' WHERE student_id='$id' AND class_id='$class_id'");
        }
    }
    $success = "Student names updated successfully!";
}

// ✅ Save/Update Attendance
if(isset($_POST['submit_attendance'])){
    $student_sql = "SELECT * FROM students WHERE class_id='$class_id'";
    $student_result2 = $conn->query($student_sql);

    while($row = $student_result2->fetch_assoc()){
        $student_id = $row['id'];
        $student_name = $conn->real_escape_string($row['name']);
        $status = isset($_POST['attendance'][$student_id]) ? $_POST['attendance'][$student_id] : '';

        if($status != ''){
            $check_sql = "SELECT id FROM attendance WHERE student_id='$student_id' AND class_id='$class_id' AND date='$date'";
            $check_result = $conn->query($check_sql);

            if($check_result->num_rows > 0){
                // Update existing
                $conn->query("UPDATE attendance 
                              SET status='$status', student_name='$student_name' 
                              WHERE student_id='$student_id' AND class_id='$class_id' AND date='$date'");
            } else {
                // Insert new
                $conn->query("INSERT INTO attendance (student_id, student_name, class_id, date, status) 
                              VALUES ('$student_id','$student_name','$class_id', '$date', '$status')");
            }
        }
    }
    $success = "Attendance saved/updated successfully!";
    $show_report = true; // ✅ Save pannumbodane report show aaganum
}

// ✅ Fetch Students
$student_sql = "SELECT * FROM students WHERE class_id='$class_id' ORDER BY name ASC";
$student_result = $conn->query($student_sql);

// ✅ Calculate today's summary
$total_present = 0;
$total_absent = 0;
$total_students = 0;

// Count total students
$stu_count_sql = "SELECT COUNT(*) as cnt FROM students WHERE class_id='$class_id'";
$stu_count_res = $conn->query($stu_count_sql);
if($stu_count_res){
    $total_students = $stu_count_res->fetch_assoc()['cnt'];
}

// Count Present
$present_sql = "SELECT COUNT(*) as cnt FROM attendance 
                WHERE class_id='$class_id' AND date='$date' AND status='P'";
$present_res = $conn->query($present_sql);
if($present_res){
    $total_present = $present_res->fetch_assoc()['cnt'];
}

// Count Absent
$absent_sql = "SELECT COUNT(*) as cnt FROM attendance 
               WHERE class_id='$class_id' AND date='$date' AND status='A'";
$absent_res = $conn->query($absent_sql);
if($absent_res){
    $total_absent = $absent_res->fetch_assoc()['cnt'];
}

// ✅ Calculate percentage
$present_percent = 0;
$absent_percent = 0;
if($total_students > 0){
    $present_percent = round(($total_present / $total_students) * 100, 2);
    $absent_percent = round(($total_absent / $total_students) * 100, 2);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance</title>
 <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<!-- Common CSS -->
<link rel="stylesheet" href="assets/css/common.css">
</head>
<body>

<h2>Mark Attendance for Class ID: <?php echo $class_id; ?> (<?php echo date('d-M-Y'); ?>)</h2>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<!-- Add Student -->
<h3>Add New Student</h3>
<form method="POST">
    <input type="text" name="student_name" placeholder="Enter student name">
    <input type="submit" name="add_student" value="Add Student">
</form>

<!-- Update Names & Attendance -->
<form method="POST">
<table>
<tr>
    <th>Roll</th>
    <th>Student Name</th>
    <th>Update Name</th>
    <th>Delete</th>
    <th>Present</th>
    <th>Absent</th>
</tr>

<?php
if($student_result->num_rows > 0){
    $roll = 1;
    while($row = $student_result->fetch_assoc()){
        $id = $row['id'];
        $att_res = $conn->query("SELECT status FROM attendance WHERE student_id='$id' AND class_id='$class_id' AND date='$date'");
        $status = ($att_res && $att_res->num_rows>0) ? $att_res->fetch_assoc()['status'] : '';

        echo "<tr>";
        echo "<td>$roll</td>";
        echo "<td>".htmlspecialchars($row['name'])."</td>";
        echo "<td><input type='text' name='student_name[$id]' value='".htmlspecialchars($row['name'])."'></td>";
        echo "<td><a class='delete' href='?class_id=$class_id&delete_student=$id' onclick=\"return confirm('Delete this student?')\">X</a></td>";
        echo "<td><input type='radio' id='p_$id' name='attendance[$id]' value='P' ".($status=='P'?'checked':'')."><label class='btn' for='p_$id'>P</label></td>";
        echo "<td><input type='radio' id='a_$id' name='attendance[$id]' value='A' ".($status=='A'?'checked':'')."><label class='btn' for='a_$id'>A</label></td>";
        echo "</tr>";
        $roll++;
    }
} else {
    echo "<tr><td colspan='6'>No students found in this class.</td></tr>";
}
?>
</table>
<input type="submit" name="update_students" value="Update Names">
<input type="submit" name="submit_attendance" value="Save Attendance">
</form>

<!-- Today's Attendance Summary -->
<div class="summary">
    <p>Total Students: <?php echo $total_students; ?></p>
    <p>Total Present: <?php echo $total_present; ?></p>
    <p>Total Absent: <?php echo $total_absent; ?></p>
</div>

<!-- Report Section -->
<?php if(isset($show_report) && $show_report){ ?>
<div class="report">
    <h3>Attendance Percentage Report</h3>
    <p>Present: <?php echo $present_percent; ?>%</p>
    <p>Absent: <?php echo $absent_percent; ?>%</p>
</div>
<?php } ?>

</body>
</html>
