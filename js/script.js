// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navMenu = document.getElementById('navMenu');

if (mobileMenuBtn && navMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        mobileMenuBtn.classList.toggle('active');
    });
}

// Smooth Scroll for Navigation Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = target.offsetTop - navbarHeight;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                // Close mobile menu if open
                if (navMenu && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    if (mobileMenuBtn) {
                        mobileMenuBtn.classList.remove('active');
                    }
                }
            }
        }
    });
});

// Update Current Year in Footer
const currentYearElement = document.getElementById('currentYear');
if (currentYearElement) {
    currentYearElement.textContent = new Date().getFullYear();
}

// Form Submission Handler
const contactForm = document.getElementById('contactForm');
const formMessage = document.getElementById('formMessage');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Get form data
        const formData = new FormData(contactForm);

        // Show loading state
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.textContent = 'Envoi en cours...';
        submitButton.disabled = true;

        try {
            // Send form data
            const response = await fetch(contactForm.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            // Show message
            formMessage.style.display = 'block';
            if (result.success) {
                formMessage.className = 'form-message success';
                formMessage.textContent = result.message || 'Votre demande a été envoyée avec succès ! Je vous recontacterai dans les plus brefs délais.';
                contactForm.reset();
            } else {
                formMessage.className = 'form-message error';
                formMessage.textContent = result.message || 'Une erreur est survenue. Veuillez réessayer ou me contacter directement par email.';
            }

            // Scroll to message
            formMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            // Hide message after 5 seconds
            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);

        } catch (error) {
            formMessage.style.display = 'block';
            formMessage.className = 'form-message error';
            formMessage.textContent = 'Une erreur est survenue. Veuillez réessayer ou me contacter directement par email : gaelle.fay@galynup.fr';
            
            setTimeout(() => {
                formMessage.style.display = 'none';
            }, 5000);
        } finally {
            // Reset button state
            submitButton.textContent = originalButtonText;
            submitButton.disabled = false;
        }
    });
}

// Navbar Background on Scroll
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 50) {
        navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';
    } else {
        navbar.style.boxShadow = 'none';
    }
    
    lastScroll = currentScroll;
});

// Fade-in Animation on Scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.service-card, .experience-card, .expertise-card').forEach(el => {
    observer.observe(el);
});

// Keywords Carousel
const keywordCarouselItems = document.querySelectorAll('.keyword-carousel-item');
if (keywordCarouselItems.length > 0) {
    let currentKeywordIndex = 0;
    
    function rotateKeywords() {
        // Remove active class from current item
        keywordCarouselItems[currentKeywordIndex].classList.remove('active');
        
        // Move to next item
        currentKeywordIndex = (currentKeywordIndex + 1) % keywordCarouselItems.length;
        
        // Add active class to new item
        keywordCarouselItems[currentKeywordIndex].classList.add('active');
    }
    
    // Rotate every 3 seconds
    setInterval(rotateKeywords, 3000);
}

// Expertise Cards Carousel
const expertiseCards = document.querySelectorAll('.expertise-card');
if (expertiseCards.length > 0) {
    let currentExpertiseIndex = 0;
    
    function rotateExpertiseCards() {
        // Add exiting class to current card
        expertiseCards[currentExpertiseIndex].classList.add('exiting');
        expertiseCards[currentExpertiseIndex].classList.remove('active');
        
        // Move to next card
        currentExpertiseIndex = (currentExpertiseIndex + 1) % expertiseCards.length;
        
        // Add active class to new card
        expertiseCards[currentExpertiseIndex].classList.add('active');
        
        // Remove exiting class after animation completes
        setTimeout(() => {
            expertiseCards.forEach(card => {
                if (!card.classList.contains('active')) {
                    card.classList.remove('exiting');
                }
            });
        }, 800); // Match the CSS transition duration
    }
    
    // Rotate every 4 seconds
    setInterval(rotateExpertiseCards, 4000);
}

// Cookie Management
const COOKIE_CONSENT_NAME = 'galynup_cookie_consent';
const COOKIE_CONSENT_DURATION = 180; // 6 mois en jours

// Check if user has already made a choice
function checkCookieConsent() {
    const consent = getCookie(COOKIE_CONSENT_NAME);
    if (!consent) {
        // Show banner if no consent recorded
        setTimeout(() => {
            document.getElementById('cookieBanner').style.display = 'block';
        }, 1000);
    } else {
        // Apply user's previous choice
        const preferences = JSON.parse(consent);
        if (preferences.analytics) {
            enableAnalytics();
        }
    }
}

