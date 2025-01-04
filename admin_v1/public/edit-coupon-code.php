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

if (isset($_POST['btnEdit'])) {
	$plan_id = $db->escapeString($_POST['plan_id']);
	$amount = $db->escapeString($_POST['amount']);
	$coupon_code = $db->escapeString($_POST['coupon_code']);
	
	$sql_query = "UPDATE coupon_code SET plan_id='$plan_id', amount='$amount', coupon_code='$coupon_code' WHERE id = $ID";
	$db->sql($sql_query);
	$result = $db->getResult();             

	if (!empty($result)) {
		$error['update_languages'] = "<span class='label label-danger'>Failed</span>";
	} else {
		$error['update_languages'] = "<span class='label label-success'>Coupon Code Updated Successfully</span>";
	}
}

$data = array();

$sql_query = "SELECT * FROM coupon_code WHERE id = $ID";
$db->sql($sql_query);
$res = $db->getResult();

if (isset($_POST['btnCancel'])) { ?>
	<script>
		window.location.href = "coupon_code.php";
	</script>
<?php } ?>
<section class="content-header">
	<h1>
		Edit Coupon Code<small><a href='copon_code.php'><i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Coupon Code</a></small>
	</h1>
	<small><?php echo isset($error['update_languages']) ? $error['update_languages'] : ''; ?></small>
	<ol class="breadcrumb">
		<li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
	</ol>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-10">
			<div class="box box-primary">
				<div class="box-header with-border"></div>
				<form id="edit_languages_form" method="post" enctype="multipart/form-data">
					<div class="box-body">
						 <div class="form-group">
							<label for="plan_id">Plan ID</label>
							<select class="form-control" name="plan_id">
								<?php
								$sql = "SELECT id, name FROM plan";
								$db->sql($sql);
								$plans = $db->getResult();
								foreach ($plans as $plan) {
									$selected = ($plan['id'] == $res[0]['plan_id']) ? 'selected' : '';
									echo "<option value='" . $plan['id'] . "' $selected>" . $plan['name'] . "</option>";
								}
								?>
							</select>
                        </div>
						<div class="form-group">
							<label for="amount">Amount</label>
							<input type="text" class="form-control" name="amount" value="<?php echo isset($res[0]['amount']) ? $res[0]['amount'] : ''; ?>">
						</div>
						<div class="form-group">
							<label for="coupon_code">Coupon Code</label>
							<input type="text" class="form-control" name="coupon_code" value="<?php echo isset($res[0]['coupon_code']) ? $res[0]['coupon_code'] : ''; ?>">
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary" name="btnEdit">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<div class="separator"></div>
<?php $db->disconnect(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah')
					.attr('src', e.target.result)
					.width(150)
					.height(200)
					.css('display', 'block');
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
<script>
	var changeCheckbox = document.querySelector('#stock_button');
	var init = new Switchery(changeCheckbox);
	changeCheckbox.onchange = function() {
		if ($(this).is(':checked')) {
			$('#stock').val(1);
		} else {
			$('#stock').val(0);
		}
	};
</script>
