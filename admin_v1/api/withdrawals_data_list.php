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

$sql = "SELECT w.id, w.amount, w.status, w.datetime, u.name
FROM withdrawals w
JOIN users u ON w.user_id = u.id
WHERE w.status = 1
ORDER BY w.id DESC
LIMIT 30;";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        $temp['amount'] = $row['amount'];
        $temp['status'] = $row['status'];
        $temp['datetime'] = $row['datetime'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Withdrawals Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Withdrawals Not found";
    print_r(json_encode($response));
}
?>
