// CKEditor5 Build Classic
ClassicEditor
  .create(document.querySelector('#editor'))
  .then(editor => {
    console.log('Editor was initialized');
  })
  .catch(error => {
    console.log(error.stack);
  });

// Show And Hide Sidebar
(function() {
  const toggleBtn = document.getElementById("toggle-btn"),
        cancelBtn = document.querySelector(".fa.fa-times"),
        sideBar = document.querySelector(".sidebar");
        
  toggleBtn.addEventListener("click", event => {
    sideBar.classList.add("show-sidebar");
  });

  cancelBtn.addEventListener("click", event => {
    sideBar.classList.remove("show-sidebar");
  });
})();
