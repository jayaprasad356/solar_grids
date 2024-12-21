<?php
include_once('includes/connection.php');
session_start();

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null; // Ensure user_id is set

if (!$user_id) {
    header("Location: login.php");
    exit();
}

$data = array(
    "user_id" => $user_id,
);

$apiUrl = API_URL . "transactions_list.php";

$curl = curl_init($apiUrl);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
    $userdetails = [];
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Store transaction details
        $userdetails = $responseData["data"];
    } else {
     
        $userdetails = [];
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
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
        .transaction-container {
            position: relative; 
            padding: 20px; 
        }
        tr{
            border: 2px solid black ;
            
        }

        tr .no{
          background-color: #44eba7;
          text-align: center;
        }
        .td{
            text-align: center;
        }
         .btn-back{
             padding: 15px; 
             margin-bottom: 15px;
            font-size:15px;
            top: 20px;
            right: 20px;
            text-decoration: none;
            border-color: #44eba7; 
            font-weight: 600;
            border-radius: 99999px;
            background-color: #44eba7;
            margin-left: 20px;
           
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
            <div class="transaction-container" id="transactions">
                 <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="menu.php" style="color:black;" class="btn"><i style="color:rgb(2, 2, 2); font-size: 1rem;" class="bi bi-arrow-left"></i>Back</a>
                    
                </div>
                <h2 class="text-center mb-4">Transactions List</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="no" scope="col">S.No</th>
                            <th class="no" scope="col">Type</th>
                            <th class="no" scope="col">Amount</th>
                            <th class="no" scope="col">DateTime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through all withdrawals and display each one -->
                        <?php foreach ($userdetails as $index => $transaction): ?>
                            <tr>
                                <th class="td" scope="row"><?php echo $index + 1; ?></th>
                                <td class="td"><?php echo htmlspecialchars($transaction['type']); ?></td>
                                <td class="td"><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                <td class="td"><?php echo htmlspecialchars($transaction['datetime']); ?></td>
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
