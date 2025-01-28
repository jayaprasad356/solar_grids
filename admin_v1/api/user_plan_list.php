<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
include_once('../library/jwt.php');
include_once('verify-token.php'); // Include the token functions

$db = new Database();
$db->connect();

$response = array();

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

// Ensure requested user matches authenticated user
if ($user_id != $authenticated_user_id) {
    $response['success'] = false;
    $response['message'] = "Unauthorized: Invalid User ID";
    echo json_encode($response);
    return;
}

$sql = "SELECT valid FROM users WHERE id = $user_id ";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT user_plan.* ,plan.name,plan.image,plan.price,plan.daily_earnings,plan.invite_bonus,plan.monthly_earnings,plan.quantity
        FROM user_plan 
        LEFT JOIN plan ON user_plan.plan_id = plan.id
        WHERE user_plan.user_id = '$user_id' AND user_plan.inactive = 0";

$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num >= 1) {
    foreach ($res as &$job) {
        $imagePath = $job['image'];
        $imageURL = DOMAIN_URL . $imagePath;
        $job['image'] = $imageURL;

       
    }

    $response['success'] = true;
    $response['message'] = "User Plan Details Retrieved Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Plan Not found";
    print_r(json_encode($response));

}
?>