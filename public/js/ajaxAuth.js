/* metodo para enviar formulario de registro via ajax */
$('#registerForm').submit(function(event){
    /*Impede o envio do formulario por métodos tradicionais*/
    event.preventDefault();
    /*verifica se o html5 valido o formulario*/
    if(document.getElementById("registerForm").checkValidity()) {
        /*Obtem o elemento html de exibição de erros */
        var registerError = $("#registerError");
        /*Se estiver habilitado esconde ele */
        registerError.hide();
        /*serializa todas as informações do formulario */
        var registerDate = $("#registerForm").serialize();
        var url = location.hostname;
        /*Metodo ajax envia o formulario de forma assicrona para a url */
        $.ajax({
            type:"POST",
            url:"../ajax",
            data:registerDate,
            beforeSend:function(){
                $("#registerSubmit").prop("disabled",true);
            },
            success:function(data){
                data = JSON.parse(data);
                if(data['result'] == "error"){
                    registerError.html(data['errors']);
                    registerError.show();
                    $("#registerSubmit").prop("disabled",false);
                }else{
                    /* Muda da tela de registro para tela de login com uma mensagem de sucesso */
                    $("#registerSubmit").prop("disabled",false);

                    $("#registerBoard").hide();
                    $("#registerForm input:not([type='submit'],[name='method'])").val("");
                    $("#buttonLogin").css("background-color","#0d47a1");
                    $("#buttonLogin").css("color","#fff");
                
                    $("#buttonRegister").css("background-color","#fff");
                    $("#buttonRegister").css("color","#000");
                
                    $("#loginBoard").show();
                    $("#alertRegisterSuccess").show();
                }
            }
        });
    }
});

/* metodo para enviar formulario de registro via ajax */
$('#loginForm').submit(function(event){
    /*Impede o envio do formulario por métodos tradicionais*/
    event.preventDefault();
    /*verifica se o html5 valido o formulario*/
    if(document.getElementById("loginForm").checkValidity()) {
        /*Obtem o elemento html de exibição de erros */
        var registerSuccess = $("#alertRegisterSuccess");
        var loginError = $("#alertLoginError");
        /*Se estiver habilitado esconde ele */
        loginError.hide();
        registerSuccess.hide();
        /*serializa todas as informações do formulario */
        var loginDate = $("#loginForm").serialize();
        var url = location.hostname;

        /*Metodo ajax envia o formulario de forma assicrona para a url */
        $.ajax({
            type:"POST",
            url:"../ajax",
            data:loginDate,
            beforeSend:function(){
                $("#loginSubmit").prop("disabled",true);
            },
            success:function(data){
                data = JSON.parse(data);
                $("#loginSubmit").prop("disabled",false);
                if(data['result'] == "error"){
                    loginError.html(data['errors']);
                    loginError.show();
                }else{
                    location.href = "../";
                }
            }
        });
    }
});
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