<?php
include_once('includes/connection.php');
session_start();

// Check if the user is logged in and has a token
if (!isset($_SESSION['id']) || !isset($_SESSION['token'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$token = $_SESSION['token'];  // Get token from session

if (!$token) {
    // If no token, redirect to login page
    header("Location: login.php");
    exit();
}

// Initialize recharge variable
$recharge = 0; // Default value in case no recharge is found

$data = array(
    "user_id" => $user_id,
    "category" => 'yearly',
);

// API URL for fetching plan list
$apiUrl = API_URL . "plan_list.php";

// Initialize cURL session
$curl = curl_init($apiUrl);

// Set the cURL options for the POST request with token authorization
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // URL encode the data
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// Add token in Authorization header
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $token,  // Pass token in Authorization header
));

$response = curl_exec($curl);

if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
    $plans = [];
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Store all plan details
        $plans = $responseData["data"];
    } else {
        if ($responseData !== null) {
            echo "<script>alert('".$responseData["message"]."')</script>";
        }
        $plans = [];
    }
}

curl_close($curl);

if (isset($_POST['btnactivate'])) {
    $plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;

    if (!$plan_id) {
        die("Plan ID not provided.");
    }

    if (!$token) {
        // If no token, redirect to login page
        header("Location: login.php");
        exit();
    }
    $data = array(
        "plan_id" => $plan_id,
        "user_id" => $user_id,
    );

    // API URL for activating the plan
    $apiUrl = API_URL . "activate_plan.php";

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // URL encode the data
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // Add token in Authorization header
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $token,  // Pass token in Authorization header
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        // Error in cURL request
        echo "Error: " . curl_error($curl);
    } else {
        // Successful API response
        $responseData = json_decode($response, true);
        if ($responseData !== null && isset($responseData["success"])) {
            $message = $responseData["message"];
            if (isset($responseData["balance"])) {
                $_SESSION['balance'] = $responseData['balance'];
                $balance = $_SESSION['balance'];
            }
            echo "<script>alert('$message');</script>";
        } else {
            // Failed to fetch transaction details
            if ($responseData !== null) {
                echo "<script>alert('".$responseData["message"]."')</script>";
            }
        }
    }
    curl_close($curl);
}

// Fetch user recharge details
$data = array(
    "user_id" => $user_id,
);

if (!$token) {
    // If no token, redirect to login page
    header("Location: login.php");
    exit();
}

// API URL for fetching user details
$apiUrl = API_URL . "user_details.php";

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // URL encode the data
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// Add token in Authorization header
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $token,  // Pass token in Authorization header
));

$response = curl_exec($curl);

if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Display transaction details
        $userdetails = $responseData["data"];
        if (!empty($userdetails)) {
            $recharge = $userdetails[0]["recharge"];
        } else {
            echo "No recharge details found.";
        }
    } else {
        if ($responseData !== null) {
            echo "<script>alert('".$responseData["message"]."')</script>";
        }
    }
    curl_close($curl);
}

