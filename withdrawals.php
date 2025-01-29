<?php
include_once('includes/connection.php');
session_start();

// Check if the user is logged in
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Fetch the authentication token from the session
$token = isset($_SESSION['token']) ? $_SESSION['token'] : null;

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
$apiUrl = API_URL . "withdrawals_list.php";

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

$response = curl_exec($curl);

if ($response === false) {
    echo "Error: " . curl_error($curl);
    $userdetails = []; // Initialize as an empty array in case of error
} else {
    // Parse the API response
    $responseData = json_decode($response, true);
    
    // Check if token is expired or invalid
    if ($responseData === null || (isset($responseData["success"]) && !$responseData["success"])) {
        if (isset($responseData["message"]) && stripos($responseData["message"], "token") !== false) {
            // Token is expired or invalid -> Clear session and redirect to login
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
        $userdetails = []; // If there's an error or no data
    } else {
        // Extract the withdrawal details from the response
        $userdetails = $responseData["data"];
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Additional styles for the boxes */
        .info-box {
            background-color: rgb(68 235 167);
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
        .withdrawal-container {
            position: relative; 
            padding: 20px; 
        }
        .withdrawal-container h2 {
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .withdrawal-button {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1rem;
            border-color: #44eba7; 
            font-weight: 600;
            border-radius: 99999px;
            background-color: #44eba7; 
        }
       tr{
            border: 2px solid black ;
            
        }

        tr .td{
          background-color: #44eba7;
          text-align: center;
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
        @media (max-width: 576px) {
            .withdrawal-container h2 {
                font-size: 1.5rem;
            }
            .withdrawal-button {
                font-size: 0.650rem;
                top: 21px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
    <?php include_once('sidebar.php'); ?>
        <div class="col py-3">
            <div class="withdrawal-container" id="withdrawals">
                
                 <div class="d-flex justify-content-between align-items-center mb-3">
                    
                    <a href="menu.php" style="color:black;" class="btn"><i style="color:rgb(2, 2, 2); font-size: 1rem;" class="bi bi-arrow-left"></i>Back</a>
                    
                    
                </div>
                <h2 class="m-1">Withdrawal List</h2>
                <a href="withdrawal_request.php"  style=" color:black;" class="btn withdrawal-button">Request Withdrawal</a>
                
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="td" scope="col">S.No</th>
                            <th class="td" scope="col">Status</th>
                            <th class="td" scope="col">Amount</th>
                            <th class="td" scope="col">DateTime</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- Loop through all withdrawals and display each one -->
                        <?php foreach ($userdetails as $index => $withdrawal): ?>
                            <tr>
                                <th scope="row"><?php echo $index + 1; ?></th>
                                <td>
                                    <?php 
                                    if ($withdrawal['status'] === '1') {
                                        echo '<span class="text-success">Paid</span>';
                                    } elseif ($withdrawal['status'] === '0') {
                                        echo '<span class="text-primary">Not Paid</span>';
                                    } elseif ($withdrawal['status'] === '2') {
                                         echo '<span class="text-danger">Cancelled</span>';
                                    } 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($withdrawal['amount']); ?></td>
                                <td><?php echo htmlspecialchars($withdrawal['datetime']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($userdetails)): ?>
                            <tr>
                                <td colspan="4">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
            
        </div>
        
    </div>
    
</div>

<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
