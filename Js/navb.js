function toggleNav() {
  const sidebar = document.getElementById("mySidebar");

  if (sidebar.classList.contains('collapsed')) {
      sidebar.classList.remove('collapsed');
      main.style.marginLeft = "250px";
  } else {
      sidebar.classList.add('collapsed');
      main.style.marginLeft = "70px";
  }
}
// Function to highlight the selected link
function setActiveLink(linkElement) {
  const links = document.querySelectorAll(".nav-link");
  
  // Remove active class from all links
  links.forEach(link => link.classList.remove("active"));
  
  // Add active class to the clicked link
  linkElement.classList.add("active");

  // Store the active link in localStorage
  localStorage.setItem("activeLink", linkElement.getAttribute("href"));
}

// Load the active link from localStorage on page load
document.addEventListener("DOMContentLoaded", () => {
  const savedActiveLink = localStorage.getItem("activeLink");
  if (savedActiveLink) {
      const activeElement = document.querySelector(`a[href='${savedActiveLink}']`);
      if (activeElement) {
          activeElement.classList.add("active");
      }
  }

  // Add click event listeners to all nav links
  const links = document.querySelectorAll(".nav-link");
  links.forEach(link => {
      link.addEventListener("click", function () {
          setActiveLink(this);
      });
  });
});