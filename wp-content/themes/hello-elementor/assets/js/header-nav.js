document.addEventListener("DOMContentLoaded", () => {

    const toggle = document.querySelector(".categories-toggle");
    const dropdown = document.querySelector(".categories-dropdown");

    if (!toggle || !dropdown) return;

    const mobileCatToggle = document.querySelector(".mobile-cat-toggle");
    const mobileCatSub = document.querySelector(".categories-list.mobile-only");

    // Use event delegation: listen on nav, check if click is on .categories-toggle or its children
    const nav = document.querySelector(".header-nav");
    if (nav) {
        nav.addEventListener("click", function(e) {
            const target = e.target;
            // Check if .categories-toggle or its descendant was clicked
            if (toggle.contains(target)) {
                e.stopPropagation();
                const isVisible = dropdown.style.display === "block";
                if (isVisible) {
                    dropdown.style.display = "none";
                    toggle.classList.remove("is-active");
                } else {
                    dropdown.style.display = "block";
                    toggle.classList.add("is-active");
                }
            }
            // don't prevent other clicks here; active link handling is below
        });
    }

    document.addEventListener("click", () => {
        dropdown.style.display = "none";
        toggle.classList.remove("is-active");

        // Reset mobile sub-menu when closing main menu
        if (mobileCatToggle) {
            mobileCatToggle.classList.remove("is-active");
        }
    });

    // Mobile Sub-menu Toggle (Categories inside the menu)
    if (mobileCatToggle && mobileCatSub) {
        mobileCatToggle.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();
            mobileCatToggle.classList.toggle("is-active");
            // CSS handles the .categories-list.mobile-only display
        });
    }

    // Menu selectors
    var menuSelector = '.primary-menu li a, .mobile-menu-items li a';

    function getFooterLinks() {
        // Prefer anchors inside a <footer> element; fallback to common footer classes
        var footer = document.querySelector('footer');
        if (footer) return footer.querySelectorAll('a');
        var maybe = document.querySelector('.site-footer, .footer, #colophon, .elementor-footer');
        if (maybe) return maybe.querySelectorAll('a');
        return document.querySelectorAll('.footer-menu li a');
    }

    function normalizeHref(href) {
        if (!href) return '';
        return href.replace(/\/$/, '');
    }

    function linkKeyFromHref(href) {
        try {
            var u = new URL(href, window.location.href);
            var p = (u.pathname || '').replace(/\/$/, '') || '/';
            return p + (u.hash || '');
        } catch (e) {
            return href || '';
        }
    }

    // Desktop-only active logic (useful when desktop menu links are independent)
    function updateActiveDesktop(clickedLink) {
        var desktopSelector = '.primary-menu.desktop-menu li a';
        var desktopLinks = document.querySelectorAll(desktopSelector);
        if (!desktopLinks || desktopLinks.length === 0) {
            // fallback to any primary-menu anchors
            desktopLinks = document.querySelectorAll('.primary-menu li a');
        }

        // clear desktop-only classes
        desktopLinks.forEach(function(l) { l.classList.remove('active-menu-link', 'active-menu-desktop'); var p = l.closest('li'); if (p) p.classList.remove('active-menu-link', 'active-menu-desktop'); });

        var currentHash = window.location.hash || '';
        var currentPath = window.location.pathname.replace(/\/$/, '') || '/';

        // If a desktop link was clicked, mark it active and return
        if (clickedLink && clickedLink.closest('.primary-menu')) {
            clickedLink.classList.add('active-menu-link', 'active-menu-desktop');
            var pl = clickedLink.closest('li'); if (pl) pl.classList.add('active-menu-link', 'active-menu-desktop');
            return true;
        }

        // If we have a hash, try to match desktop links by hash
        if (currentHash) {
            for (var h = 0; h < desktopLinks.length; h++) {
                var dl = desktopLinks[h];
                var hrefH = (dl.getAttribute('href') || '').trim();
                if (hrefH === currentHash) { dl.classList.add('active-menu-link', 'active-menu-desktop'); var p2 = dl.closest('li'); if (p2) p2.classList.add('active-menu-link', 'active-menu-desktop'); return true; }
                try { var urlH = new URL(hrefH, window.location.href); if (urlH.hash && urlH.hash === currentHash) { dl.classList.add('active-menu-link', 'active-menu-desktop'); var p3 = dl.closest('li'); if (p3) p3.classList.add('active-menu-link', 'active-menu-desktop'); return true; } } catch (e) {}
            }
        }

        // Fall back to matching by page path
        for (var i = 0; i < desktopLinks.length; i++) {
            var d = desktopLinks[i];
            var hrefAttr = (d.getAttribute('href') || '').trim();
            if (!hrefAttr) continue;
            if (hrefAttr.charAt(0) === '#') continue;
            try { var linkUrl = new URL(hrefAttr, window.location.href); } catch (e) { continue; }
            var linkPath = (linkUrl.pathname || '').replace(/\/$/, '') || '/';
            if (linkPath === currentPath) {
                d.classList.add('active-menu-link', 'active-menu-desktop'); var p4 = d.closest('li'); if (p4) p4.classList.add('active-menu-link', 'active-menu-desktop');
                return true;
            }
        }
        return false;
    }

    function updateActiveMenu(clickedLink) {
        // First, run desktop-specific logic if desktop menu exists
        var desktopExists = document.querySelector('.primary-menu') !== null;
        if (desktopExists) {
            updateActiveDesktop(clickedLink);
        }

        // Now handle mobile links, footer links and syncing
        var mobileLinks = document.querySelectorAll('.mobile-menu-items li a');
        var footerLinks = getFooterLinks();
        // clear mobile and footer classes
        mobileLinks.forEach(function(l) { l.classList.remove('active-menu-link', 'active-menu-mobile'); var p = l.closest('li'); if (p) p.classList.remove('active-menu-link', 'active-menu-mobile'); });
        Array.from(footerLinks).forEach(function(l) { l.classList.remove('active-menu-link', 'active-menu-footer'); var p = l.closest('li'); if (p) p.classList.remove('active-menu-link', 'active-menu-footer'); });

        var currentHash = window.location.hash || '';

        // If a link was clicked
        if (clickedLink) {
            // If clicked in mobile, mark it and sync desktop + footer
            if (clickedLink.closest('.mobile-menu-items')) {
                clickedLink.classList.add('active-menu-link', 'active-menu-mobile');
                var plm = clickedLink.closest('li'); if (plm) plm.classList.add('active-menu-link', 'active-menu-mobile');
                // sync to desktop
                var clickedHref = (clickedLink.getAttribute('href') || '').trim();
                var key = linkKeyFromHref(clickedHref);
                var desktopLinksAll = document.querySelectorAll('.primary-menu li a');
                desktopLinksAll.forEach(function(dl) { if (linkKeyFromHref(dl.getAttribute('href') || '') === key) { dl.classList.add('active-menu-link', 'active-menu-desktop'); var p2 = dl.closest('li'); if (p2) p2.classList.add('active-menu-link', 'active-menu-desktop'); } });
                // sync to footer
                Array.from(footerLinks).forEach(function(fl){ if (linkKeyFromHref((fl.getAttribute('href')||'') ) === key) { fl.classList.add('active-menu-link', 'active-menu-footer'); var p4 = fl.closest('li'); if (p4) p4.classList.add('active-menu-link', 'active-menu-footer'); } });
                return;
            }

            // If clicked in primary menu, ensure mobile sync
            if (clickedLink.closest('.primary-menu')) {
                var clickedHref2 = (clickedLink.getAttribute('href') || '').trim();
                var key2 = linkKeyFromHref(clickedHref2);
                mobileLinks.forEach(function(ml) { if (linkKeyFromHref(ml.getAttribute('href') || '') === key2) { ml.classList.add('active-menu-link', 'active-menu-mobile'); var p3 = ml.closest('li'); if (p3) p3.classList.add('active-menu-link', 'active-menu-mobile'); } });
                // sync to footer
                Array.from(footerLinks).forEach(function(fl){ if (linkKeyFromHref((fl.getAttribute('href')||'') ) === key2) { fl.classList.add('active-menu-link', 'active-menu-footer'); var p5 = fl.closest('li'); if (p5) p5.classList.add('active-menu-link', 'active-menu-footer'); } });
                return;
            }
        }

        // On initial load or hash navigation, attempt to sync mobile from desktop active
            var activeDesktop = document.querySelector('.primary-menu li.active-menu-desktop > a, .primary-menu li a.active-menu-desktop, .primary-menu li a.active-menu-link');
        if (activeDesktop) {
            var keyd = linkKeyFromHref((activeDesktop.getAttribute('href')||'').trim());
                mobileLinks.forEach(function(ml) { if (linkKeyFromHref(ml.getAttribute('href')||'') === keyd) { ml.classList.add('active-menu-link', 'active-menu-mobile'); var p5 = ml.closest('li'); if (p5) p5.classList.add('active-menu-link', 'active-menu-mobile'); } });
                Array.from(footerLinks).forEach(function(fl){ if (linkKeyFromHref((fl.getAttribute('href')||'') ) === keyd) { fl.classList.add('active-menu-link', 'active-menu-footer'); var p6 = fl.closest('li'); if (p6) p6.classList.add('active-menu-link', 'active-menu-footer'); } });
            return;
        }

        // Otherwise, try to set mobile active by hash first
        if (currentHash) {
            for (var m = 0; m < mobileLinks.length; m++) {
                var ml2 = mobileLinks[m];
                var hrefM = (ml2.getAttribute('href')||'').trim();
                if (hrefM === currentHash) { ml2.classList.add('active-menu-link', 'active-menu-mobile'); var p6 = ml2.closest('li'); if (p6) p6.classList.add('active-menu-link', 'active-menu-mobile'); return; }
                try { var urlM = new URL(hrefM, window.location.href); if (urlM.hash && urlM.hash === currentHash) { ml2.classList.add('active-menu-link', 'active-menu-mobile'); var p7 = ml2.closest('li'); if (p7) p7.classList.add('active-menu-link', 'active-menu-mobile'); return; } } catch(e){}
            }
        }

        // Finally, try path-based mobile matching
        var currentPath = window.location.pathname.replace(/\/$/, '') || '/';
        for (var mm = 0; mm < mobileLinks.length; mm++) {
            var ml3 = mobileLinks[mm];
            var hrefAttr = (ml3.getAttribute('href')||'').trim();
            if (!hrefAttr) continue; if (hrefAttr.charAt(0) === '#') continue;
            try { var linkUrl2 = new URL(hrefAttr, window.location.href); } catch(e) { continue; }
            var linkPath2 = (linkUrl2.pathname || '').replace(/\/$/, '') || '/';
            if (linkPath2 === currentPath) { ml3.classList.add('active-menu-link', 'active-menu-mobile'); var p8 = ml3.closest('li'); if (p8) p8.classList.add('active-menu-link', 'active-menu-mobile'); return; }
        }
    }

    // initial run
    updateActiveMenu();
    // delayed run to handle browsers that update hash after load/navigation
    setTimeout(function() { updateActiveMenu(); }, 120);

    // update on hash change
    window.addEventListener('hashchange', function() { updateActiveMenu(); });

    // update on history navigation (back/forward)
    window.addEventListener('popstate', function() { updateActiveMenu(); });

    // update on clicks inside the header nav: handle both desktop and mobile menu clicks
    if (nav) {
        nav.addEventListener('click', function(e) {
            var a = e.target.closest('a');
            if (!a) return;
            if (a.closest('.primary-menu') || a.closest('.mobile-menu-items')) {
                updateActiveMenu(a);
            }
        });
    }

    // update on clicks inside the primary menu (to catch anchor clicks)
    var primaryMenu = document.querySelector('.primary-menu');
    if (primaryMenu) {
        primaryMenu.addEventListener('click', function(e) {
            var a = e.target.closest('a');
            if (!a) return;
            // mark clicked link active (anchor + parent li)
            updateActiveMenu(a);
        });
    }

});
