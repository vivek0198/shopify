<?php
session_start();
$accessToken = $_SESSION['access_token'];

if (!$accessToken) {
    // Redirect to the login page if access token is not found
    header('Location: login.php');
    exit();
}

$authorId = $_GET['author_id']; // Get the author ID from the URL parameter

// Fetch author details from the API
$authorUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/authors/' . $authorId;
$authorHeaders = [
    'Accept: application/json',
    'Authorization: Bearer ' . $accessToken,
];

$authorCh = curl_init($authorUrl);
curl_setopt($authorCh, CURLOPT_HTTPHEADER, $authorHeaders);
curl_setopt($authorCh, CURLOPT_RETURNTRANSFER, true);

$authorResponse = curl_exec($authorCh);
$httpCode = curl_getinfo($authorCh, CURLINFO_HTTP_CODE);

curl_close($authorCh);

if ($httpCode === 200) {
    $authorData = json_decode($authorResponse, true);

    // Fetch books for the author from the API
    $booksUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/books?author_id=' . $authorId;
    $booksHeaders = [
        'Accept: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $booksCh = curl_init($booksUrl);
    curl_setopt($booksCh, CURLOPT_HTTPHEADER, $booksHeaders);
    curl_setopt($booksCh, CURLOPT_RETURNTRANSFER, true);

    $booksResponse = curl_exec($booksCh);
    $booksHttpCode = curl_getinfo($booksCh, CURLINFO_HTTP_CODE);

    curl_close($booksCh);

    if ($booksHttpCode === 200) {
        $booksData = json_decode($booksResponse, true);
    } else {
        // Handle API error when fetching books
        echo 'Failed to fetch books for the author.';
        exit();
    }
} else {
    // Handle API error when fetching author details
    echo 'Failed to fetch author details from the API.';
    exit();
}

// Function to delete a book
function deleteBook($bookId, $accessToken)
{
    $deleteUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/books/' . $bookId;
    $deleteHeaders = [
        'Accept: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $deleteCh = curl_init($deleteUrl);
    curl_setopt($deleteCh, CURLOPT_HTTPHEADER, $deleteHeaders);
    curl_setopt($deleteCh, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($deleteCh, CURLOPT_RETURNTRANSFER, true);

    $deleteResponse = curl_exec($deleteCh);
    $deleteHttpCode = curl_getinfo($deleteCh, CURLINFO_HTTP_CODE);

    curl_close($deleteCh);

    if ($deleteHttpCode === 204) {
        return true; // Book deleted successfully
    } else {
        return false; // Failed to delete the book
    }
}

// Check if the delete request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'])) {
    $bookIdToDelete = $_POST['delete_book'];

    // Delete the book
    $isDeleted = deleteBook($bookIdToDelete, $accessToken);

    if ($isDeleted) {
        // Redirect to the same page to refresh the books list
        header('Location: author.php?author_id=' . $authorId);
        exit();
    } else {
        // Handle book deletion error
        echo 'Failed to delete the book.';
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Author - <?=$authorData['first_name'] . ' ' . $authorData['last_name'];?></title>
</head>
<body>
<?php require_once('layout.php'); ?>
    <h2>Author - <?=$authorData['first_name'] . ' ' . $authorData['last_name'];?></h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Release Date</th>
            <th>ISBN</th>
            <th>Format</th>
            <th>Number of Pages</th>
            <th>Action</th>
        </tr>
        <?php foreach ($booksData['items'] as $book): ?>
        <tr>
            <td><?=$book['id'];?></td>
            <td><?=$book['title'];?></td>
            <td><?=$book['release_date'];?></td>
            <td><?=$book['isbn'];?></td>
            <td><?=$book['format'];?></td>
            <td><?=$book['number_of_pages'];?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                    <input type="hidden" name="delete_book" value="<?=$book['id'];?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach;?>
    </table>
</body>
</html>
