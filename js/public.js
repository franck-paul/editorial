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

    $(window).scroll(function () {
        if ($(this).scrollTop() != 0) {
            $('#gotop').fadeIn();
        } else {
            $('#gotop').fadeOut();
        }
    });
    $('#gotop').on('click', function (e) {
        $('body,html').animate({
            scrollTop: 0,
        },
            800
        );
        e.preventDefault();
    });
})
