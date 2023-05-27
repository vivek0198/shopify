<?php
session_start();
$accessToken = $_SESSION['access_token'];

if (!$accessToken) {
    // Redirect to the login page if access token is not found
    header('Location: login.php');
    exit();
}

$authorId = 78; // Replace with the actual author ID you want to delete
$url = 'https://candidate-testing.api.royal-apps.io/api/v2/authors/' . $authorId;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: */*',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode === 204) {
    // Author successfully deleted, redirect to authors page
    header('Location: authors.php');
    exit();
} elseif ($httpCode === 404) {
    // Author not found, display an error message
    echo 'Author not found.';
    header('Location: authors.php');
    exit();
} else {
    // Handle other API errors
    echo 'Failed to delete the author.';
}
?>
