/* ============================================
   Practical 4
   JavaScript DOM Manipulation & Event Handling
   ============================================ */

/* -------------------------------
   DOM Element Selection
-------------------------------- */

const themeBtn = document.getElementById("themeBtn");

const menuIcon = document.querySelector(".menu-icon");
const navbar = document.querySelector(".navbar");

const questions = document.querySelectorAll(".question");

const modal = document.getElementById("modal");
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");

const banner = document.querySelector(".notification");
const closeBanner = document.getElementById("closeBanner");

const slider = document.getElementById("slider");
const nextBtn = document.getElementById("next");
const prevBtn = document.getElementById("prev");


/* -------------------------------
   FAQ (Collapsible)
-------------------------------- */

questions.forEach(function(question){

    question.addEventListener("click", function(){

        const answer = question.nextElementSibling;

        if(answer.style.display === "block")
        {
            answer.style.display = "none";
        }
        else
        {
            answer.style.display = "block";
        }

    });

});


/* -------------------------------
   Modal Popup
-------------------------------- */

openModal.addEventListener("click", function(){

    modal.style.display = "block";

});

closeModal.addEventListener("click", function(){

    modal.style.display = "none";

});


/* Close modal when user clicks outside */

window.addEventListener("click", function(event){

    if(event.target == modal)
    {
        modal.style.display = "none";
    }

});


/* -------------------------------
   Notification Banner
-------------------------------- */

closeBanner.addEventListener("click", function(){

    banner.style.display = "none";

});


/* -------------------------------
   Hamburger Menu
-------------------------------- */

menuIcon.addEventListener("click", function(){

    navbar.classList.toggle("active");

});


/* -------------------------------
   Image Slider
-------------------------------- */

const images = [

    "images/image1.jpg",
    "images/image2.jpg",
    "images/image3.jpg"

];

let currentImage = 0;


/* Next Image */

nextBtn.addEventListener("click", function(){

    currentImage++;

    if(currentImage >= images.length)
    {
        currentImage = 0;
    }

    slider.src = images[currentImage];

});


/* Previous Image */

prevBtn.addEventListener("click", function(){

    currentImage--;

    if(currentImage < 0)
    {
        currentImage = images.length - 1;
    }

    slider.src = images[currentImage];

});


/* -------------------------------
   Theme Switcher
-------------------------------- */

/* Restore Theme */

const savedTheme = localStorage.getItem("theme");

if(savedTheme === "dark")
{
    document.body.classList.add("dark");
    themeBtn.innerHTML = "☀ Light Mode";
}
else
{
    themeBtn.innerHTML = "🌙 Dark Mode";
}


/* Change Theme */

themeBtn.addEventListener("click", function(){

    document.body.classList.toggle("dark");

    if(document.body.classList.contains("dark"))
    {
        localStorage.setItem("theme","dark");
        themeBtn.innerHTML = "☀ Light Mode";
    }
    else
    {
        localStorage.setItem("theme","light");
        themeBtn.innerHTML = "🌙 Dark Mode";
    }

});


/* -------------------------------
   Console Messages (Testing)
-------------------------------- */

console.log("DOM Loaded Successfully");
console.log("FAQ Ready");
console.log("Modal Ready");
console.log("Slider Ready");
console.log("Theme Ready");
console.log("Hamburger Menu Ready");
console.log("Notification Ready");