$data = array(
    "user_id" => $user_id,
);
$apiUrl = API_URL . "settings.php";

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Display transaction details
        $settingsdetails = $responseData["data"];
        if (!empty($settingsdetails)) {
            $demo_video = $settingsdetails[0]["demo_video"];
        } else {
            echo "No demo video found.";
        }
    } else {
        if ($responseData !== null) {
            echo "<script>alert('".$responseData["message"]."')</script>";
        }
    }
}
curl_close($curl);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web</title>
    <link rel="icon" type="image/x-icon" href="admin_v1/dist/img/jiyo">

     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>

        .plan-box {

            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .plan-box img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 5px;

        }

        .plan-details {
            flex-grow: 1;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .plan-details p {
            margin: 5px 0;
            font-size: 1.1rem;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .highlight {
            background-color: yellow;
            font-weight: bold;
            padding: 0 5px;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .purchase-btn {
           background-color:#44eba7; 
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .trail-btn {
     /* background-color:#44eba7;  */
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px; /* Border color set to #4A148C */
            font-family: 'Poppins', Helvetica, sans-serif;
}


        .product-name-box {
            background-color: rgb(68 235 167);
            color: black;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: flex; /* Use flexbox for alignment */
            justify-content: space-between; /* Space out children */
            align-items: center; /* Center items vertically */
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .product-name {
            font-size: 0.90rem; /* Size for the product name */
            font-weight: bold; /* Bold for product name */
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .watch-demo-link {
            color: black; /* Change color as needed */
            text-decoration: none; /* Remove underline */
            font-weight: bold; /* Make it bold */
            padding-left: 10px; /* Add padding for a gap */
            font-size: 0.80rem; /* Size for the product name */
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .watch-demo-link:hover {
            text-decoration: underline; /* Underline on hover for better UX */
        }

        .activated-jobs-link {
    margin-bottom: 20px;
    background-color: rgb(68 235 167); /* Background color for the link */
    border-radius: 10px;
    font-family: 'Poppins', Helvetica, sans-serif;
}


.alert-info{
    top: 0px; /* Distance from the top */
    left: 680px; /* Distance from the right */ 
    width: 100%; /* Full width */
    max-width: 300px; /* Set a max width */
    font-family: 'Poppins', Helvetica, sans-serif;
    font-weight: bold;
}
.small-font {
            font-size: 0.8rem; /* Adjust the size as needed */
            font-family: 'Poppins', Helvetica, sans-serif;
        }
@media (max-width: 576px) {
    .plan-details p {
        margin: 5px 0;
        font-size: 0.8rem;
         font-family:Verdana, Geneva, Tahoma, sans-serif;
    }

    .alert-info {
        width: 60%; /* Adjust width for smaller screens */
        font-size: 0.7rem; /* Slightly smaller font size for better fit */
        top: 3px; /* Distance from the top */
        left: 0px; /* Distance from the right */
    }
    .btn-info{
        font-size: 0.7rem; /* Slightly smaller font size for better fit */  
        top: 10px; /* Distance from the top */
        right: 20px; /* Distance from the right */
        width: 40%; /* Adjust width for smaller screens */
    }
}
/* .days{
    background-color: gold;
}
.life{
    color: red;
} */
 .small-font{
    font-size: 0.9rem;
    color: red;
    font-weight: 700;
 }
 .discount-container{
    background-color: gold;
    width: 40%;
    text-align: center;
    border-radius: 5px;
}

@media (max-width: 576px) {
    .discount-container {
        width: 100%; /* Adjust width for smaller screens */
    }
}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <?php include_once('sidebar.php'); ?>
        <div class="col py-3">
        <div class="d-flex align-items-center justify-content-start ms-3">
        <!-- Button -->
        <a href="#" class="btn btn-info me-3" style="background-color:#44eba7" data-bs-toggle="modal" data-bs-target="#rechargeGuideModal">
            Click Here to Recharge <i class="bi bi-arrow-right"></i>
        </a>
        <!-- Alert -->
        <div class="alert alert-info mb-2" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#rechargeGuideModal">
            Recharge Value: <strong>₹<?php echo isset($recharge) ? htmlspecialchars($recharge) : '0'; ?></strong>
        </div>
    </div>

            <!-- Activated Jobs Link -->
            <div class="activated-jobs-link">
                <a href="my_plans.php" class="btn w-100 d-flex justify-content-between align-items-center">
                    <i style="color: #03242b; font-size: 1.5rem; padding: 10px; font-weight: bold;" class="bi bi-briefcase-fill"></i>  <!-- Left icon (briefcase) -->
                    <span style="color:rgb(0, 0, 0); font-size: 0.90rem; padding: 10px; font-weight: bold; ">My Activated Income</span> <!-- Button Text -->
                    <i style="color: #03242b; font-size: 1.5rem; padding: 10px; font-weight: bold;" class="bi bi-arrow-right"></i> <!-- Right icon (arrow) -->
                </a>
            </div>
            <!-- <div class="mb-4">
                <button class="btn level-btn" data-level="1">Yearly</button>
                <button class="btn level-btn" data-level="2">Lifetime</button>
               
            </div> -->

            <div id="plansSection" class="plansSection-container">
                <div class="row">
                    <!-- Loop through all plans and display each one -->
                    <?php foreach ($plans as $plan): ?>
                        <div class="col-md-6 mb-4">
                            <span class="product-name-box">
                                <?php echo htmlspecialchars($plan['name']); ?>
                                <!-- <a href="<?php echo htmlspecialchars($plan['demo_video']); ?>" target="_blank" class="watch-demo-link">
                                    Watch Demo Video
                                </a> -->
                            </span>

                            <div class="plan-box">
                                <?php if (!empty($plan['image'])): ?>
                                    <a data-lightbox="plan" href="<?php echo htmlspecialchars($plan['image']); ?>" data-title="<?php echo htmlspecialchars($plan['name']); ?>">
                                        <img src="<?php echo htmlspecialchars($plan['image']); ?>" alt="Plan image" title="<?php echo htmlspecialchars($plan['name']); ?>">
                                    </a>
                                <?php else: ?>
                                    <p>No Image Available</p>
                                <?php endif; ?>

                                <div class="plan-details">
                                    <p>Cost: <strong><?php echo '₹' . htmlspecialchars($plan['price']); ?></strong></p>
                                    <p>Invite Bonus: <strong><?php echo '₹' . htmlspecialchars($plan['invite_bonus']); ?></strong></p>
                                    <p>Daily Earnings: <strong><?php echo '₹' . htmlspecialchars($plan['daily_earnings']); ?></strong></p>
                                    <p> LifeTime Earnings: <strong class="days">Unlimited Days</strong></p>
                                    <!-- <p>Daily Codes: <strong><?php echo '' . htmlspecialchars($plan['daily_codes']); ?></strong></p> -->
                                    <?php if ($plan['id'] != 1): ?>
                                        <!-- <p>Validity: <span class="highlight">Life Time</span></p> -->
                                    <?php else: ?>
                                        <!-- <p>Validity: <span class="highlight">30 Days</span></p> -->
                                    <?php endif; ?>

                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="margin-top: 10px;">
                                        <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($plan['id']); ?>">
                                        <button type="submit" name="btnactivate" class="btn purchase-btn">Purchase</button>
                                        <!-- <button type="button" onclick="startWork(<?php echo htmlspecialchars($plan['id']); ?>)" class="btn trail-btn">Take Trial</button> -->
                                    </form>
                                       <!-- <div class="discount-container" style="margin-top: 10px;">
                                            <span class="small-font">Discount ends in:</span>
                                            <span class="discount-timer small-font" style="color: red; font-weight: bold;">02:00:00</span>
                                        </div> -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
       function startWork(planId) {
    let redirectUrl = "";
    switch (planId) {
        case 1:
            redirectUrl = "30_days_trail.php";
            break;
        case 2:
            redirectUrl = "associate_job_trail.php";
            break;
        case 3:
            redirectUrl = "supervisor_job_trail.php";
            break;
        case 4:
            redirectUrl = "asst_manager_job_trail.php";
            break;
        case 5:
            redirectUrl = "manager_job_trail.php";
            break;
        default:
            alert("Invalid Plan ID");
            return;
    }
    window.location.href = redirectUrl;
}
function startCountdown(duration, display) {
      let timer = duration, hours, minutes, seconds;

      const interval = setInterval(function () {
          hours = parseInt(timer / 3600, 10);
          minutes = parseInt((timer % 3600) / 60, 10);
          seconds = parseInt(timer % 60, 10);

          hours = hours < 10 ? "0" + hours : hours;
          minutes = minutes < 10 ? "0" + minutes : minutes;
          seconds = seconds < 10 ? "0" + seconds : seconds;

          display.textContent = `${hours}:${minutes}:${seconds}`;

          if (--timer < 0) {
              clearInterval(interval);
              display.textContent = "Discount expired!";
          }
      }, 1000);
  }

  // Initialize the timer
  document.addEventListener("DOMContentLoaded", function () {
      const countdownElements = document.querySelectorAll(".discount-timer");

      countdownElements.forEach((element) => {
          const duration = 2 * 60 * 60; // 2 hours in seconds
          startCountdown(duration, element);
      });
  });

    </script>
    
<!-- Recharge Guide Modal -->
<div class="modal fade" id="rechargeGuideModal" tabindex="-1" aria-labelledby="rechargeGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rechargeGuideModalLabel">Recharge Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <center><p>1.Click on the below link & complete the payment.</p></center>
                <center><a href="https://www.slveenterprises.org/product/32099322/Solarpe-Green-Energy" class="btn" style = "background-color: #44eba7; color:black;" target="_blank">Click here for making payment</a></center>
                <center> <a href="https://solarpe.org/recharges_video.mp4" target="_blank" class="watch-demo-link">How to recharge<i style="color: #03242B;  padding: 5px; font-weight: bold;" class="fas fa-arrow-right"></i></a> </center>
                <!-- <a href="demo_video_url" class="watch-demo-link">Watch Demo Video</a> -->
            </div>
        </div>
    </div>
</div>
<!-- Purchase Modal -->



    <!-- JavaScript to handle redirection based on plan_id -->
 
<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

</body>
</html>