function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
  
  // Hide the toggle button when the sidebar is opened
  document.querySelector('.openbtn').style.display = 'none';
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
  
  // Show the toggle button when the sidebar is closed
  document.querySelector('.openbtn').style.display = 'block';
}
