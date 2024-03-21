$(document).ready(function(){
    $('#sendButton').click(function(){
        $.ajax({
            url: 'ajaxDataAPI.php',
            type : 'POST',
            dataType : 'json',
            data : {
                'pagRef' : "usarAPI",
                'dni': $('#dni').val(),
                'nombre' : $('#nombre').val(),
                'correo' : $('#correo').val(),
                'token': "d372fea29cd06e069cfa869f753d8c13" 
            },
            success: function(response){
                alert(response);
                //$('#result').html(response);
            },
            error: function(xhr, status, error){
                alert(status +" "+error);
                console.error(xhr.responseText);
            }
        });
    });
});