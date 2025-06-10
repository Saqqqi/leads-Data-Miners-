<?php
header('Content-Type: application/json');


require_once 'db_connect.php';

$userId = isset($_GET['userId']) ? $_GET['userId'] : (isset($_POST['userId']) ? $_POST['userId'] : null);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
       
        $stmt = $conn->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'error' => 'Failed to delete lead']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    }
    exit;
}

$checkRoleQuery = "SELECT role FROM employees WHERE id = ?";
$stmt = $conn->prepare($checkRoleQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData || $userData['role'] !== 'admin') {
    echo json_encode([
        'error' => "Access denied: Admin privileges required",
        'status' => 'error'
    ]);
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function normalizePhone($phone) {
    return preg_replace('/[^0-9]/', '', trim($phone));
}

function normalizeEmail($email) {
    return strtolower(trim($email));
}

function getEmployeeUsername($conn, $owner_id) {
    $sql = "SELECT username FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['username'] : 'N/A';
}

$phone_search = '';
$email_search = '';
$phone_results = [];
$email_results = [];
$notification = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['phone_search']) && !empty($_POST['phone_search'])) {
        $phone_search = normalizePhone($_POST['phone_search']);
        $raw_phone_search = trim($_POST['phone_search']);
        error_log("Normalized phone search: $phone_search, Raw input: $raw_phone_search");

        $debug_sql = "SELECT phone_numbers, primaryNumber FROM leads WHERE owner_id IS NOT NULL";
        $debug_result = $conn->query($debug_sql);
        while ($row = $debug_result->fetch_assoc()) {
            error_log("Debug - phone_numbers: " . $row['phone_numbers'] . ", primaryNumber: " . $row['primaryNumber']);
        }

        $sql = "SELECT * FROM leads 
                WHERE owner_id IS NOT NULL 
                AND (
                    REGEXP_REPLACE(phone_numbers, '[^0-9]', '') LIKE CONCAT('%', ?, '%')
                    OR REGEXP_REPLACE(primaryNumber, '[^0-9]', '') LIKE CONCAT('%', ?, '%')
                    OR FIND_IN_SET(?, REGEXP_REPLACE(REPLACE(phone_numbers, '\n', ','), '[^0-9,]', '')) > 0
                    OR FIND_IN_SET(?, REGEXP_REPLACE(REPLACE(primaryNumber, '\n', ','), '[^0-9,]', '')) > 0
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $phone_search, $phone_search, $phone_search, $phone_search);
        $stmt->execute();
        $phone_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (!empty($phone_results)) {
            foreach ($phone_results as $result) {
                error_log("Found - phone_numbers: " . $result['phone_numbers'] . ", primaryNumber: " . $result['primaryNumber']);
            }
        } else {
            error_log("No results found for: $phone_search");
        }

        foreach ($phone_results as &$result) {
            $result['employee'] = getEmployeeUsername($conn, $result['owner_id']);
        }
        unset($result);

        if (empty($phone_results)) {
            $notification = json_encode([
                'text' => 'No phone number results found',
                'duration' => 3000,
                'gravity' => 'top',
                'position' => 'right',
                'backgroundColor' => '#ef4444'
            ]);
        }
    }

    if (isset($_POST['email_search']) && !empty($_POST['email_search'])) {
        $email_search = normalizeEmail($_POST['email_search']);
        error_log("Email search: $email_search");

        $sql = "SELECT * FROM leads 
                WHERE owner_id IS NOT NULL 
                AND (
                    emails LIKE CONCAT('%', ?, '%') 
                    OR FIND_IN_SET(?, REPLACE(REPLACE(emails, '\n', ','), ' ', ',')) > 0
                )";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email_search, $email_search);
        $stmt->execute();
        $email_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (!empty($email_results)) {
            foreach ($email_results as $result) {
                error_log("Found emails: " . $result['emails']);
            }
        } else {
            error_log("No results from email search for: $email_search");
        }

        if (empty($email_results)) {
            $sql = "SELECT * FROM leads 
                    WHERE owner_id IS NOT NULL 
                    AND TRIM(emails) LIKE ?";
            $stmt = $conn->prepare($sql);
            $search_term = "%$email_search%";
            $stmt->bind_param("s", $search_term);
            $stmt->execute();
            $email_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            error_log("Fallback email search with: $email_search, Results count: " . count($email_results));
        }

        foreach ($email_results as &$result) {
            $result['employee'] = getEmployeeUsername($conn, $result['owner_id']);
        }
        unset($result);

        if (empty($email_results)) {
            $notification = json_encode([
                'text' => 'No email results found',
                'duration' => 3000,
                'gravity' => 'top',
                'position' => 'right',
                'backgroundColor' => '#ef4444'
            ]);
        }
    }

    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        $sql = "SELECT emails, phone_numbers FROM leads WHERE id = ? AND owner_id IS NOT NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {

            $sql = "DELETE FROM leads WHERE id = ? AND owner_id IS NOT NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $delete_id);
            
            if ($stmt->execute()) {
  
                $phone_results = [];
                $email_results = [];
                
                $notification = json_encode([
                    'text' => 'Lead deleted successfully',
                    'duration' => 3000,
                    'gravity' => 'top',
                    'position' => 'right',
                    'backgroundColor' => '#22c55e'
                ]);
            }
        }
    }

    $response = [
        'phone_results' => $phone_results,
        'email_results' => $email_results,
        'phone_search' => $phone_search,
        'email_search' => $email_search,
        'notification' => $notification
    ];
    echo json_encode($response);
    $conn->close();
    exit();
}

$conn->close();
?>