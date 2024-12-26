<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php
if (isset($_POST['btnAdd'])) {
  $user_id = $db->escapeString(($_POST['user_id']));
  $link = $db->escapeString(($_POST['link']));
  $amount = $db->escapeString(($_POST['amount']));
  $datetime = date('Y-m-d H:i:s'); // Get current datetime

  $error = array();

  // Check if link already exists
  $sql_query = "SELECT * FROM youtuber_income WHERE link = '$link'";
  $db->sql($sql_query);
  $existing_link = $db->getResult();

  if (!empty($existing_link)) {
    $error['add_languages'] = "<section class='content-header'>
                      <span class='label label-danger'>Link already submitted</span> </section>";
  } else {
    // Check if user status is 1
    if (!empty($user_id) && !empty($link) && !empty($amount)) {
      $sql_query = "INSERT INTO youtuber_income (user_id, link, amount, status, datetime) VALUES ('$user_id', '$link', '$amount', 0, '$datetime')";
      $db->sql($sql_query);
      $result = $db->getResult();
      if (!empty($result)) {
        $result = 0;
      } else {
        $result = 1;
      }

      if ($result == 1) {
        $error['add_languages'] = "<section class='content-header'>
                        <span class='label label-success'>User Youtube Income Added Successfully</span> </section>";
      } else {
        $error['add_languages'] = " <span class='label label-danger'>Failed</span>";
      }
    }
  }
}
?>
<section class="content-header">
    <h1>Add New Youtube Income <small><a href='youtube_income.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Youtube Income </a></small></h1>

    <?php echo isset($error['add_languages']) ? $error['add_languages'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
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
                                <input type="text" id="details" name="user_id" class="form-control" value="<?php echo $userDetails['id'] . ' | ' . $userDetails['name'] . ' | ' . $userDetails['mobile']; ?>" disabled>
                            <?php else : ?>
                                <input type="text" id="details" name="user_id" class="form-control" value="User details not available" disabled>
                            <?php endif; ?>
                            <input type="hidden" id="user_id" name="user_id">
                        </div>
                        <div class="form-group">
                            <label for="">Link</label>
                            <input type="text" class="form-control" name="link" required>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Amount</label> <i class="text-danger asterik">*</i>
                        <input type="number" class="form-control" name="amount" required>
                    </div>
	
             
            </div><!-- /.box-body -->

            <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
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
    $('#details').val(row.id + " | " + row.name + " | " + row.mobile);
    $('#user_id').val(row.id); // Update 'user_id' with the selected user's id
  });
</script>
