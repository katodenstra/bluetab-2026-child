(function () {
    'use strict';

    const SELECTOR = '[data-bt-history-timeline]';
    const VISIBLE_COUNT = 6;
    const CONTENT_FADE_DURATION = 240;

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function createTextElement(tagName, className, text) {
        if (!text) {
            return null;
        }

        const element = document.createElement(tagName);
        element.className = className;
        element.textContent = text;

        return element;
    }

    function parseTimelineData(root) {
        const dataNode = root.querySelector('.bt-history-timeline__data');

        if (!dataNode) {
            return [];
        }

        try {
            const parsed = JSON.parse(dataNode.textContent.trim());

            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function initTimeline(root) {
        if (root.dataset.btHistoryInitialized === 'true') {
            return;
        }

        const items = parseTimelineData(root);
        const yearButtons = Array.from(
            root.querySelectorAll('.bt-history-timeline__year-button'),
        );
        const prevControl = root.querySelector(
            '.bt-history-timeline__control--prev',
        );
        const nextControl = root.querySelector(
            '.bt-history-timeline__control--next',
        );
        const yearsList = root.querySelector('.bt-history-timeline__years');
        const content = root.querySelector('.bt-history-timeline__content');

        if (
            !items.length ||
            !yearButtons.length ||
            !prevControl ||
            !nextControl ||
            !yearsList ||
            !content
        ) {
            return;
        }

        root.dataset.btHistoryInitialized = 'true';

        const yearItems = yearButtons
            .map((button) => button.closest('.bt-history-timeline__year-item'))
            .filter(Boolean);

        let yearsViewport = yearsList.parentElement;

        if (!yearsViewport || !yearsViewport.classList.contains('bt-history-timeline__years-viewport')) {
            yearsViewport = document.createElement('div');
            yearsViewport.className = 'bt-history-timeline__years-viewport';
            yearsList.parentNode.insertBefore(yearsViewport, yearsList);
            yearsViewport.appendChild(yearsList);
        }

        root.addEventListener('pointerdown', () => {
            root.classList.add('is-pointer-input');
        });

        root.addEventListener('keydown', (event) => {
            if (
                event.key === 'Tab' ||
                event.key === 'ArrowUp' ||
                event.key === 'ArrowDown' ||
                event.key === 'Home' ||
                event.key === 'End'
            ) {
                root.classList.remove('is-pointer-input');
            }
        });

        let activeIndex = 0;
        let windowStart = 0;
        let transitionToken = 0;
        let fadeOutTimer = null;
        let fadeInTimer = null;
        const visibleCount = Math.min(VISIBLE_COUNT, items.length);

        function getMaxWindowStart() {
            return Math.max(items.length - visibleCount, 0);
        }

        function updateWindow(nextActiveIndex) {
            if (items.length <= visibleCount) {
                windowStart = 0;
                return;
            }

            const windowEnd = windowStart + visibleCount - 1;

            if (nextActiveIndex >= windowEnd - 1 && nextActiveIndex < items.length - 1) {
                windowStart = clamp(
                    nextActiveIndex - (visibleCount - 2),
                    0,
                    getMaxWindowStart(),
                );
                return;
            }

            if (nextActiveIndex === items.length - 1) {
                windowStart = getMaxWindowStart();
                return;
            }

            if (nextActiveIndex <= windowStart + 1 && nextActiveIndex > 0) {
                windowStart = clamp(nextActiveIndex - 1, 0, getMaxWindowStart());
                return;
            }

            if (nextActiveIndex === 0) {
                windowStart = 0;
            }
        }

        function getDistanceClass(distance) {
            if (distance === 0) {
                return 'is-active';
            }

            if (distance === 1) {
                return 'is-near';
            }

            if (distance === 2) {
                return 'is-mid';
            }

            return 'is-far';
        }

        function getVisibleIndexes(start = windowStart) {
            const end = start + visibleCount - 1;
            const indexes = [];

            for (let index = start; index <= end; index++) {
                if (index >= 0 && index < items.length) {
                    indexes.push(index);
                }
            }

            return indexes;
        }

        function setTrackPosition(animate = true) {
            const targetItem = yearItems[windowStart];
            const targetOffset = targetItem ? targetItem.offsetTop : 0;

            yearsList.classList.toggle(
                'is-not-animated',
                !animate || prefersReducedMotion(),
            );
            yearsList.style.transform = `translateY(${-targetOffset}px)`;

            if (!animate || prefersReducedMotion()) {
                window.requestAnimationFrame(() => {
                    yearsList.classList.remove('is-not-animated');
                });
            }
        }

        function updateYears(previousWindowStart = windowStart, animate = true) {
            const windowEnd = windowStart + visibleCount - 1;
            const previousVisibleIndexes = new Set(getVisibleIndexes(previousWindowStart));
            const currentVisibleIndexes = new Set(getVisibleIndexes(windowStart));
            const didWindowMove = previousWindowStart !== windowStart;
            const enterClass =
                windowStart > previousWindowStart
                    ? 'is-entering-from-next'
                    : 'is-entering-from-prev';

            yearButtons.forEach((button, index) => {
                const isVisible = index >= windowStart && index <= windowEnd;
                const distance = Math.abs(index - activeIndex);
                const item = yearItems[index];

                button.classList.remove('is-active', 'is-near', 'is-mid', 'is-far');
                button.classList.add(getDistanceClass(distance));
                button.removeAttribute('hidden');
                button.tabIndex = isVisible ? 0 : -1;

                if (item) {
                    item.removeAttribute('hidden');
                    item.classList.remove('is-entering-from-next', 'is-entering-from-prev');
                    item.setAttribute('aria-hidden', isVisible ? 'false' : 'true');

                    if (
                        animate &&
                        didWindowMove &&
                        currentVisibleIndexes.has(index) &&
                        !previousVisibleIndexes.has(index) &&
                        !prefersReducedMotion()
                    ) {
                        item.classList.add(enterClass);
                    }
                }

                if (index === activeIndex) {
                    button.setAttribute('aria-current', 'true');
                } else {
                    button.removeAttribute('aria-current');
                }
            });

            setTrackPosition(animate);

            if (animate && didWindowMove && !prefersReducedMotion()) {
                window.requestAnimationFrame(() => {
                    yearItems.forEach((item) => {
                        item.classList.remove('is-entering-from-next', 'is-entering-from-prev');
                    });
                });
            }

            root.classList.toggle('has-hidden-years-before', windowStart > 0);
            root.classList.toggle(
                'has-hidden-years-after',
                windowEnd < items.length - 1,
            );
        }

        function renderContent(item) {
            content.replaceChildren();

            const titleElement = createTextElement(
                'h3',
                'bt-history-timeline__title',
                item.title,
            );
            const textElement = createTextElement(
                'p',
                'bt-history-timeline__text',
                item.text,
            );

            if (titleElement) {
                content.appendChild(titleElement);
            }

            const bodyElement = document.createElement('div');
            bodyElement.className = 'bt-history-timeline__body';

            const copyElement = document.createElement('div');
            copyElement.className = 'bt-history-timeline__copy';

            if (textElement) {
                copyElement.appendChild(textElement);
            }

            bodyElement.appendChild(copyElement);

            const mediaElement = document.createElement('div');
            mediaElement.className = 'bt-history-timeline__media';

            if (item.image) {
                const image = document.createElement('img');
                image.src = item.image;
                image.alt = item.image_alt || '';
                image.loading = 'lazy';
                mediaElement.appendChild(image);
            } else {
                mediaElement.hidden = true;
            }

            bodyElement.appendChild(mediaElement);
            content.appendChild(bodyElement);
        }

        function clearContentTimers() {
            if (fadeOutTimer) {
                window.clearTimeout(fadeOutTimer);
                fadeOutTimer = null;
            }

            if (fadeInTimer) {
                window.clearTimeout(fadeInTimer);
                fadeInTimer = null;
            }
        }

        function updateContent(item) {
            const token = transitionToken + 1;
            transitionToken = token;

            clearContentTimers();

            if (prefersReducedMotion()) {
                content.classList.remove('is-fading-out', 'is-fading-in');
                renderContent(item);
                return;
            }

            content.classList.remove('is-fading-in');
            content.classList.add('is-fading-out');

            fadeOutTimer = window.setTimeout(() => {
                if (token !== transitionToken) {
                    return;
                }

                renderContent(item);
                content.classList.remove('is-fading-out');
                content.classList.add('is-fading-in');

                fadeInTimer = window.setTimeout(() => {
                    if (token !== transitionToken) {
                        return;
                    }

                    content.classList.remove('is-fading-in');
                }, CONTENT_FADE_DURATION);
            }, CONTENT_FADE_DURATION);
        }

        function updateControls() {
            prevControl.disabled = activeIndex === 0;
            nextControl.disabled = activeIndex === items.length - 1;
        }

        function setActive(index, options = {}) {
            const nextIndex = clamp(index, 0, items.length - 1);
            const didChange = nextIndex !== activeIndex;
            const previousWindowStart = windowStart;

            if (!didChange && !options.forceContent) {
                return;
            }

            activeIndex = nextIndex;
            updateWindow(activeIndex);
            updateYears(previousWindowStart, didChange);
            updateControls();

            if (didChange || options.forceContent) {
                updateContent(items[activeIndex]);
            }
        }

        function focusActiveButton() {
            const activeButton = yearButtons[activeIndex];

            if (activeButton && !activeButton.hidden) {
                activeButton.focus();
            }
        }

        yearButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.btHistoryIndex);

                if (Number.isInteger(index)) {
                    setActive(index);
                }
            });
        });

        prevControl.addEventListener('click', () => {
            setActive(activeIndex - 1);
            focusActiveButton();
        });

        nextControl.addEventListener('click', () => {
            setActive(activeIndex + 1);
            focusActiveButton();
        });

        root.addEventListener('keydown', (event) => {
            const isTimelineControl =
                event.target instanceof Element &&
                event.target.closest('.bt-history-timeline__nav');

            if (!isTimelineControl) {
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActive(activeIndex - 1);
                focusActiveButton();
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActive(activeIndex + 1);
                focusActiveButton();
            }

            if (event.key === 'Home') {
                event.preventDefault();
                setActive(0);
                focusActiveButton();
            }

            if (event.key === 'End') {
                event.preventDefault();
                setActive(items.length - 1);
                focusActiveButton();
            }
        });

        updateWindow(activeIndex);
        updateYears(windowStart, false);
        updateControls();
    }

    window.initBluetabHistoryTimeline = function () {
        document.querySelectorAll(SELECTOR).forEach(initTimeline);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initBluetabHistoryTimeline);
    } else {
        window.initBluetabHistoryTimeline();
    }
})();