// Set cookie
function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
}

// Get cookie
function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Enable Google Analytics (placeholder for future implementation)
function enableAnalytics() {
    // TODO: Add Google Analytics code here when ready
    console.log('Analytics enabled');
    // Example:
    // window.dataLayer = window.dataLayer || [];
    // function gtag(){dataLayer.push(arguments);}
    // gtag('js', new Date());
    // gtag('config', 'GA_MEASUREMENT_ID');
}

// Save consent
function saveConsent(analytics) {
    const preferences = {
        essential: true,
        analytics: analytics,
        timestamp: new Date().toISOString()
    };
    setCookie(COOKIE_CONSENT_NAME, JSON.stringify(preferences), COOKIE_CONSENT_DURATION);
    
    if (analytics) {
        enableAnalytics();
    }
}

// Hide banner
function hideCookieBanner() {
    document.getElementById('cookieBanner').style.display = 'none';
}

// Show modal
function showCookieModal() {
    document.getElementById('cookieModal').style.display = 'flex';
}

// Hide modal
function hideCookieModal() {
    document.getElementById('cookieModal').style.display = 'none';
}

// Accept all cookies
document.getElementById('acceptCookies')?.addEventListener('click', () => {
    saveConsent(true);
    hideCookieBanner();
});

// Refuse all cookies
document.getElementById('refuseCookies')?.addEventListener('click', () => {
    saveConsent(false);
    hideCookieBanner();
});

// Open customization modal
document.getElementById('customizeCookies')?.addEventListener('click', () => {
    hideCookieBanner();
    showCookieModal();
});

// Close modal
document.getElementById('closeCookieModal')?.addEventListener('click', () => {
    hideCookieModal();
});

// Save custom preferences
document.getElementById('saveCookiePreferences')?.addEventListener('click', () => {
    const analyticsEnabled = document.getElementById('analyticsCookies').checked;
    saveConsent(analyticsEnabled);
    hideCookieModal();
});

// Manage cookies link in footer
document.getElementById('manageCookies')?.addEventListener('click', (e) => {
    e.preventDefault();
    showCookieModal();
    
    // Load current preferences
    const consent = getCookie(COOKIE_CONSENT_NAME);
    if (consent) {
        const preferences = JSON.parse(consent);
        document.getElementById('analyticsCookies').checked = preferences.analytics;
    }
});

// Check consent on page load
checkCookieConsent();


// Back to Top Button
const backToTopButton = document.getElementById('backToTop');

