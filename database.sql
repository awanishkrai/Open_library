USE open_library;

-- Drop the existing books table if it exists
DROP TABLE IF EXISTS books;

-- Create a new books table with image_path before pdf_path
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    image_path VARCHAR(255),   -- 🖼️ Book cover image path
    pdf_path VARCHAR(255)      -- 📄 PDF file path
);

-- Insert sample data (image_path and pdf_path are left NULL for now)
INSERT INTO books (title, author, year, image_path, pdf_path) VALUES
('1984', 'George Orwell', 1949, NULL, NULL),
('To Kill a Mockingbird', 'Harper Lee', 1960, NULL, NULL),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, NULL, NULL);

CREATE TABLE discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    UNIQUE (book_id, username)
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
);
CREATE TABLE club_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(club_id, user_id), -- Prevent duplicate entries
    FOREIGN KEY (club_id) REFERENCES book_clubs(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE club_discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    book_id VARCHAR(100) NOT NULL,  -- Open Library ID
    title VARCHAR(255) NOT NULL,
    started_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES book_clubs(id),
    FOREIGN KEY (started_by) REFERENCES users(id)
);
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discussion_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES discussions(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
