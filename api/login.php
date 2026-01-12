<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $secret_key = mysqli_real_escape_string($conn, $_POST['secret-key']);

    $sql = "SELECT id, username, designation FROM employees WHERE secret_key = '$secret_key'";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);

        $id = $employee['id'];  
        $username = $employee['username'];
        $designation = $employee['designation'];

        $response = array(
            'status' => 'success',
            'message' => 'Login successful.',
            'id' => $id,  
            'username' => $username,
            'designation' => $designation
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Invalid Secret Key.'
        );
    }

    echo json_encode($response);
}

mysqli_close($conn);
?>