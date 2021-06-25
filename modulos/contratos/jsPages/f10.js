/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var boton_id='btnCrearF10';
var div_mensajes='div_mensajes_f10';
var div_general="div_listado_f10";

function mostrar_spinner(mensaje){
    var cadena = '';            

        cadena += '<div id="spinner1" class="overlay" style="position:fixed;top: 50%;left: 50%;z-index:1;text-align:center;color:blue"> ';
            cadena += '<strong>'+mensaje+'</strong><br> ';
            cadena += '<i class="fa fa-refresh fa-spin" style="font-size:60px;"></i>';                
        cadena += '</div>'; 
        var spinner = $(cadena);
        $("#div_spinner").prepend(spinner);
}

function ocultar_spinner(){
    $("#spinner1").remove();    
}

function f10_init(){
    document.getElementById('BtnMuestraMenuLateral').click();
    listarF10();
    $('#ips_id').select2();
    $('#TxtBusquedas').keypress(function(e) {
            
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            listarF10();
        }

    });
}

function SeleccioneAccionFormularios(){
    var idFormulario=document.getElementById('idFormulario').value;
    if(idFormulario==110){
        CrearContratoPercapita();
    }
    if(idFormulario==111){
        CierraModal('ModalAcciones');
    }
}

function IniciarCreacionActualizacionF10(){
    
    document.getElementById(boton_id).disabled=true;
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //hizo la tarea de copiar los contratos al f10
                
                alertify.success(respuestas[1]);
                ActualizarMarcaF10();
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                document.getElementById(boton_id).disabled=false;
               
            }else{
                
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function ActualizarMarcaF10(){
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        
        
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //hizo la tarea de copiar los contratos al f10
                
                alertify.success(respuestas[1]);
                ActualizarF10_control_cambios();
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                document.getElementById(boton_id).disabled=false;
               
            }else{
               
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function ActualizarF10_control_cambios(){
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        
        
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //hizo la tarea de copiar los contratos al f10
                
                alertify.success(respuestas[1]);
                
                inicializa_registros_actualizacion_automatica_f10();
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                document.getElementById(boton_id).disabled=false;
               
            }else{
               
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function inicializa_registros_actualizacion_automatica_f10(){
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        
        
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //hizo la tarea de copiar los contratos al f10
                
                alertify.success(respuestas[1]);
                
                actualiza_valores_automaticos_f10();
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                document.getElementById(boton_id).disabled=false;
               
            }else{
               
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function actualiza_valores_automaticos_f10(){
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //Actualizó un contrato y sigue con otro
                
                document.getElementById(div_mensajes).innerHTML=respuestas[1]; 
                console.log(respuestas[2]);
                actualiza_valores_automaticos_f10();
            }else if(respuestas[0]==="FIN"){ //Si existe debe pedir o no actualizacion
                alertify.success(respuestas[1]);
                listarF10();
                document.getElementById(boton_id).disabled=false;
               
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                document.getElementById(boton_id).disabled=false;
               
            }else{
            
            
               
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function listarF10(page=1){
    
    var ips_id=document.getElementById("ips_id").value;
    var estado=document.getElementById("estado").value;
    var TxtBusquedas=document.getElementById("TxtBusquedas").value;
    
    urlQuery='Consultas/f10.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 1);  
        form_data.append('ips_id', ips_id);
        form_data.append('estado', estado);       
        form_data.append('TxtBusquedas', TxtBusquedas); 
        form_data.append('Page', page);       
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Cargando...");
        },
        complete: function(){
           ocultar_spinner();
        },
        success: function(data){    
            
            document.getElementById(div_general).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(div_general).innerHTML=alertMensanje;
            alert("Error de Conexión");
          }
      });

}

function ver_f10(f10_id=1){
    
    urlQuery='Consultas/f10.draw.php';    
    var form_data = new FormData();
        form_data.append('Accion', 2);  
        form_data.append('f10_id', f10_id);
               
       $.ajax({// se arma un objecto por medio de ajax  
        url: urlQuery,// se indica donde llegara la informacion del objecto
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        beforeSend: function() { //lo que hará la pagina antes de ejecutar el proceso
            mostrar_spinner("Cargando...");
        },
        complete: function(){
           ocultar_spinner();
        },
        success: function(data){    
            
            document.getElementById(div_general).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            toogle_box_init();
            $('.ts-select2').select2();
            add_events_dropzone_f10();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            var alertMensanje='<div class="alert alert-danger mt-3"><h4 class="alert-heading">Error!</h4><p>Parece que no hay conexión con el servidor.</p><hr><p class="mb-0">Intentalo de nuevo.</p></div>';
            document.getElementById(div_general).innerHTML=alertMensanje;
            alert("Error de Conexión");
          }
      });

}

function toogle_box_init(){
    $('.ts-collapse').boxWidget();
}

function toogle_box(){
    $('.ts-collapse').boxWidget('toggle');
}

function editar_campo_f10(f10_id,campo_id,caja_id=''){
    if(caja_id==''){
        var valor_campo=document.getElementById(campo_id).value;
    }else{
        var valor_campo=document.getElementById(caja_id).value;
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('campo_id', campo_id);
        form_data.append('f10_id', f10_id);
        form_data.append('valor_campo', valor_campo);
        $.ajax({
        url: './procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           
           if(respuestas[0]==="OK"){  //Actualizó un contrato y sigue con otro
                
                alertify.success(respuestas[1]);
                valor_anterior=valor_campo;
                
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                alertify.error(respuestas[1]);
                $('#'+campo_id).val(respuestas[3]);              
            }else{
            
            
               
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(boton_id).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function add_events_dropzone_f10(){
    Dropzone.autoDiscover = false;
           
    urlQuery='procesadores/f10.process.php';
    var f10_id=$("#f10_adjuntos").data("f10_id");
    
    var myDropzone = new Dropzone("#f10_adjuntos", { url: urlQuery,paramName: "adjunto_f10"});
        myDropzone.on("sending", function(file, xhr, formData) { 

            formData.append("Accion", 7);
            formData.append("f10_id", f10_id);
            
            
        });

        myDropzone.on("addedfile", function(file) {
            file.previewElement.addEventListener("click", function() {
                myDropzone.removeFile(file);
            });
        });

        myDropzone.on("success", function(file, data) {

            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                listar_adjuntos_f10(f10_id);
            }else if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
            }else{
                alert(data);
            }

        });
    listar_adjuntos_f10(f10_id);
}


 
 function listar_adjuntos_f10(f10_id=''){
    var idDiv="div_adjuntos_f10";
     
    var form_data = new FormData();
        form_data.append('Accion', 3);// pasamos la accion y el numero de accion para el dibujante sepa que caso tomar
        
        
        form_data.append('f10_id', f10_id);
                        
       $.ajax({// se arma un objecto por medio de ajax  
        url: 'Consultas/f10.draw.php',
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post', // se especifica que metodo de envio se utilizara normalmente y por seguridad se utiliza el post
        success: function(data){            
            document.getElementById(idDiv).innerHTML=data; //La respuesta del servidor la dibujo en el div DivTablasBaseDatos                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {// si hay error se ejecuta la funcion
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function EliminarItemf10(tabla_id,item_id,f10_id){    
    
    var form_data = new FormData();
        
        form_data.append('Accion', 8);
        
        form_data.append('item_id', item_id);  
        form_data.append('tabla_id', tabla_id);
                        
        $.ajax({
        url: 'procesadores/f10.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                if(tabla_id==1){
                    listar_adjuntos_f10(f10_id);
                }
                
                
            }else if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);
                
                MarqueErrorElemento(respuestas[2]);
                
            }else{
                alertify.alert(data);
            }
               
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  


f10_init();

