$("#buttonLogin").click(function(){
    $("#registerBoard").hide();
    $(this).css("background-color","#0d47a1");
    $(this).css("color","#fff");

    $("#buttonRegister").css("background-color","#fff");
    $("#buttonRegister").css("color","#000");

    $("#loginBoard").show();
});
$("#buttonRegister").click(function(){
    $("#registerBoard").show();
    $(this).css("background-color","#0d47a1");
    $(this).css("color","#fff");
    
    $("#buttonLogin").css("background-color","#fff");
    $("#buttonLogin").css("color","#000");

    $("#loginBoard").hide();
});