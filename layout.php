<?php
session_start();
$accessToken = $_SESSION['access_token'];

if (!$accessToken) {
    // Redirect to the login page if access token is not found
    header('Location: login.php');
    exit();
}

// Fetch user profile from the API
$url = 'https://candidate-testing.api.royal-apps.io/api/v2/token/refresh/' . $accessToken;
$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $accessToken
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$profileResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode === 200) {
    $profileData = json_decode($profileResponse, true);
} else {
    // Handle API error when fetching user profile
    echo 'Failed to fetch user profile from the API.';
    exit();
}
?>


    <nav>
        <ul>
            <li><a href="profile.php"><?= $profileData['first_name'] . ' ' . $profileData['last_name']; ?></a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

