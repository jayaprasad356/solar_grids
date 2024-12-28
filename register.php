<?php
include_once('includes/connection.php');
session_start();
ob_start();

$refer_code = isset($_GET['refer_code']) ? htmlspecialchars($_GET['refer_code']) : ''; 
$isReferCodeSet = !empty($refer_code);

function generateDeviceID() {
    return uniqid(); 
}

if (isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    // Handle AJAX request
    $response = array('success' => false, 'message' => 'Unknown error');

    $mobile = $_POST["mobilenum"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $age = $_POST["age"];
    $otpstatus = $_POST["otpstatus"];
    $referred_by = isset($_POST["referred_by"]) ? $_POST["referred_by"] : $refer_code; 
    $device_id = generateDeviceID();

    if ($password !== $confirmPassword) {
        $response['message'] = 'Password and Confirm Password do not match';
    } else {
        if($otpstatus == '1'){
            $data = array(
                "mobile" => $mobile,
                "password" => $password,
                "name" => $name,
                "email" => $email,
                "city" => $city,
                "state" => $state,
                "age" => $age,
                "referred_by" => $referred_by,
                "device_id" => $device_id,
            );

            $apiUrl = API_URL."register.php";
            $curl = curl_init($apiUrl);

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $responseData = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if ($responseData === null) {
                $response['message'] = 'Error decoding API response.';
            } else {
                if (isset($responseData["success"]) && $responseData["success"]) {
                    $user_id = $responseData["data"][0]['id'];
                    $_SESSION['id'] = $user_id;
                    $_SESSION['codes'] = 0;
                    $response['success'] = true;
                    $response['redirect'] = 'dashboard.php';
                } else {
                    $response['message'] = isset($responseData["message"]) ? $responseData["message"] : "Registration failed. Please try again.";
                }
            }
        } else {
            $response['message'] = 'Mobile number not verified';
        }
    }

    echo json_encode($response);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="icon" type="image/x-icon" href="admin_v1/dist/img/jiyo">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        body {
            font-family: Poppins, Helvetica, sans-serif;
            background: #efefef;
            background: url(./admin_v1/images/bg-green-grid.png);
            background-position: center;
            background-size: cover;
        }
        .btn-custom {
            width: 100%;
            margin-top: 25px;
            border-radius: 10px;
            border: 2px solid #44eba7;
        }
        .btn-customs {
            width: 100%;
            border-radius: 15px;
        }
        .heading {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .heading h2, .heading h3 {
            margin: 10px;
            font-weight: 600;
        }
        .heading h2 {
            font-size: 1.5rem;
            color: rgb(255, 255, 255);
        }
        .heading h3 {
            font-size: 40px;
            font-weight: 600;
            line-height: 30px;
            text-align: center;
            color: rgb(238, 232, 232);
        }
        @media (max-width: 576px) {
            .nowrap-mobile {
                white-space: nowrap;
                font-size: 10px;
            }
            .btn-customs {
                width: 100%;
                border-radius: 15px;
                margin-top: 6px;
            }
            .heading h2 {
                font-size: 1.2rem;
            }
            .heading h3 {
                font-size: 1.5rem;
            }
        }
        form {
            width: 450px;
            background-color: rgba(255, 255, 255, 0.13);
            position: absolute;
            transform: translate(-50%, -50%);
            top: 110%;
            left: 50%;
            border-radius: 17px;
            backdrop-filter: blur(2px);
            border: 5px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(129, 236, 174, 0.6);
            padding: 20px;
        }
        form * {
            font-family: 'Quicksand', sans-serif;
            letter-spacing: 1px;
            outline: none;
            border: none;
        }
        .label {
            color: #efefef;
        }
        .input-group-text {
            background-color: rgba(255, 255, 255, 0.1);
            color: #efefef;
        }
        @media (max-width: 576px) {
            form {
                top: 80%;
                width: 90%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="heading">
<h3>Welcome to Solar Grids</h3>
        <!-- <h2>Most Trusted App in India</h2> -->
    </div>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="custom-container">
            <form method="post" enctype="multipart/form-data">
            <input type="hidden" class="form-control" id="otpstatus" name="otpstatus" value="0">
            <input type="hidden" class="form-control" id="mobilenum" name="mobilenum" value="">
                        
                <div class="form-group">
                    <label class="label" for="mobile" style="font-weight:bold;">Mobile Number:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-mobile-alt"></i></span>
                        </div>
                        <input type="number" class="form-control" id="mobile" name="mobile" placeholder="Mobile" style="border-left: none" required>
                        <div class="input-group-append">
                            <div class="btn btn-primary"  id="sendOtpButton">Send</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="otp" style="font-weight:bold;">OTP:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-message"></i></span>
                        </div>
                        <input type="number" class="form-control" id="otp" name="otp" placeholder="OTP" style="border-left: none">
                        <div class="input-group-append">
                            <div class="btn btn-success"  id="verifyOtpButton">Verify</div>
                            
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="name" style= "font-weight:bold;">Full Name:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required style="border-left: none;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="password" style= "font-weight:bold;">Password:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required style="border-left: none;">
                    </div>
                    <span id="passwordError" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <label class="label" for="confirmPassword" style= "font-weight:bold;">Confirm Password:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required style="border-left: none;">
                    </div>
                    <span id="confirmPasswordError" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <label class="label" for="email" style= "font-weight:bold;">Email:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required style="border-left: none;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="age" style= "font-weight:bold;">Age:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="number" class="form-control" id="age" name="age" placeholder="Age" required style="border-left: none;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="city" style= "font-weight:bold;">City:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" class="form-control" id="city" name="city" placeholder="City" required style="border-left: none;">
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="state" style="font-weight:bold;">State:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <select class="form-control" id="state" name="state" required>
                            <option value="">Select State</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Dadra and Nagar Haveli">Dadra and Nagar Haveli</option>
                            <option value="Daman and Diu">Daman and Diu</option>
                            <option value="Lakshadweep">Lakshadweep</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Puducherry">Puducherry</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label" for="referred_by"  style= "font-weight:bold;">Referral code:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-right: none; background: transparent;"><i class="fas fa-book"></i></span>
                        </div>
                        <input type="text" class="form-control" id="referred_by" name="referred_by" required value="<?php echo htmlspecialchars($refer_code); ?>" style="border-left: none;" <?php if ($isReferCodeSet) echo 'readonly'; ?>>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn  btn-custom" name="btnSignup" style="background-color:#44eba7; color:black; font-weight:bold;">Register</button>
                </div>
                <div class="text-center mt-3">
                    <p><a href="login.php" class="text-white">Back to Login</a></p>
                </div>
            </form>
        </div>
    </div>


    <script>
$(document).ready(function() {
    var crctotp = '';

    $("#sendOtpButton").click(function() {
        var mobile = $("#mobile").val();
        var mobilenum = document.getElementById("mobilenum");
        if (mobile.length === 10) {
            var otp = Math.floor(100000 + Math.random() * 900000);
            $.ajax({
                url: "https://api.authkey.io/request",
                type: "GET",
                data: {
                    authkey: "673e807e1f672335",
                    mobile: mobile,
                    country_code: "91",
                    sid: "14324",
                    otp: otp
                },
                success: function(response) {
                    crctotp = otp;
                    mobilenum.value = mobile;
                    $("#mobile").prop("disabled", true);
                    alert("OTP Sent Successfully");
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed: ", status, error);
                    alert("OTP Failed");
                }
            });

        } else {
            alert("Please enter a valid 10-digit mobile number.");
        }
    });

    $("#verifyOtpButton").click(function() {
        var otp = $("#otp").val();
        var otpStatusElement = document.getElementById("otpstatus");
        if (otp.length === 6) {
            if (otp == crctotp) {
                otpStatusElement.value = "1";
                alert("OTP Verified Successfully");
            } else {
                otpStatusElement.value = "0";
                alert("OTP Wrong");
            }
        } else {
            alert("Please enter a valid 6-digit OTP.");
        }
    });

    $("form").submit(function(event) {
        event.preventDefault();
        var formData = $(this).serializeArray();
        formData.push({name: 'ajax', value: 'true'});

        $.ajax({
            url: "",  // The PHP file URL (current file if it's the same)
            type: "POST",
            data: $.param(formData),
            success: function(response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    window.location.href = jsonResponse.redirect;
                } else {
                    alert(jsonResponse.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed: ", status, error);
                alert("An error occurred. Please try again.");
            }
        });
    });
});
</script>
<script>window.$zoho=window.$zoho || {};$zoho.salesiq=$zoho.salesiq||{ready:function(){}}</script><script id="zsiqscript" src="https://salesiq.zohopublic.in/widget?wc=siq7f332814434ba123f5efbf2d82a7e47947952e33ecc6bf4b78f9f89edf3ad350" defer></script>
</body>
</html>
