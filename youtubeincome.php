<?php
include_once('includes/connection.php');
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id']; // Retrieve user ID from session

// Initialize variables
$youtubeIncomeList = [];
$message = "";

// Fetch YouTube Income List
if ($user_id) {
    $apiUrl = API_URL . "youtube_income_list.php";
    $data = array("user_id" => $user_id);

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    if ($response !== false) {
        $responseData = json_decode($response, true);
        $youtubeIncomeList = $responseData['data'] ?? [];
    }
    curl_close($curl);
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnlink'])) {
  $link = trim($_POST['link']);

  if (!empty($link)) {
      $data = array(
          "user_id" => $user_id,
          "link" => $link,
      );

      $apiUrl = API_URL . "youtube_income.php";
      $curl = curl_init($apiUrl);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
      $response = curl_exec($curl);
  
      if ($response === false) {
          // Error in cURL request
          echo "Error: " . curl_error($curl);
      }else {
            // Successful API response
            $responseData = json_decode($response, true);
            if ($responseData !== null) {
                if ($responseData["success"]) {
                    // Success: YouTube Income added successfully
                    
                    $_SESSION['message'] = $responseData["message"];
                    //  $_SESSION['alert_type'] = "success";
                } else {
                    // Failure: Link already exists
                    $_SESSION['message'] = $responseData["message"];
                    //  $_SESSION['alert_type'] = "warning";
                }
              }
      }
      curl_close($curl);
  } else {
      $message = "Please enter a valid video link.";
  }
  

  // Redirect to the same page to prevent resubmission
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['message']) . "');</script>";
    unset($_SESSION['message']);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="admin_v1/dist/img/jiyo">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">  <title>YouTuber Income</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }
    .container {
      /* border: 2px solid black; */
      /* width: 100%; */
      /* margin: 50px auto; */
      padding: 20px;
      background-color: white;
      /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
    }
    .header {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .form-container {
      margin-bottom: 30px;
    }
    input[type="text"] {
      width: 50%;
      padding: 10px;
      margin-right: 10px;
      /* border: 2px solid black; */
      border-radius: 4px;
    }
    button {
      /* padding: 10px 20px; */
      font-size: 16px;
      border: 2px solid black;
      border-radius: 8px;
      background-color: white;
      cursor: pointer;
    }
    button:hover {
      background-color: black;
      color: white;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table th, table td {
      border: 1px solid black;
      padding: 10px;
      text-align: left;
    }
    /* .amount {
      color: green;
      font-weight: bold;
    }
    .status {
      color: green;
      font-weight: bold;
    } */
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
          .btn{
             background-color:#44eba7; 
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px;
            padding: 10px;
           
        }
        .btn:hover{
            color:rgb(0, 0, 0);
            background-color: #44eba7;
        }
        .btn-details {
  background-color:rgb(223, 50, 50);
  border: none;
  color: black;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 999px;
}

.btn-details:hover {
  background-color:rgb(233, 48, 45);
  color: white;
}

/* .modal-content {
  background: linear-gradient(135deg,rgb(230, 224, 224),rgb(255, 255, 255));
  border-radius: 12px;
  color: #2d3436;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.modal-title {
  font-size: 22px;
  font-weight: bold;
  text-align: center;
}

.pop-list
 {
  list-style-type: none;
  padding-left: 0;

} */
 .card {
            border: none;
            border-radius: 10px;
        }
        .card-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #343a40;
        }
        .list-group-item {
            font-size: 1rem;
            color: #495057;
        }
        h4 {
            font-size: 1.2rem;
            font-weight: bold;
            color: #495057;
        }
        ul {
            padding-left: 1.5rem;
        }
        li {
            line-height: 1.8;
        }
        .text-left {
            text-align: left;
        }

        .mt-3{
          margin-left: 50px;
        }

         @media (max-width: 768px) {
      .header {
        font-size: 1.5rem;
      }
      .form-container input, .form-container button {
        width: 100%;
        margin-bottom: 10px;
      }
      .btn {
        padding: 8px 16px;
      }
    }
  </style>
</head>
<body>
    
  <div class="container">
    <!-- Message Display Section -->
  <!-- Message Display Section -->
<?php if ($message): ?>
    <div class="alert alert-info" role="alert">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>


    
    <div class="header">YouTuber Income</div>
    
    <div class="form-container">
        
      <form method="POST" action="youtubeincome.php">
<div class="transaction-container" id="transactions">
                 <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="menu.php" style="color:black;" class="btn"><i style="color:rgb(2, 2, 2); font-size: 1rem;" class="bi bi-arrow-left"></i>Back</a>
                  <!-- <button type="button" class="btn btn-details" data-bs-toggle="modal" data-bs-target="#moreDetailsModal"> More Details</button> -->
                </div>
             
        <input type="text" name="link" placeholder="Paste your video link" required />
        
        <button type="submit" name="btnlink"  class="btn">Submit</button>
        
      </form>
      
    </div>
   <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="no">Video Link</th>
                <th class="no">Amount</th>
                <th class="no">DateTime</th>
                <th class="no">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($youtubeIncomeList)): ?>
                <?php foreach ($youtubeIncomeList as $income): ?>
                    <tr>
                        <td><a href="<?= htmlspecialchars($income['link']) ?>" target="_blank"><?= htmlspecialchars($income['link']) ?></a></td>
                        <td><?= htmlspecialchars(number_format($income['amount'], 2)) ?></td>
                        <td><?= htmlspecialchars($income['datetime']) ?></td>
                        <td>
                            <?php
                            switch ($income['status']) {
                                case 0:
                                    echo '<span class="badge bg-warning">Wait for Approvals</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge bg-success">Paid</span>';
                                    break;
                                case 2:
                                    echo '<span class="badge bg-danger">Cancelled</span>';
                                    break;
                                default:
                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                    break;
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No income records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

   <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body text-left">
                <h1 class="card-title mb-4">YouTuber Earnings Plan <span role="img" aria-label="mobile">📱</span></h1>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>1.Earnings per view: ₹1</strong></li>
                    <li class="list-group-item"><strong>2.Minimum views required: 100 views</strong></li>
                    <li class="list-group-item"><strong>3.Extra incentive for 5000 views crossed: ₹1000</strong></li>
                    <li class="list-group-item"><strong>4.Submission Rule:</strong> 
                </ul>
                <div class="mt-3">
                    
                    <ul>
                        <li>YouTubers can only submit their video for <strong>verification</strong> once they reach the maximum views.</li>
                        <li>After submission, the views will be verified, and the final earnings will be calculated.</li>
                        <li><strong>Resubmission is not allowed</strong> for the same video once it's submitted for verification.</li>
                        <li><strong>The video must be related to promoting our app,</strong> not any other content.</li>
                        <li><strong>Minimum video duration:</strong> The video or shorts must be at least<strong> 30 seconds</strong> long.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
