<?php


include('./includes/variables.php');
include_once('includes/custom-functions.php');
$fn = new custom_functions;

if (isset($_POST['btnLogin'])) {

    // get email and password
    $email = $db->escapeString($_POST['email']);
    $password = $db->escapeString($_POST['password']);

    // set time for session timeout
    $currentTime = time() + 25200;
    $expired = 3600;

    // create array variable to handle error
    $error = array();

    // check whether $email is empty or not
    if (empty($email)) {
        $error['email'] = "*Email should be filled.";
    }

    // check whether $password is empty or not
    if (empty($password)) {
        $error['password'] = "*Password should be filled.";
    }

    // if email and password is not empty, check in database
    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM admin WHERE id = 1";
        $db->sql($sql);
        $res = $db->getResult();
        $d_email = $res[0]['email']; // Fetch the count from the result
        $d_password = $res[0]['password'];
        $encrypt_password = md5($password);
        if ($email == $d_email && $encrypt_password == $d_password) {
            $_SESSION['id'] = '1';
            $_SESSION['role'] ='admin';
            $_SESSION['username'] = 'Solarpe';
            $_SESSION['email'] = 'admin@gmail.com';
            $_SESSION['timeout'] = $currentTime + $expired;

            $type = 'login';
            $datetime = date('Y-m-d H:i:s');
    
            $sql = "INSERT INTO tracking (type, datetime) VALUES ('$type', '$datetime')";
            $db->sql($sql);

            header("location: home.php");
        } else {
            $error['failed'] = "<span class='label label-danger'>Invalid Email or Password!</span>";
        }
    }
}
?>
<?php echo isset($error['update_user']) ? $error['update_user'] : ''; ?>
<div class="col-md-4 col-md-offset-4 " style="margin-top:150px;">
    <!-- general form elements -->
    <div class='row'>
        <div class="col-md-12 text-center">
        <img src="dist/img/solar.png" height="100">
            <h3>Solarpe-Dashboard</h3>
        </div>
        <div class="box box-info col-md-12">
            <div class="box-header with-border">
                <h3 class="box-title">Admin Login</h3>
                <center>
                    <div class="msg"><?php echo isset($error['failed']) ? $error['failed'] : ''; ?></div>
                </center>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form method="post" enctype="multipart/form-data">
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email :</label>
                        <input type="text" name="email" class="form-control" value="<?= defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0 ? 'admin' : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Password :</label>
                        <input type="password" class="form-control" name="password" value="<?= defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0 ? 'admin123' : '' ?>" required>
                    </div>
                    <div class="box-footer">
                        <button type="submit" name="btnLogin" class="btn btn-info pull-left">Login</button>
                    </div>
                </div>
            </form>
        </div><!-- /.box -->
    </div>
</div>
<?php include('footer.php'); ?>