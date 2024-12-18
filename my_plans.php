<?php
include_once('includes/connection.php');
session_start();
// Check if the user is logged in
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
if (!$user_id) {
    header("Location: index.php");
    exit();
}
$data = array(
    "user_id" => $user_id,
);
// Fetch user plans
$apiUrl = API_URL . "user_plan_list.php";
$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($curl);
if ($response === false) {
    echo "Error: " . curl_error($curl);
    $plans = [];
} else {
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        $plans = $responseData["data"];
    } else {
        echo "<script>alert('" . ($responseData["message"] ?? "Failed to fetch plans.") . "')</script>";
        $plans = [];
    }
}
curl_close($curl);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'])) {
    $plan_id = $_POST['plan_id'];
    $claimData = array(
        "user_id" => $user_id,
        "plan_id" => $plan_id
    );
    $apiUrl = API_URL . "claim.php";
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $claimData);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);
    curl_close($curl);
     $responseData = json_decode($response, true);
    if ($responseData !== null && isset($responseData["success"]) && $responseData["success"]) {
        echo json_encode(["success" => true, "message" => $responseData["message"]]);
    } else {
        echo json_encode(["success" => false, "message" => $responseData["message"] ?? "Failed to process claim."]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web</title>
    <link rel="icon" type="image/x-icon" href="admin_v1/dist/img/jiyo">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Lightbox CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <style>
        /* Additional styles for the boxes */
        .plan-box {
            background-color: #F8F9FA;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
        }
        .plan-details p {
            margin: 5px 0;
            font-size: 1.1rem;
            color: black;
        }
        .highlight {
            background-color: yellow;
            font-weight: bold;
            padding: 0 5px;
        }
        .purchase-btn {
             background-color:#44eba7; 
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px;
            font-family: 'Poppins', Helvetica, sans-serif;
        }
        /* Style for the product name box */
        .product-name-box {
            background-color: #44eba7;
            color: black;
            padding: 15px;
            text-align: center;
            font-size: 0.90rem;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .claim-btn {
            background-color: #44eba7;
            border-color: #44eba7;
            color: black; 
            font-weight: 600;
            border-radius: 99999px; /* Border color set to #4A148C */
            font-family: 'Poppins', Helvetica, sans-serif;
        }
        .claim-btn:hover{
            background-color: #44eba7;
        }
        /* .btn-success[disabled] {
            background-color: #ccc;
            border-color: #ccc;
        } */
        .activated-jobs-link {
            margin-bottom: 20px;
            background-color: #44eba7;
            /* Background color for the link */
            border-radius: 10px;
        }
        @media (max-width: 576px) {
            .plan-details p {
                margin: 5px 0;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php include_once('sidebar.php'); ?>
            <div class="col py-3">
                <!-- Activated Jobs Link -->
                <div class="activated-jobs-link">
                    <a href="plan.php" class="btn w-100 d-flex justify-content-center align-items-center" style="background-color: #44eba7;">
                        <i style="color:rgb(2, 2, 2); font-size: 1.5rem;font-weight: bold;" class="bi bi-arrow-left"></i>
                        <span style="color:rgb(0, 0, 0); font-size: 1.2rem; font-weight: bold; flex-grow: 1; text-align: center;">Jobs</span>
                    </a>
                </div>
                <div id="plansSection" class="plansSection-container">
                    <div class="row">
                        <!-- Loop through all plans and display each one -->
                        <?php foreach ($plans as $plan): ?>
                        <div class="col-md-6 mb-4">
                            <!-- Separate Product Name Box -->
                            <div class="product-name-box">
                                <?php echo htmlspecialchars($plan['name']); ?>
                            </div>
                            <div class="plan-box">
                                <!-- Left side: Image with Lightbox -->
                                <?php if (!empty($plan['image'])): ?>
                                <a data-lightbox="plan" href="<?php echo htmlspecialchars($plan['image']); ?>" data-title="<?php echo htmlspecialchars($plan['name']); ?>">
                                    <img src="<?php echo htmlspecialchars($plan['image']); ?>" alt="Plan image" title="<?php echo htmlspecialchars($plan['name']); ?>">
                                </a>
                                <?php else: ?>
                                <p>No Image Available</p>
                                <?php endif; ?>
                                <!-- Right side: Details -->
                                <div class="plan-details">
                                    <p>Cost: <strong><?php echo '₹' . htmlspecialchars($plan['price']); ?></strong></p>
                                    <p>Quantity: <strong><?php echo htmlspecialchars($plan['quantity'] ?? '1 Kilo watt'); ?></strong></p>
                                    <p>Daily Earnings: <strong><?php echo '₹' . htmlspecialchars($plan['daily_earnings']); ?></strong></p>
                                    <p>Monthly Earnings: <strong><?php echo '₹' . htmlspecialchars($plan['monthly_earnings']); ?></strong></p>
                                    
                                    <button class="btn claim-btn" data-plan-id="<?php echo htmlspecialchars($plan['plan_id']); ?>">Claim</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const claimButtons = document.querySelectorAll('.claim-btn');
            claimButtons.forEach(button => {
                button.addEventListener('click', function () {
                                    const planId = this.getAttribute('data-plan-id');
                    fetch('', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `plan_id=${planId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</body>
</html>