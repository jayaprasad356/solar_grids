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

if (empty($_POST['youtuber_income_id'])) {
    $response['success'] = false;
    $response['message'] = "youtuber_income Id is Empty";
    print_r(json_encode($response));
    return false;
}
$youtuber_income_id = $db->escapeString($_POST['youtuber_income_id']);

$sql = "SELECT * FROM youtuber_income WHERE id = $youtuber_income_id ORDER BY id DESC LIMIT 10";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);

if ($num >= 1){
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['video_link'] = $row['video_link'];
        $temp['amount'] = $row['amount'];
        $temp['status'] = $row['status'];
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = " youtube income  Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "youtube income Not found";
    print_r(json_encode($response));

}
?>