document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const bookId = params.get("id");

  const bookDetailsContainer = document.getElementById("book-details");
  if (!bookDetailsContainer) {
    console.error("Error: 'book-details' div not found.");
    return;
  }

  if (!bookId) {
    bookDetailsContainer.innerHTML =
      "<p class='text-red-500'>Error: No book selected.</p>";
    return;
  }

  fetch("../backend/api/book_details.php?id=" + bookId) // ✅ Use relative path
    .then((response) => response.json())
    .then((book) => {
      if (!book || !book.title) {
        bookDetailsContainer.innerHTML =
          "<p class='text-red-500'>Book not found.</p>";
        return;
      }
      bookDetailsContainer.innerHTML = `
        <h2 class="text-xl font-bold">${book.title}</h2>
        <p><strong>Author:</strong> ${book.author}</p>
        <p><strong>Year:</strong> ${book.year}</p>`;
    })
    .catch((error) => {
      console.error("Error fetching book details:", error);
      bookDetailsContainer.innerHTML =
        "<p class='text-red-500'>Failed to load book details.</p>";
    });
});
