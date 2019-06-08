/**
 * Controlador para cartera x edades
 * JULIAN ALVARAN 2019-05-24
 * TECHNO SOLUCIONES SAS 
 * 
 */

/**
 * Limpia los divs de la compra despues de guardar
 * @returns {undefined}
 */
function LimpiarDivs(){
    document.getElementById('DivProcess').innerHTML='';
    
    //document.getElementById('DivTotalesCompra').innerHTML='';
}

document.getElementById('BtnMuestraMenuLateral').click();


function ConfirmarCarga(){
    var EPS=$('#select2-CmbEPS-container').text();
    alertify.confirm('Está seguro que desea Cargar este archivo </strong>',
        function (e) {
            if (e) {

                alertify.success("Cargando Archivo");                    
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
        url: './procesadores/carteraxedades.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){  //SI no existe 
                $('.progress-bar').css('width','10%').attr('aria-valuenow', 10);  
                document.getElementById('LyProgresoUP').innerHTML="10%";
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
        url: './procesadores/carteraxedades.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','30%').attr('aria-valuenow', 30);  
                document.getElementById('LyProgresoUP').innerHTML="30%";
                alertify.success(respuestas[1]);
                GuardeEnTemporal();
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                BorrarTemporales();
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                BorrarTemporales();
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

function ObtengaHora(){
    var f=new Date();
    var cad=f.getHours()+":"+f.getMinutes()+":"+f.getSeconds(); 
    return (cad)
}

function GuardeEnTemporal(){
    
    var Hora = ObtengaHora();
    document.getElementById('DivMensajes').innerHTML="Iniciando Registros en la tabla temporal " + Hora;
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
        form_data.append('Accion', 7);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
        form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
      
    $.ajax({
        //async:false,
        url: './procesadores/carteraxedades.process.php',
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
                var TotalLineas=respuestas[2];
                alertify.success(respuestas[1]);
                Hora = ObtengaHora();
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1]+" "+Hora;
                InserteRegistrosNuevos();
            }else if(respuestas[0]==="E1"){
                LimpiarDivs();
                BorrarTemporales();
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
                var Hora = ObtengaHora();
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+data+" "+Hora;
                BorrarTemporales();
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            BorrarTemporales();
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
        url: './procesadores/carteraxedades.process.php',
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
        url: './procesadores/carteraxedades.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){                
                alertify.error(respuestas[1]);
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