# Bookshelf API

A simple REST API built with PHP for managing a my book collection. This project uses PHPMyAdmin for database management and provides basic CRUD operations for my books.

## Features

- Add new books to the collection
- Edit existing book information
- Remove books from the collection
- View all books in the collection

## Requirements

- PHP 7.4 or higher
- MySQL database
- PHPMyAdmin (for database management)
- MAMP, XAMPP, or similar local server environment

## Installation

1. Clone or download this repository to your local server directory:

   ```bash
   git clone https://github.com/ReedorReed/php-api
   cd php-api
   ```

2. Import the database:
   - Open PHPMyAdmin (typically at `http://localhost/phpmyadmin` or `http://localhost:8888/phpMyAdmin5` on Mac)
   - Create a new database (e.g., `bookshelf_db`)
   - Import the database structure (if you have a SQL file)

3. Configure database connection:
   - Open [`db.php`](db.php)
   - Update the database credentials:

     ```php
     $host = 'localhost';
     $dbname = 'bookshelf_db';
     $username = 'your_username';
     $password = 'your_password';
     ```

4. Start your local server (MAMP/XAMPP)

5. Access the API at `http://localhost/php-api/` or `http://localhost:8888/php-api/` on Mac

## Project Structure

```
php-api/
├── index.php          # Main entry point - displays all books
├── add.php            # Add new books to the collection
├── edit.php           # Edit existing book information
├── remove.php         # Remove books from the collection
├── db.php             # Database connection configuration
├── book_shelf_api.csv # CSV data file for import/export
├── .gitignore         # Git ignore file
└── README.md          # Readme file (the one you're reading)
```

## API Endpoints

### Get All Books

```
GET /index.php
```

Returns a list of all books in the collection.

### Add a Book

```
POST /add.php
```

Add a new book to the collection.

### Edit a Book

```
POST /edit.php
```

Update an existing book's information.

### Remove a Book

```
POST /remove.php
```

Delete a book from the collection.

## Database Setup

Create a database table using the following structure:

```sql
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20),
    published_year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Usage Example

### Adding a Book

```php
// POST request to add.php
$data = [
    'title' => 'Book Title',
    'author' => 'Author Name',
    'isbn' => '1234567890',
    'published_year' => 2024
];
```

### Editing a Book

```php
// POST request to edit.php
$data = [
    'id' => 1,
    'title' => 'Updated Title',
    'author' => 'Updated Author'
];
```

### Removing a Book

```php
// POST request to remove.php
$data = [
    'id' => 1
];
```

## CSV Import/Export

The project includes CSV functionality through [`book_shelf_api.csv`](book_shelf_api.csv) for easy data backup and migration.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## License

© Christian Reed

## Support

For issues or questions, please open an issue in the repository.

## Author

Christian Reed

---

**Note**: This is a learning project and should not be used in production without proper security implementations (SQL injection prevention, authentication, input validation, etc.).
