<?php
include_once('includes/connection.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['id']; // Retrieve the user ID from session

// Fetch the authentication token from the session or elsewhere
$token = isset($_SESSION['token']) ? $_SESSION['token'] : null; // Adjust based on your implementation

if (!$token) {
    // If no token is found, redirect the user to login
    header("Location: login.php");
    exit();
}

// Prepare data for the API request
$data = array(
    "user_id" => $user_id,
);

// API URL
$apiUrl = API_URL . "user_details.php";

// Initialize cURL session
$curl = curl_init($apiUrl);

// Set cURL options
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Use http_build_query for POST data
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// Set authorization token in headers
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . $token, // Pass the token as Bearer in the header
));

// Execute cURL request and capture response
$response = curl_exec($curl);

// Check for cURL errors
if ($response === false) {
    echo "Error: " . curl_error($curl);
} else {
    // Parse the API response
    $responseData = json_decode($response, true);
    
    if ($responseData !== null && $responseData["success"]) {
        // Extract the user details from the response
        $userdetails = $responseData["data"];
        
        if (!empty($userdetails)) {
            $refer_code = $userdetails[0]["refer_code"]; // Store the refer_code
           
        } else {
            echo "No user details found.";
        }
    } else {
        // Handle error message from API
        if ($responseData !== null) {
            echo "<script>alert('".$responseData["message"]."')</script>";
        }
    }
}


curl_close($curl);
// Fetch the user's current balance
$apiUrl = API_URL . "settings.php"; // Ensure this endpoint provides the user's balance

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

if ($response === false) {
    echo "Error: " . curl_error($curl);
    $telegram_channel = "N/A";
} else {
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        $details = $responseData["data"];
        if (!empty($details)) {
            $telegram_channel = $details[0]["telegram_channel"];
        } else {
            $telegram_channel = "No telegram_channel information available.";
        }
    } else {
        $telegram_channel = "Failed to fetch telegram_channel.";
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
    <link rel="icon" type="image/x-icon" href="main_controlv3/dist/img/jiyo">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Additional styles for the boxes */
        .info-box {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-box h4 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .info-box p {
            font-size: 1.25rem;
            margin: 0;
        }
        .friends-container {
            position: relative; 
            padding: 20px; 
        }
        .friends-container h2 {
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .friends-button {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1rem;
             background-color:#4A148C;
        }
        .button{
            padding:10px;
            background-color:#4A148C;
            
        }
        .btn_copy{
            border-radius: 99999px;
            padding: 7px;
            border-color: #44eba7;
            font-size: 1rem;
            
        }
        .form-container {
            max-width: 400px; 
        }
        @media (max-width: 576px) {
            .friends-container h2 {
                font-size: 0.9rem;
            }
            .friends-button {
                font-size: 0.600rem;
                top: 19px;
                right: 8px;
            }
        }
        .btn{
             background-color:#44eba7; 
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px;
           
        }
        .btn:hover{
            color:rgb(0, 0, 0);
            background-color: #44eba7;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
    <?php include_once('sidebar.php'); ?>
        <div class="col py-3">
        <div class="friends-container" id="invitefriends">
            <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="menu.php" style="color:black;" class="btn"><i class="bi bi-arrow-left"></i>Back</a>  
                </div>
           
            <h2>Invite Friends</h2>
            <!-- Withdrawal Request Form -->
            <div class="form-container mt-4">
                <form action="submit_withdrawal_request.php" method="post">
                <?php
                // Dynamically get the current page URL and generate the invite link
                $currentPageUrl = "https://" . $_SERVER['HTTP_HOST'] . "/register.php?refer_code=" . $refer_code;
                ?>
                <div class="mb-3">
                    <label for="link" class="form-label">Invite Link</label>
                    <input type="text" class="form-control" id="inviteLink" name="link" value="<?php echo $currentPageUrl; ?>" disabled>
                </div>

            <button type="button" id="copyButton" style="background-color:rgb(68 235 167); color:black;" class="btn_copy">
                <i class="fs-5 bi-copy"></i> Copy Link
            </button>
            <br><br>
            <button type="button" id="telegramButton" style="background-color:#3290ec; color:white;" class="btn">
                <i class="fs-5 bi-telegram"></i> Join Telegram
            </button>
            
                </form>
            </div>
            
        </div>
        
    </div>
    
    </div>
</div>
  

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var copyButton = document.getElementById('copyButton');
        var inviteLink = document.getElementById('inviteLink');

        copyButton.addEventListener('click', function() {
            // Select the text field
            inviteLink.select();
            inviteLink.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(inviteLink.value)
                .then(function() {
                    // Success message
                    alert('Link copied to clipboard!');
                })
                .catch(function(err) {
                    // Error message
                    console.error('Failed to copy: ', err);
                });
        });

        var telegramButton = document.getElementById('telegramButton');
        telegramButton.addEventListener('click', function() {
            // Redirect to the Telegram channel
            var telegramChannelUrl = <?php echo json_encode($telegram_channel); ?>;
            if (telegramChannelUrl && telegramChannelUrl !== "N/A" && telegramChannelUrl !== "Failed to fetch telegram_channel.") {
                window.open(telegramChannelUrl, '_blank');
            } else {
                alert('No valid Telegram channel URL available.');
            }
        });
    });
</script>

</body>
</html>