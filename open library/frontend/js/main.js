document.addEventListener("DOMContentLoaded", function () {
  // 🔹 Login Function
  function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    fetch("http://localhost/new%20proj/open%20library/backend/auth/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          localStorage.setItem("user", JSON.stringify(data.user));
          alert("Login successful!");
          window.location.href = "index.html";
        } else {
          alert("Invalid credentials, try again.");
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  // 🔹 Register Function
  function register() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    fetch("http://localhost/new_proj/open_library/backend/auth/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Registration successful! You can now log in.");
          window.location.href = "login.html";
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  // 🔹 Logout Function
  function logout() {
    localStorage.removeItem("user");
    alert("Logged out successfully!");
    window.location.href = "login.html";
  }

  // 🔹 Check if User is Logged In
  function checkUserSession() {
    const user = localStorage.getItem("user")
      ? JSON.parse(localStorage.getItem("user"))
      : null;
    if (!user) {
      alert("You need to log in first!");
      window.location.href = "login.html";
    }
  }

  // 🔹 Fetch and Display Books
  function loadBooks() {
    const booksContainer = document.getElementById("booksList");
    if (!booksContainer) return console.error("Book list container not found!");

    fetch("http://localhost/new_proj/open_library/backend/api/books.php")
      .then((response) => response.json())
      .then((data) => {
        booksContainer.innerHTML = "";

        if (data.length === 0) {
          booksContainer.innerHTML = `<p class="text-gray-500">No books available.</p>`;
          return;
        }

        data.forEach((book) => {
          const bookItem = document.createElement("div");
          bookItem.classList.add(
            "p-4",
            "border",
            "rounded",
            "shadow-md",
            "bg-white",
            "mb-4"
          );

          bookItem.innerHTML = `
              <h3 class="text-xl font-bold">${book.title}</h3>
              <p class="text-gray-700">${book.author}</p>
              <a href="book.html?id=${book.id}" class="text-blue-500">View Details</a>
          `;

          booksContainer.appendChild(bookItem);
        });
      })
      .catch((error) => console.error("Error fetching books:", error));
  }

  // 🔹 Load Book Details
  function loadBookDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const bookId = urlParams.get("id");

    if (!bookId) return console.error("No book ID found in URL!");

    fetch(
      `http://localhost/new_proj/open_library/backend/api/book_details.php?id=${bookId}`
    )
      .then((response) => response.json())
      .then((book) => {
        const titleElement = document.getElementById("book-title");
        const authorElement = document.getElementById("book-author");
        const descriptionElement = document.getElementById("book-description");

        if (!titleElement || !authorElement || !descriptionElement) {
          console.error("Book detail elements not found!");
          return;
        }

        titleElement.innerText = book.title;
        authorElement.innerText = `Author: ${book.author}`;
        descriptionElement.innerText =
          book.description || "No description available.";
      })
      .catch((error) => console.error("Error fetching book details:", error));
  }

  // 🔹 Attach Functions to Window for Global Access
  window.login = login;
  window.register = register;
  window.logout = logout;
  window.checkUserSession = checkUserSession;
  window.loadBooks = loadBooks;
  window.loadBookDetails = loadBookDetails;
});
