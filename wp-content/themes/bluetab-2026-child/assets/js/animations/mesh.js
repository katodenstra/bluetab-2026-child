(function () {
    'use strict';

    const DEFAULT_CONFIG = {
        lineCount: 70,
        lineWidth: 0.75,
        opacity: 0.7,
        speed: 0.012,
        xStep: 8,
        amplitudeBase: 30,
        amplitudeVariance: 12,
        frequencyBase: 0.005,
        frequencyVariance: 0.001,
        colors: ['#212492', '#542675', '#e05206'],
    };

    function createCanvas(container) {
        const canvas = document.createElement('canvas');

        canvas.className = 'bt-mesh-canvas';
        canvas.setAttribute('aria-hidden', 'true');

        Object.assign(canvas.style, {
            position: 'absolute',
            inset: '0',
            width: '100%',
            height: '100%',
            display: 'block',
            pointerEvents: 'none',
            zIndex: '0',
        });

        container.appendChild(canvas);

        return canvas;
    }

    function createGradient(ctx, width, height, colors) {
        const gradient = ctx.createLinearGradient(0, 0, width, height);

        if (colors.length === 1) {
            gradient.addColorStop(0, colors[0]);
            gradient.addColorStop(1, colors[0]);
            return gradient;
        }

        colors.forEach((color, index) => {
            gradient.addColorStop(index / (colors.length - 1), color);
        });

        return gradient;
    }

    function initMesh(container) {
        const canvas = createCanvas(container);
        const ctx = canvas.getContext('2d');

        if (!ctx) return;

        const config = { ...DEFAULT_CONFIG };

        let width = 0;
        let height = 0;
        let dpr = 1;
        let time = 0;
        let waveGradient = null;
        let animationFrame = null;
        let lastFrameTime = 0;
        let targetFps = 30;
        let frameInterval = 1000 / targetFps;
        let isVisible = true;
        let isPageVisible = !document.hidden;
        let observer = null;
        const variant = {
            phase: Math.random() * Math.PI * 2,
            drift: 0.12 + Math.random() * 0.16,
            amplitude: 0.9 + Math.random() * 0.18,
            frequency: 0.96 + Math.random() * 0.08,
            perspective: 0.72 + Math.random() * 0.18,
            horizonShift: -18 + Math.random() * 36,
        };

        function resize() {
            dpr = Math.min(window.devicePixelRatio || 1, 1.25);

            const rect = canvas.getBoundingClientRect();

            width = rect.width;
            height = rect.height;

            const viewportWidth =
                window.innerWidth || document.documentElement.clientWidth;

            if (viewportWidth <= 767) {
                config.lineCount = Math.min(config.lineCount, 24);
                targetFps = 24;
            } else if (viewportWidth <= 980) {
                config.lineCount = Math.min(config.lineCount, 32);
                targetFps = 30;
            } else {
                targetFps = 30;
            }

            frameInterval = 1000 / targetFps;

            canvas.width = Math.floor(width * dpr);
            canvas.height = Math.floor(height * dpr);
            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;

            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

            waveGradient = createGradient(ctx, width, height, config.colors);
        }

        function drawFlowingHorizontalLine(
            y,
            amplitude,
            frequency,
            phase,
            opacity,
            color,
            lineWidth,
        ) {
            ctx.beginPath();
            ctx.strokeStyle = color;
            ctx.lineWidth = lineWidth;
            ctx.globalAlpha = opacity;

            for (let x = 0; x <= width; x += config.xStep) {
                let waveY = Math.sin(x * frequency + phase) * amplitude;

                waveY +=
                    Math.sin(x * frequency * 1.5 + phase * 1.2) *
                    (amplitude * 0.4);

                waveY +=
                    Math.sin(x * frequency * 0.5 + phase * 0.8) *
                    (amplitude * 0.25);

                const finalY = y + waveY;

                if (x === 0) {
                    ctx.moveTo(x, finalY);
                } else {
                    ctx.lineTo(x, finalY);
                }
            }

            ctx.stroke();
            ctx.globalAlpha = 1;
        }

        function animate(currentTime = 0) {
            const prefersReducedMotion =
                document.documentElement.classList.contains(
                    'bt-reduced-motion',
                );

            if (prefersReducedMotion || !isVisible || !isPageVisible) {
                animationFrame = null;
                return;
            }

            ctx.clearRect(0, 0, width, height);

            const hSpacing = height / (config.lineCount - 10);

            for (let i = -5; i < config.lineCount; i++) {
                const progress = i / config.lineCount;
                const perspective = 1 + progress * variant.perspective;
                const y = i * hSpacing + variant.horizonShift * progress;
                const phase =
                    time * config.speed * (1 + progress * variant.drift) +
                    i * 0.25 +
                    variant.phase;

                const opacity = Math.max(
                    0,
                    Math.min(
                        1,
                        config.opacity +
                            Math.sin(i * 0.4 + variant.phase) * 0.08,
                    ),
                );

                const amplitude =
                    (config.amplitudeBase +
                        Math.sin(i * 0.3 + variant.phase) *
                            config.amplitudeVariance) *
                    variant.amplitude *
                    perspective;

                const frequency =
                    (config.frequencyBase +
                        Math.sin(i * 0.2 + variant.phase) *
                            config.frequencyVariance) *
                    variant.frequency;

                drawFlowingHorizontalLine(
                    y,
                    amplitude,
                    frequency,
                    phase,
                    opacity,
                    waveGradient,
                    config.lineWidth,
                );
            }

            time += 1;
            lastFrameTime = 0;
            animationFrame = requestAnimationFrame(animate);
        }

        function start() {
            resize();

            if (animationFrame || !isVisible || !isPageVisible) {
                return;
            }

            animationFrame = requestAnimationFrame(animate);
        }

        function pause() {
            if (!animationFrame) {
                return;
            }

            cancelAnimationFrame(animationFrame);
            animationFrame = null;
        }

        function handleVisibilityChange() {
            isPageVisible = !document.hidden;

            if (isPageVisible && isVisible) {
                start();
            } else {
                pause();
            }
        }

        function setupVisibilityObserver() {
            if (!('IntersectionObserver' in window)) {
                return;
            }

            observer = new IntersectionObserver(
                (entries) => {
                    const entry = entries[0];
                    isVisible = entry.isIntersecting;

                    if (isVisible && isPageVisible) {
                        start();
                    } else {
                        pause();
                    }
                },
                {
                    root: null,
                    threshold: 0,
                    rootMargin: '160px 0px 160px 0px',
                },
            );

            observer.observe(container);
        }

        function stop() {
            pause();
            window.removeEventListener('resize', resize);
            document.removeEventListener(
                'visibilitychange',
                handleVisibilityChange,
            );

            if (observer) {
                observer.disconnect();
                observer = null;
            }
        }

        window.addEventListener('resize', resize);
        document.addEventListener('visibilitychange', handleVisibilityChange);
        setupVisibilityObserver();

        start();

        return {
            resize,
            stop,
        };
    }

    window.initBluetabMesh = function () {
        const containers = document.querySelectorAll('.js-bt-mesh');

        containers.forEach((container) => {
            if (container.dataset.meshInitialized === 'true') return;

            const computedStyle = window.getComputedStyle(container);

            if (computedStyle.position === 'static') {
                container.style.position = 'relative';
            }

            container.style.overflow =
                container.dataset.meshOverflow || 'hidden';
            container.dataset.meshInitialized = 'true';

            initMesh(container);
        });
    };
})();
