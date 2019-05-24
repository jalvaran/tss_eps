/**
 * Controlador para cartera
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

/**
 * Cierra una ventana modal
 * @param {type} idModal
 * @returns {undefined}
 */
function CierraModal(idModal) {
    $("#"+idModal).modal('hide');//ocultamos el modal
    $('body').removeClass('modal-open');//eliminamos la clase del body para poder hacer scroll
    $('.modal-backdrop').remove();//eliminamos el backdrop del modal
}


/**
 * Muestra u oculta un elemento por su id
 * @param {type} id
 * @returns {undefined}
 */

function MuestraOcultaXID(id){
    
    var estado=document.getElementById(id).style.display;
    if(estado=="none" | estado==""){
        document.getElementById(id).style.display="block";
    }
    if(estado=="block"){
        document.getElementById(id).style.display="none";
    }
    
}

/**
 * Limpia los divs de la compra despues de guardar
 * @returns {undefined}
 */
function LimpiarDivs(){
    document.getElementById('DivProcess').innerHTML='';
    
    //document.getElementById('DivTotalesCompra').innerHTML='';
}

/*
$('#CmbBusquedas').bind('change', function() {
    
    document.getElementById('CodigoBarras').value = document.getElementById('CmbBusquedas').value;
    BusquePrecioVentaCosto();
    
});

*/
document.getElementById('BtnMuestraMenuLateral').click();

function getInfoForm(){
          
    var form_data = new FormData();
    form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
    form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
    
    
    return form_data;
}

function ConfirmarCarga(){
    var EPS=$('#select2-CmbEPS-container').text();
    alertify.confirm('Está seguro que desea Cargar la Cartera de la EPS:<br> <strong>'+EPS+'</strong>',
        function (e) {
            if (e) {

                alertify.success("Actualizar Archivo");                    
                VerifiqueFechaCargue();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

/**
 * Verifica Si ya fue cargado el archivo a subir
 * @returns {undefined}
 */
function VerifiqueFechaCargue(){
    
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value; 
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('FechaCorteCartera', FechaCorteCartera);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
        $.ajax({
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){  //SI no existe 
                $('.progress-bar').css('width','20%').attr('aria-valuenow', 20);  
                document.getElementById('LyProgresoUP').innerHTML="20%";
                alertify.success(respuestas[1]);
                EnviarCartera();
            }else if(respuestas[0]==="E1"){ //Si existe debe pedir o no actualizacion
                LimpiarDivs();
                alertify.confirm(respuestas[1],
                        function (e) {
                            if (e) {

                                alertify.success("Actualizar Archivo");                    
                                EnviarCartera();
                            }else{
                                alertify.error("Se canceló el proceso");
                                
                                return;
                            }
                        });
                document.getElementById('BtnSubir').disabled=false;
                return;      
                
            }else{
                LimpiarDivs();
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

/**
 * Se envia el archivo para almacenarlo
 * @returns {undefined}
 */
function EnviarCartera(){
    document.getElementById("DivProcess").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    document.getElementById('BtnSubir').disabled=true;
    document.getElementById('BtnSubir').value="Subiendo...";
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var UpCartera=document.getElementById('UpCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
    
    if($('#UpCartera').val()==null || $('#UpCartera').val()==''){
          alertify.alert("por favor seleccione un archivo");
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('UpCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('UpCartera').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
      
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','40%').attr('aria-valuenow', 40);  
                document.getElementById('LyProgresoUP').innerHTML="40%";
                alertify.success(respuestas[1]);
                CalcularTotalLineasArchivo();
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                alertify.alert(data);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function CalcularTotalLineasArchivo(){
    document.getElementById('DivMensajes').innerHTML="Obteniendo Numero de Registros en el archivo";
    document.getElementById('BtnSubir').disabled=true;
    document.getElementById('BtnSubir').value="Subiendo...";
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var UpCartera=document.getElementById('UpCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var Separador=document.getElementById('CmbSeparador').value;
    
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
    
    if($('#UpCartera').val()==null || $('#UpCartera').val()==''){
          alertify.alert("por favor seleccione un archivo");
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('UpCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('UpCartera').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('Separador', Separador);
        form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
      
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','40%').attr('aria-valuenow', 40);  
                document.getElementById('LyProgresoUP').innerHTML="40%";
                var TotalLineas=respuestas[2];
                alertify.success(respuestas[1]);
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                EnviarArchivoATemporal(TotalLineas);
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                document.getElementById('DivMensajes').innerHTML=data;
                //alertify.alert(data);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function EnviarArchivoATemporal(TotalLineas,LineaActual=0){
    
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var Separador=document.getElementById('CmbSeparador').value;
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
        
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('Separador', Separador);
        form_data.append('LineaActual', LineaActual);
         
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                $('.progress-bar').css('width','60%').attr('aria-valuenow', 60);  
                document.getElementById('LyProgresoUP').innerHTML="60%";
                
                LineaActual=respuestas[2];
                
                if(LineaActual>=TotalLineas){
                    document.getElementById('DivMensajes').innerHTML="Carga a Temporal Terminada";
                    CopiarAlHistorialCargas();
                    
                }else{
                    document.getElementById('DivMensajes').innerHTML=LineaActual+" Lineas Cargandas de un total de: "+TotalLineas;
                    EnviarArchivoATemporal(TotalLineas,LineaActual);
                }
                
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                alertify.alert(respuestas[1]);
                BorrarTemporales();
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                BorrarTemporales();
                document.getElementById('DivMensajes').innerHTML=data;
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function CopiarAlHistorialCargas(){
    
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
        
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
         
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){  
               
               $('.progress-bar').css('width','80%').attr('aria-valuenow', 80);  
                document.getElementById('LyProgresoUP').innerHTML="80%";
                alertify.success(respuestas[1]);
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                InserteRegistrosNuevos();
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                //alertify.alert(respuestas[1]);
                document.getElementById('DivMensajes').innerHTML=respuestas[1];
                BorrarTemporales();
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                BorrarTemporales();
                document.getElementById('DivMensajes').innerHTML=data;
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            BorrarTemporales();
            LimpiarDivs();
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function InserteRegistrosNuevos(){
    document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Iniciando Registros en la Cartera de la EPS";
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
        
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
         
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
               LimpiarDivs();
               $('.progress-bar').css('width','100%').attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP').innerHTML="100%";
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Proceso Terminado";
                alertify.success(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                //InserteRegistrosNuevos();
            }else if(respuestas[0]==="E1"){
                BorrarTemporales();
                LimpiarDivs();
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                BorrarTemporales();
                LimpiarDivs();
                document.getElementById('DivMensajes').innerHTML=data;
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            BorrarTemporales();
            LimpiarDivs();
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function BorrarTemporales(){
    
    var FechaCorteCartera=document.getElementById('FechaCorteCartera').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    if($('#FechaCorteCartera').val()==null || $('#FechaCorteCartera').val()==''){
          alertify.alert("por favor seleccione una fecha");   
          document.getElementById('BtnSubir').disabled=false;
          document.getElementById('BtnSubir').value="Ejecutar";
          document.getElementById('FechaCorteCartera').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('FechaCorteCartera').style.backgroundColor="white";
    }
        
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
         
    $.ajax({
        //async:false,
        url: './procesadores/carteraeps.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){                
                alertify.success(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                //InserteRegistrosNuevos();
            }else if(respuestas[0]==="E1"){
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


$('#CmbIPS').select2();
$('#CmbEPS').select2();