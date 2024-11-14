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