if (backToTopButton) {
    // Show/hide button based on scroll position
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });

    // Scroll to top when clicked
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Carrousel des recommandations (Desktop et Mobile)
(function() {
    const isMobile = () => window.innerWidth <= 768;
    
    let currentIndex = 0;
    let intervalId = null;
    let touchTimeout = null;
    let grid = null;
    let cards = null;
    let dotsContainer = null;
    let prevBtn = null;
    let nextBtn = null;
    
    function init() {
        grid = document.querySelector('.recommandations-grid');
        if (!grid) return;
        
        cards = grid.querySelectorAll('.recommandation-card');
        if (cards.length === 0) return;
        
        dotsContainer = document.querySelector('.carousel-dots');
        prevBtn = document.querySelector('.carousel-prev');
        nextBtn = document.querySelector('.carousel-next');
        
        if (isMobile()) {
            initMobileCarousel();
        } else {
            initDesktopCarousel();
        }
    }
    
    // ===== MOBILE CAROUSEL =====
    function initMobileCarousel() {
        currentIndex = 0;
        
        function autoScrollHorizontal() {
            currentIndex = (currentIndex + 1) % cards.length;
            const card = cards[currentIndex];
            if (card && grid) {
                // Scroll horizontal uniquement, sans forcer le scroll vertical
                const cardLeft = card.offsetLeft;
                const cardWidth = card.offsetWidth;
                const gridWidth = grid.offsetWidth;
                const scrollPosition = cardLeft - (gridWidth - cardWidth) / 2;
                
                grid.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });
            }
        }
        
        function startAutoScroll() {
            if (intervalId) clearInterval(intervalId);
            intervalId = setInterval(autoScrollHorizontal, 3000);
        }
        
        function stopAutoScroll() {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        }
        
        startAutoScroll();
        
        grid.addEventListener('touchstart', stopAutoScroll);
        grid.addEventListener('touchend', () => {
            if (touchTimeout) clearTimeout(touchTimeout);
            touchTimeout = setTimeout(startAutoScroll, 3000);
        });
    }
    
    // ===== DESKTOP CAROUSEL =====
    function initDesktopCarousel() {
        currentIndex = 0;
        
        // Créer les dots
        if (dotsContainer) {
            dotsContainer.innerHTML = '';
            const totalSlides = Math.ceil(cards.length / 3);
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('div');
                dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }
        }
        
        function updateCarousel() {
            const slideIndex = Math.floor(currentIndex / 3);
            const offset = slideIndex * 3;
            
            // Masquer toutes les cartes
            cards.forEach((card, index) => {
                if (index >= offset && index < offset + 3) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Mettre à jour les dots
            const dots = dotsContainer?.querySelectorAll('.carousel-dot');
            dots?.forEach((dot, index) => {
                dot.classList.toggle('active', index === slideIndex);
            });
        }
        
        function goToSlide(slideIndex) {
            currentIndex = slideIndex * 3;
            updateCarousel();
        }
        
        function nextSlide() {
            const totalSlides = Math.ceil(cards.length / 3);
            const currentSlide = Math.floor(currentIndex / 3);
            const nextSlide = (currentSlide + 1) % totalSlides;
            goToSlide(nextSlide);
        }
        
        function prevSlide() {
            const totalSlides = Math.ceil(cards.length / 3);
            const currentSlide = Math.floor(currentIndex / 3);
            const prevSlideIndex = (currentSlide - 1 + totalSlides) % totalSlides;
            goToSlide(prevSlideIndex);
        }
        
        function autoScroll() {
            nextSlide();
        }
        
        function startAutoScroll() {
            if (intervalId) clearInterval(intervalId);
            intervalId = setInterval(autoScroll, 4000);
        }
        
        function stopAutoScroll() {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        }
        
        // Boutons de navigation
        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoScroll();
            setTimeout(startAutoScroll, 5000);
        });
        
        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoScroll();
            setTimeout(startAutoScroll, 5000);
        });
        
        // Pause au survol
        grid.addEventListener('mouseenter', stopAutoScroll);
        grid.addEventListener('mouseleave', startAutoScroll);
        
        updateCarousel();
        startAutoScroll();
    }
    
    // Initialiser au chargement
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Réinitialiser au redimensionnement
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (intervalId) clearInterval(intervalId);
            init();
        }, 250);
    });
})();


// ===== COMPTEUR ET FILTRE DES RECOMMANDATIONS =====
(function() {
    const recommendationsGrid = document.getElementById('recommendationsGrid');
    const recommendationsCounter = document.getElementById('recommendationsCounter');
    const sortFilter = document.getElementById('sortFilter');
    
    if (!recommendationsGrid || !recommendationsCounter || !sortFilter) return;
    
    function updateRecommendations() {
        const cards = Array.from(recommendationsGrid.querySelectorAll('.recommandation-card'));
        
        if (cards.length === 0) return;
        
        // Mettre à jour le compteur
        const count = cards.length;
        const text = count === 1 ? '1 recommandation client' : `${count} recommandations clients`;
        recommendationsCounter.textContent = text;
        
        // Récupérer la valeur de tri
        const sortValue = sortFilter.value;
        
        // Créer un tableau avec les données de chaque carte
        const cardsData = cards.map(card => {
            const ratingScore = parseFloat(card.querySelector('.rating-score')?.textContent || '0');
            const approvedAtText = card.dataset.approvedAt || new Date().toISOString();
            return {
                element: card,
                rating: ratingScore,
                approvedAt: new Date(approvedAtText)
            };
        });
        
        // Trier selon la sélection
        if (sortValue === 'best-rated') {
            cardsData.sort((a, b) => b.rating - a.rating);
        } else if (sortValue === 'oldest') {
            cardsData.sort((a, b) => a.approvedAt - b.approvedAt);
        } else { // 'recent' par défaut
            cardsData.sort((a, b) => b.approvedAt - a.approvedAt);
        }
        
        // Réorganiser les éléments dans le DOM
        cardsData.forEach(item => {
            recommendationsGrid.appendChild(item.element);
        });
    }
    
    // Mettre à jour au chargement
    updateRecommendations();
    
    // Mettre à jour quand le filtre change
    sortFilter.addEventListener('change', updateRecommendations);
})();
