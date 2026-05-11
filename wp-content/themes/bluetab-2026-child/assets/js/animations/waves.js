(function () {
    'use strict';

    const DEFAULT_CONFIG = {
        lineCount: 70,
        pointsPerLine: 260,
        lineWidth: 1,
        alpha: 0.75,

        speed: 0.000085,

        originX: 0.54,
        originY: 0.52,

        ribbonHeight: 1180,
        ribbonWidth: 390,
        ribbonDepth: 165,

        verticalFlow: 30,
        lateralFlow: 18,
        breathing: 12,

        perspective: 0.34,
        startSpread: 34,
        rotationDeg: 20.5,

        canvasWidth: '58%',
        canvasHeight: '136%',
        canvasTop: '-18%',
        canvasRight: '0',
    };

    const WAVE_VARIANTS = {
        generic: {
            colors: ['#212492', '#1f222d'],
        },

        strategy: {
            colors: ['#212492', '#31277e'],
        },

        readiness: {
            colors: ['#31277e', '#542675'],
        },

        products: {
            colors: ['#9b3c3d', '#e05206'],
        },

        ai: {
            colors: ['#9b3c3d', '#e05206'],
        },
    };

    function mergeConfig(variantConfig) {
        return {
            ...DEFAULT_CONFIG,
            ...variantConfig,
        };
    }

    function hexToRgb(hex) {
        const value = hex.replace('#', '');

        return {
            r: parseInt(value.slice(0, 2), 16),
            g: parseInt(value.slice(2, 4), 16),
            b: parseInt(value.slice(4, 6), 16),
        };
    }

    function lerp(a, b, t) {
        return a + (b - a) * t;
    }

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    function prefersReducedMotion() {
        return (
            document.documentElement.classList.contains('bt-reduced-motion') ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        );
    }

    function getGradientColor(colors, t, alpha) {
        const rgbColors = colors.map(hexToRgb);
        const safeT = clamp(t, 0, 1);

        if (rgbColors.length === 1) {
            const color = rgbColors[0];
            return `rgba(${color.r}, ${color.g}, ${color.b}, ${alpha})`;
        }

        const segmentCount = rgbColors.length - 1;
        const scaledT = safeT * segmentCount;
        const index = Math.min(Math.floor(scaledT), segmentCount - 1);
        const localT = scaledT - index;

        const from = rgbColors[index];
        const to = rgbColors[index + 1];

        const r = Math.round(lerp(from.r, to.r, localT));
        const g = Math.round(lerp(from.g, to.g, localT));
        const b = Math.round(lerp(from.b, to.b, localT));

        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function createCanvas(container, config) {
        const canvas = document.createElement('canvas');

        canvas.className = 'bt-wave-canvas';
        canvas.setAttribute('aria-hidden', 'true');

        Object.assign(canvas.style, {
            position: 'absolute',
            top: config.canvasTop,
            right: config.canvasRight,
            width: config.canvasWidth,
            height: config.canvasHeight,
            display: 'block',
            pointerEvents: 'none',
            zIndex: '0',
        });

        container.appendChild(canvas);

        return canvas;
    }

    function getResponsiveConfig(config) {
        const width = window.innerWidth;

        if (width <= 767) {
            return {
                ...config,
                lineCount: 32,
                pointsPerLine: 160,
            };
        }

        if (width <= 980) {
            return {
                ...config,
                lineCount: 48,
                pointsPerLine: 200,
            };
        }

        return {
            ...config,
            lineCount: 70,
            pointsPerLine: 260,
        };
    }

    function initWave(container, options = {}) {
        const variantName = container.dataset.waveVariant || 'generic';
        const variantConfig =
            WAVE_VARIANTS[variantName] || WAVE_VARIANTS.generic;
        const baseConfig = mergeConfig(variantConfig);
        let config = getResponsiveConfig(baseConfig);

        const canvas = options.canvas || createCanvas(container, config);
        const ctx = canvas.getContext('2d');

        if (!ctx) return;

        let width = 0;
        let height = 0;
        let dpr = 1;
        let animationFrame = null;
        let isVisible = true;
        let lastFrame = 0;

        const start = performance.now();
        const seed = Math.random() * 1000;
        const frameInterval = 1000 / 30;

        function resize() {
            config = getResponsiveConfig(baseConfig);
            dpr = Math.min(window.devicePixelRatio || 1, 1.5);

            const rect = canvas.getBoundingClientRect();

            width = rect.width;
            height = rect.height;

            canvas.width = Math.floor(width * dpr);
            canvas.height = Math.floor(height * dpr);
            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;

            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }

        function centerCurve(t, time, scale) {
            const y = (t - 0.5) * config.ribbonHeight * scale;

            const x =
                Math.sin(t * Math.PI * 1.62 - 0.28 + seed * 0.01) *
                    config.ribbonWidth *
                    0.4 *
                    scale +
                Math.sin(t * Math.PI * 3.05 + 1.72 + seed * 0.006) *
                    config.ribbonWidth *
                    0.14 *
                    scale +
                Math.sin(t * Math.PI * 4.8 - 0.7 + seed * 0.004) *
                    config.ribbonWidth *
                    0.045 *
                    scale +
                Math.sin(time * 0.48 + t * Math.PI * 1.1 + seed * 0.003) *
                    14 *
                    scale;

            return { x, y };
        }

        function getNormal(t, time, scale) {
            const e = 0.002;

            const p1 = centerCurve(Math.max(0, t - e), time, scale);
            const p2 = centerCurve(Math.min(1, t + e), time, scale);

            const dx = p2.x - p1.x;
            const dy = p2.y - p1.y;
            const length = Math.hypot(dx, dy) || 1;

            return {
                x: -dy / length,
                y: dx / length,
            };
        }

        function getRibbonPoint(t, lineT, time, scale) {
            const center = centerCurve(t, time, scale);
            const normal = getNormal(t, time, scale);

            const band = lineT - 0.5;
            const envelope = Math.sin(t * Math.PI);

            const startVariance =
                Math.sin(lineT * Math.PI * 2.7 + seed) *
                config.startSpread *
                scale *
                (1 - t) *
                (1 - t);

            const softWidth =
                0.58 +
                envelope * 0.72 +
                Math.sin(t * Math.PI * 2.4 + time * 1.4) * 0.045;

            const twist =
                Math.sin(t * Math.PI * 2.7 + lineT * 1.9 + time * 2.2) * 0.5 +
                Math.sin(t * Math.PI * 5.6 - lineT * 1.25 - time * 1.6) * 0.22;

            const pinchA = Math.exp(-Math.pow((t - 0.39) / 0.12, 2));
            const pinchB = Math.exp(-Math.pow((t - 0.73) / 0.14, 2));
            const pinch = 1 - pinchA * 0.32 - pinchB * 0.24;

            const depth =
                band *
                config.ribbonDepth *
                scale *
                softWidth *
                pinch *
                (1 + twist * config.perspective);

            const verticalMotion =
                Math.sin(t * Math.PI * 2.15 + lineT * 1.8 + time * 2.8) *
                config.verticalFlow *
                scale *
                envelope;

            const lateralMotion =
                Math.sin(t * Math.PI * 4.25 - lineT * 2.4 + time * 1.9) *
                config.lateralFlow *
                scale *
                envelope;

            const breathingMotion =
                Math.sin(t * Math.PI * 3.1 + time * 1.2) *
                config.breathing *
                scale *
                envelope;

            const vortexA =
                pinchA *
                Math.sin(lineT * Math.PI * 2 + time * 2.4 + seed * 0.01) *
                14 *
                scale;

            const vortexB =
                pinchB *
                Math.sin(lineT * Math.PI * 2 - time * 2.1 + seed * 0.008) *
                10 *
                scale;

            const x =
                center.x +
                normal.x * depth +
                lateralMotion +
                vortexA +
                vortexB +
                startVariance;

            const y =
                center.y + normal.y * depth + verticalMotion + breathingMotion;

            return { x, y };
        }

        function draw(now) {
            if (prefersReducedMotion() || document.hidden || !isVisible) {
                animationFrame = null;
                return;
            }

            if (now - lastFrame < frameInterval) {
                animationFrame = requestAnimationFrame(draw);
                return;
            }

            lastFrame = now;

            const elapsed = now - start;
            const time = elapsed * config.speed;

            ctx.clearRect(0, 0, width, height);

            ctx.lineWidth = config.lineWidth;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            const scale = Math.max(width / 900, height / 900);
            const originX = width * config.originX;
            const originY = height * config.originY;

            const rotation = (config.rotationDeg * Math.PI) / 180;
            const cos = Math.cos(rotation);
            const sin = Math.sin(rotation);

            for (let i = 0; i < config.lineCount; i++) {
                const lineT = i / (config.lineCount - 1);

                ctx.beginPath();

                for (let j = 0; j < config.pointsPerLine; j++) {
                    const t = j / (config.pointsPerLine - 1);
                    const point = getRibbonPoint(t, lineT, time, scale);

                    const flowOffset =
                        Math.sin(time * 1.8 + lineT * Math.PI * 2) * 36 * scale;

                    const localX = point.x;
                    const localY = point.y + flowOffset;

                    const x = originX + localX * cos - localY * sin;
                    const y = originY + localX * sin + localY * cos;

                    if (j === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                }

                const edgeFade = Math.sin(lineT * Math.PI);
                const alpha = config.alpha * (0.3 + edgeFade * 0.7);

                ctx.strokeStyle = getGradientColor(config.colors, lineT, alpha);
                ctx.stroke();
            }

            animationFrame = requestAnimationFrame(draw);
        }

        function startAnimation() {
            if (
                animationFrame ||
                document.hidden ||
                !isVisible ||
                prefersReducedMotion()
            ) {
                return;
            }

            resize();
            animationFrame = requestAnimationFrame(draw);
        }

        function stopAnimation() {
            if (animationFrame) {
                cancelAnimationFrame(animationFrame);
                animationFrame = null;
            }
        }

        function handleVisibilityChange() {
            if (document.hidden) {
                stopAnimation();
                return;
            }

            startAnimation();
        }

        let observer = null;

        if ('IntersectionObserver' in window) {
            observer = new IntersectionObserver(
                (entries) => {
                    const entry = entries[0];
                    isVisible = Boolean(entry && entry.isIntersecting);

                    if (isVisible) {
                        startAnimation();
                    } else {
                        stopAnimation();
                    }
                },
                {
                    threshold: 0.01,
                },
            );

            observer.observe(container);
        }

        window.addEventListener('resize', resize);
        document.addEventListener('visibilitychange', handleVisibilityChange);

        startAnimation();

        return {
            resize,
            stop() {
                stopAnimation();
                window.removeEventListener('resize', resize);
                document.removeEventListener(
                    'visibilitychange',
                    handleVisibilityChange,
                );

                if (observer) {
                    observer.disconnect();
                }
            },
        };
    }

    window.initBluetabWaves = function () {
        const containers = document.querySelectorAll('.js-bt-wave');

        containers.forEach((container) => {
            if (container.dataset.waveInitialized === 'true') return;

            const computedStyle = window.getComputedStyle(container);

            if (computedStyle.position === 'static') {
                container.style.position = 'relative';
            }

            container.style.overflow =
                container.dataset.waveOverflow || 'hidden';
            container.dataset.waveInitialized = 'true';

            initWave(container);
        });

        const solutionContainers = document.querySelectorAll(
            '.js-bt-solution-wave',
        );

        solutionContainers.forEach((container) => {
            if (container.dataset.waveInitialized === 'true') return;

            const canvas = container.querySelector('.bt-solution-wave__canvas');

            if (!canvas) return;

            container.dataset.waveInitialized = 'true';

            initWave(container, { canvas });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initBluetabWaves);
    } else {
        window.initBluetabWaves();
    }
})();
