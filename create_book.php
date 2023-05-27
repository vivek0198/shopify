<?php
session_start();
$accessToken = $_SESSION['access_token'];

if (!$accessToken) {
    // Redirect to the login page if access token is not found
    header('Location: login.php');
    exit();
}

// Function to fetch the list of authors
function fetchAuthors($accessToken) {
    $authorsUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/authors?orderBy=id&direction=ASC&limit=12&page=1';
    $authorsHeaders = [
        'Accept: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    $authorsCh = curl_init($authorsUrl);
    curl_setopt($authorsCh, CURLOPT_HTTPHEADER, $authorsHeaders);
    curl_setopt($authorsCh, CURLOPT_RETURNTRANSFER, true);

    $authorsResponse = curl_exec($authorsCh);
    $authorsHttpCode = curl_getinfo($authorsCh, CURLINFO_HTTP_CODE);

    curl_close($authorsCh);

    if ($authorsHttpCode === 200) {
        return json_decode($authorsResponse, true);
    } else {
        return false; // Failed to fetch authors
    }
}

// Function to create a new book
function createBook($data, $accessToken) {
    $createUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/books';
    $createHeaders = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    $createCh = curl_init($createUrl);
    curl_setopt($createCh, CURLOPT_HTTPHEADER, $createHeaders);
    curl_setopt($createCh, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($createCh, CURLOPT_POST, true);
    curl_setopt($createCh, CURLOPT_POSTFIELDS, json_encode($data));

    $createResponse = curl_exec($createCh);
    $createHttpCode = curl_getinfo($createCh, CURLINFO_HTTP_CODE);

    curl_close($createCh);

    if ($createHttpCode === 201) {
        return true; // Book created successfully
    } else {
        return false; // Failed to create the book
    }
}

// Check if the create request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_book'])) {
    $authorId = $_POST['author_id'];
    $title = $_POST['title'];
    $releaseDate = $_POST['release_date'];
    $description = $_POST['description'];
    $isbn = $_POST['isbn'];
    $format = $_POST['format'];
    $numberOfPages = $_POST['number_of_pages'];

    // Prepare the data for creating a new book
    $bookData = [
        'author' => [
            'id' => $authorId
        ],
        'title' => $title,
        'release_date' => $releaseDate,
        'description' => $description,
        'isbn' => $isbn,
        'format' => $format,
        'number_of_pages' => $numberOfPages
    ];

    // Create the book
    $isCreated = createBook($bookData, $accessToken);

    if ($isCreated) {
        // Redirect to the same page to refresh the books list
        header('Location: books.php');
        exit();
    } else {
        // Handle book creation error
        echo 'Failed to create the book.';
        exit();
    }
}

// Fetch the list of authors
$authorsData = fetchAuthors($accessToken);

if (!$authorsData) {
    // Handle error when fetching authors
    echo 'Failed to fetch authors.';
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>
    <h2>Add Book</h2>
    <form method="POST" action="">
        <label for="author_id">Author:</label>
        <select name="author_id" id="author_id">
            <?php foreach ($authorsData['items'] as $author): ?>
            <option value="<?= $author['id']; ?>"><?= $author['first_name'] . ' ' . $author['last_name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br><br>
        <label for="release_date">Release Date:</label>
        <input type="date" name="release_date" id="release_date" required><br><br>
        <label for="description">Description:</label>
        <input type="text" name="description" id="description" required><br><br>
        <label for="isbn">ISBN:</label>
        <input type="text" name="isbn" id="isbn" required><br><br>
        <label for="format">Format:</label>
        <input type="text" name="format" id="format" required><br><br>
        <label for="number_of_pages">Number of Pages:</label>
        <input type="number" name="number_of_pages" id="number_of_pages" required><br><br>
        <button type="submit" name="create_book">Add Book</button>
    </form>

    <h2>Books</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Release Date</th>
            <th>Description</th>
            <th>ISBN</th>
            <th>Format</th>
            <th>Number of Pages</th>
            <th>Author</th>
        </tr>
        <?php
        // Fetch books from the API
        $booksUrl = 'https://candidate-testing.api.royal-apps.io/api/v2/books';
        $booksHeaders = [
            'Accept: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $booksCh = curl_init($booksUrl);
        curl_setopt($booksCh, CURLOPT_HTTPHEADER, $booksHeaders);
        curl_setopt($booksCh, CURLOPT_RETURNTRANSFER, true);

        $booksResponse = curl_exec($booksCh);
        $booksHttpCode = curl_getinfo($booksCh, CURLINFO_HTTP_CODE);

        curl_close($booksCh);

        if ($booksHttpCode === 200) {
            $booksData = json_decode($booksResponse, true);

            foreach ($booksData['items'] as $book) {
                // Find the author of the book
                $author = null;
                foreach ($authorsData['items'] as $authorData) {
                    if ($authorData['id'] === $book['author']['id']) {
                        $author = $authorData;
                        break;
                    }
                }

                if ($author) {
                    echo '<tr>';
                    echo '<td>' . $book['id'] . '</td>';
                    echo '<td>' . $book['title'] . '</td>';
                    echo '<td>' . $book['release_date'] . '</td>';
                    echo '<td>' . $book['description'] . '</td>';
                    echo '<td>' . $book['isbn'] . '</td>';
                    echo '<td>' . $book['format'] . '</td>';
                    echo '<td>' . $book['number_of_pages'] . '</td>';
                    echo '<td>' . $author['first_name'] . ' ' . $author['last_name'] . '</td>';
                    echo '</tr>';
                }
            }
        } else {
            // Handle error when fetching books
            echo '<tr><td colspan="8">Failed to fetch books.</td></tr>';
        }
        ?>
    </table>
</body>
</html>
