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
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
$db = new Database();
$db->connect();

$response = array();

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "user_id is Empty";
    echo json_encode($response);
    return;
}
if (empty($_POST['link'])) {
    $response['success'] = false;
    $response['message'] = "link is Empty";
    echo json_encode($response);
    return;
}


$user_id = $db->escapeString($_POST['user_id']);
$link = $db->escapeString($_POST['link']);
$amount = $db->escapeString($_POST['amount']);


$sql = "INSERT INTO youtuber_income (user_id, link, amount, status) VALUES ('$user_id', '$link', '$amount', 0)";
if ($db->sql($sql)) {
    $response['success'] = true;
    $response['message'] = "Data inserted successfully";
} else {
    $response['success'] = false;
    $response['message'] = "Failed to insert data";
}

echo json_encode($response);
?>
