
const form = document.getElementById("registrationForm");

const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const mobileInput = document.getElementById("mobile");
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirmPassword");
const courseInput = document.getElementById("course");
const yearInput = document.getElementById("year");
const termsInput = document.getElementById("terms");

const showPassword = document.getElementById("showPassword");
const showConfirmPassword = document.getElementById("showConfirmPassword");
const resetButton = document.getElementById("resetButton");
const successMessage = document.getElementById("successMessage");

const nameRegex = /^[A-Za-z ]{3,30}$/;

const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

const mobileRegex = /^[6-9][0-9]{9}$/;

const passwordRegex =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;

function showError(input, errorId, message) {
    document.getElementById(errorId).innerHTML = message;

    input.classList.add("input-error");
    input.classList.remove("input-success");
}

function showSuccess(input, errorId) {
    document.getElementById(errorId).innerHTML = "";

    input.classList.remove("input-error");
    input.classList.add("input-success");
}

function validateName() {
    const name = nameInput.value.trim();

    if (name === "") {
        showError(nameInput, "nameError", "Name is required.");
        return false;
    }

    if (!nameRegex.test(name)) {
        showError(
            nameInput,
            "nameError",
            "Enter only letters and spaces."
        );
        return false;
    }

    showSuccess(nameInput, "nameError");
    return true;
}

function validateEmail() {
    const email = emailInput.value.trim();

    if (email === "") {
        showError(emailInput, "emailError", "Email is required.");
        return false;
    }

    if (!emailRegex.test(email)) {
        showError(
            emailInput,
            "emailError",
            "Enter a valid email address."
        );
        return false;
    }

    showSuccess(emailInput, "emailError");
    return true;
}

function validateMobile() {
    const mobile = mobileInput.value.trim();

    if (mobile === "") {
        showError(
            mobileInput,
            "mobileError",
            "Mobile number is required."
        );
        return false;
    }

    if (!mobileRegex.test(mobile)) {
        showError(
            mobileInput,
            "mobileError",
            "Enter a valid 10 digit mobile number."
        );
        return false;
    }

    showSuccess(mobileInput, "mobileError");
    return true;
}

function validatePassword() {
    const password = passwordInput.value;

    if (password === "") {
        showError(
            passwordInput,
            "passwordError",
            "Password is required."
        );
        return false;
    }

    if (!passwordRegex.test(password)) {
        showError(
            passwordInput,
            "passwordError",
            "Use uppercase, lowercase, number and special character."
        );
        return false;
    }

    showSuccess(passwordInput, "passwordError");
    return true;
}

function validateConfirmPassword() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (confirmPassword === "") {
        showError(
            confirmPasswordInput,
            "confirmPasswordError",
            "Please confirm your password."
        );
        return false;
    }

    if (password !== confirmPassword) {
        showError(
            confirmPasswordInput,
            "confirmPasswordError",
            "Passwords do not match."
        );
        return false;
    }

    showSuccess(confirmPasswordInput, "confirmPasswordError");
    return true;
}

function checkPasswordStrength() {
    const password = passwordInput.value;

    const strengthBar = document.getElementById("strengthBar");
    const strengthText = document.getElementById("strengthText");

    if (password.length === 0) {
        strengthBar.style.width = "0%";
        strengthText.innerHTML = "";
    }

    else if (password.length < 6) {
        strengthBar.style.width = "30%";
        strengthBar.style.backgroundColor = "red";

        strengthText.innerHTML = "Weak Password";
        strengthText.style.color = "red";
    }

    else if (password.length < 8) {
        strengthBar.style.width = "60%";
        strengthBar.style.backgroundColor = "orange";

        strengthText.innerHTML = "Medium Password";
        strengthText.style.color = "orange";
    }

    else if (passwordRegex.test(password)) {
        strengthBar.style.width = "100%";
        strengthBar.style.backgroundColor = "green";

        strengthText.innerHTML = "Strong Password";
        strengthText.style.color = "green";
    }

    else {
        strengthBar.style.width = "70%";
        strengthBar.style.backgroundColor = "orange";

        strengthText.innerHTML = "Medium Password";
        strengthText.style.color = "orange";
    }
}

nameInput.addEventListener("input", validateName);

emailInput.addEventListener("input", validateEmail);

mobileInput.addEventListener("input", validateMobile);

passwordInput.addEventListener("input", function () {
    checkPasswordStrength();
    validatePassword();
});

confirmPasswordInput.addEventListener(
    "input",
    validateConfirmPassword
);

showPassword.addEventListener("click", function () {

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        showPassword.innerHTML = "Hide";
    }

    else {
        passwordInput.type = "password";
        showPassword.innerHTML = "Show";
    }
});

showConfirmPassword.addEventListener("click", function () {

    if (confirmPasswordInput.type === "password") {
        confirmPasswordInput.type = "text";
        showConfirmPassword.innerHTML = "Hide";
    }

    else {
        confirmPasswordInput.type = "password";
        showConfirmPassword.innerHTML = "Show";
    }
});

form.addEventListener("submit", function (event) {

    event.preventDefault();

    const validName = validateName();
    const validEmail = validateEmail();
    const validMobile = validateMobile();
    const validPassword = validatePassword();
    const validConfirmPassword = validateConfirmPassword();

    let validCourse = true;

    if (courseInput.value === "") {
        showError(
            courseInput,
            "courseError",
            "Please select your course."
        );

        validCourse = false;
    }

    else {
        showSuccess(courseInput, "courseError");
    }

    let validYear = true;

    if (yearInput.value === "") {
        showError(
            yearInput,
            "yearError",
            "Please select your year."
        );

        validYear = false;
    }

    else {
        showSuccess(yearInput, "yearError");
    }

    const gender = document.querySelector(
        'input[name="gender"]:checked'
    );

    let validGender = true;

    if (gender === null) {
        document.getElementById("genderError").innerHTML =
            "Please select your gender.";

        validGender = false;
    }

    else {
        document.getElementById("genderError").innerHTML = "";
    }

    let validTerms = true;

    if (!termsInput.checked) {
        document.getElementById("termsError").innerHTML =
            "Please accept the Terms and Conditions.";

        validTerms = false;
    }

    else {
        document.getElementById("termsError").innerHTML = "";
    }

    if (
        validName &&
        validEmail &&
        validMobile &&
        validPassword &&
        validConfirmPassword &&
        validCourse &&
        validYear &&
        validGender &&
        validTerms
    ) {

        successMessage.style.display = "block";

        successMessage.innerHTML =
            "Registration Successful! Welcome, " +
            nameInput.value +
            ".";
    }

    else {
        successMessage.style.display = "none";
    }
});

resetButton.addEventListener("click", function () {

    const errors = document.querySelectorAll(".error");

    for (let i = 0; i < errors.length; i++) {
        errors[i].innerHTML = "";
    }

    const inputs = document.querySelectorAll("input, select");

    for (let i = 0; i < inputs.length; i++) {
        inputs[i].classList.remove("input-error");
        inputs[i].classList.remove("input-success");
    }

    document.getElementById("strengthBar").style.width = "0%";

    document.getElementById("strengthText").innerHTML = "";

    successMessage.style.display = "none";
});

