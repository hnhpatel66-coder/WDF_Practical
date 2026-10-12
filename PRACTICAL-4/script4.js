// ======================================
// GUJARAT TOURISM
// PRACTICAL 4
// ======================================


// Get HTML elements

const themeBtn =
    document.getElementById("themeBtn");

const menuIcon =
    document.querySelector(".menu-icon");

const navbar =
    document.querySelector(".navbar");

const questions =
    document.querySelectorAll(".question");

const modal =
    document.getElementById("modal");

const openModal =
    document.getElementById("openModal");

const closeModal =
    document.getElementById("closeModal");

const banner =
    document.querySelector(".notification");

const closeBanner =
    document.getElementById("closeBanner");

const slider =
    document.getElementById("slider");

const next =
    document.getElementById("next");

const prev =
    document.getElementById("prev");


// ======================================
// 1. FAQ
// ======================================

questions.forEach(function(question) {

    question.addEventListener("click", function() {

        const answer =
            question.nextElementSibling;

        if (answer.style.display == "block") {

            answer.style.display = "none";

        }

        else {

            answer.style.display = "block";

        }

    });

});


// ======================================
// 2. MODAL
// ======================================

openModal.addEventListener("click", function() {

    modal.style.display = "block";

});


closeModal.addEventListener("click", function() {

    modal.style.display = "none";

});


// ======================================
// 3. NOTIFICATION
// ======================================

closeBanner.addEventListener("click", function() {

    banner.style.display = "none";

});


// ======================================
// 4. HAMBURGER MENU
// ======================================

menuIcon.addEventListener("click", function() {

    navbar.classList.toggle("active");

});


// ======================================
// 5. IMAGE SLIDER
// ======================================

const images = [

    "../images/statue.jpeg",

    "../images/dwarka.jpeg",

    "../images/rann.jpeg",

    "../images/ranikivav.jpeg"

];


let current = 0;


// NEXT BUTTON

next.addEventListener("click", function() {

    current++;

    if (current >= images.length) {

        current = 0;

    }

    slider.src = images[current];

});


// PREVIOUS BUTTON

prev.addEventListener("click", function() {

    current--;

    if (current < 0) {

        current = images.length - 1;

    }

    slider.src = images[current];

});


// ======================================
// 6. DARK MODE
// ======================================

themeBtn.addEventListener("click", function() {

    document.body.classList.toggle("dark");


    if (document.body.classList.contains("dark")) {

        themeBtn.innerHTML =
            "☀ Light Mode";

    }

    else {

        themeBtn.innerHTML =
            "🌙 Dark Mode";

    }

});