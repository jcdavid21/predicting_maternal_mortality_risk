const form = document.getElementById("loginForm");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const feedback = document.getElementById("feedback");
const loginBtn = document.getElementById("loginBtn");
const togglePasswordBtn = document.getElementById("togglePassword");

const setLoading = (isLoading) => {
    loginBtn.disabled = isLoading;
    loginBtn.classList.toggle("loading", isLoading);
};

const showFeedback = (message, type = "") => {
    feedback.textContent = message;
    feedback.classList.remove("error", "success");
    if (type) {
        feedback.classList.add(type);
    }
};

togglePasswordBtn?.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    const icon = togglePasswordBtn.querySelector("i");
    icon?.classList.toggle("fa-eye", !isPassword);
    icon?.classList.toggle("fa-eye-slash", isPassword);
    togglePasswordBtn.setAttribute("aria-label", isPassword ? "Hide password" : "Show password");
});

form?.addEventListener("submit", async (event) => {
    event.preventDefault();

    const username = usernameInput.value.trim();
    const password = passwordInput.value;

    if (!username || !password) {
        showFeedback("Please enter both username and password.", "error");
        return;
    }

    setLoading(true);
    showFeedback("Signing in...");

    try {
        const response = await fetch("backend/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                username,
                password,
                rememberMe: document.getElementById("rememberMe")?.checked || false
            })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Login failed.");
        }

        showFeedback(result.message || "Login successful.", "success");

        if (result.redirect) {
            window.setTimeout(() => {
                window.location.href = result.redirect;
            }, 700);
        }
    } catch (error) {
        showFeedback(error.message || "Unable to connect to server.", "error");
    } finally {
        setLoading(false);
    }
});
