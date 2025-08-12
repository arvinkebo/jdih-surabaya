document.addEventListener('DOMContentLoaded', function() {
    const animateCards = document.querySelectorAll('.card-animate');
    
    animateCards.forEach((card, index) => {
        card.dataset.delay = index * 100 + 100;
        // Set initial state
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'none'; // Nonaktifkan sementara
    });

    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const delay = card.dataset.delay || 0;
                
                setTimeout(() => {
                    // Aktifkan transisi sebelum animasi
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    
                    requestAnimationFrame(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                        
                        // Setelah animasi selesai, aktifkan hover
                        setTimeout(() => {
                            card.style.transition = '';
                            card.classList.add('animation-complete');
                        }, 600);
                    });
                    
                    cardObserver.unobserve(card);
                }, delay);
            }
        });
    }, {
        threshold: 0.05,
        rootMargin: '0px 0px -100px 0px'
    });

    animateCards.forEach(card => cardObserver.observe(card));

    const animateElements = [
        ...document.querySelectorAll('.search-animate'), 
        ...document.querySelectorAll('.search-section')
    ];

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const baseDelay = element.classList.contains('search-section') ? 0 : 100;
                
                setTimeout(() => {
                    element.classList.add('animated');
                    
                    // Hanya untuk cards (opsional)
                    if(element.classList.contains('search-animate')) {
                        setTimeout(() => {
                            observer.unobserve(element);
                        }, 800);
                    }
                }, baseDelay);
            }
        });
    }, {
        threshold: 0.05,
        rootMargin: '0px 0px -100px 0px'
    });

    animateElements.forEach(el => observer.observe(el));
});