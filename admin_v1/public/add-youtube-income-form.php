<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;
?>
<?php
if (isset($_POST['btnAdd'])) {

        $link = $db->escapeString(($_POST['link']));
        $amount = $db->escapeString(($_POST['amount']));
        $status = $db->escapeString(($_POST['status']));
        
   
        $error = array();
       
        if (empty($link)) {
            $error['link'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($amount)) {
            $error['amount'] = " <span class='label label-danger'>Required!</span>";
        }
        if (empty($status)) {
            $error['status'] = " <span class='label label-danger'>Required!</span>";
        }
        
       
  
       
            // Validate and process the image upload
    
            if (empty($error)) {
        $sql = "INSERT INTO youtube_income (link, amount, status) VALUES ('$link', '$amount', '$status')";
        if ($db->sql($sql)) {
            $error['add_languages'] = "<section class='content-header'><span class='label label-success'>youtube income Added Successfully</span></section>";
        } else {
            $error['add_languages'] = "<span class='label label-danger'>Failed to add income record</span>";
        }
    }
}
        
?>
<section class="content-header">
    <h1>Add New Youtube Income <small><a href='youtube_income.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Youtube Income</a></small></h1>

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
                            <div class="form-group">
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Video Link</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="link" required>
                                </div>
                                <!-- <div class='col-md-3'>
                                    <label for="exampleInputtitle">Demo Video</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="demo_video" required>
                                </div> -->
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Amount</label> <i class="text-danger asterik">*</i>
                                    <input type="number" class="form-control" name="amount">
                                </div>
                                <div class='col-md-3'>
                                    <label for="exampleInputtitle">Status</label> <i class="text-danger asterik">*</i>
                                    <input type="text" class="form-control" name="status" required>
                                </div>
                                
                            </div> 
                        </div> 
                        <br>
                        <!-- <div class="row">
                            <div class="form-group">
                
                            </div> 
                        </div> 
                        <br> -->
                        
                        <br> 
                    
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