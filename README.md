# Book Management System

This is a Book Management System web application that allows users to add and view books. It integrates with an API to retrieve and store book data.

## Features

- User authentication: Users can log in to access the book management functionality.
- Add Book: Users can add new books to the system by providing details such as author, title, release date, description, ISBN, format, and number of pages.
- View Books: Users can view a list of books stored in the system. The list displays the book ID, title, release date, description, ISBN, format, number of pages, and author information.

## Technologies Used

- PHP: Backend scripting language used to handle server-side logic and API integration.
- HTML: Markup language used for creating the web pages and user interface.
- CSS: Styling language used for designing the appearance and layout of the web pages.
- cURL: PHP library used for making HTTP requests to the API endpoints.
- JavaScript: Used for client-side interactions and form validations.

## Setup and Installation

1. Clone the repository or download the project files.

2. Configure a web server (such as Apache) to serve the PHP files.
3. Deploy the project to your web server.

4. Access the application through the web browser.

## Usage

1. Open the application in your web browser.

2. If you are not logged in, you will be redirected to the login page. Enter your credentials to log in.

3. After logging in, you will be redirected to the homepage where you can add books using the provided form.

4. Fill in the required details for the book (author, title, release date, description, ISBN, format, number of pages).

5. Click the "Add Book" button to submit the form. The book will be created and added to the system.

6. You can view the list of books on the homepage, which displays the book details and author information.

7. To log out, click on the "Logout" link in the navigation menu.

## API Integration

This application integrates with an API to fetch and store book data. The API endpoints are specified in the PHP code, and cURL is used to make HTTP requests to the API.

## License

This project is licensed under the [MIT License](LICENSE).

