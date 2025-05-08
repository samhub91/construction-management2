document.addEventListener("DOMContentLoaded", function () {
    let slides = document.querySelectorAll(".project-slider img");
    let currentSlide = 0;

    function showNextSlide() {
        slides[currentSlide].classList.remove("active"); // Hide current slide
        currentSlide = (currentSlide + 1) % slides.length; // Move to the next slide
        slides[currentSlide].classList.add("active"); // Show the next slide
    }

    // Initially show the first image
    slides[currentSlide].classList.add("active");
    
    // Change image every 3 seconds
    setInterval(showNextSlide, 3000);
});
