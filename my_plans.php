<?php
include_once('includes/connection.php');
session_start();

// Check if the user is logged in
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Fetch user plans
$data = array("user_id" => $user_id);
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
    $claimData = array("user_id" => $user_id, "plan_id" => $plan_id);
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
        .purchase-btn, .claim-btn{
            background-color: #44eba7; 
            border-color: #44eba7; 
            color: black; 
            font-weight: 600;
            border-radius: 99999px;
            font-family: 'Poppins', Helvetica, sans-serif;
        }
        .purchase-btn:hover, .claim-btn:hover {
            background-color: #44eba7;
        }
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
        .activated-jobs-link {
            margin-bottom: 20px;
            background-color: #44eba7;
            border-radius: 10px;
        }
        .btn.watch-claim-btn {
        background-color: #007bff;
        color: white;
        font-weight: bold;
        border-radius: 9999px;
        padding: 10px 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    @media (max-width: 768px) {
   

    .purchase-btn, .claim-btn, .btn.watch-claim-btn {
        width: 100%; /* Makes buttons full width on small screens */
        padding: 15px;
        font-size:0.76rem;
    }
    }

    .days{
    background-color: gold;
}
.life{
    color: red;
}
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php include_once('sidebar.php'); ?>
            <div class="col py-3">
                <div class="activated-jobs-link">
                    <a href="plan.php" class="btn w-100 d-flex justify-content-center align-items-center">
                        <i class="bi bi-arrow-left" style="color: rgb(2, 2, 2); font-size: 1.5rem;font-weight: bold;"></i>
                        <span style="color: rgb(0, 0, 0); font-size: 1.2rem; font-weight: bold; flex-grow: 1; text-align: center;">Rented list</span>
                    </a>
                </div>
                <div id="plansSection" class="plansSection-container">
                    <div class="row">
                        <?php foreach ($plans as $plan): ?>
                        <div class="col-md-6 mb-4">
                            <div class="product-name-box">
                                <?php echo htmlspecialchars($plan['name']); ?>
                            </div>
                            <div class="plan-box">
                                <?php if (!empty($plan['image'])): ?>
                                <a data-lightbox="plan" href="<?php echo htmlspecialchars($plan['image']); ?>" data-title="<?php echo htmlspecialchars($plan['name']); ?>">
                                    <img src="<?php echo htmlspecialchars($plan['image']); ?>" alt="Plan image">
                                </a>
                                <?php else: ?>
                                <p>No Image Available</p>
                                <?php endif; ?>
                                <div class="plan-details">
                                    <p>Cost: <strong><?php echo '₹' . htmlspecialchars($plan['price']); ?></strong></p>
                                    <p>Invite Bonus: <strong><?php echo '₹' .htmlspecialchars($plan['invite_bonus']) ; ?></strong></p>
                                    <p>Daily Earnings: <strong><?php echo '₹' . htmlspecialchars($plan['daily_earnings']); ?></strong></p>
                                    <p><strong class="life"> LifeTime Earnings:</strong> <strong class="days">Unlimited Days</strong></p>
                                    <?php if ($plan['plan_id'] == 1): ?>
                                        <button class="btn watch-claim-btn" data-plan-id="<?php echo htmlspecialchars($plan['plan_id']); ?>" data-bs-toggle="modal" data-bs-target="#videoModal">
                                            Watch & Claim
                                        </button>
                                        <button class="btn claim-btn" data-plan-id="<?php echo htmlspecialchars($plan['plan_id']); ?>" disabled>Claim</button>
                                    <?php else: ?>
                                        <button class="btn claim-btn" data-plan-id="<?php echo htmlspecialchars($plan['plan_id']); ?>">Claim</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const videoPlayer = document.getElementById('videoPlayer');
    const videoModal = document.getElementById('videoModal');

    // Pause the video when the modal is closed
    videoModal.addEventListener('hidden.bs.modal', function () {
        videoPlayer.pause();
        videoPlayer.currentTime = 0;
    });

    // Watch & Claim button logic (only for plan_id == 1)
    document.querySelectorAll('.watch-claim-btn').forEach(button => {
        button.addEventListener('click', function () {
            const planId = this.getAttribute('data-plan-id');
            const claimButton = document.querySelector(`.claim-btn[data-plan-id="${planId}"]`);

            if (planId === "1") {
                // Reset video state and disable the claim button
                videoPlayer.currentTime = 0;
                videoPlayer.play();
                claimButton.disabled = true;
                claimButton.classList.remove('enabled');

                // Enable the claim button only when the video ends
                videoPlayer.onended = function () {
                    claimButton.disabled = false;
                    claimButton.classList.add('enabled');
                };
            }
        });
    });

    // Claim button logic
    document.querySelectorAll('.claim-btn').forEach(button => {
        button.addEventListener('click', function () {
            const planId = this.getAttribute('data-plan-id');
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
