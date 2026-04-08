/**
 * Main JavaScript file
 *
 * @package Opencode_Theme_Example
 */

(function () {
    'use strict';

    /**
     * Mobile Navigation Toggle
     */
    const mobileMenuToggle = () => {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNavigation = document.querySelector('.main-navigation');
        const menuContainer = mainNavigation.querySelector('ul');

        if (!menuToggle || !mainNavigation) {
            return;
        }

        menuToggle.addEventListener('click', function () {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            mainNavigation.classList.toggle('toggled');
            
            // Add screen reader text
            if (!isExpanded) {
                menuToggle.innerHTML = '<span class="screen-reader-text">Close Menu</span>✕';
            } else {
                menuToggle.innerHTML = '<span class="screen-reader-text">Open Menu</span>☰';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!mainNavigation.contains(e.target) && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.innerHTML = '<span class="screen-reader-text">Open Menu</span>☰';
            }
        });

        // Close menu on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.innerHTML = '<span class="screen-reader-text">Open Menu</span>☰';
                menuToggle.focus();
            }
        });
    };

    /**
     * Smooth scroll for anchor links
     */
    const smoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                
                if (targetId === '#') {
                    return;
                }

                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Set focus for accessibility
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                }
            });
        });
    };

    /**
     * Add class to header on scroll
     */
    const stickyHeader = () => {
        const header = document.querySelector('.site-header');
        
        if (!header) {
            return;
        }

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll <= 0) {
                header.classList.remove('scrolled');
                return;
            }

            if (currentScroll > lastScroll && !header.classList.contains('scrolled')) {
                // Scrolling down
                header.classList.add('scrolled');
            } else if (currentScroll < lastScroll && header.classList.contains('scrolled')) {
                // Scrolling up
                header.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });
    };

    /**
     * Lazy load images (native)
     */
    const lazyLoadImages = () => {
        // WordPress 5.5+ has native lazy loading support
        // This is just a fallback for older browsers
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback to Intersection Observer
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const image = entry.target;
                            if (image.dataset.src) {
                                image.src = image.dataset.src;
                            }
                            observer.unobserve(image);
                        }
                    });
                });

                lazyImages.forEach(img => {
                    imageObserver.observe(img);
                });
            }
        }
    };

    /**
     * Initialize all functions
     */
    const init = () => {
        mobileMenuToggle();
        smoothScroll();
        stickyHeader();
        lazyLoadImages();
    };

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
