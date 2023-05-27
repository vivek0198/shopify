<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Make API request to authenticate user and retrieve access token
    $url = 'https://candidate-testing.api.royal-apps.io/api/v2/token';
    $data = [
        'email' => $email,
        'password' => $password,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response !== false) {
        $responseData = json_decode($response, true);
        $accessToken = $responseData['token_key'];

        // Store the access token (you can use session or cookies)
        session_start();
        $_SESSION['access_token'] = $accessToken;

        // Redirect to the authors page or any other desired page
        header('Location: authors.php');
        exit();
    } else {
        // Handle login error
        echo 'Login failed. Please check your credentials.';
    }

    curl_close($ch);
}
