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

if (empty($_POST['youtuber_income_id'])) {
    $response['success'] = false;
    $response['message'] = "youtuber_income ID is Empty";
    echo json_encode($response);
    return;
}
if (empty($_POST['video_link'])) {
    $response['success'] = false;
    $response['message'] = "Video link is Empty";
    echo json_encode($response);
    return;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    echo json_encode($response);
    return;
}
if (empty($_POST['status'])) {
    $response['success'] = false;
    $response['message'] = "Status is Empty";
    echo json_encode($response);
    return;
}

$youtuber_income_id = $db->escapeString($_POST['youtuber_income_id']);
$video_link = $db->escapeString($_POST['video_link']);
$amount = $db->escapeString($_POST['amount']);
$status = $db->escapeString($_POST['status']);

$sql = "INSERT INTO youtuber_income (video_link, amount, status) VALUES ('$video_link', '$amount', '$status')";
if ($db->sql($sql)) {
    $response['success'] = true;
    $response['message'] = "Data inserted successfully";
} else {
    $response['success'] = false;
    $response['message'] = "Failed to insert data";
}

echo json_encode($response);
?>
