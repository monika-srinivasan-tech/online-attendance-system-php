<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// ✅ Fix timezone
date_default_timezone_set("Asia/Kolkata");

// Class ID
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
if($class_id <= 0){
    header("Location: dashboard.php");
    exit();
}

// Days range (default 30)
$days = isset($_GET['days']) ? intval($_GET['days']) : 30;
if($days < 1) $days = 1;
if($days > 90) $days = 90;

// Generate last $days dates
$dates = [];
for($i=$days-1;$i>=0;$i--){
    $dates[] = date('Y-m-d', strtotime("-{$i} days"));
}

// Fetch students in alphabetical order
$student_sql = "SELECT id, name FROM students WHERE class_id = {$class_id} ORDER BY name ASC";
$student_result = $conn->query($student_sql);

$attendance_data = [];
if($student_result && $student_result->num_rows > 0){
    while($student = $student_result->fetch_assoc()){
        $student_id = intval($student['id']);
        $student_name = $student['name'];

        $daily_attendance = [];
        $total_present = 0;
        $total_absent = 0;

        // Fetch attendance per day
        foreach($dates as $d){
            $att_sql = "SELECT status FROM attendance 
                        WHERE student_id={$student_id} 
                          AND class_id={$class_id} 
                          AND DATE(`date`) = '$d'";
            $att_result = $conn->query($att_sql);

            if($att_result && $att_result->num_rows > 0){
                $status = $att_result->fetch_assoc()['status'];
                if($status=='P') $total_present++;
                if($status=='A') $total_absent++;
            } else {
                $status = ''; // leave empty if not marked
            }

            $daily_attendance[$d] = $status;
        }

        $total_marked = $total_present + $total_absent;
        $percentage = ($total_marked > 0) ? round(($total_present/$total_marked)*100,2) : 0;

        $attendance_data[] = [
            'name' => $student_name,
            'daily' => $daily_attendance,
            'present' => $total_present,
            'absent' => $total_absent,
            'percentage' => $percentage
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Report</title>
<style>
body { font-family: Arial; background: #fff; padding: 30px; }
h2 { color: #333; }
table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size:14px; }
th, td { border: 1px solid #000; padding: 5px; text-align: center; }
select { padding: 5px; font-size: 16px; }
input[type=submit]{ padding: 6px 12px; font-size: 16px; cursor: pointer; }
td.empty { background: #f0f0f0; } /* unmarked cell style */
td.today { background: #ffe0b2; } /* today highlight */
</style>
</head>
<body>

<h2>Attendance Report (Last <?php echo $days; ?> Days)</h2>
<a href="dashboard.php">Back to Dashboard</a><br><br>

<form method="GET" action="">
    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
    <label>Days: </label>
    <select name="days">
        <?php for($i=1;$i<=90;$i++): ?>
            <option value="<?php echo $i; ?>" <?php echo ($i==$days)?'selected':''; ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <input type="submit" value="View Report">
</form>

<table>
<tr>
    <th>Roll No</th>
    <th>Student Name</th>
    <?php foreach($dates as $d): ?>
        <th class="<?php echo ($d==date('Y-m-d'))?'today':''; ?>">
            <?php echo date('d-M', strtotime($d)); ?>
        </th>
    <?php endforeach; ?>
    <th>Total P</th>
    <th>Total A</th>
    <th>%</th>
</tr>

<?php if(!empty($attendance_data)): $roll=1; ?>
    <?php foreach($attendance_data as $data): ?>
        <tr>
            <td><?php echo $roll; ?></td>
            <td><?php echo htmlspecialchars($data['name']); ?></td>
            <?php foreach($data['daily'] as $date_key => $status): ?>
                <td class="<?php echo $status==''?'empty':''; echo ($date_key==date('Y-m-d'))?' today':''; ?>">
                    <?php echo $status; ?>
                </td>
            <?php endforeach; ?>
            <td><?php echo $data['present']; ?></td>
            <td><?php echo $data['absent']; ?></td>
            <td><?php echo $data['percentage']; ?>%</td>
        </tr>
    <?php $roll++; endforeach; ?>
<?php else: ?>
<tr><td colspan="<?php echo count($dates)+4; ?>">No students found.</td></tr>
<?php endif; ?>
</table>

</body>
</html>
