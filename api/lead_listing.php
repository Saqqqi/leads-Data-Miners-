<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

date_default_timezone_set('Asia/Karachi');

$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

if (!$authHeader) {
    die(json_encode(['status' => 'error', 'message' => 'Authorization header missing'])); 
}

preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
$loginUserId = isset($matches[1]) ? $matches[1] : null;

if (!$loginUserId) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid token'])); 
}

$sql = "SELECT role FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]));
}

$stmt->bind_param("i", $loginUserId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die(json_encode(['status' => 'error', 'message' => 'Please Login '])); 
}
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

$currentDateTime = new DateTime("now", new DateTimeZone('Asia/Karachi'));
$currentHour = (int)$currentDateTime->format('H');
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
$employeeId = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : null;

if ($role !== 'admin') {     
    $employeeId = $loginUserId;   
}  

if ($filter === 'monthly') {     
    $startDate = new DateTime("first day of this month 00:00", new DateTimeZone('Asia/Karachi'));     
    $endDate = new DateTime("last day of this month 23:59", new DateTimeZone('Asia/Karachi')); 
} else {     
    if ($currentHour >= 15) { // 3 PM onwards                
        $startDate = new DateTime("today 15:00", new DateTimeZone('Asia/Karachi'));         
        $endDate = new DateTime("tomorrow 10:00", new DateTimeZone('Asia/Karachi'));     
    } else { // Before 3 PM          
        $startDate = new DateTime("yesterday 15:00", new DateTimeZone('Asia/Karachi'));         
        $endDate = new DateTime("today 10:00", new DateTimeZone('Asia/Karachi'));     
    } 
}  

$startDateFormatted = $startDate->format('Y-m-d H:i:s'); 
$endDateFormatted = $endDate->format('Y-m-d H:i:s');  

// Get total leads
$sqlTotalLeads = "SELECT COUNT(*) AS total_leads FROM leads" . ($role !== 'admin' ? " WHERE owner_id = ?" : "");
$stmtTotalLeads = $conn->prepare($sqlTotalLeads);
if ($role !== 'admin') {
    $stmtTotalLeads->bind_param("i", $employeeId);
}
$stmtTotalLeads->execute();
$stmtTotalLeads->bind_result($totalLeads);
$stmtTotalLeads->fetch();
$stmtTotalLeads->close();

// Get monthly leads
$sqlCurrentMonthLeads = "SELECT COUNT(*) AS current_month_leads FROM leads WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())" . ($role !== 'admin' ? " AND owner_id = ?" : "");
$stmtCurrentMonthLeads = $conn->prepare($sqlCurrentMonthLeads);
if ($role !== 'admin') {
    $stmtCurrentMonthLeads->bind_param("i", $employeeId);
}
$stmtCurrentMonthLeads->execute();
$stmtCurrentMonthLeads->bind_result($currentMonthLeads);
$stmtCurrentMonthLeads->fetch();
$stmtCurrentMonthLeads->close();

// Get weekly leads
$sqlCurrentWeekLeads = "SELECT COUNT(*) AS current_week_leads 
    FROM leads 
    WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL WEEKDAY(CURRENT_DATE()) DAY) 
    AND created_at < DATE_ADD(DATE_SUB(CURRENT_DATE(), INTERVAL WEEKDAY(CURRENT_DATE()) DAY), INTERVAL 7 DAY)" 
    . ($role !== 'admin' ? " AND owner_id = ?" : "");
$stmtCurrentWeekLeads = $conn->prepare($sqlCurrentWeekLeads);
if ($role !== 'admin') {
    $stmtCurrentWeekLeads->bind_param("i", $employeeId);
}
$stmtCurrentWeekLeads->execute();
$stmtCurrentWeekLeads->bind_result($currentWeekLeads);
$stmtCurrentWeekLeads->fetch();
$stmtCurrentWeekLeads->close();

