
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Practical 13 - AJAX CRUD</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 25px;
}
.container {
    max-width: 1000px;
    margin: auto;
}
h1 {
    text-align: center;
    color: #243447;
}
.card {
    background: white;
    padding: 25px;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 2px 8px #ddd;
}
input {
    padding: 12px;
    width: 100%;
    box-sizing: border-box;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}
button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background: #007bff;
    color: white;
    cursor: pointer;
    margin: 3px;
}
button:hover {
    opacity: 0.85;
}
button:disabled {
    opacity: 0.5;
}
.edit {
    background: #e6a000;
}
.delete {
    background: #dc3545;
}
.cancel {
    background: #666;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}
th {
    background: #243447;
    color: white;
}
.table-wrapper {
    overflow-x: auto;
}
#message {
    display: none;
    padding: 12px;
    border-radius: 5px;
}
.success {
    background: #d4edda;
    color: #155724;
}
.error {
    background: #f8d7da;
    color: #721c24;
}
#loading {
    display: none;
    color: #007bff;
}
</style>
</head>

<body>

<div class="container">

<h1>Student Management System</h1>
<p style="text-align:center">
    Practical 13 - PHP JSON API and AJAX CRUD
</p>

<div id="message" role="status"></div>
<p id="loading">Loading, please wait...</p>

<div class="card">
<h2 id="formTitle">Add Student</h2>

<form id="studentForm">

<input type="hidden" id="studentId">

<label for="name">Student Name</label>
<input type="text" id="name" required maxlength="100">

<label for="email">Email Address</label>
<input type="email" id="email" required maxlength="150">

<label for="course">Course</label>
<input type="text" id="course" required maxlength="100">

<button type="submit" id="saveBtn">
    Add Student
</button>

<button type="button" class="cancel"
        onclick="resetForm()">
    Cancel
</button>

</form>
</div>

<div class="card">
<h2>Student Records</h2>

<input type="search" id="search"
       placeholder="Search by name, email or course">

<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Actions</th>
</tr>
</thead>
<tbody id="studentTable"></tbody>
</table>
</div>

</div>
</div>

<script>

const API = "api.php";
let students = [];
let searchTimer;

const form = document.getElementById("studentForm");
const table = document.getElementById("studentTable");
const messageBox = document.getElementById("message");
const loading = document.getElementById("loading");
const saveBtn = document.getElementById("saveBtn");

function showMessage(text, success = true) {
    messageBox.textContent = text;
    messageBox.className = success ? "success" : "error";
    messageBox.style.display = "block";
}

function setLoading(state) {
    loading.style.display = state ? "block" : "none";
    saveBtn.disabled = state;
}

async function apiRequest(method, data = null) {

    const options = {
        method: method,
        headers: {
            "Accept": "application/json"
        }
    };

    if (data !== null) {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
    }

    const response = await fetch(API, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "API request failed");
    }

    return result;
}

// READ
async function loadStudents() {

    setLoading(true);

    try {
        const result = await apiRequest("GET");
        students = result.data;
        filterStudents();

    } catch (error) {
        showMessage(error.message, false);
        table.innerHTML = "";
    } finally {
        setLoading(false);
    }
}

// DISPLAY
function displayStudents(data) {

    table.innerHTML = "";

    if (data.length === 0) {
        const row = table.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 5;
        cell.textContent = "No students found.";
        return;
    }

    data.forEach(student => {

        const row = table.insertRow();

        [
            student.id,
            student.name,
            student.email,
            student.course
        ].forEach(value => {
            const cell = row.insertCell();
            cell.textContent = value;
        });

        const actions = row.insertCell();

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.className = "edit";
        editBtn.onclick = () => editStudent(student.id);

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.className = "delete";
        deleteBtn.onclick = () => deleteStudent(student.id);

        actions.append(editBtn, deleteBtn);
    });
}

// ADD OR UPDATE
form.addEventListener("submit", async function(event) {

    event.preventDefault();

    const id = document.getElementById("studentId").value;

    const data = {
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    if (!data.name || !data.email || !data.course) {
        showMessage("All fields are required.", false);
        return;
    }

    const method = id ? "PUT" : "POST";

    if (id) {
        data.id = Number(id);
    }

    setLoading(true);

    try {
        const result = await apiRequest(method, data);
        resetForm();
        await loadStudents();
        showMessage(result.message);

    } catch (error) {
        showMessage(error.message, false);
    } finally {
        setLoading(false);
    }
});

// EDIT
function editStudent(id) {

    const student = students.find(
        s => Number(s.id) === Number(id)
    );

    if (!student) return;

    document.getElementById("studentId").value = student.id;
    document.getElementById("name").value = student.name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    document.getElementById("formTitle").textContent =
        "Update Student";

    saveBtn.textContent = "Update Student";

    window.scrollTo({ top: 0, behavior: "smooth" });
}

// DELETE
async function deleteStudent(id) {

    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    setLoading(true);

    try {
        const result = await apiRequest("DELETE", { id });

        await loadStudents();

        showMessage(result.message);

    } catch (error) {
        showMessage(error.message, false);
    } finally {
        setLoading(false);
    }
}

// RESET
function resetForm() {

    form.reset();
    document.getElementById("studentId").value = "";

    document.getElementById("formTitle").textContent =
        "Add Student";

    saveBtn.textContent = "Add Student";
}

// SEARCH WITH DEBOUNCE
function filterStudents() {

    const keyword = document.getElementById("search")
        .value.toLowerCase().trim();

    const filtered = students.filter(student =>
        student.name.toLowerCase().includes(keyword) ||
        student.email.toLowerCase().includes(keyword) ||
        student.course.toLowerCase().includes(keyword)
    );

    displayStudents(filtered);
}

document.getElementById("search").addEventListener(
    "input",
    function() {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(filterStudents, 300);
    }
);

// LOAD WHEN PAGE OPENS
loadStudents();

</script>

</body>
</html>
