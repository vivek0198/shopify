<?php
session_start();
$accessToken = $_SESSION['access_token'];

if (!$accessToken) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit();
}

$authorsUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/authors?orderBy=id&direction=ASC&limit=12&page=1';
$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $accessToken, // Include the access token obtained during login
];

$ch = curl_init($authorsUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$authorsResponse = curl_exec($ch);
//print_r($authorsResponse); die();
curl_close($ch);

if ($authorsResponse !== false) {
    $authors = json_decode($authorsResponse, true);
} else {
    // Handle API error
    echo 'Failed to fetch authors from the API.';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Authors</title>
</head>
<body>
<?php require_once('layout.php'); ?>
    <h2>Authors</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Birthday</th>
            <th>Gender</th>
            <th>Place of Birth</th>
            <th>Action</th>
        </tr>
        <?php foreach ($authors['items'] as $author): ?>
        <tr>
            <td><?=$author['id'];?></td>
            <td><a href="view_author.php?author_id=<?=$author['id']?>" target="_blank"><?=$author['first_name'];?></a></td>
            <td><?=$author['last_name'];?></td>
            <td><?=$author['birthday'];?></td>
            <td><?=$author['gender'];?></td>
            <td><?=$author['place_of_birth'];?></td>
            <td>
                <?php
$booksUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/books?orderBy=id&direction=ASC&limit=12&page=1';
$headers = [
    'Accept: application/json',
    'Authorization: Bearer ' . $accessToken, // Include the access token obtained during login
];

$ch = curl_init($booksUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$booksResponse = curl_exec($ch);
curl_close($ch);

if ($booksResponse !== false) {
    $books = json_decode($booksResponse, true);
    $hasRelatedBooks = false;
    foreach ($books['items'] as $book) {
        if (isset($book['author']['id'])) {
            if ($book['author']['id'] == $author['id']) {
                $hasRelatedBooks = true;
                break;
            }
        }
    }
    if (!$hasRelatedBooks) {
        echo '<a href="delete_author.php?author_id=' . $author['id'] . '">Delete</a>';
    }
}
?>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
</body>
</html>
