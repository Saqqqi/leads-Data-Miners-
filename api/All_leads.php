<?php
header("Content-Type: application/json; charset=UTF-8");

// Database configuration
require_once 'db_connect.php';

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Get user ID from header
$userId = isset($_SERVER['HTTP_USER_ID']) ? $_SERVER['HTTP_USER_ID'] : null;

// Function to check user role
function getUserRole($conn, $userId) {
    if (!$userId) {
        return null;
    }
    
    $query = "SELECT role FROM employees WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $row ? $row['role'] : null;
}

// Function to get Daily Leads (summary)
function getDailyLeads($conn) {
    $query = "SELECT l.date, COUNT(*) AS daily_leads 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE l.owner_id IS NOT NULL 
              GROUP BY l.date 
              ORDER BY l.date ASC";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get Total Leads (summary)
function getTotalLeads($conn) {
    $query = "SELECT COUNT(*) AS total_leads 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE l.owner_id IS NOT NULL";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get Weekly Leads (summary)
function getWeeklyLeads($conn) {
    $query = "SELECT YEAR(l.date) AS year, WEEK(l.date) AS week_number, COUNT(*) AS weekly_leads 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE l.owner_id IS NOT NULL 
              GROUP BY YEAR(l.date), WEEK(l.date) 
              ORDER BY year, week_number ASC";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get Monthly Leads (summary)
function getMonthlyLeads($conn) {
    $query = "SELECT YEAR(l.date) AS year, MONTH(l.date) AS month_number, MONTHNAME(l.date) AS month_name, COUNT(*) AS monthly_leads 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE l.owner_id IS NOT NULL 
              GROUP BY YEAR(l.date), MONTH(l.date) 
              ORDER BY year, month_number ASC";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get Leads by Specific Date (all details)
function getLeadsByDate($conn, $date) {
    $query = "SELECT l.owner_id, e.username AS owner_name, l.name, l.date, l.location, l.phone_numbers, l.emails, l.employee_name, l.data_source_link, l.primaryNumber 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE l.date = ? AND l.owner_id IS NOT NULL 
              ORDER BY e.username ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get Leads by Specific Month (all details)
function getLeadsByMonth($conn, $year, $month) {
    $query = "SELECT l.owner_id, e.username AS owner_name, l.name, l.date, l.location, l.phone_numbers, l.emails, l.employee_name, l.data_source_link, l.primaryNumber 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE YEAR(l.date) = ? AND MONTH(l.date) = ? AND l.owner_id IS NOT NULL 
              ORDER BY e.username ASC"; 
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $year, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get Leads by Specific Week (all details)
function getLeadsByWeek($conn, $year, $week) {
    $query = "SELECT l.owner_id, e.username AS owner_name, l.name, l.date, l.location, l.phone_numbers, l.emails, l.employee_name, l.data_source_link, l.primaryNumber 
              FROM leads l 
              JOIN employees e ON l.owner_id = e.id 
              WHERE YEAR(l.date) = ? AND WEEK(l.date) = ? AND l.owner_id IS NOT NULL 
              ORDER BY e.username ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $year, $week);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Check user role
$userRole = getUserRole($conn, $userId);

if (!$userRole) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Authentication required"]);
    exit;
}

// Handle Requests
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'daily':
    case 'weekly':
    case 'monthly':
        if ($userRole !== 'admin') {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "Access denied: Admin privileges required"]);
            break;
        }
        if ($action === 'daily') {
            echo json_encode(getDailyLeads($conn));
        } elseif ($action === 'weekly') {
            echo json_encode(getWeeklyLeads($conn));
        } elseif ($action === 'monthly') {
            echo json_encode(getMonthlyLeads($conn));
        }
        break;
    
    case 'total':
        echo json_encode(getTotalLeads($conn));
        break;
    
    case 'by_date':
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        echo json_encode(getLeadsByDate($conn, $date));
        break;
    
    case 'by_month':
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        echo json_encode(getLeadsByMonth($conn, $year, $month));
        break;
    
    case 'by_week':
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $week = isset($_GET['week']) ? $_GET['week'] : 1;
        echo json_encode(getLeadsByWeek($conn, $year, $week));
        break;
    
    default:
        echo json_encode(["error" => "Invalid action"]);
        break;
}

// Close the connection
mysqli_close($conn);
?>