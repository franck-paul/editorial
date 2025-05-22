document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById("toggle");
    const sidebar = document.getElementById("sidebar");

    if (window.innerWidth > 900) {
        sidebar.classList.add("open");
        toggle.classList.add("open");
    } else {
        sidebar.classList.add("close");
        toggle.classList.add("close");
    }
    window.addEventListener("resize", () => {
        if (window.innerWidth > 900) {
            sidebar.classList.remove("close");
            sidebar.classList.add("open");
            toggle.classList.remove("close");
            toggle.classList.add("open");
        } else {
            sidebar.classList.remove("open");
            sidebar.classList.add("close");
            toggle.classList.remove("open");
            toggle.classList.add("close");
        }
    });
    
    document.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        if (sidebar.classList.contains("open")) {
            sidebar.classList.remove("open");
            sidebar.classList.add("close");
            toggle.classList.remove("open");
            toggle.classList.add("close");
        }
    });
    
    toggle.addEventListener("click", () => {
        if (sidebar.classList.contains("open")) {
            sidebar.classList.remove("open");
            sidebar.classList.add("close");
            toggle.classList.remove("open");
            toggle.classList.add("close");
        } else {
            sidebar.classList.remove("close");
            sidebar.classList.add("open");
            toggle.classList.remove("close");
            toggle.classList.add("open");
        }

    });

    // Scroll to top management
    document.addEventListener('scroll', () => {
        const gotopButton = document.getElementById('gotop');
        if (gotopButton) {
            gotopButton.style.display = document.querySelector('html').scrollTop === 0 ? 'none' : 'block';
        }
    });

    // Accessibility flags
    dotclear.animationisReduced =
        window.matchMedia('(prefers-reduced-motion: reduce)') === true ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches === true;
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    mediaQuery.onchange = (event) => {
        dotclear.animationisReduced = event.matches;
    };

    const gotopButton = document.getElementById('gotop');
    if (gotopButton) {
        gotopButton.addEventListener('click', (event) => {
            if (dotclear.animationisReduced) {
                // Scroll to top instantly
                document.querySelector('html').scrollTop = 0;
            } else {
                // Scroll to top smoothly
                const scrollToTop = (duration) => {
                    // cancel if already on top
                    if (document.scrollingElement.scrollTop === 0) return;

                    // if duration is zero, no animation
                    if (duration === 0) {
                        document.scrollingElement.scrollTop = 0;
                        return;
                    }

                    const cosParameter = document.scrollingElement.scrollTop / 2;
                    let scrollCount = 0;
                    let oldTimestamp = null;

                    const step = (newTimestamp) => {
                        if (oldTimestamp !== null) {
                            scrollCount += (Math.PI * (newTimestamp - oldTimestamp)) / duration;
                            if (scrollCount >= Math.PI) {
                                document.scrollingElement.scrollTop = 0;
                                return;
                            }
                            document.scrollingElement.scrollTop = cosParameter + cosParameter * Math.cos(scrollCount);
                        }
                        oldTimestamp = newTimestamp;
                        window.requestAnimationFrame(step);
                    };
                    window.requestAnimationFrame(step);
                };
                scrollToTop(800);
            }
            event.preventDefault();
        });
    }
})
