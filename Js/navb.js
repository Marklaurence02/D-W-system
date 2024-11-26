function toggleNav() {
  const sidebar = document.getElementById("mySidebar");
  const mainContent = document.getElementById("main-content");
  const isMobile = window.innerWidth <= 768;

  if (isMobile) {
      // Mobile behavior
      sidebar.classList.toggle('show');
      document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
  } else {
      // Desktop behavior
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('sidebar-collapsed');
      
      // Store the state
      localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
  }
}

// Handle resize
window.addEventListener('resize', function() {
  const sidebar = document.getElementById("mySidebar");
  const mainContent = document.getElementById("main-content");
  const isMobile = window.innerWidth <= 768;

  if (isMobile) {
      sidebar.classList.remove('collapsed');
      mainContent.classList.remove('sidebar-collapsed');
      sidebar.classList.remove('show');
      document.body.style.overflow = '';
  } else {
      // Restore desktop state
      const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      sidebar.classList.toggle('collapsed', isCollapsed);
      mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
  }
});

// On page load
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById("mySidebar");
  const mainContent = document.getElementById("main-content");
  
  if (window.innerWidth > 768) {
      // Restore desktop state
      const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
      sidebar.classList.toggle('collapsed', isCollapsed);
      mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
  }
});

// Function to highlight the selected link
function setActiveLink(linkElement) {
  const links = document.querySelectorAll(".nav-link");
  
  // Remove active class from all links
  links.forEach(link => link.classList.remove("active"));
  
  // Add active class to the clicked link
  linkElement.classList.add("active");

  // Store the active link in localStorage
  localStorage.setItem("activeLink", linkElement.getAttribute("href"));

  // Scroll to top of the page
  window.scrollTo(0, 0);
}

// Load the active link from localStorage on page load
document.addEventListener("DOMContentLoaded", () => {
  const savedActiveLink = localStorage.getItem("activeLink");
  const links = document.querySelectorAll(".nav-link");
  
  if (savedActiveLink) {
      const activeElement = document.querySelector(`a[href='${savedActiveLink}']`);
      if (activeElement) {
          activeElement.classList.add("active");
      } else {
          // If saved link not found, set first link as active
          links[0]?.classList.add("active");
      }
  } else {
      // If no saved link, set first link as active
      links[0]?.classList.add("active");
  }

  // Add click event listeners to all nav links
  links.forEach(link => {
      link.addEventListener("click", function () {
          setActiveLink(this);
      });
  });
});
