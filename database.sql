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
