<?php
include_once('includes/connection.php');
session_start();

// Check if user is logged in and has a token
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

// Function to make an authenticated API request (for user details)
function fetchUserData($apiUrl, $user_id, $token) {
    $data = array(
        "user_id" => $user_id  // Ensure that user_id is passed to the API
    );

    // Set authorization header with token
    $headers = array(
        "Authorization: Bearer " . $token,  // Pass token in Authorization header
        "Content-Type: application/x-www-form-urlencoded"
    );

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Ensure data is URL-encoded
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);  // Set timeout to 10 seconds
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Connection timeout

    // Execute the request and decode the response
    $response = curl_exec($curl);
    curl_close($curl); // Close curl after request is done

    // Return the decoded response or an empty array if the response is false
    return $response ? json_decode($response, true) : [];
}

// Fetch user details (Requires Token)
$userApiUrl = API_URL . "user_details.php";
$userResponseData = fetchUserData($userApiUrl, $user_id, $token);

// Check if the response is successful
if (!empty($userResponseData) && isset($userResponseData["success"]) && $userResponseData["success"]) {
    $userdetails = $userResponseData["data"];
    if (!empty($userdetails)) {
        // Extract necessary user details
        $total_income = $userdetails[0]["total_income"];
        $total_recharge = $userdetails[0]["total_recharge"];
        $balance = $userdetails[0]["balance"];
        $total_withdrawal = $userdetails[0]["total_withdrawal"];
        $today_income = $userdetails[0]["today_income"];
        $team_income = $userdetails[0]["team_income"];
    }
} else {
    // Handle error if the response doesn't indicate success
    echo "<script>alert('".$userResponseData["message"]."'); window.location='login.php';</script>";
    exit();
}
?>

<?php
// Ensure to define $data for the second API request
$data = array(
    "user_id" => $user_id // Pass the user_id here if needed
);

// API URL for settings
$apiUrl = API_URL . "settings.php";

// Initialize cURL session
$curl = curl_init($apiUrl);

// Set the cURL options for the POST request
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Ensure data is URL-encoded
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// Execute the cURL request
$response = curl_exec($curl);

// Check for cURL errors
if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
    $settingsdetails = [];
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Store transaction details
        $settingsdetails = $responseData["data"];
        if (!empty($settingsdetails) && isset($settingsdetails[0]["offer_image"])) {
            // Assign offer image if it exists
            $offer_image = $settingsdetails[0]["offer_image"];
        } else {
            // Fallback to default image if no offer image is available
            $offer_image = 'path/to/default-image.jpg';
        }
    }
}

// Close cURL session
curl_close($curl);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Dashboard</title>
    <link rel="icon" type="image/x-icon" href="main_controlv3/dist/img/">

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
                    <!-- <h5 class="modal-title" id="offerModalLabel">Special New Year Offer!</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                <img src="<?php echo htmlspecialchars($offer_image); ?>" alt="New Year Offer" class="img-fluid" style="width:300px; max-width: 900px; height: 350px;">
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

