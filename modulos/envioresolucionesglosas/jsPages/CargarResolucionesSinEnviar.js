/**
 * Controlador para cargar los egresos
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
                EnviarArchivo();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

/**
 * Se envia el archivo para almacenarlo
 * @returns {undefined}
 */
function EnviarArchivo(){
    document.getElementById("DivProcess").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    document.getElementById('BtnSubir').disabled=true;
    document.getElementById('BtnSubir').value="Subiendo...";
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
      
    $.ajax({
        //async:false,
        url: './procesadores/CargarResolucionesSinEnviar.process.php',
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

function ObtengaHora(){
    var f=new Date();
    var cad=f.getHours()+":"+f.getMinutes()+":"+f.getSeconds(); 
    return (cad)
}

function GuardeEnTemporal(){
    
    
    document.getElementById('DivMensajes').innerHTML="Iniciando Registros en la tabla temporal ";
    document.getElementById('BtnSubir').disabled=true;
    document.getElementById('BtnSubir').value="Subiendo...";
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        
      
    $.ajax({
        //async:false,
        url: './procesadores/CargarResolucionesSinEnviar.process.php',
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
               
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                LimpiarDivs();
               
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+data;
                
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

function InserteRegistrosNuevos(){
    document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Iniciando Registros Nuevos";
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        
         
    $.ajax({
        //async:false,
        url: './procesadores/CargarResolucionesSinEnviar.process.php',
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
                
                LimpiarDivs();
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
               
                LimpiarDivs();
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

