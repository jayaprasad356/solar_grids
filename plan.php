<?php
include_once('includes/connection.php');
session_start();

// Check if the user is logged in
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Initialize recharge variable
$recharge = 0; // Default value in case no recharge is found

$data = array(
    "user_id" => $user_id,
    
);

$apiUrl = API_URL . "plan_list.php";

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

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

if (isset($_POST['btnactivate']) || isset($_POST['check_coupon'])) {
    $plan_id = isset($_POST['plan_id']) ? $_POST['plan_id'] : null;
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : null;

    if (!$plan_id) {
        die("Plan ID not provided.");
    }

 // If it's a coupon check request
if (isset($_POST['check_coupon'])) {
    if ($coupon_code) {
        // Call the API to check if the coupon is valid
        $apiUrl = API_URL . "validate_coupon.php";  // Assuming this is the correct API for validating coupons

        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'plan_id' => $plan_id,
            'coupon_code' => $coupon_code
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        if ($response === false) {
            echo json_encode(['success' => false, 'message' => 'Error validating coupon']);
        } else {
            $responseData = json_decode($response, true);
            if ($responseData['success']) {
                // Include final_price and other relevant data in the response
                $final_price = $responseData['data']['final_price'] ?? null; // Assuming API includes final_price
                echo json_encode([
                    'success' => true,
                    'message' => 'Coupon is valid',
                    'data' => [
                        'plan_id' => $responseData['data']['plan_id'],
                        'original_price' => $responseData['data']['original_price'],
                        'discount_amount' => $responseData['data']['discount_amount'],
                        'final_price' => $final_price
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid coupon code']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Coupon code is required']);
    }
    exit();
}

    // Prepare data without coupon_code if it's empty
    $data = [
        "plan_id" => $plan_id,
        "user_id" => $user_id,
    ];

    // Only add coupon_code if it's provided
    if ($coupon_code) {
        $data["coupon_code"] = $coupon_code;
    }

    $apiUrl = API_URL . "activate_plan.php";  // Assuming this is the correct API for activation

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);

    if ($response === false) {
        echo json_encode(['success' => false, 'message' => curl_error($curl)]);
    } else {
        $responseData = json_decode($response, true);
        if ($responseData !== null && $responseData["success"]) {
            if (isset($responseData['balance'])) {
                $_SESSION['balance'] = $responseData['balance'];
                $balance = $_SESSION['balance'];
            }
            echo json_encode(['success' => true, 'message' => $responseData['message']]);
        } else {
            echo json_encode(['success' => false, 'message' => $responseData["message"] ?? 'Activation failed']);
        }
    }
    curl_close($curl);
    exit();  // Stop further execution
}


// Fetch user recharge details
$data = array(
    "user_id" => $user_id,
);

$apiUrl = API_URL . "user_details.php";

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
                                    <p>Monthly Earnings: <strong><?php echo '₹' . htmlspecialchars($plan['monthly_earnings']); ?></strong></p>
                                    <!-- <p>Daily Codes: <strong><?php echo '' . htmlspecialchars($plan['daily_codes']); ?></strong></p> -->
                                    <?php if ($plan['id'] != 1): ?>
                                        <!-- <p>Validity: <span class="highlight">Life Time</span></p> -->
                                    <?php else: ?>
                                        <!-- <p>Validity: <span class="highlight">30 Days</span></p> -->
                                    <?php endif; ?>
                                    
                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="margin-top: 10px;">
                                        <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($plan['id']); ?>">
                                        <button type="button" 
                                            class="btn purchase-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#purchaseModal"
                                            data-plan-id="<?php echo htmlspecialchars($plan['id']); ?>" 
                                            data-plan-name="<?php echo htmlspecialchars($plan['name']); ?>" 
                                            data-plan-price="<?php echo htmlspecialchars($plan['price']); ?>">
                                            Purchase
                                        </button>
                                    </form>
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
<!-- Purchase Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Purchase Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <center><small id="couponMessage" style="display: none;"></small></center>
            <div class="modal-body">
                <form id="couponForm">
                    <div class="mb-3">
                        <label for="couponCode" class="form-label">Enter Coupon Code</label>
                        <input type="text" class="form-control" id="couponCode" placeholder="Enter code">
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="applyCoupon">Apply</button>
                </form>

                <p>Plan Price: <strong><span id="planPrice">0</span></strong></p>
                <button type="button" class="btn btn-success" id="btnactivate">Purchase Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const purchaseModal = document.getElementById('purchaseModal');
    const planPriceElem = document.getElementById('planPrice');
    const confirmPurchaseBtn = document.getElementById('btnactivate');
    const applyCouponBtn = document.getElementById('applyCoupon');
    const couponMessageElem = document.getElementById('couponMessage');
    let selectedPlanId = null;
    let originalPrice = null;
    let final_price = null;  // Store the final price after discount

    // Populate modal with plan details
    purchaseModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        selectedPlanId = button.getAttribute('data-plan-id');
        originalPrice = parseInt(button.getAttribute('data-plan-price'), 10);
        final_price = originalPrice;  // Initialize the final price as the original price
        planPriceElem.textContent = originalPrice;
    });

    // Handle "Apply Coupon" button click
    applyCouponBtn.addEventListener('click', function () {
        const couponCode = document.getElementById('couponCode').value;

        // Send AJAX request to validate coupon code
        fetch('<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `plan_id=${selectedPlanId}&coupon_code=${encodeURIComponent(couponCode)}&check_coupon=true`
        })
        .then(response => response.json())  // Expecting a JSON response from API
        .then(data => {
            if (data.success) {
                // Display success message with green background
                couponMessageElem.textContent = data.message;
                couponMessageElem.style.backgroundColor = 'green';
                couponMessageElem.style.color = 'white';
                couponMessageElem.style.display = 'block'; // Show the message

                // Check if data.data exists and has final_price
                if (data.data && data.data.final_price !== undefined) {
                    final_price = parseInt(data.data.final_price, 10);
                    planPriceElem.textContent = `₹${final_price}`;  // Display the final price
                } else {
                    console.error('Error: final_price is not defined in the response.');
                }
            } else {
                // Display error message with red background
                couponMessageElem.textContent = data.message;
                couponMessageElem.style.backgroundColor = 'red';
                couponMessageElem.style.color = 'white';
                couponMessageElem.style.display = 'block'; // Show the message
            }

            // Hide the coupon message after 3 seconds
            setTimeout(function() {
                couponMessageElem.style.display = 'none';
            }, 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to apply coupon code.');
        });
    });

    // Handle "Purchase Plan" button click
    confirmPurchaseBtn.addEventListener('click', function () {
        const couponCode = document.getElementById('couponCode').value;

        // Send AJAX request to activate the plan with final price
        fetch('<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `plan_id=${selectedPlanId}&coupon_code=${encodeURIComponent(couponCode)}&final_price=${final_price}&btnactivate=true`
        })
        .then(response => response.json())  // Expecting a JSON response from API
        .then(data => {
            if (data.success) {
                alert(data.message);  // Display success message from API
                location.reload();  // Reload the page to reflect changes
            } else {
                alert('Failed to activate the plan. ' + (data.message || 'Please try again.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to activate the plan.');
        });
    });
});

</script>




    <!-- JavaScript to handle redirection based on plan_id -->
 
<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

</body>
</html>