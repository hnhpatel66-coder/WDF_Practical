import { fetchEvents } from "./api.js";
import { renderEvents } from "./render.js";

const eventContainer = document.getElementById("eventContainer");

const searchInput = document.getElementById("searchInput");

const categoryFilter = document.getElementById("categoryFilter");

const cityFilter = document.getElementById("cityFilter");

const sortSelect = document.getElementById("sortSelect");

const resetButton = document.getElementById("resetButton");

const resultCount = document.getElementById("resultCount");

const loading = document.getElementById("loading");

const errorBox = document.getElementById("error");

const pagination = document.getElementById("pagination");


let allEvents = [];

let currentPage = 1;

const eventsPerPage = 6;


async function loadEvents() {

    try {

        loading.style.display = "block";

        errorBox.textContent = "";

        allEvents = await fetchEvents();

        updateEvents();

    } catch (error) {

        errorBox.textContent = error.message;

    } finally {

        loading.style.display = "none";

    }

}


function updateEvents() {

    let filteredEvents = [...allEvents];


    const searchText =
        searchInput.value.toLowerCase().trim();


    filteredEvents = filteredEvents.filter(event =>

        event.title
            .toLowerCase()
            .includes(searchText)

    );


    if (categoryFilter.value !== "All") {

        filteredEvents = filteredEvents.filter(event =>

            event.category === categoryFilter.value

        );

    }


    if (cityFilter.value !== "All") {

        filteredEvents = filteredEvents.filter(event =>

            event.city === cityFilter.value

        );

    }


    if (sortSelect.value === "title") {

        filteredEvents.sort((a, b) =>
            a.title.localeCompare(b.title)
        );

    }


    if (sortSelect.value === "date") {

        filteredEvents.sort((a, b) =>
            new Date(a.date) - new Date(b.date)
        );

    }


    if (sortSelect.value === "priceLow") {

        filteredEvents.sort((a, b) =>
            a.price - b.price
        );

    }


    if (sortSelect.value === "priceHigh") {

        filteredEvents.sort((a, b) =>
            b.price - a.price
        );

    }


    resultCount.textContent =
        `${filteredEvents.length} event(s) found`;


    displayPage(filteredEvents);

}


function displayPage(events) {

    const totalPages =
        Math.ceil(events.length / eventsPerPage);


    if (currentPage > totalPages) {
        currentPage = 1;
    }


    const startIndex =
        (currentPage - 1) * eventsPerPage;


    const endIndex =
        startIndex + eventsPerPage;


    const pageEvents =
        events.slice(startIndex, endIndex);


    renderEvents(pageEvents, eventContainer);


    renderPagination(totalPages);

}


function renderPagination(totalPages) {

    pagination.innerHTML = "";


    for (let i = 1; i <= totalPages; i++) {

        const button =
            document.createElement("button");


        button.textContent = i;


        if (i === currentPage) {

            button.classList.add("active");

        }


        button.addEventListener("click", () => {

            currentPage = i;

            updateEvents();

        });


        pagination.appendChild(button);

    }

}


searchInput.addEventListener("input", () => {

    currentPage = 1;

    updateEvents();

});


categoryFilter.addEventListener("change", () => {

    currentPage = 1;

    updateEvents();

});


cityFilter.addEventListener("change", () => {

    currentPage = 1;

    updateEvents();

});


sortSelect.addEventListener("change", () => {

    currentPage = 1;

    updateEvents();

});


resetButton.addEventListener("click", () => {

    searchInput.value = "";

    categoryFilter.value = "All";

    cityFilter.value = "All";

    sortSelect.value = "default";

    currentPage = 1;

    updateEvents();

});


loadEvents();