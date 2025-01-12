// scroll-animations.js
class ScrollAnimationHandler {
    constructor() {
        this.elements = document.querySelectorAll('.animate-on-scroll');
        this.init();
    }

    init() {
        this.setupIntersectionObserver();
        this.setupScrollEvents();
    }

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Only animate once
                }
            });
        }, options);

        this.elements.forEach(element => observer.observe(element));
    }

    setupScrollEvents() {
        const staggerContainers = document.querySelectorAll('.stagger-animation');
        
        const staggerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        staggerContainers.forEach(container => staggerObserver.observe(container));
    }
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ScrollAnimationHandler();
    
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100,
        disable: 'mobile' // Disable on mobile devices if needed
    });
});