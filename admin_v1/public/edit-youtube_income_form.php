<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php

if (isset($_GET['id'])) {
	$ID = $db->escapeString($_GET['id']);
} else {
	// $ID = "";
	return false;
	exit(0);
}
if (isset($_POST['btnEdit'])) {
    $user_id = $db->escapeString(($_POST['user_id']));
    $link = $db->escapeString(($_POST['link']));
    $amount = $db->escapeString(($_POST['amount']));
    $status = $db->escapeString(($_POST['status']));
   
    //$refer_bonus_sent = $db->escapeString(($_POST['refer_bonus_sent']));
    //$refer_user_id = $db->escapeString(($_POST['refer_user_id']));
    $error = array();

    // Check if user status is 1
   
        $sql_query = "UPDATE youtuber_income SET user_id='$user_id',link='$link',amount='$amount',status='$status' WHERE id =  $ID";
        $db->sql($sql_query);
        $update_result = $db->getResult();

        // Check update result
        if (!empty($update_result)) {
            $error['update_jobs'] = "<section class='content-header'><span class='label label-danger'>Failed to Update</span></section>";
        } else {
            $error['update_jobs'] = "<section class='content-header'><span class='label label-success'>Youtube Income updated Successfully</span></section>";
        }
    }


// Fetch user plan data
$data = array();
$sql_query = "SELECT * FROM youtuber_income WHERE id = $ID";
$db->sql($sql_query);
$res = $db->getResult();

$user_id = $res[0]['user_id'];
$sql_query_user = "SELECT id, name, email FROM users WHERE id = $user_id";
$db->sql($sql_query_user);
$result = $db->getResult();


if (isset($_POST['btnCancel'])) { ?>
<script>
window.location.href = "youtube_income.php";
</script>
<?php } ?>

<section class="content-header">
	<h1>
		Edit Youtube Income<small><a href='youtube_income.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Youtube Income</a></small></h1>
	<small><?php echo isset($error['update_jobs']) ? $error['update_jobs'] : ''; ?></small>
	<ol class="breadcrumb">
		<li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
	</ol>
</section>
<section class="content">
	<!-- Main row -->

	<div class="row">
		<div class="col-md-6">

			<!-- general form elements -->
			<div class="box box-primary">
				<div class="box-header with-border">
				</div><!-- /.box-header -->
				<!-- form start -->
				<form name="add_slide_form" method="post" enctype="multipart/form-data">
				<div class="box-body">
				<div class="form-group">
                            <label for="">Users</label>
                            <?php if (!empty($result) && isset($result[0]['id'], $result[0]['name'], $result[0]['email'])) : ?>
                                <?php $userDetails = $result[0]; ?>
                                <input type="text" id="details" name="user_id" class="form-control" value="<?php echo $userDetails['id'] . ' | ' . $userDetails['name'] . ' | ' . $userDetails['email']; ?>" disabled>
                            <?php else : ?>
                                <input type="text" id="details" name="user_id" class="form-control" value="User details not available" disabled>
                            <?php endif; ?>
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $res[0]['user_id']; ?>">
                        </div>
						<div class="form-group">
							<label for="">Link</label>
							<input type="text" class="form-control" name="link" value="<?php echo $res[0]['link']; ?>" required>
						</div>
						<div class="form-group">
							<label for="">Amount</label>
							<input type="number" class="form-control" name="amount" value="<?php echo $res[0]['amount']; ?>" required>
						</div>
            <div class="form-group">
                                    <label class="control-label">Status</label><i class="text-danger asterik">*</i><br>
                                    <div id="status" class="btn-group">
                                        <label class="btn btn-primary" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                            <input type="radio" name="status" value="0" <?= ($res[0]['status'] == 0) ? 'checked' : ''; ?>> Not-paid
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
              <button type="submit" class="btn btn-primary" id="submit_btn" name="btnEdit">Update</button>

            </div>
            <div class="form-group">

              <div id="result" style="display: none;"></div>
            </div>
          </form>
        </div><!-- /.box -->
      </div>
      <!-- Left col -->
      <div class="col-xs-6">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">users</h3>
          </div>
          <div class="box-body table-responsive">
            <table class="table table-hover" data-toggle="table" id="users" data-toggle="table" data-url="api-firebase/get-bootstrap-table-data.php?table=users" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-trim-on-search="false" data-show-refresh="true" data-show-columns="true" data-sort-name="id" data-sort-order="asc" data-mobile-responsive="true" data-toolbar="#toolbar" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-export-options='{
                            "fileName": "users-list-<?= date('d-m-y') ?>",
                            "ignoreColumn": ["state"]   
                        }'>
              <thead>
                <tr>
                  <th data-field="state" data-radio="true"></th>
                  <th data-field="id" data-sortable="true">ID</th>
                  <th data-field="name" data-sortable="true">Name</th>
                  <th data-field="mobile" data-sortable="true">Mobile</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <div class="separator"> </div>
    </div>
  </section>
  <script>
  $('#users').on('check.bs.table', function(e, row) {
    $('#details').val(row.id + " | " + row.name + " | " + row.email);
    $('#user_id').val(row.id); // Update 'user_id' with the selected user's id
  });
</script>
