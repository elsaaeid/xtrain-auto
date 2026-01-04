/**
 * Newsletter Section GSAP Animations
 * Creative floating and scroll-triggered animations
 */

document.addEventListener('DOMContentLoaded', function () {
    // Register ScrollTrigger plugin
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    const newsletterWrapper = document.querySelector('.newsletter-wrapper');
    if (!newsletterWrapper) return;

    const newsletterImage = newsletterWrapper.querySelector('.newsletter-image');
    const newsletterContent = newsletterWrapper.querySelector('.newsletter-content');
    const newsletterTitle = newsletterWrapper.querySelector('.newsletter-title');
    const newsletterDesc = newsletterWrapper.querySelector('.newsletter-desc');

    // Set initial states
    gsap.set([newsletterImage, newsletterContent], {
        opacity: 0
    });

    if (newsletterImage) {
        gsap.set(newsletterImage, {
            x: -80,
            scale: 0.9
        });
    }

    if (newsletterContent) {
        gsap.set(newsletterContent, {
            x: 80
        });
    }

    // Create main timeline for scroll animation
    const mainTimeline = gsap.timeline({
        scrollTrigger: {
            trigger: newsletterWrapper,
            start: 'top 80%',
            end: 'top 30%',
            toggleActions: 'play none none reverse'
        }
    });

    // Animate image entrance
    if (newsletterImage) {
        mainTimeline.to(newsletterImage, {
            opacity: 1,
            x: 0,
            scale: 1,
            duration: 0.8,
            ease: 'power3.out'
        }, 0);
    }

    // Animate content entrance
    if (newsletterContent) {
        mainTimeline.to(newsletterContent, {
            opacity: 1,
            x: 0,
            duration: 0.8,
            ease: 'power3.out'
        }, 0.2);
    }

    // Animate title with text reveal
    if (newsletterTitle) {
        mainTimeline.from(newsletterTitle, {
            y: 30,
            opacity: 0,
            duration: 0.6,
            ease: 'power2.out'
        }, 0.4);
    }

    // Animate description
    if (newsletterDesc) {
        mainTimeline.from(newsletterDesc, {
            y: 20,
            opacity: 0,
            duration: 0.6,
            ease: 'power2.out'
        }, 0.5);
    }

    // ===== Floating Animation for Image =====
    // Creates a gentle floating effect that runs continuously
    if (newsletterImage) {
        const imgElement = newsletterImage.querySelector('img');

        if (imgElement) {
            // Create floating animation timeline
            const floatTimeline = gsap.timeline({
                repeat: -1,
                yoyo: true,
                delay: 1 // Wait for entrance animation
            });

            floatTimeline.to(imgElement, {
                y: -15,
                rotation: 2,
                duration: 2.5,
                ease: 'sine.inOut'
            })
                .to(imgElement, {
                    y: 10,
                    rotation: -1,
                    duration: 2.5,
                    ease: 'sine.inOut'
                })
                .to(imgElement, {
                    y: -8,
                    rotation: 1.5,
                    duration: 2,
                    ease: 'sine.inOut'
                });

            // Add subtle scale pulse
            gsap.to(imgElement, {
                scale: 1.03,
                duration: 3,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
                delay: 1.5
            });

            // Add shadow animation for depth effect
            gsap.to(newsletterImage, {
                '--shadow-offset': '20px',
                duration: 2.5,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });
        }
    }

    // ===== Parallax Effect on Scroll =====
    if (newsletterImage) {
        gsap.to(newsletterImage, {
            y: -50,
            ease: 'none',
            scrollTrigger: {
                trigger: newsletterWrapper,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1.5
            }
        });
    }

    // ===== Decorative Particles Animation =====
    // Create floating particles around the image
    if (newsletterImage) {
        createFloatingParticles(newsletterImage);
    }

    function createFloatingParticles(container) {
        const particleCount = 6;
        const colors = ['#EA7739', '#f47c33', '#64748B', '#CBD5E1'];

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'newsletter-particle';

            // Random size
            const size = Math.random() * 8 + 4;

            // Random position around the image
            const angle = (i / particleCount) * Math.PI * 2;
            const radius = 80 + Math.random() * 60;
            const x = Math.cos(angle) * radius;
            const y = Math.sin(angle) * radius;

            // Style the particle
            Object.assign(particle.style, {
                position: 'absolute',
                width: size + 'px',
                height: size + 'px',
                borderRadius: '50%',
                backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                opacity: '0',
                pointerEvents: 'none',
                left: '50%',
                top: '50%',
                transform: `translate(${x}px, ${y}px)`,
                zIndex: '1'
            });

            container.style.position = 'relative';
            container.appendChild(particle);

            // Animate each particle
            gsap.to(particle, {
                opacity: 0.6,
                duration: 0.5,
                delay: 1 + (i * 0.1),
                scrollTrigger: {
                    trigger: newsletterWrapper,
                    start: 'top 70%'
                }
            });

            // Floating animation for particles
            gsap.to(particle, {
                y: '+=' + (Math.random() * 40 - 20),
                x: '+=' + (Math.random() * 30 - 15),
                duration: 2 + Math.random() * 2,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
                delay: Math.random() * 2
            });

            // Opacity pulse
            gsap.to(particle, {
                opacity: 0.3,
                duration: 1.5 + Math.random(),
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut',
                delay: 1.5 + Math.random()
            });
        }
    }

    // ===== Mouse Move Parallax Effect =====
    // Creates subtle movement based on mouse position
    let mouseX = 0;
    let mouseY = 0;
    let currentX = 0;
    let currentY = 0;

    newsletterWrapper.addEventListener('mousemove', (e) => {
        const rect = newsletterWrapper.getBoundingClientRect();
        mouseX = (e.clientX - rect.left - rect.width / 2) / rect.width;
        mouseY = (e.clientY - rect.top - rect.height / 2) / rect.height;
    });

    newsletterWrapper.addEventListener('mouseleave', () => {
        gsap.to({ x: currentX, y: currentY }, {
            x: 0,
            y: 0,
            duration: 0.8,
            ease: 'power2.out',
            onUpdate: function () {
                currentX = this.targets()[0].x;
                currentY = this.targets()[0].y;
                applyMouseParallax();
            }
        });
    });

    function applyMouseParallax() {
        if (newsletterImage) {
            const img = newsletterImage.querySelector('img');
            if (img) {
                gsap.set(img, {
                    x: currentX * 15,
                    y: currentY * 15
                });
            }
        }
    }

    // Smooth mouse tracking
    function updateMousePosition() {
        currentX += (mouseX - currentX) * 0.1;
        currentY += (mouseY - currentY) * 0.1;
        applyMouseParallax();
        requestAnimationFrame(updateMousePosition);
    }

    updateMousePosition();
});
