// when page is ready, execute this
$(document).ready(function() {
    // hide loginForm and show registerForm
    $("#hideLogin").click(function() {
        $("#loginForm").hide();
        $("#registerForm").show();
    });

    // hide registerForm and show loginForm
    $("#hideRegister").click(function() {
        $("#loginForm").show();
        $("#registerForm").hide();
    });
});