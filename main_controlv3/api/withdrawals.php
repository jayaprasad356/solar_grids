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

include_once('../library/jwt.php');
include_once('verify-token.php'); // Include the token functions

$response = array();

// Verify token and get the authenticated user_id
$authenticated_user_id = verify_token();
if (!$authenticated_user_id) {
    return false;
}

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    echo json_encode($response);
    return;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    echo json_encode($response);
    return;
}
$date = date('Y-m-d');
function isBetween10AMand6PM() {
    $currentHour = date('H');
    $startTimestamp = strtotime('10:00:00');
    $endTimestamp = strtotime('18:00:00');
    return ($currentHour >= date('H', $startTimestamp)) && ($currentHour < date('H', $endTimestamp));
}

$user_id = $db->escapeString($_POST['user_id']);
$amount = $db->escapeString($_POST['amount']);
$datetime = date('Y-m-d H:i:s');
$dayOfWeek = date('w', strtotime($datetime));

// Ensure requested user matches authenticated user
if ($user_id != $authenticated_user_id) {
    $response['success'] = false;
    $response['message'] = "Unauthorized: Invalid User ID";
    echo json_encode($response);
    return;
}


// Fetch withdrawal settings
$sql = "SELECT * FROM settings WHERE id=1";
$db->sql($sql);
$result = $db->getResult();
if (!is_array($result) || empty($result)) {
    $response['success'] = false;
    $response['message'] = "Withdrawal settings not found";
    echo json_encode($response);
    return;
}
$min_withdrawal = $result[0]['min_withdrawal'];
$withdrawal_status = $result[0]['withdrawal_status'];

if ($withdrawal_status == 0) {
    $response['success'] = false;
    $response['message'] = "Withdrawal Disabled";
    echo json_encode($response);
    return;
}

// Fetch user data
$sql = "SELECT * FROM users WHERE id='$user_id'";
$db->sql($sql);
$res = $db->getResult();
if (!is_array($res) || empty($res)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    echo json_encode($response);
    return;
}
$balance = $res[0]['balance'];
$account_num = $res[0]['account_num'];
$withdrawal_status = $res[0]['withdrawal_status'];
$mobile = $res[0]['mobile'];
$ifsc = $res[0]['ifsc'];
$branch = $res[0]['branch'];
$bank = $res[0]['bank'];
$holder_name = $res[0]['holder_name'];

// Prepare data to send to Laravel API
$data = array(
    'user_id' => $user_id,
    'amount' => $amount,
    'mobile' => $mobile,
    'account_num' => $account_num,
    'ifsc' => $ifsc,
    'branch' => $branch,
    'bank' => $bank,
    'holder_name' => $holder_name,
    'datetime' => $datetime
);

// Send the data to the Laravel API
$apiUrl = 'https://solarpenew.solarpe.org/api/withdrawals'; // Replace with your correct Laravel API URL

// Use cURL to send the request to the Laravel API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$apiResponse = curl_exec($ch);
curl_close($ch);

if ($withdrawal_status == 0) {
    $response['success'] = false;
    $response['message'] = "Withdrawal Disabled";
    echo json_encode($response);
    return;
}

// Check for pending withdrawals
$sql = "SELECT * FROM withdrawals WHERE user_id='$user_id' AND status = 0";
$db->sql($sql);
$pendingWithdrawals = $db->getResult();
if (is_array($pendingWithdrawals) && !empty($pendingWithdrawals)) {
    $response['success'] = false;
    $response['message'] = "Please withdraw again after your pending withdrawal is paid";
    echo json_encode($response);
    return;
}

// Check if the current time is within withdrawal hours
if (!isBetween10AMand6PM()) {
    $response['success'] = false;
    $response['message'] = "Withdrawal time is between 10:00 AM to 6:00 PM";
    echo json_encode($response);
    return;
}

// Check if the current day is a weekend (Sunday or Saturday)
if ($dayOfWeek == 0 || $dayOfWeek == 7) {
    $response['success'] = false;
    $response['message'] = "Withdrawal allowed only Monday to Saturday";
    echo json_encode($response);
    return;
}

if ($amount >= $min_withdrawal) {
    if ($amount <= $balance) {
        if ($account_num == '') {
            $response['success'] = false;
            $response['message'] = "Please update your bank details";
            echo json_encode($response);
            return;
        } else {
            // Insert withdrawal request
            $sql = "INSERT INTO withdrawals (`user_id`, `amount`, `balance`, `status`, `datetime`) VALUES ('$user_id', '$amount', $balance, 0, '$datetime')";
            $db->sql($sql);

            // Update user balance
            $sql = "UPDATE users SET balance = balance - '$amount', total_withdrawal = total_withdrawal + '$amount' WHERE id='$user_id'";
            $db->sql($sql);

            // Fetch updated user data
            $sql = "SELECT * FROM users WHERE id = $user_id";
            $db->sql($sql);
            $userDetails = $db->getResult();

            // Return success response
            $response['success'] = true;
            $response['message'] = "Withdrawal Requested Successfully.";
            $response['data'] = $userDetails;
            echo json_encode($response);
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Insufficient Balance";
        echo json_encode($response);
    }
} else {
    $response['success'] = false;
    $response['message'] = "Minimum Withdrawal Amount is $min_withdrawal";
    echo json_encode($response);
}
?>
