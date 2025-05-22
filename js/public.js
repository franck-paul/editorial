document.addEventListener('DOMContentLoaded', () => {

    function setSidebarState(isOpen) {
        const addClass = isOpen ? "open" : "close";
        const removeClass = isOpen ? "close" : "open";

        sidebar.classList.remove(removeClass);
        sidebar.classList.add(addClass);
        toggle.classList.remove(removeClass);
        toggle.classList.add(addClass);
    }

    setSidebarState(window.innerWidth > 900);

    window.addEventListener("resize", () => {
        setSidebarState(window.innerWidth > 900);
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && sidebar.classList.contains("open")) {
            setSidebarState(false);
        }
    });

    toggle.addEventListener("click", () => {
        setSidebarState(!sidebar.classList.contains("open"));
    });

    // Scroll to top management
    document.addEventListener('scroll', () => {
        const gotopButton = document.getElementById('gotop');
        if (gotopButton) {
            gotopButton.style.display = document.querySelector('html').scrollTop === 0 ? 'none' : 'block';
        }
    });

    const gotopButton = document.getElementById('gotop');
    if (gotopButton) {
        gotopButton.addEventListener('click', (event) => {

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

            event.preventDefault();
        });
    }
})
