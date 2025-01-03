<?php
if (isset($_POST['plan_id']) && isset($_POST['coupon_code'])) {
    $plan_id = $_POST['plan_id'];
    $coupon_code = $_POST['coupon_code'];

    // Your existing code to validate the coupon code
    // Query the database to check if the plan_id and coupon_code match
    $sql = "SELECT * FROM coupon_code WHERE plan_id = $plan_id AND coupon_code = '$coupon_code'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);

    if ($num >= 1) {
        // Coupon found, return success response
        $response['success'] = true;
        $response['message'] = "Coupon applied successfully!";
        $response['data'] = $res[0]; // Assuming `amount` is part of the result
    } else {
        // Coupon not found
        $response['success'] = false;
        $response['message'] = "Invalid coupon code!";
    }

    echo json_encode($response);
}
?>
