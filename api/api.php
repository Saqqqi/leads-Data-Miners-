<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Karachi');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

try {
    $conn->query("SET SESSION time_zone = 'Asia/Karachi'");
} catch (Exception $e) {
    $conn->query("SET SESSION time_zone = '+00:00'");
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = $_GET['query'] ?? '';
    $type = $_GET['type'] ?? '';

    if (empty($query) || empty($type)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
        exit;
    }

    $column = ($type === 'phone') ? 'phone_numbers' : (($type === 'primaryphone') ? 'primaryNumber' : (($type === 'email') ? 'emails' : null));

    if (!$column) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM leads WHERE $column LIKE ?");

    $searchQuery = "%$query%";
    $stmt->bind_param('s', $searchQuery);

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data['count'] > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'Already exists']);
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'New entry']);
    }

    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $employeeName = isset($_POST['user_name']) && !empty($_POST['user_name']) ? $_POST['user_name'] : "Unknown"; 
    $leadName = $_POST['lead_name'] ?? null;
    $leadDate = $_POST['lead_date'] ?? null;
    $leadLocation = $_POST['lead_location'] ?? null;
    $primaryNumber = $_POST['primary_phones'] ?? null;
    $dataSourceLink = $_POST['data_source_link'] ?? null;  

    if (empty($leadName)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead name is required']);
        exit;
    }

    if (empty($leadDate)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead date is required']);
        exit;
    }

    if (empty($leadLocation)) {
        echo json_encode(['status' => 'error', 'message' => 'Lead location is required']);
        exit;
    }
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM leads WHERE name = ? AND location = ?");
    $stmt->bind_param('ss', $leadName, $leadLocation);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data['count'] > 0) {
        echo json_encode(['status' => 'error', 'message' => "Lead with the name '$leadName' in location '$leadLocation' already exists."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    
    $phoneNumbers = $_POST['phone_numbers'] ?? [];
    if (!empty($phoneNumbers)) {
        foreach ($phoneNumbers as $phone) {
            if (empty($phone)) continue; 
    
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM leads WHERE phone_numbers LIKE ?");
            $phonePattern = "%$phone%";
            $stmt->bind_param('s', $phonePattern);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
    
            if ($data['count'] > 0) {
                echo json_encode(['status' => 'error', 'message' => "Phone number $phone already exists"]);
                $stmt->close();
                exit;
            }
            $stmt->close();
        }
    }

    if (!empty($primaryNumber)) {
        $primaryNumberToCheck = is_array($primaryNumber) ? reset($primaryNumber) : $primaryNumber;
    
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM leads WHERE primaryNumber = ?");
        $stmt->bind_param('s', $primaryNumberToCheck);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        if ($data['count'] > 0) {
            echo json_encode(['status' => 'error', 'message' => "Primary phone number $primaryNumberToCheck already exists"]);
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    $emails = $_POST['emails'] ?? [];
    if (!empty($emails)) {
        foreach ($emails as $email) {
            if (empty($email)) continue; 
    
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM leads WHERE emails LIKE ?");
            $emailPattern = "%$email%";
            $stmt->bind_param('s', $emailPattern);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
    
            if ($data['count'] > 0) {
                echo json_encode(['status' => 'error', 'message' => "Email $email already exists"]);
                $stmt->close();
                exit;
            }
            $stmt->close();
        }
    }

$phoneNumbersStr = is_array($phoneNumbers) ? implode(',', $phoneNumbers) : (string)$phoneNumbers;
$emailsStr = is_array($emails) ? implode(',', $emails) : (string)$emails;
$primaryNumber = is_array($primaryNumber) ? implode(',', $primaryNumber) : (string)$primaryNumber;

$karachiTime = new DateTime("now", new DateTimeZone('Asia/Karachi'));
$createdAt = $karachiTime->format('Y-m-d H:i:s');

$insertLead = $conn->prepare("
INSERT INTO leads (owner_id, employee_name, name, date, location, primaryNumber, phone_numbers, emails, data_source_link, created_at) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$insertLead->bind_param("isssssssss", $userId, $employeeName, $leadName, $leadDate, $leadLocation, $primaryNumber, $phoneNumbersStr, $emailsStr, $dataSourceLink, $createdAt);  if ($insertLead->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database insertion failed: ' . $insertLead->error]);
    }

    $insertLead->close();
}

$conn->close();