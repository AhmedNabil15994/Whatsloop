var menuBtn =  document.querySelector(".menu");
var sideBar =  document.querySelector(".side--bar");
var pageCon = document.querySelector(".page-container");



menuBtn.onclick = function openSideBar() {

    sideBar.classList.toggle("expand");
    pageCon.classList.toggle("sided");

 }
 