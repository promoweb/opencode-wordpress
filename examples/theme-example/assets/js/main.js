/**
 * Theme JavaScript functionality
 *
 * @package Opencode_Theme_Example
 */

(function() {
    'use strict';

    /**
     * DOM ready handler
     */
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    /**
     * Initialize theme functionality
     */
    function init() {
        initMobileMenu();
        initAccessibility();
        initSmoothScroll();
    }

    /**
     * Mobile menu toggle
     */
    function initMobileMenu() {
        var menuToggle = document.querySelector('.menu-toggle');
        var mainNavigation = document.querySelector('.main-navigation');

        if (!menuToggle || !mainNavigation) {
            return;
        }

        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mainNavigation.classList.toggle('toggled');
            
            var expanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !expanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mainNavigation.contains(e.target) && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mainNavigation.classList.contains('toggled')) {
                mainNavigation.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
        });
    }

    /**
     * Accessibility enhancements
     */
    function initAccessibility() {
        // Add focus styles for keyboard navigation
        document.body.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('using-keyboard');
            }
        });

        document.body.addEventListener('mousedown', function() {
            document.body.classList.remove('using-keyboard');
        });
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var targetId = this.getAttribute('href');
                
                if (targetId === '#') {
                    return;
                }

                var targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Set focus to target for accessibility
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                }
            });
        });
    }

    // Initialize on DOM ready
    ready(init);

})();