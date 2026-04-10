window.addEventListener("load", function() {
    const splash = document.getElementById("splash-screen");
    
    // Add a slight delay so the user actually sees the logo (optional)
    setTimeout(() => {
        splash.classList.add("hidden");
    }, 2000); // 2000ms = 2 seconds
});