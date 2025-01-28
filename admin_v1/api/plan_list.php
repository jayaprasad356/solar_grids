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

$db = new Database();
$db->connect();
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
include_once('verify-token.php');
$fn = new functions;

// Verify token and get the authenticated user_id
$authenticated_user_id = verify_token();
if (!$authenticated_user_id) {
    return false;
}

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$category = isset($_POST['category']) ? $db->escapeString($_POST['category']) : '';

if ($user_id != $authenticated_user_id) {
    $response['success'] = false;
    $response['message'] = "Unauthorized: Invalid User ID";
    echo json_encode($response);
    return;
}

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

if (!empty($category)) {
    $sql = "SELECT * FROM plan WHERE category = '$category'";
} else {
    $sql = "SELECT * FROM plan";
}
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $plan_id = $res;
    $category = $plan_id[0]['category'];

    $sql = "SELECT * FROM plan WHERE category = '$category'";
    $db->sql($sql);
}

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        // Remove all HTML tags except for <br>
        $temp['description'] = strip_tags_except($row['description'], array('br'));
        $temp['image'] = DOMAIN_URL . $row['image'];
        $temp['daily_earnings'] = $row['daily_earnings'];
        $temp['monthly_earnings'] = $row['monthly_earnings'];
        $temp['invite_bonus'] = $row['invite_bonus'];
        $temp['price'] = $row['price'];
        $temp['quantity'] = $row['quantity'];
        $temp['category'] = $row['category'];
        
        $plan_id = $row['id'];
        $sql_check_plan = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
        $db->sql($sql_check_plan);
        $plan_exists = $db->numRows() > 0;
        $temp['status'] = $plan_exists ? 1 : 0;
        
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Plan Details Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    print_r(json_encode($response));
}

function strip_tags_except($string, $exceptions = array()) {
    foreach ($exceptions as $tag) {
        $string = str_replace("<$tag>", "#{$tag}#", $string);
        $string = str_replace("</$tag>", "#/{$tag}#", $string);
    }
    // Remove HTML tags and their attributes
    // Remove \r\n characters
    $string = str_replace(array("\r", "\n"), '', $string);
    $string = strip_tags($string);
    // Decode HTML entities to symbols
    $string = html_entity_decode($string);
    foreach ($exceptions as $tag) {
        $string = str_replace("#{$tag}#", "<$tag>", $string);
        $string = str_replace("#/{$tag}#", "</$tag>", $string);
    }
    return $string;
}

?>

