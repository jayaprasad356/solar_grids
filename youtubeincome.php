<?php
include_once('includes/connection.php');
session_start();

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null; // Ensure user_id is set

if ($user_id) {
    $apiUrl = API_URL . "youtube_income_list.php";
    $data = array("user_id" => $user_id);

    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    if ($response === false) {
        die("Error: " . curl_error($curl));
    }

    curl_close($curl);

    // Parse the API response
    $responseData = json_decode($response, true);
    $youtubeIncomeList = $responseData['data'] ?? [];
}

if (!$user_id) {
    header("Location: login.php");
    exit();
}


if (isset($_POST['btnlink'])) {
    $link = $_POST['link'];
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
    } else {
        // Successful API response
        $responseData = json_decode($response, true);
        if ($responseData !== null && isset($responseData["success"])) {
            $message = $responseData["message"];
            if (isset($responseData["link"])) {
                $_SESSION['link'] = $responseData['link'];
            }
        } else {
            $message = "Failed to insert data";
        }
    }
        curl_close($curl);

  }
    ?>
            
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <title>YouTuber Income</title>
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
        
  </style>
</head>
<body>
    
  <div class="container">
    
    
    <div class="header">YouTuber Income</div>
    
    <div class="form-container">
        
      <form method="POST" action="youtubeincome.php">
<div class="transaction-container" id="transactions">
                 <div class="d-flex justify-content-between align-items-center mb-2">
                    <a href="menu.php" style="color:black;" class="btn"><i style="color:rgb(2, 2, 2); font-size: 1rem;" class="bi bi-arrow-left"></i>Back</a>
                    
                </div>
        <input type="text" name="link" placeholder="Paste your video link" required />
        
        <button type="submit" name="btnlink"   class="btn">Submit</button>
      </form>
      
    </div>
    <table>
      <thead>
        <tr>
          <th class="no">Video Link</th>
          <th class="no">Amount</th>
          <th class="no">Status</th>
        </tr>
      </thead>
        <tbody>
                     <?php if (!empty($youtubeIncomeList)): ?>
          <?php foreach ($youtubeIncomeList as $income): ?>
            <tr>
              <td><a href="<?= htmlspecialchars($income['link']) ?>" target="_blank"><?= htmlspecialchars($income['link']) ?></a></td>
              <td><?= htmlspecialchars(number_format($income['amount'], 2)) ?></td>
              <td>
                <?php
                switch ($income['status']) {
                    case 0:
                        echo '<span class="badge bg-warning">Not Verified</span>';
                        break;
                    case 1:
                        echo '<span class="badge bg-success">Verified</span>';
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
            <td colspan="3" class="text-center">No income records found.</td>
          </tr>
        <?php endif; ?>
                    </tbody>
    </table>
  </div>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
