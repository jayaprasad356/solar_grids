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

// Verify token and get the authenticated user_id
$authenticated_user_id = verify_token();
if (!$authenticated_user_id) {
    return false;
}

    if (empty($_POST['user_id'])) {
        $response['success'] = false;
        $response['message'] = "User Id is Empty";
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

$sql = "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY id DESC LIMIT 10";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);

if ($num >= 1){
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['amount'] = $row['amount'];
        $temp['ads'] = $row['ads'];
        $temp['type'] = $row['type'];
        $temp['datetime'] = $row['datetime'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Transactions Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Transactions Not found";
    print_r(json_encode($response));

}
?>