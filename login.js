document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.querySelector('.wrapper');
    const signupSection = document.getElementById('signupSection');
    const forgotPasswordSection = document.getElementById('forgotPasswordSection');
  
    const registerLink = document.getElementById('registerLink');
    const loginLink = document.getElementById('loginLink');
    const forgotPassword = document.getElementById('forgotPassword');
    const backToLogin = document.getElementById('backToLogin');
  
    
    signupSection.classList.add('hidden');
    forgotPasswordSection.classList.add('hidden');
  
  
    registerLink.addEventListener('click', function (e) {
      e.preventDefault(); 
      loginForm.style.display = 'none'; 
      signupSection.classList.remove('hidden'); 
      forgotPasswordSection.classList.add('hidden'); 
    });
  
    
    loginLink.addEventListener('click', function (e) {
      e.preventDefault(); 
      signupSection.classList.add('hidden'); 
      forgotPasswordSection.classList.add('hidden'); 
      loginForm.style.display = 'block'; 
    });
  
    
    forgotPassword.addEventListener('click', function (e) {
      e.preventDefault(); 
      loginForm.style.display = 'none';
      signupSection.classList.add('hidden'); 
      forgotPasswordSection.classList.remove('hidden'); 
    });
  
   
    backToLogin.addEventListener('click', function (e) {
      e.preventDefault(); 
      forgotPasswordSection.classList.add('hidden'); 
      loginForm.style.display = 'block'; 
    });
  
   
    document.querySelector('.wrapper form').addEventListener('submit', function (e) {
      e.preventDefault(); 
      alert('Login successful!');
    
    });
  
    
    document.getElementById('signupSection').querySelector('form').addEventListener('submit', function (e) {
      e.preventDefault(); 
      alert('Sign up successful! Please log in.');
      signupSection.classList.add('hidden'); 
      loginForm.style.display = 'block'; 
    });
  
    
    document.getElementById('forgotPasswordSection').querySelector('form').addEventListener('submit', function (e) {
      e.preventDefault(); 
      alert('Password reset instructions sent to your email.');
      forgotPasswordSection.classList.add('hidden'); 
      loginForm.style.display = 'block'; 
    });
  });