export function renderEvents(events, container) {

    container.innerHTML = "";

    if (events.length === 0) {

        container.innerHTML = `
            <p>No events found.</p>
        `;

        return;
    }

    events.forEach(event => {

        const card = document.createElement("div");

        card.className = "event-card";

        card.innerHTML = `
            <span class="badge">
                ${event.category}
            </span>

            <h2>
                ${event.title}
            </h2>

            <p>
                📍 ${event.city}
            </p>

            <p>
                📅 ${formatDate(event.date)}
            </p>

            <p class="price">
                ₹${event.price}
            </p>
        `;

        container.appendChild(card);

    });

}

function formatDate(date) {

    return new Date(date).toLocaleDateString(
        "en-IN",
        {
            day: "numeric",
            month: "short",
            year: "numeric"
        }
    );

}