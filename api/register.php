<?php
require_once 'db_connect.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $cnic = mysqli_real_escape_string($conn, $_POST['cnic']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Get role

    if (empty($username) || empty($cnic) || empty($designation) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit();
    }

    $check_cnic_sql = "SELECT * FROM employees WHERE cnic = '$cnic'";
    $result = mysqli_query($conn, $check_cnic_sql);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'CNIC already exists!']);
        exit();
    }

    if (strlen($cnic) == 13 && is_numeric($cnic)) {
        $secret_key = substr($cnic, -6);

        $sql = "INSERT INTO employees (username, cnic, designation, role, secret_key) VALUES ('$username', '$cnic', '$designation', '$role', '$secret_key')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'New employee registered successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CNIC!']);
    }

    mysqli_close($conn);
}
?>