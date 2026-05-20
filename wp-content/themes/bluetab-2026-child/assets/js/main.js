(function () {
    'use strict';

    const Bluetab = {
        init() {
            this.initExternalLinks();
            this.initReducedMotionClass();
            this.initMegaMenus();
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

            if (typeof window.initBluetabHistoryTimeline === 'function') {
                window.initBluetabHistoryTimeline();
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

        initMegaMenus() {
            const items = document.querySelectorAll('.bt-nav__item--has-mega');
            const closeDelay = 120;

            const closeItem = (item) => {
                const trigger = item.querySelector('.bt-nav__link');

                item.classList.remove('is-mega-open');

                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            };

            const openItem = (item) => {
                const trigger = item.querySelector('.bt-nav__link');

                items.forEach((otherItem) => {
                    if (otherItem !== item) {
                        window.clearTimeout(otherItem.btMegaCloseTimer);
                        closeItem(otherItem);
                    }
                });

                window.clearTimeout(item.btMegaCloseTimer);
                item.classList.add('is-mega-open');

                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'true');
                }
            };

            const scheduleClose = (item, event) => {
                const nextTarget = event && event.relatedTarget;

                if (nextTarget instanceof Node && item.contains(nextTarget)) {
                    return;
                }

                window.clearTimeout(item.btMegaCloseTimer);
                item.btMegaCloseTimer = window.setTimeout(() => {
                    if (item.matches(':hover') || item.contains(document.activeElement)) {
                        return;
                    }

                    closeItem(item);
                }, closeDelay);
            };

            items.forEach((item) => {
                const trigger = item.querySelector('.bt-nav__link');
                const megaMenu = item.querySelector('.bt-mega-menu');

                if (!trigger || !megaMenu) {
                    return;
                }

                trigger.setAttribute('aria-expanded', 'false');

                item.addEventListener('mouseenter', () => openItem(item));
                item.addEventListener('mouseleave', (event) => scheduleClose(item, event));
                item.addEventListener('focusin', () => openItem(item));
                item.addEventListener('focusout', (event) => scheduleClose(item, event));

                megaMenu.addEventListener('mouseenter', () => {
                    window.clearTimeout(item.btMegaCloseTimer);
                    openItem(item);
                });

                megaMenu.addEventListener('mouseleave', (event) => scheduleClose(item, event));

                item.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') {
                        return;
                    }

                    event.preventDefault();
                    closeItem(item);
                    trigger.focus();
                });
            });
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        Bluetab.init();
    });

    window.Bluetab = Bluetab;
})();
