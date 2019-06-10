/*
    Fonction qui s'occupe de tous les resize
*/

$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
    setTimeout(correctResize, 500);
})

function correctResize() {
    windowResizeListeners.forEach(function (listener) {
        listener();
    })
}

window.onresize = correctResize;
window.onload = correctResize;

windowResizeListeners.push(function () {
    if ($("#wrapper").hasClass("toggled")) {
        let width = window.innerWidth;
        $("#page-content-wrapper").css('max-width', (width - 250) + 'px');
    } else {
        $("#page-content-wrapper").css('max-width', '100%');
    }
})

