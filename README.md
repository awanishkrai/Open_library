# ADHYAY – Your Personal Open Library  

Adhyay is a web-based book platform that allows users to explore books, read PDFs, manage personal readlists, rate books, and engage in community-driven book club discussions.  

This project combines a PHP backend, a responsive TailwindCSS frontend, and a MySQL database to create an interactive digital library experience.

---

## Project Structure

# ADHYAY

A digital book platform that allows users to explore books, create reading lists, rate books, join book clubs, and participate in discussions.

---

## Project Directory Structure

```text
Adhyay/
├── README.md
├── database.sql
└── open library/
    ├── backend/
    │   ├── api/
    │   │   ├── add_book.php
    │   │   ├── auth.php
    │   │   ├── book_details.php
    │   │   ├── books.php
    │   │   ├── discussion.php
    │   │   ├── rating.php
    │   │   ├── readlist.php
    │   │   └── club_discussions/
    │   │       ├── comment.php
    │   │       ├── create.php
    │   │       ├── details.php
    │   │       ├── join_club.php
    │   │       ├── list.php
    │   │       ├── list_clubs.php
    │   │       └── list_members.php
    │   ├── auth/
    │   │   ├── login.php
    │   │   └── register.php
    │   ├── config.php
    │   └── db.php
    ├── frontend/
    │   ├── index.html
    │   ├── book.html
    │   ├── readlist.html
    │   ├── dashboard.html
    │   ├── club.html
    │   ├── add_book.html
    │   ├── login.html
    │   ├── register.html
    │   ├── js/
    │   │   ├── main.js
    │   │   └── book.js
    │   ├── background.jpg
    │   ├── falback.jpg
    │   ├── logo.jpeg
    │   ├── logo1.png
    │   ├── pic1.jpg
    │   ├── pic2.jpg
    │   ├── pic3.jpeg
    │   ├── pic4.webp
    │   └── pic5.jpg
    └── uploads/
        ├── 67f156fbc6e98_download (2).jpg
        ├── 67f156fbc70c6_George Orwell - 1984.pdf
        ├── 67f15803aa103_Introduction1.jpg
        ├── 67f15803aa2a0_requirements.pdf
        ├── 67f158aebe896_Introduction1.jpg
        ├── 67f158aebed75_George Orwell - 1984.pdf
        ├── 67f2b18e80951_pic4.jpg
        ├── 67f2b18e80d56_Bhagavad_Gita_As_It_Is.pdf
        ├── 67f2b2b288130_rig-veda.webp
        ├── 67f2b2b2882f9_RigVeda.pdf
        ├── 67f2b3b91daae_Capture.jpg
        ├── 67f2b3b91dc74_Hindi Book-Samved Sanhita.pdf
        ├── 67f2ba04602be_Valmiki-Ramayana-edited-600.jpg
        └── 67f2ba0460613_valmiki_ramayanam.pdf



## Features

- User Authentication – Register, login, and maintain sessions  
- Book Management – Add, list, and view books with images and PDFs  
- Readlist Management – Mark books as Want to Read, Reading, or Read  
- Book Ratings and Reviews – Submit 1–5 star ratings and comment on books  
- Book Clubs – Create clubs, join them, and participate in discussions  
- Responsive UI – Optimized for desktop and mobile devices  

---

## Database Schema

The database schema is defined in `database.sql`.  
Main tables:

- books – Stores book metadata (title, author, year, image_path, pdf_path)  
- users – Stores user credentials (username, password)  
- ratings – Stores book ratings per user  
- discussions – Stores user comments for books  
- club_discussions, club_members – Manages book clubs and members  
- comments – Stores discussion replies  

---

## Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/adhyay.git
cd adhyay/open library
2. Set Up Database
Create a MySQL database open_library

Import schema:

bash
Copy code
mysql -u root -p open_library < database.sql
3. Configure Backend
Edit backend/config.php:

php
Copy code
<?php
$host = "localhost";
$db_name = "open_library";
$username = "root";
$password = "";
?>
4. Run Locally
Place project in your local server root (htdocs for XAMPP)

Start Apache and MySQL

Visit:
http://localhost/open library/frontend/index.html

API Documentation
Authentication
POST /backend/auth/register.php
Registers a new user.

jsonc
Copy code
// Request
{
  "username": "awanish_rai",
  "password": "secure123"
}

// Response
{ "status": "success", "message": "User registered successfully" }
POST /backend/auth/login.php
Authenticates a user.

jsonc
Copy code
// Request
{
  "username": "awanish_rai",
  "password": "secure123"
}

// Response
{
  "status": "success",
  "token": "abc123xyz",
  "username": "awanish_rai"
}
Books
GET /backend/api/books.php
Fetch all books.

jsonc
Copy code
[
  {
    "id": 1,
    "title": "1984",
    "author": "George Orwell",
    "year": 1949,
    "image_path": "/uploads/book1.jpg",
    "pdf_path": "/uploads/book1.pdf"
  }
]
GET /backend/api/book_details.php?id={book_id}
Fetch details for a single book.

jsonc
Copy code
{
  "id": 1,
  "title": "1984",
  "author": "George Orwell",
  "year": 1949,
  "image_path": "/uploads/book1.jpg",
  "pdf_path": "/uploads/book1.pdf"
}
POST /backend/api/add_book.php
Add a new book.

jsonc
Copy code
// Request
{
  "title": "Bhagavad Gita",
  "author": "Vyasa",
  "year": 500,
  "image_path": "/uploads/gita.jpg",
  "pdf_path": "/uploads/gita.pdf"
}
Readlist
GET /backend/api/readlist.php?username=awanish_rai
Fetch user's readlist.

POST /backend/api/readlist.php
Update book status in readlist.

jsonc
Copy code
// Request
{
  "username": "awanish_rai",
  "book_id": 1,
  "status": "Reading"
}
Ratings
POST /backend/api/rating.php
Submit rating for a book.

jsonc
Copy code
// Request
{
  "username": "awanish_rai",
  "book_id": 1,
  "rating": 5
}
Discussions
GET /backend/api/discussion.php?book_id=1
Fetch all comments for a book.

POST /backend/api/discussion.php
Add a comment.

jsonc
Copy code
// Request
{
  "book_id": 1,
  "username": "awanish_rai",
  "comment": "This book is a masterpiece!"
}
Book Clubs
GET /backend/api/club_discussions/list_clubs.php
Fetch list of clubs.

POST /backend/api/club_discussions/create.php
Create a new club.

jsonc
Copy code
{
  "name": "Sci-Fi Readers Club",
  "description": "Discuss sci-fi classics"
}
POST /backend/api/club_discussions/join_club.php
Join an existing club.

GET /backend/api/club_discussions/list.php?club_id=1
Fetch discussions for a club.

Tech Stack
Frontend: HTML, Tailwind CSS, Vanilla JavaScript

Backend: PHP (REST-like APIs)

Database: MySQL

Hosting: Compatible with XAMPP/WAMP, or any VPS with PHP and MySQL

Contribution Guidelines
Fork the repository

Create a feature branch

Commit changes with descriptive messages

Open a pull request

Maintainer
Developed and maintained by Awanish Rai.
Contributions and suggestions are welcome!

License
This project is licensed under the MIT License – free to use and modify.

