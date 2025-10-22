document.addEventListener('DOMContentLoaded', function () {

  const loginForm = document.querySelector('.wrapper');
  const signupSection = document.getElementById('signupSection');
  const forgotPasswordSection = document.getElementById('forgotPasswordSection');

  const registerLink = document.getElementById('registerLink');
  const loginLink = document.getElementById('loginLink');
  const forgotPassword = document.getElementById('forgotPassword');
  const backToLogin = document.getElementById('backToLogin');

  // Default: hide signup and forgot password sections
  signupSection.classList.add('hidden');
  forgotPasswordSection.classList.add('hidden');

  // 🔹 Switch to Signup
  registerLink.addEventListener('click', function (e) {
    e.preventDefault();
    loginForm.style.display = 'none';
    signupSection.classList.remove('hidden');
    forgotPasswordSection.classList.add('hidden');
  });

  // 🔹 Switch to Login
  loginLink.addEventListener('click', function (e) {
    e.preventDefault();
    signupSection.classList.add('hidden');
    forgotPasswordSection.classList.add('hidden');
    loginForm.style.display = 'block';
  });

  // 🔹 Switch to Forgot Password
  forgotPassword.addEventListener('click', function (e) {
    e.preventDefault();
    loginForm.style.display = 'none';
    signupSection.classList.add('hidden');
    forgotPasswordSection.classList.remove('hidden');
  });

  // 🔹 Back to Login from Forgot Password
  backToLogin.addEventListener('click', function (e) {
    e.preventDefault();
    forgotPasswordSection.classList.add('hidden');
    loginForm.style.display = 'block';
  });

  // ✅ Login form handler (optional placeholder)
  document.querySelector('.wrapper form').addEventListener('submit', function (e) {
    // You can connect this to login.php if needed
    console.log("Login form submitted");
  });

  // ✅ Signup form handler with confirm password validation
  const signupForm = document.getElementById('signupSection').querySelector('form');
  signupForm.addEventListener('submit', function (e) {
    const password = signupForm.querySelector('input[name="password"]').value.trim();
    const confirmPassword = signupForm.querySelector('input[name="confirm_password"]').value.trim();

    // Check if passwords match
    if (password !== confirmPassword) {
      e.preventDefault();
      alert('❌ Passwords do not match. Please try again.');
      return;
    }

    // Check minimum password length
    if (password.length < 6) {
      e.preventDefault();
      alert('⚠️ Password must be at least 6 characters long.');
      return;
    }

    // Success message
    alert('✅ Registration successful! Redirecting to login...');
    // Automatically show login form again
    signupSection.classList.add('hidden');
    loginForm.style.display = 'block';
  });

  // ✅ Forgot password form handler
  document.getElementById('forgotPasswordSection').querySelector('form').addEventListener('submit', function (e) {
    e.preventDefault();
    alert('📧 Password reset instructions sent to your email.');
    forgotPasswordSection.classList.add('hidden');
    loginForm.style.display = 'block';
  });

});
