<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
} else {
    return false;
    exit(0);
}

// Fetch the existing record from the database
$sql_query = "SELECT * FROM youtuber_income WHERE id = $ID";
$db->sql($sql_query);
$res = $db->getResult();

$user_id = $res[0]['user_id'];

// Initialize error array and set a default value for $update_result
$error = array();
$update_result = 0; // Default initialization

if (isset($_POST['btnEdit'])) {
    $link = $db->escapeString($_POST['link']);
    $amount = $db->escapeString($_POST['amount']);
    $status = $db->escapeString($_POST['status']);

    // Fetch the existing record again after POST
    $sql_query = "SELECT * FROM youtuber_income WHERE id = $ID";
    $db->sql($sql_query);
    $res = $db->getResult();
    $existing_status = $res[0]['status'];

    // Initialize error variable
    $error_message = "";

    // Handle status update logic
    if ($existing_status == 1 && $status != 1) {
        // If status is already Paid and attempting to change it
        $error_message = "<section class='content-header'><span class='label label-danger'>This income is already marked as 'Paid'. Status cannot be changed.</span></section>";
    } elseif ($existing_status == 1 && $status == 1 && $amount == $res[0]['amount'] && $link == $res[0]['link']) {
        // No change in data; avoid unnecessary updates
        $error_message = "<section class='content-header'><span class='label label-danger'>This income is already marked as 'Paid'.</span></section>";
    } elseif ($status == 2 && $existing_status == 1) {
        // Prevent setting status to 2 if already Paid
        $error_message = "<section class='content-header'><span class='label label-danger'>This income is already marked as 'Paid'. Cannot change status to 'Rejected'.</span></section>";
    } elseif ($amount <= 0) {
        // Check if amount is valid (greater than 0)
        $error_message = "<section class='content-header'><span class='label label-danger'>Amount must be greater than 0</span></section>";
    }

    // Display the first encountered error
    if (!empty($error_message)) {
        $error['update_jobs'] = $error_message;
    } else {
        // Proceed with the update if no error
        $datetime = date("Y-m-d H:i:s"); // Current datetime
        $sql_query = "UPDATE youtuber_income SET link='$link', amount='$amount', status='$status', datetime='$datetime' WHERE id = $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();
        if (!empty($update_result)) {
			$update_result = 0;
		} else {
			$update_result = 1;
		}

        // If the status is 'Paid', update the user's wallet and income
        if ($status == 1) {
            $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$user_id', '$amount', '$datetime', 'youtube_income')";
            $db->sql($sql);

            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $amount, today_income = today_income + $amount, total_income = total_income + $amount WHERE id = $user_id";
            $db->sql($sql);
        }

        if ($update_result == 1) {
            $error['update_jobs'] = "<section class='content-header'><span class='label label-success'>Income updated successfully</span></section>";
            ?>
            <!-- <script>
                window.location.href = "youtube_income.php";
            </script> -->
            <?php
        } 
    }
}


$data = array();

$sql_query = "SELECT * FROM youtuber_income WHERE id =" . $ID;
$db->sql($sql_query);
$res = $db->getResult();
// Fetch user details using user_id
$sql_query_user = "SELECT id, name, mobile FROM users WHERE id = $user_id";
$db->sql($sql_query_user);
$user_details = $db->getResult();
$user_name = isset($user_details[0]['name']) ? $user_details[0]['name'] : '';
$user_mobile = isset($user_details[0]['mobile']) ? $user_details[0]['mobile'] : '';

if (isset($_POST['btnCancel'])) { ?>
    <script>
        window.location.href = "youtube_income.php";
    </script>
<?php } ?>

<section class="content-header">
    <h1>
        Edit Youtube Income
        <small><a href='youtube_income.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Youtube Income</a></small>
    </h1>

    <small>
        <?php 
            if (isset($error['update_jobs'])) {
                echo $error['update_jobs'];
            }
            if (isset($error['amount'])) {
                echo $error['amount'];
            }
            if (isset($error['status'])) {
                echo $error['status'];
            }
        ?>
    </small>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
</section>

<section class="content">
    <!-- Main row -->
    <div class="row">
        <div class="col-md-10">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border"></div><!-- /.box-header -->
                <!-- form start -->
                <form id="edit_languages_form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">User Details</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="user_details" value="<?php echo $res[0]['user_id'] . ' - ' . $user_name . ' - ' . $user_mobile; ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Link</label><i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="link" value="<?php echo $res[0]['link']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="exampleInputEmail1">Amount</label><i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="amount" value="<?php echo $res[0]['amount']; ?>">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                            <div id="status" class="btn-group">
                                <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Wait for Approvals
                                </label>
                                <label class="btn btn-success" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="1" <?= ($res[0]['status'] == 1) ? 'checked' : ''; ?>> Paid
                                </label>
                                <label class="btn btn-danger" data-toggle-class="btn-default" data-toggle-passive-class="btn-default">
                                    <input type="radio" name="status" value="2" <?= ($res[0]['status'] == 2) ? 'checked' : ''; ?>> Cancelled
                                </label>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
                    </div>
                </form>
            </div><!-- /.box -->
        </div>
    </div>
</section>

<div class="separator"></div>
<?php $db->disconnect(); ?>
