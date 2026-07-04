/**
 * Portfolio — Frontend JavaScript
 * Particles, dark mode, counters, contact form, scroll effects
 */

document.addEventListener('DOMContentLoaded', () => {
    initPreloader();
    initTheme();
    initNavbarScroll();
    initActiveNav();
    initMobileMenu();
    initScrollReveal();
    initSmoothScroll();
    initTypingEffect();
    initParticles();
    initCounters();
    initSkillBars();
    initBackToTop();
    initContactForm();
});

/* ---------- Preloader ---------- */
function initPreloader() {
    const preloader = document.getElementById('preloader');
    if (!preloader) return;

    const hide = () => {
        preloader.classList.add('hidden');
        document.body.style.overflow = '';
    };

    if (document.readyState === 'complete') {
        setTimeout(hide, 600);
    } else {
        window.addEventListener('load', () => setTimeout(hide, 600));
    }
    setTimeout(hide, 3000);
}

/* ---------- Dark / Light Theme ---------- */
function initTheme() {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.documentElement.setAttribute('data-theme', saved);
    } else if (saved === 'light') {
        document.documentElement.removeAttribute('data-theme');
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    document.querySelectorAll('.theme-toggle, .mobile-theme-toggle').forEach(btn => {
        btn.addEventListener('click', toggleTheme);
    });
}

function toggleTheme() {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    if (next === 'light') {
        html.removeAttribute('data-theme');
    } else {
        html.setAttribute('data-theme', 'dark');
    }
    localStorage.setItem('theme', next);
}

/* ---------- Navbar Scroll Effect ---------- */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* ---------- Active Nav Section ---------- */
function initActiveNav() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    if (!sections.length || !navLinks.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.dataset.section === id);
                });
            }
        });
    }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });

    sections.forEach(s => observer.observe(s));
}

/* ---------- Mobile Menu ---------- */
function initMobileMenu() {
    const btn = document.querySelector('.mobile-menu-btn');
    const menu = document.querySelector('.mobile-menu');
    if (!btn || !menu) return;

    const setOpen = (open) => {
        btn.classList.toggle('active', open);
        menu.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', String(open));
        menu.setAttribute('aria-hidden', String(!open));
        document.body.style.overflow = open ? 'hidden' : '';
    };

    btn.addEventListener('click', () => {
        setOpen(!menu.classList.contains('open'));
    });

    menu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            setOpen(false);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.classList.contains('open')) {
            setOpen(false);
            btn.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768 && menu.classList.contains('open')) {
            setOpen(false);
        }
    });
}

/* ---------- Scroll Reveal ---------- */
function initScrollReveal() {
    const elements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(el => observer.observe(el));
}

/* ---------- Smooth Scrolling ---------- */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

/* ---------- Typing Effect ---------- */
function initTypingEffect() {
    const el = document.querySelector('.typing-text');
    if (!el) return;

    const text = el.getAttribute('data-text');
    if (!text) return;

    el.textContent = '';
    let i = 0;

    function type() {
        if (i < text.length) {
            el.textContent += text.charAt(i);
            i++;
            setTimeout(type, 50);
        }
    }
    setTimeout(type, 800);
}

/* ---------- Particle Background ---------- */
function initParticles() {
    const canvas = document.getElementById('particleCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let particles = [];
    let animId;
    const count = 60;

    function resize() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }

    function createParticles() {
        particles = [];
        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                r: Math.random() * 2 + 0.5,
                dx: (Math.random() - 0.5) * 0.4,
                dy: (Math.random() - 0.5) * 0.4,
                opacity: Math.random() * 0.5 + 0.2
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach((p, i) => {
            p.x += p.dx;
            p.y += p.dy;
            if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.dy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 143, 184, ${p.opacity})`;
            ctx.fill();

            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                if (dist < 120) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = `rgba(255, 143, 184, ${0.15 * (1 - dist / 120)})`;
                    ctx.stroke();
                }
            }
        });

        animId = requestAnimationFrame(draw);
    }

    resize();
    createParticles();
    draw();

    window.addEventListener('resize', () => {
        resize();
        createParticles();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            cancelAnimationFrame(animId);
        } else {
            draw();
        }
    });
}

/* ---------- Animated Counters ---------- */
function initCounters() {
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.dataset.count, 10);
            animateCounter(el, target);
            observer.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
}

function animateCounter(el, target) {
    const duration = 1500;
    const start = performance.now();

    function update(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target);
        if (progress < 1) requestAnimationFrame(update);
        else el.textContent = target;
    }
    requestAnimationFrame(update);
}

function animatePercent(el, target) {
    const duration = 1200;
    const start = performance.now();

    function update(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target) + '%';
        if (progress < 1) requestAnimationFrame(update);
        else el.textContent = target + '%';
    }
    requestAnimationFrame(update);
}

/* ---------- Skill Progress Bars ---------- */
function initSkillBars() {
    const categories = document.querySelectorAll('.skill-category');
    if (!categories.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            entry.target.querySelectorAll('.skill-item').forEach((item, i) => {
                setTimeout(() => {
                    const level = parseInt(item.querySelector('.skill-bar-fill')?.dataset.level || 0, 10);
                    const fill = item.querySelector('.skill-bar-fill');
                    const percent = item.querySelector('.skill-percent');
                    if (fill) fill.style.width = level + '%';
                    if (percent) animatePercent(percent, level);
                }, i * 100);
            });

            observer.unobserve(entry.target);
        });
    }, { threshold: 0.3 });

    categories.forEach(c => observer.observe(c));
}

/* ---------- Back to Top ---------- */
function initBackToTop() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 500);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ---------- Contact Form (AJAX) ---------- */
function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = form.querySelector('.contact-submit');
        const feedback = document.getElementById('formFeedback');
        const submitText = submitBtn.querySelector('.submit-text');
        const submitLoading = submitBtn.querySelector('.submit-loading');

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        feedback.classList.remove('show', 'success', 'error');

        const data = {
            name: form.name.value.trim(),
            email: form.email.value.trim(),
            message: form.message.value.trim(),
            csrf_token: form.querySelector('[name="csrf_token"]')?.value || window.CSRF_TOKEN
        };

        try {
            const res = await fetch('api/contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json().catch(() => ({
                success: false,
                message: 'Server error. Please try again.'
            }));

            feedback.textContent = result.message || 'Server error. Please try again.';
            feedback.classList.add('show', result.success ? 'success' : 'error');

            if (result.success) {
                form.reset();
            }

            if (result.csrf_token) {
                form.querySelector('[name="csrf_token"]').value = result.csrf_token;
                window.CSRF_TOKEN = result.csrf_token;
            }
        } catch {
            feedback.textContent = 'Network error. Please try again.';
            feedback.classList.add('show', 'error');
        } finally {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
        }
    });
}
