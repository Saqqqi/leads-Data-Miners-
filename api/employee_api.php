<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');


require_once 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

date_default_timezone_set('Asia/Karachi');
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

if (!$authHeader) {
    die(json_encode(['status' => 'error', 'message' => 'Authorization header missing']));
}

preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
$loginUserId = isset($matches[1]) ? (int) $matches[1] : null;

if (!$loginUserId) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid token']));
}

$sql = "SELECT id, username, cnic, designation, role FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loginUserId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die(json_encode(['status' => 'error', 'message' => 'User not found']));
}

$stmt->bind_result($id, $username, $cnic, $designation, $role);
$stmt->fetch();
$stmt->close();

$monthlyTargetLeads = 1000; 

function getTodayLeads($conn, $userId)
{
    $currentDate = date('Y-m-d');
    $startOfWorkDay = date('Y-m-d 17:00:00');  // 5 PM today
    $endOfWorkDay = date('Y-m-d 09:00:00', strtotime('+1 day')); // 9 AM next day

    $sql = "SELECT COUNT(DISTINCT id) 
            FROM leads 
            WHERE owner_id = ? 
            AND created_at >= ? 
            AND created_at < ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $userId, $startOfWorkDay, $endOfWorkDay);
    $stmt->execute();
    $stmt->store_result();

    $todayLeads = 0;

    $stmt->bind_result($todayLeads);
    $stmt->fetch();
    $stmt->close();

    return $todayLeads;
}

if ($role === 'admin') {
    $sql = "SELECT e.id, e.username, e.cnic, e.designation, e.role,
                   COUNT(DISTINCT CASE WHEN DATE(l.created_at) = CURDATE() THEN l.id END) AS today_leads,
                   COUNT(DISTINCT CASE WHEN MONTH(l.created_at) = MONTH(CURDATE()) THEN l.id END) AS monthly_leads
            FROM employees e
            LEFT JOIN leads l ON e.id = l.owner_id
            GROUP BY e.id";
    $result = $conn->query($sql);

    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $progress = ($row['monthly_leads'] / $monthlyTargetLeads) * 100;
        $row['progress'] = round($progress, 2); 
        $employees[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'role' => 'admin',
        'employees' => $employees
    ]);
} else {
    $todayLeads = getTodayLeads($conn, $loginUserId);

    $sql = "SELECT COUNT(DISTINCT CASE WHEN MONTH(created_at) = MONTH(CURDATE()) THEN id END) AS monthly_leads
            FROM leads
            WHERE owner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $loginUserId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($monthlyLeads);
    $stmt->fetch();
    $stmt->close();

    $progress = ($monthlyLeads / $monthlyTargetLeads) * 100;
    $progress = round($progress, 2);

    echo json_encode([
        'status' => 'success',
        'role' => 'employee',
        'employee' => [
            'id' => $id,
            'username' => $username,
            'cnic' => $cnic,
            'designation' => $designation,
            'role' => $role,
            'today_leads' => $todayLeads,
            'monthly_leads' => $monthlyLeads,
            'progress' => $progress
        ]
    ]);
}

$conn->close();
?>