document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('[data-site-header]');
    const toggle = document.querySelector('.mobile-nav-toggle');
    const nav = document.querySelector('.main-navigation');
    const dropdownItems = document.querySelectorAll('.main-navigation .has-dropdown');
    const normalNavItems = document.querySelectorAll('.main-navigation .nav-item:not(.has-dropdown)');
    const backToTop = document.querySelector('.back-to-top');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const mobileQuery = window.matchMedia('(max-width: 991px)');

    function isMobileNavigation() {
        return mobileQuery.matches;
    }

    function setSubmenuHeight(item) {
        const menu = item.querySelector('.dropdown-menu');
        if (!menu) return;

        if (isMobileNavigation()) {
            menu.style.setProperty('--submenu-height', menu.scrollHeight + 'px');
        } else {
            menu.style.removeProperty('--submenu-height');
        }
    }

    function closeDropdown(item) {
        const trigger = item.querySelector('.dropdown-toggle');
        item.classList.remove('open');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
    }

    function openDropdown(item) {
        closeAllDropdowns(item);
        setSubmenuHeight(item);
        item.classList.add('open');
        const trigger = item.querySelector('.dropdown-toggle');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }
    }

    function toggleDropdown(item) {
        if (item.classList.contains('open')) {
            closeDropdown(item);
        } else {
            openDropdown(item);
        }
    }

    function closeAllDropdowns(exceptItem) {
        dropdownItems.forEach(function (item) {
            if (item !== exceptItem) {
                closeDropdown(item);
            }
        });
    }

    function closeMobileNav() {
        if (!toggle || !nav) return;
        nav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        closeAllDropdowns();
    }

    function openMobileNav() {
        if (!toggle || !nav) return;
        nav.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function updateScrolledState() {
        const isScrolled = window.scrollY > 12;
        if (header) {
            header.classList.toggle('is-scrolled', isScrolled);
        }
        if (backToTop) {
            backToTop.classList.toggle('is-visible', window.scrollY > 420);
        }
    }

    if (toggle && nav) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            if (nav.classList.contains('open')) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });
    }

    if (nav) {
        nav.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobileNavigation() && !link.classList.contains('dropdown-toggle')) {
                    closeMobileNav();
                }
            });
        });
    }

    dropdownItems.forEach(function (item) {
        const trigger = item.querySelector('.dropdown-toggle');

        item.addEventListener('mouseenter', function () {
            if (!isMobileNavigation()) {
                openDropdown(item);
            }
        });

        item.addEventListener('mouseleave', function () {
            if (!isMobileNavigation()) {
                closeDropdown(item);
            }
        });

        item.addEventListener('focusin', function () {
            if (!isMobileNavigation()) {
                openDropdown(item);
            }
        });

        item.addEventListener('focusout', function (event) {
            if (!isMobileNavigation() && !item.contains(event.relatedTarget)) {
                closeDropdown(item);
            }
        });

        if (!trigger) return;

        trigger.addEventListener('click', function (event) {
            if (!isMobileNavigation()) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            toggleDropdown(item);
        });

        trigger.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDropdown(item);
                trigger.focus();
            }
        });
    });

    normalNavItems.forEach(function (item) {
        item.addEventListener('mouseenter', function () {
            if (!isMobileNavigation()) {
                closeAllDropdowns();
            }
        });
    });

    document.addEventListener('click', function (event) {
        if (nav && !nav.contains(event.target) && toggle && !toggle.contains(event.target)) {
            closeAllDropdowns();
            if (isMobileNavigation()) {
                closeMobileNav();
            }
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
            if (isMobileNavigation()) {
                closeMobileNav();
            }
        }
    });

    window.addEventListener('resize', function () {
        closeAllDropdowns();
        if (!isMobileNavigation() && toggle && nav) {
            nav.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    window.addEventListener('scroll', updateScrolledState, { passive: true });
    updateScrolledState();

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: reducedMotion.matches ? 'auto' : 'smooth'
            });
        });
    }

    const heroCarousel = document.querySelector('[data-hero-carousel]');
    if (heroCarousel) {
        const slides = Array.from(heroCarousel.querySelectorAll('[data-hero-slide]'));
        const dots = Array.from(heroCarousel.querySelectorAll('[data-hero-dot]'));
        const prevButton = heroCarousel.querySelector('[data-hero-prev]');
        const nextButton = heroCarousel.querySelector('[data-hero-next]');
        const intervalTime = 5500;
        let currentSlide = 0;
        let carouselTimer = null;

        function showHeroSlide(index) {
            if (!slides.length) return;
            currentSlide = (index + slides.length) % slides.length;

            slides.forEach(function (slide, slideIndex) {
                const isActive = slideIndex === currentSlide;
                slide.classList.toggle('is-active', isActive);
                slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });

            dots.forEach(function (dot, dotIndex) {
                const isActive = dotIndex === currentSlide;
                dot.classList.toggle('is-active', isActive);
                dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
        }

        function stopHeroCarousel() {
            if (carouselTimer) {
                window.clearInterval(carouselTimer);
                carouselTimer = null;
            }
        }

        function startHeroCarousel() {
            stopHeroCarousel();
            if (slides.length < 2 || reducedMotion.matches) return;
            carouselTimer = window.setInterval(function () {
                showHeroSlide(currentSlide + 1);
            }, intervalTime);
        }

        function manuallyShowHeroSlide(index) {
            showHeroSlide(index);
            startHeroCarousel();
        }

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                manuallyShowHeroSlide(currentSlide - 1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                manuallyShowHeroSlide(currentSlide + 1);
            });
        }

        dots.forEach(function (dot, index) {
            dot.addEventListener('click', function () {
                manuallyShowHeroSlide(index);
            });
        });

        showHeroSlide(0);
        startHeroCarousel();
    }
    const previewVideos = document.querySelectorAll('video[data-preview-time]');
    previewVideos.forEach(function (video) {
        const previewTime = Number(video.dataset.previewTime || '0');
        let previewReady = false;
        let firstPlay = true;
        let resettingForFirstPlay = false;
        let previewAttempts = 0;
        const maxPreviewAttempts = 12;

        if (!Number.isFinite(previewTime) || previewTime <= 0) return;

        function canUsePreviewTime() {
            return Number.isFinite(video.duration) && video.duration > previewTime;
        }

        function showPreviewFrame() {
            if (previewReady || !firstPlay || resettingForFirstPlay) return;

            if (!canUsePreviewTime()) {
                if (previewAttempts < maxPreviewAttempts) {
                    previewAttempts += 1;
                    window.setTimeout(showPreviewFrame, 180);
                }
                return;
            }

            try {
                video.currentTime = previewTime;
                video.pause();
                previewReady = true;
            } catch (error) {
                if (previewAttempts < maxPreviewAttempts) {
                    previewAttempts += 1;
                    window.setTimeout(showPreviewFrame, 180);
                }
            }
        }

        function playFromBeginningAfterSeek() {
            video.removeEventListener('seeked', playFromBeginningAfterSeek);
            video.play().catch(function () {
                firstPlay = true;
            }).finally(function () {
                resettingForFirstPlay = false;
            });
        }

        ['loadedmetadata', 'durationchange', 'loadeddata', 'canplay'].forEach(function (eventName) {
            video.addEventListener(eventName, showPreviewFrame);
        });

        showPreviewFrame();

        video.addEventListener('play', function () {
            if (!firstPlay || resettingForFirstPlay) return;

            firstPlay = false;
            resettingForFirstPlay = true;
            video.pause();

            if (Math.abs(video.currentTime) < 0.05) {
                playFromBeginningAfterSeek();
                return;
            }

            video.addEventListener('seeked', playFromBeginningAfterSeek);
            try {
                video.currentTime = 0;
            } catch (error) {
                video.removeEventListener('seeked', playFromBeginningAfterSeek);
                resettingForFirstPlay = false;
                video.play().catch(function () {
                    firstPlay = true;
                });
            }
        });
    });
    const galleryPage = document.querySelector('[data-gallery-page]');
    if (galleryPage) {
        const filterButtons = Array.from(galleryPage.querySelectorAll('[data-gallery-filter]'));
        const filterItems = Array.from(galleryPage.querySelectorAll('[data-gallery-filter-item]'));
        const galleryItems = Array.from(document.querySelectorAll('[data-gallery-item]'));
        const lightbox = document.querySelector('[data-gallery-lightbox]');
        const lightboxImage = lightbox ? lightbox.querySelector('[data-gallery-lightbox-image]') : null;
        const lightboxImageWrap = lightbox ? lightbox.querySelector('.gallery-lightbox-image-wrap') : null;
        const lightboxTitle = lightbox ? lightbox.querySelector('[data-gallery-lightbox-title]') : null;
        const lightboxCaption = lightbox ? lightbox.querySelector('[data-gallery-lightbox-caption]') : null;
        const lightboxLocation = lightbox ? lightbox.querySelector('[data-gallery-lightbox-location]') : null;
        const lightboxDate = lightbox ? lightbox.querySelector('[data-gallery-lightbox-date]') : null;
        const lightboxLocationWrap = lightbox ? lightbox.querySelector('[data-gallery-location-wrap]') : null;
        const lightboxDateWrap = lightbox ? lightbox.querySelector('[data-gallery-date-wrap]') : null;
        const prevGalleryButton = lightbox ? lightbox.querySelector('[data-gallery-prev]') : null;
        const nextGalleryButton = lightbox ? lightbox.querySelector('[data-gallery-next]') : null;
        let activeGalleryScope = document;
        let activeGalleryItems = galleryItems.slice();
        let activeGalleryIndex = 0;

        function updateActiveGalleryItems() {
            activeGalleryItems = Array.from(activeGalleryScope.querySelectorAll('[data-gallery-item]')).filter(function (item) {
                return !item.hidden;
            });
        }

        function setGalleryFilter(category) {
            activeGalleryScope = galleryPage;
            filterItems.forEach(function (item) {
                const shouldShow = category === 'all' || item.dataset.category === category;
                item.hidden = !shouldShow;
            });

            filterButtons.forEach(function (button) {
                const isActive = button.dataset.galleryFilter === category;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            updateActiveGalleryItems();
        }

        function setOptionalGalleryMeta(wrapper, target, value) {
            if (!wrapper || !target) return;
            const cleanValue = value || '';
            wrapper.hidden = cleanValue.trim() === '';
            target.textContent = cleanValue;
        }

        function openGalleryLightbox(item) {
            if (!lightbox || !lightboxImage || !lightboxImageWrap) return;
            updateActiveGalleryItems();
            activeGalleryIndex = Math.max(0, activeGalleryItems.indexOf(item));

            lightboxImageWrap.classList.remove('is-missing');
            lightboxImage.src = item.dataset.src || '';
            lightboxImage.alt = item.dataset.title || '';
            if (lightboxTitle) lightboxTitle.textContent = item.dataset.title || '';
            if (lightboxCaption) lightboxCaption.textContent = item.dataset.caption || '';
            setOptionalGalleryMeta(lightboxLocationWrap, lightboxLocation, item.dataset.location);
            setOptionalGalleryMeta(lightboxDateWrap, lightboxDate, item.dataset.date);

            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.classList.add('gallery-lightbox-open');
        }

        function closeGalleryLightbox() {
            if (!lightbox) return;
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('gallery-lightbox-open');
            if (lightboxImage) {
                lightboxImage.removeAttribute('src');
                lightboxImage.alt = '';
            }
        }

        function showAdjacentGalleryItem(direction) {
            if (!lightbox || !lightbox.classList.contains('is-open') || !activeGalleryItems.length) return;
            activeGalleryIndex = (activeGalleryIndex + direction + activeGalleryItems.length) % activeGalleryItems.length;
            openGalleryLightbox(activeGalleryItems[activeGalleryIndex]);
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                setGalleryFilter(button.dataset.galleryFilter || 'all');
            });
        });

        galleryItems.forEach(function (item) {
            item.addEventListener('click', function () {
                activeGalleryScope = item.closest('.gallery-section, .gallery-igp-section, .gallery-vocational-section') || document;
                openGalleryLightbox(item);
            });
        });

        if (lightboxImage && lightboxImageWrap) {
            lightboxImage.addEventListener('error', function () {
                lightboxImageWrap.classList.add('is-missing');
            });
            lightboxImage.addEventListener('load', function () {
                lightboxImageWrap.classList.remove('is-missing');
            });
        }

        if (prevGalleryButton) {
            prevGalleryButton.addEventListener('click', function () {
                showAdjacentGalleryItem(-1);
            });
        }

        if (nextGalleryButton) {
            nextGalleryButton.addEventListener('click', function () {
                showAdjacentGalleryItem(1);
            });
        }

        if (lightbox) {
            lightbox.querySelectorAll('[data-gallery-close]').forEach(function (closeButton) {
                closeButton.addEventListener('click', closeGalleryLightbox);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (!lightbox || !lightbox.classList.contains('is-open')) return;
            if (event.key === 'Escape') {
                closeGalleryLightbox();
            } else if (event.key === 'ArrowLeft') {
                showAdjacentGalleryItem(-1);
            } else if (event.key === 'ArrowRight') {
                showAdjacentGalleryItem(1);
            }
        });

        setGalleryFilter('all');
    }

    const revealItems = document.querySelectorAll('.section-reveal, .programme-card, .home-impact-item, .involvement-card, .why-involved-point, .difference-item, .news-card, .cta-card, .hero-feature-item');
    if ('IntersectionObserver' in window && !reducedMotion.matches) {
        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealItems.forEach(function (item) {
            item.classList.add('section-reveal');
            revealObserver.observe(item);
        });
    } else {
        revealItems.forEach(function (item) {
            item.classList.add('is-visible');
        });
    }

    const impactSection = document.querySelector('.impact-section');
    const statNumbers = document.querySelectorAll('.stat-number');
    let statsHaveRun = false;

    function formatNumber(value) {
        return Math.round(value).toLocaleString('en-US');
    }

    function runStats() {
        if (statsHaveRun) return;
        statsHaveRun = true;

        statNumbers.forEach(function (numberEl) {
            const finalValue = Number(numberEl.dataset.count || '0');
            if (reducedMotion.matches || finalValue <= 0) {
                numberEl.textContent = formatNumber(finalValue);
                return;
            }

            const duration = 900;
            const startTime = performance.now();

            function tick(now) {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                numberEl.textContent = formatNumber(finalValue * eased);

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    numberEl.textContent = formatNumber(finalValue);
                }
            }

            requestAnimationFrame(tick);
        });
    }

    if (impactSection && 'IntersectionObserver' in window) {
        const impactObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    runStats();
                    impactObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.35 });
        impactObserver.observe(impactSection);
    } else {
        runStats();
    }
});







