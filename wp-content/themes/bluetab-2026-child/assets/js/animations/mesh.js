(function () {
    'use strict';

    const DEFAULT_CONFIG = {
        lineCount: 75,
        lineWidth: 0.5,
        opacity: 0.8,
        speed: 0.012,
        xStep: 2,
        amplitudeBase: 30,
        amplitudeVariance: 12,
        frequencyBase: 0.005,
        frequencyVariance: 0.001,
        colors: ['#2563eb', '#6366f1', '#a855f7', '#f97316'],
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

        function resize() {
            dpr = Math.min(window.devicePixelRatio || 1, 2);

            const rect = canvas.getBoundingClientRect();

            width = rect.width;
            height = rect.height;

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

        function animate() {
            const prefersReducedMotion =
                document.documentElement.classList.contains(
                    'bt-reduced-motion',
                );

            if (prefersReducedMotion) {
                return;
            }

            ctx.clearRect(0, 0, width, height);

            const hSpacing = height / (config.lineCount - 10);

            for (let i = -5; i < config.lineCount; i++) {
                const y = i * hSpacing;
                const phase = time * config.speed + i * 0.25;

                const opacity = Math.max(
                    0,
                    Math.min(1, config.opacity + Math.sin(i * 0.4) * 0.15),
                );

                const amplitude =
                    config.amplitudeBase +
                    Math.sin(i * 0.3) * config.amplitudeVariance;

                const frequency =
                    config.frequencyBase +
                    Math.sin(i * 0.2) * config.frequencyVariance;

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
            animationFrame = requestAnimationFrame(animate);
        }

        function start() {
            resize();
            animationFrame = requestAnimationFrame(animate);
        }

        function stop() {
            if (animationFrame) {
                cancelAnimationFrame(animationFrame);
            }
        }

        window.addEventListener('resize', resize);

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
