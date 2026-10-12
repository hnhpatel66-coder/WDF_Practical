export async function fetchEvents() {

    const response = await fetch("events.json");

    if (!response.ok) {
        throw new Error("Unable to load event data.");
    }

    const data = await response.json();

    return data;
}