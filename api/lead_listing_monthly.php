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
$loginUserId = isset($matches[1]) ? (int)$matches[1] : null;

if (!$loginUserId) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid token']));
}

$sql = "SELECT role FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loginUserId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die(json_encode(['status' => 'error', 'message' => 'User not found']));
}

$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

$employeeId = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : null;
if ($role !== 'admin' && $employeeId !== $loginUserId) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'this_month';

if ($filter === 'yesterday') {
    $startDate = new DateTime("yesterday 17:00:00");
    $endDate = new DateTime("today 09:00:00");
} elseif ($filter === 'today') {
    $startDate = new DateTime("today 09:00:00");
    $endDate = new DateTime("tomorrow 09:00:00");
} elseif ($filter === 'last_month') {
    $startDate = new DateTime("first day of last month 17:00:00");
    $endDate = new DateTime("last day of last month 09:00:00");
} elseif ($filter === 'custom_range' && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = new DateTime($_GET['start_date'] . " 00:00:00"); // Start of the day
    $endDate = new DateTime($_GET['end_date'] . " 23:59:59");     // End of the day
} else {
    $startDate = new DateTime("first day of this month 17:00:00");
    $endDate = new DateTime("last day of this month 09:00:00");
}
$startDateFormatted = $startDate->format('Y-m-d H:i:s');
$endDateFormatted = $endDate->format('Y-m-d H:i:s');

$userSql = "SELECT id, username, designation FROM employees WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $employeeId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userStmt->close();

if (!$user) {
    die(json_encode(['status' => 'error', 'message' => 'Employee not found']));
}

$leadSql = "SELECT name, location, phone_numbers, emails,primaryNumber, created_at 
            FROM leads 
            WHERE owner_id = ? AND created_at BETWEEN ? AND ?";
$leadStmt = $conn->prepare($leadSql);
$leadStmt->bind_param("iss", $employeeId, $startDateFormatted, $endDateFormatted);
$leadStmt->execute();
$leadResult = $leadStmt->get_result();

$leads = [];
while ($lead = $leadResult->fetch_assoc()) {
    $leads[] = [
        'lead_name' => $lead['name'],
        'lead_date' => date('Y-m-d', strtotime($lead['created_at'])),
        'lead_location' => $lead['location'],
        'phone_numbers' => $lead['phone_numbers'],
        'emails' => $lead['emails'],
'primaryNumber' => $lead['primaryNumber']

    ];
}   

$leadStmt->close();
$conn->close();

echo json_encode([
    'status' => 'success',
    'message' => 'Leads fetched successfully',
    'data' => [
        'employee_name' => $user['username'],
        'designation' => $user['designation'],
        'lead_count' => count($leads),
        'leads' => $leads
    ]
]);
?>