const active = new IntegerProperty(1);
var showTimeOut;

$(function () {

  $(document).on(
    "click",
    ".accueil .direction-btn .fa-angle-double-left",
    goPrevious
  );

  $(document).on(
    "click",
    ".accueil .direction-btn .fa-angle-double-right",
    goNext
  );

  $(".accueil .news-titles div").each((index, el) => {
    $(el).click(event => {
      active.value = index + 1;
    });
  });

  initActive();
});

function goNext() {
  active.value += 1;
}

function goPrevious() {
  active.value -= 1;
}

function initActive() {
  active.min = 1;
  active.max = $(".accueil .news-titles div").length;

  const changeCallBack = function (currentIndex) {
    $(".accueil .news-titles div").removeClass("bg-success text-white");
    $(".accueil .news-titles div:nth-child(" + currentIndex + ")").addClass(
      "bg-success text-white"
    );

    $(".accueil .news-image img.active")
      .removeClass("active")
      .fadeOut(500, () => {
        $(".accueil .news-image img:nth-child(" + currentIndex + ")")
          .addClass("active")
          .fadeIn(800);
      });

    clearTimeout(showTimeOut);
    showTimeOut = setTimeout(goNext, 4000);
  };

  active.addListener(changeCallBack);
}
