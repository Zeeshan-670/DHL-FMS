const ValidateEmail = (mail) => {
  if (mail === '' || mail === '-') return true;
  var mailformat = /^\w+([\.-]?\w+)@\w+([\.-]?\w+)(\.\w{2,})+$/i;
  return mail.match(mailformat) !== null;
};

document.getElementById('password').addEventListener('keydown', handleKeyDown);
// Get the form and button elements
let loginForm = document.getElementById('loginForm');
let loginBtn = document.getElementById('loginBtn');

function handleKeyDown(event) {
  if (event.key === 'Enter') {
    loginBtn.click()
  }
}



loginForm.addEventListener('submit', function (event) {
  event.preventDefault();

  // Get email and password values
  let email = document.getElementById('email').value.toLowerCase().trim();
  let password = document.getElementById('password').value.trim();

  // Basic validation
  if (email === '' || password === '') {
    showNotification('error', 'All fields are required');
  } else {
    // Disable the login button
    loginBtn.disabled = true;
    loginBtn.textContent = 'Logging in...';

    const formData = new FormData();
    formData.append('username', email);
    formData.append('password', encodePassword(password));

    // AJAX request
    $.ajax({
      url: 'controller/loginApi.php',
      type: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      success: function (response) {
        if (response.success) {
          showNotification('success', 'Login Successful! Redirecting...');
          console.log(response);
          setTimeout(() => {
            window.location.href = response.type == "vendor" ? './vendor.php' : './dashboard.php';
          }, 500);
        } else {
          showNotification('error', response.message);
          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      },
      error: function () {
        showNotification('error', 'Login failed. Please try again.');
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
      },
    });
  }
});

// Toggle password visibility function
function toggleFunction() {
  var x = document.getElementById('password');
  x.type = x.type === 'password' ? 'text' : 'password';
}
