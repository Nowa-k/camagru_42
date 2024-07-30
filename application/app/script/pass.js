document.getElementById('userForm').addEventListener('submit', function(event) {
    var password = document.getElementById('pwd').value;
    var errorMessage = '';

    if (!validatePassword(password)) {
        errorMessage = 'Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.';
        alert(errorMessage);
        event.preventDefault(); // Empêche la soumission du formulaire
    }
});

function validatePassword(password) {
    var hasUpperCase = /[A-Z]/.test(password);
    var hasLowerCase = /[a-z]/.test(password);
    var hasNumber = /\d/.test(password);

    return hasUpperCase && hasLowerCase && hasNumber;
}