// Get today's leads (shift-based: 3 PM to 10 AM next day)
$sqlTodayLeads = "SELECT COUNT(*) AS today_leads FROM leads WHERE created_at BETWEEN ? AND ?" . ($role !== 'admin' ? " AND owner_id = ?" : "");
$stmtTodayLeads = $conn->prepare($sqlTodayLeads);
if ($role !== 'admin') {
    $stmtTodayLeads->bind_param("ssi", $startDateFormatted, $endDateFormatted, $employeeId);
} else {
    $stmtTodayLeads->bind_param("ss", $startDateFormatted, $endDateFormatted);
}
$stmtTodayLeads->execute();
$stmtTodayLeads->bind_result($todayLeads);
$stmtTodayLeads->fetch();
$stmtTodayLeads->close();

$latestLeads = [];
if ($role === 'admin') {
    $sqlLatestLeads = "
        SELECT 
            l.owner_id,
            MAX(l.created_at) AS latest_lead_time,
            e.username AS employee_name
        FROM leads l
        JOIN employees e ON l.owner_id = e.id
        WHERE l.created_at BETWEEN ? AND ?
        GROUP BY l.owner_id, e.username";
    $stmtLatestLeads = $conn->prepare($sqlLatestLeads);
    $stmtLatestLeads->bind_param("ss", $startDateFormatted, $endDateFormatted);
} else {
    $sqlLatestLeads = "
        SELECT 
            l.owner_id,
            MAX(l.created_at) AS latest_lead_time,
            e.username AS employee_name
        FROM leads l
        JOIN employees e ON l.owner_id = e.id
        WHERE l.created_at BETWEEN ? AND ? AND l.owner_id = ?
        GROUP BY l.owner_id, e.username";
    $stmtLatestLeads = $conn->prepare($sqlLatestLeads);
    $stmtLatestLeads->bind_param("ssi", $startDateFormatted, $endDateFormatted, $employeeId);
}

$stmtLatestLeads->execute();
$resultLatestLeads = $stmtLatestLeads->get_result();

while ($row = $resultLatestLeads->fetch_assoc()) {
    // Convert the lead time to Asia/Karachi timezone
    $leadTime = new DateTime($row['latest_lead_time'], new DateTimeZone('Asia/Karachi'));
    $currentDateTime = new DateTime("now", new DateTimeZone('Asia/Karachi'));
    $interval = $currentDateTime->diff($leadTime);

    $timeAgo = '';
    if ($interval->y > 0) {
        $timeAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
    } elseif ($interval->m > 0) {
        $timeAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
    } elseif ($interval->d > 0) {
        $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
    } elseif ($interval->h > 0) {
        // Include hours and minutes
        $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '');
        if ($interval->i > 0) {
            $timeAgo .= ' and ' . $interval->i . ' minute' . ($interval->i > 1 ? 's' : '');
        }
        $timeAgo .= ' ago';
    } elseif ($interval->i > 0) {
        $timeAgo = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
    } else {
        // For "Just now", check if the difference is less than 1 minute
        $secondsDiff = $currentDateTime->getTimestamp() - $leadTime->getTimestamp();
        if ($secondsDiff < 60) {
            $timeAgo = 'Just now';
        } else {
            $timeAgo = '1 minute ago';
        }
    }

    $latestLeads[$row['owner_id']] = [
        'employee_name' => $row['employee_name'],
        'latest_lead_time' => $leadTime->format('Y-m-d H:i:s'),
        'time_ago' => $timeAgo
    ];
}
$stmtLatestLeads->close();

$targetTotalLeads = 220;   
$targetWeeklyLeads = 55;   
$targetDailyLeads = 8;     

$progressTotalLeads = ($totalLeads / $targetTotalLeads) * 100; 
$progressWeeklyLeads = ($currentWeekLeads / $targetWeeklyLeads) * 100; 
$progressDailyLeads = ($todayLeads / $targetDailyLeads) * 100;  

$progressTotalLeads = min($progressTotalLeads, 100); 
$progressWeeklyLeads = min($progressWeeklyLeads, 100); 
$progressDailyLeads = min($progressDailyLeads, 100);    

