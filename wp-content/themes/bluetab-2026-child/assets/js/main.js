(function () {
    'use strict';

    const Bluetab = {
        init() {
            this.initExternalLinks();
            this.initReducedMotionClass();
            this.initComponents();
        },

        initComponents() {
            if (typeof window.initBluetabMesh === 'function') {
                window.initBluetabMesh();
            }

            if (typeof window.initBluetabWaves === 'function') {
                window.initBluetabWaves();
            }

            if (typeof window.initBluetabCards === 'function') {
                window.initBluetabCards();
            }
        },

        initExternalLinks() {
            const links = document.querySelectorAll('a[href^="http"]');

            links.forEach((link) => {
                const isSameHost = link.hostname === window.location.hostname;

                if (!isSameHost) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'noopener noreferrer');
                }
            });
        },

        initReducedMotionClass() {
            const prefersReducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)',
            );

            const updateMotionPreference = () => {
                document.documentElement.classList.toggle(
                    'bt-reduced-motion',
                    prefersReducedMotion.matches,
                );
            };

            updateMotionPreference();

            if (typeof prefersReducedMotion.addEventListener === 'function') {
                prefersReducedMotion.addEventListener(
                    'change',
                    updateMotionPreference,
                );
            }
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        Bluetab.init();
    });

    window.Bluetab = Bluetab;
})();
