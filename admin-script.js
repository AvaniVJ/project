// JavaScript for Switching Between Login and Sign-Up Forms
document.addEventListener("DOMContentLoaded", () => {
  const loginContainer = document.querySelector(".login-container");
  const signupContainer = document.querySelector(".signup-container");

  const switchToSignup = document.getElementById("switch-to-signup");
  const switchToLogin = document.getElementById("switch-to-login");

  // Initially show the login form
  loginContainer.classList.add("active");

  // Switch to Sign-Up form
  switchToSignup.addEventListener("click", (e) => {
    e.preventDefault();
    loginContainer.classList.remove("active");
    signupContainer.classList.add("active");
  });

  // Switch to Login form
  switchToLogin.addEventListener("click", (e) => {
    e.preventDefault();
    signupContainer.classList.remove("active");
    loginContainer.classList.add("active");
  });
});
