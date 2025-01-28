<?php
include_once('../includes/crud.php');
include_once('../library/jwt.php');

// Function to generate JWT token
function generate_token($user_id, $mobile){
    $jwt = new JWT();
    $payload = [
        'iat' => time(), /* issued at time */
        'iss' => 'eKart', // Issuer
        'exp' => time() + (30 * 60), /* expires after 30 minutes */
        'sub' => 'eKart Authentication', // Subject
        'user_id' => $user_id,   // Pass user_id in the token
        'mobile' => $mobile      // Pass mobile in the token
    ];
    $token = $jwt::encode($payload, JWT_SECRET_KEY);
    return $token;
}

// Function to verify JWT token
function verify_token(){
    $jwt = new JWT();
    try {
        // Get the token from the Authorization header
        $token = $jwt->getBearerToken();
    } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
        echo json_encode($response);
        return false;
    }

    if (!empty($token)) {
        try {
            // Decode the token and validate the payload
            $payload = $jwt->decode($token, JWT_SECRET_KEY, ['HS256']);

            // Validate the issuer
            if (!isset($payload->iss) || $payload->iss != 'eKart') {
                $response['error'] = true;
                $response['message'] = 'Invalid Hash';
                echo json_encode($response);
                return false;
            }

            // Validate the expiration time
            if (time() > $payload->exp) {
                $response['error'] = true;
                $response['message'] = 'Token has expired';
                echo json_encode($response);
                return false;
            }

            // Return the user_id from the token so it can be validated against the requested user_id
            return $payload->user_id;
            
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
            echo json_encode($response);
            return false;
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Unauthorized access not allowed";
        echo json_encode($response);
        return false;
    }
}
?>
