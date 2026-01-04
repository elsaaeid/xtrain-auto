/**
 * Features Display - Creative SVG Vector Animations
 * Advanced GSAP animations with unique motion for each SVG
 */

document.addEventListener('DOMContentLoaded', function () {

    // Check if GSAP is loaded
    if (typeof gsap === 'undefined') {
        console.warn('GSAP is not loaded. Features animations disabled.');
        return;
    }

    // Register ScrollTrigger plugin
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    // Get all feature items
    const featureItems = document.querySelectorAll('.feature-item');

    if (featureItems.length === 0) return;

    // ===== CENTRALIZED ANIMATION CONFIGURATION =====
    const animations = [
        // Animation 1: Floating + Rotation
        {
            name: 'floating-rotation',
            entrance: { scale: 0.8 },
            motion: [
                { y: -15, duration: 2.5, repeat: -1, yoyo: true, ease: 'sine.inOut' },
                { rotation: 360, duration: 20, repeat: -1, ease: 'none' }
            ],
            hover: { scale: 1.15, rotation: '+=15', duration: 0.4 }
        },
        // Animation 2: Bounce + Scale Pulse
        {
            name: 'bounce-pulse',
            entrance: { scale: 0.8, y: 20 },
            motion: [
                { scale: 1.1, duration: 1.5, repeat: -1, yoyo: true, ease: 'sine.inOut' },
                { y: -10, duration: 2, repeat: -1, yoyo: true, ease: 'bounce.inOut' }
            ],
            hover: { scale: 1.2, y: -5, duration: 0.3 }
        },
        // Animation 3: Swing + Gentle Float
        {
            name: 'swing-float',
            entrance: { rotation: -15, x: -20 },
            motion: [
                { rotation: 10, duration: 3, repeat: -1, yoyo: true, ease: 'power1.inOut' },
                { y: -8, duration: 2.2, repeat: -1, yoyo: true, ease: 'sine.inOut' }
            ],
            hover: { rotation: '+=20', scale: 1.1, duration: 0.5 }
        },
        // Animation 4: Spin + Wave
        {
            name: 'spin-wave',
            entrance: { scale: 0.8 },
            motion: [
                { rotation: -360, duration: 15, repeat: -1, ease: 'linear' },
                { x: 5, y: -5, duration: 2, repeat: -1, yoyo: true, ease: 'sine.inOut' }
            ],
            hover: { scale: 1.15, duration: 0.4 }
        },
        // Animation 5: Elastic Bounce + Tilt
        {
            name: 'elastic-tilt',
            entrance: { scaleY: 0.8 },
            motion: [
                { rotation: -5, duration: 2.5, repeat: -1, yoyo: true, ease: 'power2.inOut' },
                { y: -12, duration: 1.8, repeat: -1, yoyo: true, ease: 'elastic.inOut(1, 0.3)' }
            ],
            hover: { scaleX: 1.1, rotation: 15, duration: 0.4 }
        },
        // Animation 6: Pendulum + Breath
        {
            name: 'pendulum-breath',
            entrance: { rotation: -15, scale: 0.8 },
            motion: [
                { rotation: 15, duration: 4, repeat: -1, yoyo: true, ease: 'power1.inOut', transformOrigin: 'top center' },
                { scale: 1.08, duration: 2, repeat: -1, yoyo: true, ease: 'sine.inOut' }
            ],
            hover: { y: -10, scale: 1.15, duration: 0.5 }
        }
    ];

    // ===== Apply Animations to Each Feature =====
    featureItems.forEach((item, index) => {
        const svg = item.querySelector('.feature-image svg, .feature-image .gsap-feature-svg');
        
        if (!svg) return;

        // Get animation pattern (cycle through patterns)
        const animPattern = animations[index % animations.length];

        // Set transform origin for animations
        gsap.set(svg, {
            transformOrigin: 'center center'
        });

        // ===== Entrance Animation =====
        gsap.set(svg, animPattern.entrance);

        gsap.to(svg, {
            scale: 1,
            rotation: 0,
            x: 0,
            y: 0,
            duration: 1.2,
            delay: index * 0.2,
            ease: 'back.out(1.7)',
            scrollTrigger: {
                trigger: item,
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });

        // ===== Continuous Motion Animations =====
        animPattern.motion.forEach((motion, i) => {
            gsap.to(svg, {
                ...motion,
                delay: i * 0.2
            });
        });

        // ===== Hover Effects =====
        let hoverTween;
        
        item.addEventListener('mouseenter', () => {
            if (hoverTween) hoverTween.kill();
            hoverTween = gsap.to(svg, {
                ...animPattern.hover,
                ease: 'power2.inOut'
            });
        });

        item.addEventListener('mouseleave', () => {
            if (hoverTween) hoverTween.kill();
            hoverTween = gsap.to(svg, {
                scale: 1,
                rotation: 0,
                x: 0,
                y: 0,
                duration: 0.5,
                ease: 'power2.inOut',
                onComplete: () => {
                    // Resume continuous animations after hover
                    animPattern.motion.forEach((motion, i) => {
                        gsap.to(svg, {
                            ...motion,
                            delay: i * 0.2
                        });
                    });
                }
            });
        });

        // ===== Animate SVG Inner Elements (Paths, Circles, etc.) =====
        const paths = svg.querySelectorAll('path');
        const circles = svg.querySelectorAll('circle');
        const rects = svg.querySelectorAll('rect');

        // Path draw animation
        paths.forEach((path, i) => {
            const length = path.getTotalLength();
            if (length) {
                gsap.set(path, {
                    strokeDasharray: length,
                    strokeDashoffset: length
                });

                gsap.to(path, {
                    strokeDashoffset: 0,
                    duration: 1.5,
                    delay: index * 0.2 + i * 0.1,
                    ease: 'power2.inOut',
                    scrollTrigger: {
                        trigger: item,
                        start: 'top 80%',
                        toggleActions: 'play none none reverse'
                    }
                });
            }
        });

        // Circle scale animation
        circles.forEach((circle, i) => {
            gsap.from(circle, {
                scale: 0,
                transformOrigin: 'center center',
                duration: 0.8,
                delay: index * 0.2 + i * 0.15,
                ease: 'elastic.out(1, 0.5)',
                scrollTrigger: {
                    trigger: item,
                    start: 'top 80%',
                    toggleActions: 'play none none reverse'
                }
            });
        });

        // Rectangle morph animation
        rects.forEach((rect, i) => {
            gsap.from(rect, {
                scaleX: 0,
                transformOrigin: 'left center',
                duration: 1,
                delay: index * 0.2 + i * 0.12,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: item,
                    start: 'top 80%',
                    toggleActions: 'play none none reverse'
                }
            });
        });
    });
});
