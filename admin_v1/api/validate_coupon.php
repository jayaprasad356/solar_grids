<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');

$db = new Database();
$db->connect();

// Check if plan_id is provided
if (empty($_POST['plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Plan Id is Empty";
    print_r(json_encode($response));
    return false;
}

$plan_id = $db->escapeString($_POST['plan_id']);

// Check if coupon_code is provided
if (empty($_POST['coupon_code'])) {
    $response['success'] = false;
    $response['message'] = "Coupon Code is Empty";
    print_r(json_encode($response));
    return false;
}

$coupon_code = $db->escapeString($_POST['coupon_code']);

// Modify SQL query to check both plan_id and coupon_code
$sql = "SELECT * FROM coupon_code WHERE plan_id = $plan_id AND coupon_code = '$coupon_code'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    // Fetch the plan price from your plan table
    $sql_plan = "SELECT price FROM plan WHERE id = $plan_id"; // Assuming `plans` table contains `plan_price`
    $db->sql($sql_plan);
    $plan_res = $db->getResult();
    $price = 0;

    if ($plan_res) {
        $price = $plan_res[0]['price'];
    }

    // Assuming the coupon gives a discount (amount) on the plan price
    $discount_amount = $res[0]['amount'];
    $final_price = $price - $discount_amount; // Subtract the coupon amount from the original price

    // Ensure the final price doesn't go below zero
    if ($final_price < 0) {
        $final_price = 0;
    }

    // Prepare response data
    $response['success'] = true;
    $response['message'] = "Coupon Code and Plan ID Matched Successfully";
    $response['data'] = [
        'plan_id' => $plan_id,
        'original_price' => $price,
        'discount_amount' => $discount_amount,
        'final_price' => $final_price,
    ];
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Coupon Code or Plan ID Not Found or Do Not Match";
    print_r(json_encode($response));
}
?>
