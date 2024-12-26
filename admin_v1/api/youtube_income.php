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
$datetime = date('Y-m-d H:i:s');
// Check if the link already exists
$sql = "SELECT id FROM youtuber_income WHERE link = '$link'";
$db->sql($sql);
$res = $db->getResult();
if (count($res) > 0) {
    $response['success'] = false;
    $response['message'] = "link already exists";
    echo json_encode($response);
    return;
}
// Insert the data
$sql = "INSERT INTO youtuber_income (user_id, link, amount, status, datetime) VALUES ('$user_id', '$link', 0 , 0, '$datetime')";
$db->sql($sql);
$res = $db->getResult();
$response['success'] = true;
$response['message'] = "Youtube link added Successfully";
print_r(json_encode($response));
?>