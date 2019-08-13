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
    alertify.confirm('<strong>Está seguro que desea Cargar este archivo </strong>',
        function (e) {
            if (e) {

                alertify.success("Cargando Archivo");                    
                EnviarContrato();
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
function EnviarContrato(){
    document.getElementById("DivProcess").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    document.getElementById('BtnSubir').disabled=true;
    document.getElementById('BtnSubir').value="Subiendo...";
        
    var form_data = new FormData();
        form_data.append('Accion', 2);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('UpCartera', $('#UpCartera').prop('files')[0]);
        form_data.append('UpSoporte', $('#UpSoporte').prop('files')[0]);
    $.ajax({
        //async:false,
        url: './procesadores/cargar_contrato_liquidado.process.php',
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
                GuardeEncabezado();
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

function GuardeEncabezado(){
    
    document.getElementById('DivMensajes').innerHTML="Guardando Informacion general del contrato";
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
              
    $.ajax({
        //async:false,
        url: './procesadores/cargar_contrato_liquidado.process.php',
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
                var idContrato=respuestas[2];
                alertify.success(respuestas[1]);
                Hora = ObtengaHora();
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                InserteItemsTemporal(idContrato);
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

function InserteItemsTemporal(idContrato){
    document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Iniciando Registros de los items del contrato a la tabla temporal "+idContrato;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
       
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idContrato', idContrato);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
         
    $.ajax({
        //async:false,
        url: './procesadores/cargar_contrato_liquidado.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
              
               $('.progress-bar').css('width','70%').attr('aria-valuenow', 70);  
                document.getElementById('LyProgresoUP').innerHTML="70%";
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Iniciando carga de Items del Contrato";
                alertify.success(respuestas[1]);
                InserteItemsContrato(idContrato);
                
            }else if(respuestas[0]==="E1"){
                BorrarTemporales();
                document.getElementById("DivProcess").innerHTML='';
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                BorrarTemporales();
                document.getElementById("DivProcess").innerHTML='';
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

function InserteItemsContrato(idContrato){
    document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Iniciando Registros de los items del contrato ";
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
       
    var form_data = new FormData();
        form_data.append('Accion', 5);        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idContrato', idContrato);
         
    $.ajax({
        //async:false,
        url: './procesadores/cargar_contrato_liquidado.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
                document.getElementById("DivProcess").innerHTML='';
                $('.progress-bar').css('width','100%').attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP').innerHTML="100%";
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>Proceso Finalizado";
                alertify.success(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                
            }else if(respuestas[0]==="E1"){
                BorrarTemporales();
                document.getElementById("DivProcess").innerHTML='';
                document.getElementById('DivMensajes').innerHTML=document.getElementById('DivMensajes').innerHTML+"<br>"+respuestas[1];
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                return;                
            }else{
                BorrarTemporales();
                document.getElementById("DivProcess").innerHTML='';
                document.getElementById('DivMensajes').innerHTML=data;
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            BorrarTemporales();
            document.getElementById("DivProcess").innerHTML='';
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function BorrarTemporales(){
    
   
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
            
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('FechaCorteCartera', $('#FechaCorteCartera').val());
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
         
    $.ajax({
        //async:false,
        url: './procesadores/cargar_contrato_liquidado.process.php',
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
                document.getElementById("DivProcess").innerHTML='';
            }else if(respuestas[0]==="E1"){
                alertify.alert(respuestas[1]);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                document.getElementById("DivProcess").innerHTML='';
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnSubir').disabled=false;
                document.getElementById('BtnSubir').value="Ejecutar";
                document.getElementById("DivProcess").innerHTML='';
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('BtnSubir').disabled=false;
            document.getElementById('BtnSubir').value="Ejecutar";
            document.getElementById("DivProcess").innerHTML='';
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


$('#CmbIPS').select2();
$('#CmbEPS').select2();