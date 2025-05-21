document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById("toggle");
    const menu = document.getElementById("menu");
    const sidebar = document.getElementById("sidebar");

    sidebar.classList.add("open");
    
    toggle.addEventListener("click", () => {
        if (sidebar.classList.contains("open")) {
            sidebar.classList.remove("open");
            sidebar.classList.add("close");
            console.log("sidebar closed");
        } else {
            sidebar.classList.remove("close");
            sidebar.classList.add("open");
            console.log("sidebar opened");
        }

    });


    // totop scroll
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
