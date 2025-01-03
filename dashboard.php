<?php
include_once('includes/connection.php');
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null; // Ensure user_id is set

if (!$user_id) {
    header("Location: login.php");
    exit();
}

$data = array(
    "user_id" => $user_id,
);

$apiUrl = API_URL."user_details.php";


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
            $total_income = $userdetails[0]["total_income"];
            $total_recharge = $userdetails[0]["total_recharge"];
            $balance = $userdetails[0]["balance"];
            $total_withdrawal = $userdetails[0]["total_withdrawal"];
            $today_income = $userdetails[0]["today_income"];
            $team_income = $userdetails[0]["team_income"];
        } else {
            echo "<script>alert('".$responseData["message"]."')</script>";
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
    <title>Web Dashboard</title>
    <link rel="icon" type="image/x-icon" href="admin_v1/dist/img/">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Enhanced styles for the dashboard */
        .info-box {
            background-color: #03242b;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .info-box h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: bold;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        .info-box p {
            font-size: 1.5rem;
            margin: 0;
            font-weight: bold;
            font-family: 'Poppins', Helvetica, sans-serif;
        }

        /* Icon styling inside the info boxes */
        .info-box i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color:#03242b;
        }

        /* Colors and effects */
        .info-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .total-income {
            background: linear-gradient(45deg, #ff7043, #d84315);
            color: white;
        }

        .total-recharge {
            background: linear-gradient(45deg, #fdd835, #f57f17);
            color: white;
        }

        .total-assets {
            background: linear-gradient(45deg, #66bb6a, #2e7d32);
            color: white;
        }

        .total-withdrawal {
            background: linear-gradient(45deg, #4fc3f7, #0277bd);
            color: white;
        }

        .today-income {
            background: linear-gradient(45deg, #26a69a, #004d40);
            color: white;
        }

        .team-income {
            background: linear-gradient(45deg, #d4e157, #afb42b);
            color: white;
        }
        .btn-gradient {
                    background: linear-gradient(45deg, #ff7043, #d84315);
                    color: white;
                    border: none;
                    padding: 20px 30px;
                    font-size: 1.2rem;
                    font-family: 'Poppins', Helvetica, sans-serif;
                    transition: background 0.3s ease, transform 0.3s ease;
                    margin-bottom: 30px;
                }

                .btn-gradient:hover {
                    background: linear-gradient(45deg, #d84315, #ff7043);
                    transform: translateY(-5px);
                }

        /* Adjusting column behavior for mobile */
        /* Adjusting column behavior and height for mobile */
        @media (max-width: 768px) {
            .col-sm-6 {
                max-width: 50%;
            }

            /* Decrease the height and padding of the info boxes on mobile */
            .info-box {
                padding: 15px; /* Reduce padding */
                min-height: 150px; /* You can adjust this height as needed */
            }

            /* Adjust the font sizes on mobile */
            .info-box h4 {
                font-size: 1rem;
            }

            .info-box p {
                font-size: 1.2rem;
            }

            .info-box i {
                font-size: 2rem; /* Reduce icon size */
            }
        }

      

        /* Make the video responsive and with a fixed smaller height */
        .video-container {
            width: 100%;
            height: 500px; /* Set fixed height for video */
            object-fit: cover; /* Ensures the video maintains its aspect ratio while filling the container */
            z-index: 0;
        }


    </style>
</head>
<body>
    
<div class="container-fluid">
    <div class="row flex-nowrap">
        <?php include_once('sidebar.php'); ?>

        <div class="col py-3">
            <div class="row">
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box total-assets">
                        <i class="bi bi-wallet2"></i>
                        <h4>Main Wallet Balance</h4>
                        <p>₹<?php echo $balance; ?></p>
                    </div>
                </div>

                <!-- Total Income -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box total-income">
                        <i class="bi bi-cash-coin"></i>
                        <h4>Total Income</h4>
                        <p>₹<?php echo $total_income; ?></p>
                    </div>
                </div>

                <!-- Total Recharge -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box total-recharge">
                        <i class="bi bi-piggy-bank"></i>
                        <h4>Total Recharge</h4>
                        <p>₹<?php echo $total_recharge; ?></p>
                    </div>
                </div>

                <!-- Total Withdrawals -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box total-withdrawal">
                        <i class="bi bi-wallet-fill"></i>
                        <h4>Total Withdrawals</h4>
                        <p>₹<?php echo $total_withdrawal; ?></p>
                    </div>
                </div>

                <!-- Today's Income -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box today-income">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h4>Today's Income</h4>
                        <p>₹<?php echo $today_income; ?></p>
                    </div>
                </div>

                <!-- Team Income -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="info-box team-income">
                        <i class="bi bi-people-fill"></i>
                        <h4>Team Income</h4>
                        <p>₹<?php echo $team_income; ?></p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-auto">
                    <button class="btn btn-lg btn-gradient" data-bs-toggle="modal" data-bs-target="#videoModal">
                        <i class="bi bi-play-circle"></i> Watch Video To Know How Solarpe Works
                    </button>
                </div>
            </div>


            <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="videoModalLabel">Watch the Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <video id="videoPlayer" width="100%" height="450px" controls>
                                <source src="https://solarpe.org/solarpe.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Video Section -->
       

            <!-- <div class="video-container">
            <video width="100%" height="500px" frameborder="0" allowfullscreen controls>
                <source src="https://solargrids.graymatterworks.com/solar.mp4" type="video/mp4">
            </video>
        </div> -->

        </div>
    </div>
</div>
 
 <div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offerModalLabel">Special New Year Offer!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="offers.png" alt="New Year Offer" class="img-fluid" style="width: 100%; max-width: 800px; height: 600px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


     <script>
        // Automatically show the modal on page load
        window.onload = function() {
            var offerModal = new bootstrap.Modal(document.getElementById('offerModal'), {});
            offerModal.show();
        };
    </script>
<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

