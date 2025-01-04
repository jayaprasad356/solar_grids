<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>


<?php
if (isset($_POST['btnAdd'])) {

        $plan_id = $db->escapeString(($_POST['plan_id']));
        $amount = $db->escapeString(($_POST['amount']));
        $coupon_code = $db->escapeString(($_POST['coupon_code']));
       
        $error = array();
       
        if (empty($plan_id)) {
            $error['plan_id'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($amount)) {
            $error['amount'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($coupon_code)) {
            $error['coupon_code'] = " <span class='label label-danger'>Required!</span>";
        }
        
        if (empty($error)) {
            $sql = "SELECT * FROM coupon_code WHERE plan_id = '$plan_id' AND coupon_code = '$coupon_code'";
            $db->sql($sql);
            $result = $db->getResult();
            if (!empty($result)) {
                $error['add_languages'] = "<section class='content-header'>
                                                <span class='label label-danger'>Coupon Code Already Exists</span> </section>";
            } else {
                $sql = "INSERT INTO coupon_code (plan_id, amount, coupon_code) VALUES ('$plan_id', '$amount', '$coupon_code')";
                $db->sql($sql);
                $result = $db->getResult();
                if (!empty($result)) {
                    $result = 0;
                } else {
                    $result = 1;
                }

                if ($result == 1) {
                    $error['add_languages'] = "<section class='content-header'>
                                                <span class='label label-success'>Coupon Code Added Successfully</span> </section>";
                } else {
                    $error['add_languages'] = " <span class='label label-danger'>Failed</span>";
                }
            }
        }
       
    }
        
        
?>

<section class="content-header">
    <h1>Add New Coupon Code <small><a href='copon_code.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Coupon Code</a></small></h1>

    <?php echo isset($error['add_languages']) ? $error['add_languages'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-10">
           
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">

                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form url="add-languages-form" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                       <div class="row">
                            <div class="col-md-6">
                                 
            <div class="form-group">
                <label for="plan_id">Select Plan</label>
                <select name="plan_id" id="plan_id" class="form-control" required>
                    <option value="">Select Plan</option>
                    <?php
                    $sql = "SELECT id, name FROM plan";
                    $db->sql($sql);
                    $plans = $db->getResult();
                    foreach ($plans as $plan) {
                        echo "<option value='" . $plan['id'] . "'>" . $plan['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="coupon_code">Coupon Code</label>
                <input type="text" name="coupon_code" id="coupon_code" class="form-control" required>
            </div>
                    
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Submit</button>
                        <input type="reset" onClick="refreshPage()" class="btn-warning btn" value="Clear" />
                    </div>

                </form>
                <div id="result"></div>

            </div><!-- /.box -->
        </div>
    </div>
</section>
<div class="separator"> </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script>
    $('#add_leave_form').validate({

        ignore: [],
        debug: false,
        rules: {
        reason: "required",
            date: "required",
        }
    });
    $('#btnClear').on('click', function() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].setData('');
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#user_id').select2({
        width: 'element',
        placeholder: 'Type in name to search',

    });
    });

    if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}

</script>

<!--code for page clear-->
<script>
    function refreshPage(){
    window.location.reload();
} 
</script>
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // Set the source of the image to the selected file
            document.getElementById('blah').src = e.target.result;
            // Display the image by changing its style to block
            document.getElementById('blah').style.display = 'block';
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
<?php $db->disconnect(); ?>
