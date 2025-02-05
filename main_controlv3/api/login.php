<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kolkata');

include_once('../includes/crud.php');
include_once('../library/jwt.php');
include_once('verify-token.php'); // Include the token functions

$db = new Database();
$db->connect();

$response = array();

if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    echo json_encode($response);
    return;
}

$mobile = $db->escapeString($_POST['mobile']);
$password = $db->escapeString($_POST['password']);

$sql = "SELECT * FROM users WHERE mobile = '$mobile'";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "Your Mobile Number is not Registered";
    echo json_encode($response);
    return;
}

$password = md5($password);
$sql = "SELECT * FROM users WHERE mobile = '$mobile' AND password = '$password'";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "Your Password is Incorrect";
    echo json_encode($response);
    return;
}

$blocked = $user[0]['blocked'];

if ($blocked == 1) {
    $response['success'] = false;
    $response['message'] = "Your Account is Blocked";
    echo json_encode($response);
    return;
}

// Generate JWT token
$token = generate_token($user[0]['id'], $user[0]['mobile']); // Pass the user data to generate the token

$user_id = $user[0]['id'];
$type = 'login';
$datetime = date('Y-m-d H:i:s');

$sql = "INSERT INTO tracking (user_id, type, datetime) VALUES ('$user_id', '$type', '$datetime')";
$db->sql($sql);

// Return response with token
$response['success'] = true;
$response['registered'] = true;
$response['message'] = "Logged In Successfully";
$response['token'] = $token; // Include JWT token
$response['data'] = $user;

echo json_encode($response);
?>