// Main Leads Query (only for admins to see detailed data)
$employees = [];
if ($role === 'admin') {
    $sql = "     
        SELECT          
            leads.owner_id,          
            leads.name AS lead_name,          
            leads.date AS lead_date,          
            leads.location AS lead_location,          
            leads.phone_numbers,          
            leads.primaryNumber,         
            leads.emails,          
            leads.data_source_link,          
            leads.created_at,            
            employees.username AS employee_name,         
            employees.designation     
        FROM leads     
        JOIN employees ON leads.owner_id = employees.id     
        WHERE leads.created_at BETWEEN ? AND ?" . 
        ($employeeId ? " AND leads.owner_id = ?" : "");

    $stmt = $conn->prepare($sql);
    if ($employeeId) {
        $stmt->bind_param("ssi", $startDateFormatted, $endDateFormatted, $employeeId);
    } else {
        $stmt->bind_param("ss", $startDateFormatted, $endDateFormatted);
    }

    if (!$stmt) {
        die(json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]));
    }

    if (!$stmt->execute()) {
        die(json_encode(['status' => 'error', 'message' => 'Failed to execute SQL statement: ' . $stmt->error]));
    }

    $result = $stmt->get_result();

    if (!$result) {
        die(json_encode(['status' => 'error', 'message' => 'Failed to fetch result: ' . $stmt->error]));
    }

    while ($row = $result->fetch_assoc()) {
        $ownerId = $row['owner_id'];

        if (!isset($employees[$ownerId])) {
            $employees[$ownerId] = [
                'owner_id' => $row['owner_id'],
                'employee_name' => $row['employee_name'],
                'designation' => $row['designation'],
                'lead_count' => 0,
                'today_leads' => [],
                'latest_lead' => isset($latestLeads[$ownerId]) ? $latestLeads[$ownerId] : null
            ];
        }

        $employees[$ownerId]['lead_count']++;

        $createdAt = $row['created_at'];
        $leadDateTime = new DateTime($createdAt, new DateTimeZone('Asia/Karachi'));
        $leadHour = (int)$leadDateTime->format('H');
        $leadDate = $leadDateTime->format('Y-m-d');

        if ($leadHour >= 15) {
            $leadDate = $leadDateTime->format('Y-m-d');
        } elseif ($leadHour < 10) {
            $leadDate = $leadDateTime->modify('-1 day')->format('Y-m-d');
        }

        $employees[$ownerId]['today_leads'][] = [
            'lead_name' => $row['lead_name'],
            'lead_location' => $row['lead_location'],
            'lead_date' => $leadDate,
            'lead_time' => $leadDateTime->format('H:i:s'),
            'phone_numbers' => $row['phone_numbers'],
            'primaryNumber' => $row['primaryNumber'],
            'emails' => $row['emails'],
            'data_source_link' => html_entity_decode($row['data_source_link'] ?? ''),
        ];
    }

    $stmt->close();
} else {
    // For employees, only fetch their basic info and lead count
    $sql = "SELECT username, designation FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $employees[$employeeId] = [
        'owner_id' => $employeeId,
        'employee_name' => $row['username'],
        'designation' => $row['designation'],
        'lead_count' => $todayLeads,
        'latest_lead' => isset($latestLeads[$employeeId]) ? $latestLeads[$employeeId] : null
    ];
    $stmt->close();
}

$conn->close();

echo json_encode([
    'status' => 'success',
    'message' => 'Leads fetched successfully',
    'date_range' => [
        'start' => $startDateFormatted,
        'end' => $endDateFormatted
    ],
    'total_leads' => $totalLeads, 
    'current_month_leads' => $currentMonthLeads,
    'current_week_leads' => $currentWeekLeads,
    'today_leads' => $todayLeads,
    'progress_total_leads' => round($progressTotalLeads, 2),
    'progress_weekly_leads' => round($progressWeeklyLeads, 2),
    'progress_daily_leads' => round($progressDailyLeads, 2), 
    'data' => array_values($employees)
]);
?>