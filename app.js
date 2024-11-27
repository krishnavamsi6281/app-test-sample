document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Add login logic here
});

document.getElementById('registerForm').addEventListener('submit', function(event) {
    event.preventDefault();
    // Add registration logic here
});
function validatePassword() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    
    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }
    return true;
}

document.getElementById('registerForm').addEventListener('submit', function(event) {
    // Ensure validation occurs on submit
    if (!validatePassword()) {
        event.preventDefault();
    }
});
