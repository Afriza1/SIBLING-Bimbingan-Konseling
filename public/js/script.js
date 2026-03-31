$(document).ready(function () {
  /*========== SIDEBAR ACTIVE/DEACTIVATE ==========*/
  var sidebarClosed = localStorage.getItem("sidebar-closed") || "false";
  setCloseSidebar(sidebarClosed);

  $("#menu").click(function (e) {
    sidebarClosed = $("#sidebar").hasClass("close") ? "false" : "true";
    $("#sidebar").toggleClass("close");
    localStorage.setItem("sidebar-closed", sidebarClosed);
    updateLogo();
  });
  function setCloseSidebar(mode) {
    $("#sidebar").toggleClass("close", mode === "true");
    updateLogo(false);
  }

  /*========== SPINNER ON BUTTON ==========*/
  $("#formLogin").submit(function () {
    let btnSubmit = $(this).find('button[type="submit"]');
    let spinner = btnSubmit.find(".spinner-border");
    $("#btnSubmit").prop("disabled", true);
    $(".text_btn").hide();
    spinner.show();
  });

  /*========== CUSTOM LOGIC COLLAPSE/ACCORDION ==========*/
  $("#data").on("show.bs.collapse", function () {
    $(this).parent().addClass("nav-item-active");
  }).on("hide.bs.collapse", function () {
    $(this).parent().removeClass("nav-item-active");
  });
  // Abaikan event collapse dari #data_master
  $("#data_master").on("show.bs.collapse hide.bs.collapse", function (e) {
    e.stopPropagation();
  });
  $("#data_operasional").on("show.bs.collapse hide.bs.collapse", function (e) {
    e.stopPropagation();
  });

  /*==================== DARK LIGHT THEME ====================*/
  var darkMode = localStorage.getItem("dark-mode") || "light";
  setTheme(darkMode);

  $("#theme-button, #theme-button2").on("click", function () {
    darkMode = $("body").hasClass("dark-theme") ? "light" : "dark";
    setTheme(darkMode);
    localStorage.setItem("dark-mode", darkMode);
    updateLogo();
  });

  function setTheme(mode) {
    $("body").toggleClass("dark-theme", mode === "dark");
    $("#dark-dropdownItem, #theme-button")
      .toggleClass("uil-moon", mode === "light")
      .toggleClass("uil-sun", mode === "dark");
    updateLogo(false);
  }

  function updateLogo(animate = true) {
    var isClosed = $("#sidebar").hasClass("close");
    var isDark = $("body").hasClass("dark-theme");
    var src = isClosed ? "img/app_logo.png" : isDark ? "img/app_logo_extend_w.png" : "img/app_logo_extend.png";
    var width = isClosed ? "40" : "135";

    if (animate) {
      $("#logo_sidebar").fadeOut(150, function () {
        $(this).attr("src", src).attr("width", width).fadeIn(150);
      });
    } else {
      $("#logo_sidebar").attr("src", src).attr("width", width);
    }
  }

  /*==================== Waktu ====================*/
  setInterval(function () {
    var momentNow = moment();
    $("#date-part").html(momentNow.format("dddd").substring(0, 20) + ", " + momentNow.format("DD MMMM YYYY"));
    $("#time-part").html(momentNow.format("hh:mm:ss A"));
  }, 100);

  /*========== SHOW/HIDE PASSWORD INPUT ==========*/
  function togglePassword(inputField, iconField, eyeContainer) {
    $(inputField).keyup(function () {
      var inputs = $(inputField).val();
      $(eyeContainer).fadeIn("slow");
      if (inputs == "") {
        $(eyeContainer).fadeOut("slow");
      } else {
        $(eyeContainer).fadeIn("slow");
      }
    });

    $(iconField).click(function () {
      $(this).toggleClass("uil-eye-slash uil-eye");
      var input = $(inputField);
      if (input.attr("type") == "password") {
        input.attr("type", "text");
        $(iconField).css("color", "var(--first-color)");
      } else {
        input.attr("type", "password");
        $(iconField).css("color", "var(--text-color)");
      }
    });
  }
  // Panggil fungsi untuk masing-masing input
  togglePassword("#passInput", "#iconShowHide", "#spanEye");
togglePassword("#password-confirm", "#iconShowHide2", "#spanEye2");

/*========== GET CURRENT YEAR ==========*/
$("#year").text(new Date().getFullYear());

/*========== NAVBAR ==========*/
// Hanya jalankan jika ada sticky element
if ($("#sticky-element").length > 0) {
  $(window).scroll(function () {
    var stickyElement = $("#sticky-element");
    var customNavbar = $(".navbar-custom");
    var stickyPosition = stickyElement.offset().top;

    if (stickyPosition <= $(window).scrollTop()) {
      customNavbar.addClass("sticky-pinned");
    } else {
      customNavbar.removeClass("sticky-pinned");
    }
  });
}
});
