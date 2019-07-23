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

function AbreModal(idModal){
    $("#"+idModal).modal();
}


function MuestraXID(id){
    
    
    document.getElementById(id).style.display="block";
    
    
}


function OcultaXID(id){
    
    
    document.getElementById(id).style.display="none";
    
    
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


/**
 * Mostrar Facturas No relacionas por la IPS
 * @returns {undefined}
 */
function MuestreFacturasNRIPS(Page=1){
    document.getElementById("DivFacturasIPS").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFacturasIPS').innerHTML=data;
            $('#CmbPageFacturasIPS').select2();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaFacturasIPS(Page=""){
    console.log("entre");
    if(Page==""){
        Page = document.getElementById('CmbPageFacturasIPS').value;
    }
    MuestreFacturasNRIPS(Page);
}


function MuestreFacturasNREPS(Page=1){
    document.getElementById("DivFacturasEPS").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFacturasEPS').innerHTML=data;
           $('#CmbPageFacturasEPS').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaFacturasEPS(Page=""){
    console.log("entre");
    if(Page==""){
        Page = document.getElementById('CmbPageFacturasEPS').value;
    }
    MuestreFacturasNREPS(Page);
}

function MuestreCruce(Page=1){
    document.getElementById("DivCruce").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivCruce').innerHTML=data;
           $('#CmbPageCruce').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaCruce(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPageCruce').value;
    }
    MuestreCruce(Page);
}

function MuestreConsolidado(Page=1){
    document.getElementById("DivTab5").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 12);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab5').innerHTML=data;
           $('#CmbPageCruce').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaConsolidado(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPageConsolidado').value;
    }
    MuestreConsolidado(Page);
}

function BuscarFactura(){
    MuestreFacturasNREPS();
    MuestreFacturasNRIPS();
    MuestreCruce();
}


function VerHistorialFactura(NumeroFactura,Accion){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);        
        form_data.append('NumeroFactura', NumeroFactura);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function VerHistoriales(NumeroFactura,Accion){
    
    document.getElementById("TabModal2").click();
    document.getElementById("DivModalHistoricos").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);        
        form_data.append('NumeroFactura', NumeroFactura);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivModalHistoricos').innerHTML=data;
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function MuestrePagadasSR(Page=1){
    document.getElementById("DivPagasSinRelacion").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivPagasSinRelacion').innerHTML=data;
           $('#CmbPagePagasSR').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaPagadasSR(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPagePagasSR').value;
    }
    MuestrePagadasSR(Page);
}



function EditarFactura(NumeroFactura){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 8);        
        form_data.append('NumeroFactura', NumeroFactura);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function EnviarFacturaEditar(){
    
    
    document.getElementById('BtnEjecutar').disabled=true;
    document.getElementById('BtnEjecutar').value="Enviando...";
    var TxtNumeroFacturaEdit=document.getElementById('TxtNumeroFacturaEdit').value;
    var TxtFacturaNueva=document.getElementById('TxtFacturaNueva').value;
    var TxtObservacionesEdicioFactura=document.getElementById('TxtObservacionesEdicioFactura').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    if($('#TxtNumeroFacturaEdit').val()==null || $('#TxtNumeroFacturaEdit').val()==''){
          alertify.alert("por favor digite la factura que va a editar");   
          document.getElementById('BtnEjecutar').disabled=false;
          document.getElementById('BtnEjecutar').value="Ejecutar";
          document.getElementById('TxtNumeroFacturaEdit').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('TxtNumeroFacturaEdit').style.backgroundColor="white";
    }
    
    if($('#TxtFacturaNueva').val()==null || $('#TxtFacturaNueva').val()==''){
          alertify.alert("por favor digite la factura que reemplazará la anterior");
          document.getElementById('BtnEjecutar').disabled=false;
          document.getElementById('BtnEjecutar').value="Ejecutar";
          document.getElementById('TxtFacturaNueva').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('TxtFacturaNueva').style.backgroundColor="white";
    }
    
    if($('#TxtObservacionesEdicioFactura').val()==null || $('#TxtObservacionesEdicioFactura').val()==''){
          alertify.alert("por favor digite las observaciones");
          document.getElementById('BtnEjecutar').disabled=false;
          document.getElementById('BtnEjecutar').value="Ejecutar";
          document.getElementById('TxtObservacionesEdicioFactura').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('TxtObservacionesEdicioFactura').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('TxtNumeroFacturaEdit', TxtNumeroFacturaEdit);
        form_data.append('TxtFacturaNueva', TxtFacturaNueva);
        form_data.append('TxtObservacionesEdicioFactura', TxtObservacionesEdicioFactura);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
       
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                CierraModal("ModalAcciones");
                document.getElementById('TabCuentas1').click();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById('BtnEjecutar').disabled=false;
                document.getElementById('BtnEjecutar').value="Ejecutar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnEjecutar').disabled=false;
                document.getElementById('BtnEjecutar').value="Ejecutar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnEjecutar').disabled=false;
            document.getElementById('BtnEjecutar').value="Ejecutar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ConfirmarCarga(){
    var UpActualizaciones=document.getElementById('UpActualizaciones').value;
    if(UpActualizaciones==""){
        alertify.alert("No se ha subido ningún archivo");
        return;
    }
    alertify.confirm('Está seguro que desea Realizar ésta actualización?',
        function (e) {
            if (e) {

                alertify.success("Subiendo Archivo");                    
                InicieCarga();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function InicieCarga(){
    var idDivOutput="DivFacturasIPS";
    var idBoton="BtnSubirActualizacionesMasivas";
    
    document.getElementById(idBoton).disabled=true;
    
    
    var UpActualizaciones=document.getElementById('UpActualizaciones').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    
    if($('#UpActualizaciones').val()==null || $('#UpActualizaciones').val()==''){
          alertify.alert("por favor seleccione un archivo");
          document.getElementById(idBoton).disabled=false;
          
          document.getElementById('UpActualizaciones').style.backgroundColor="pink";
          return;
    }else{
        document.getElementById('UpActualizaciones').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('UpActualizaciones', $('#UpActualizaciones').prop('files')[0]);
      
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','20%').attr('aria-valuenow', 20);  
                document.getElementById('LyProgresoUP').innerHTML="20%";
                var RutaArchivo=respuestas[2];
                var Extension=respuestas[3];
                alertify.success(respuestas[1]);
                LeaArchivo(RutaArchivo,Extension);
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById('TabCuentas1').click();
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                document.getElementById('TabCuentas1').click();
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('TabCuentas1').click();
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function LeaArchivo(RutaArchivo,Extension){
    var idDivOutput="DivFacturasIPS";
    var idBoton="BtnSubirActualizacionesMasivas";
    
    document.getElementById(idBoton).disabled=true;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('RutaArchivo', RutaArchivo);
        form_data.append('Extension', Extension);
      
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','50%').attr('aria-valuenow', 50);  
                document.getElementById('LyProgresoUP').innerHTML="50%";
                alertify.success(respuestas[1]);
                ValidarRegistros();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById('TabCuentas1').click();
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                document.getElementById('TabCuentas1').click();
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('TabCuentas1').click();
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ValidarRegistros(){
    var idDivOutput="DivFacturasIPS";
    var idBoton="BtnSubirActualizacionesMasivas";
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
      
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
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
                alertify.success(respuestas[1]);
                ActualizarFacturasDesdeTemporal();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('TabCuentas1').click();
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ActualizarFacturasDesdeTemporal(){
    var idDivOutput="DivFacturasIPS";
    var idBoton="BtnSubirActualizacionesMasivas";
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
      
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               $('.progress-bar').css('width','100%').attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP').innerHTML="100%";
                alertify.success(respuestas[1]);
                document.getElementById('TabCuentas1').click();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('TabCuentas1').click();
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ConciliarFactura(idBoton,NumeroFactura,TipoConciliacion){
    document.getElementById(idBoton).disabled=true; 
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('NumeroFactura', NumeroFactura);
        form_data.append('TipoConciliacion', TipoConciliacion);
      
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
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
                
                document.getElementById(idBoton).disabled=false;
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('TabCuentas1').click();
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ExportarExcel(db,Tabla,st){
    //document.getElementById("DivMensajes").innerHTML="Exportando...";
    document.getElementById("DivMensajes").innerHTML='<div id="GifProcess">Exportando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var idBoton="BtnExportarExcelCruce";
    document.getElementById(idBoton).disabled=true; 
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Opcion', 2); //Obtengo el total de filas
        
        form_data.append('Tabla', Tabla);
        form_data.append('db', db);
        form_data.append('st', st);
              
    $.ajax({
        
        url: '../../general/procesadores/GeneradorCSV.process.php',
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(idBoton).disabled=false; 
               console.log(data)
                
            document.getElementById("DivMensajes").innerHTML=data;
                
           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function VerConsolidadoFactura(NumeroFactura){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 11);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('NumeroFactura', NumeroFactura);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function ConfirmarConciliacion(){
    
    alertify.confirm('Está seguro que desea Realizar ésta Conciliación?',
        function (e) {
            if (e) {

                alertify.success("Enviando Formulario");                    
                GuardarConciliacion();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function GuardarConciliacion(){
    document.getElementById('BtnConciliar').disabled=true;
    document.getElementById('BtnConciliar').value="Enviando...";
    var TxtNumeroFactura=document.getElementById('TxtNumeroFactura').value;
    var CmbTipoConciliacion=document.getElementById('CmbTipoConciliacion').value;
    var CmbConcepto=document.getElementById('CmbConcepto').value;
    var TxtObservaciones=document.getElementById('TxtObservaciones').value;
    var ValorEPS=document.getElementById('ValorEPS').value;
    var ValorIPS=document.getElementById('ValorIPS').value;
    var FechaConciliacion=document.getElementById('FechaConciliacion').value;
    var ConciliadorIPS=document.getElementById('ConciliadorIPS').value;
    var CmbMetodoConciliacion=document.getElementById('CmbMetodoConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('TxtNumeroFactura', TxtNumeroFactura);
        form_data.append('CmbTipoConciliacion', CmbTipoConciliacion);
        form_data.append('CmbConcepto', CmbConcepto);
        form_data.append('TxtObservaciones', TxtObservaciones);
        form_data.append('ValorEPS', ValorEPS);
        form_data.append('ValorIPS', ValorIPS);
        form_data.append('FechaConciliacion', FechaConciliacion);
        form_data.append('ConciliadorIPS', ConciliadorIPS);
        form_data.append('CmbMetodoConciliacion', CmbMetodoConciliacion);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('UpSoporte', $('#UpSoporte').prop('files')[0]);
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerHistorialFactura(TxtNumeroFactura,15);
                CambiePaginaCruce();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnConciliar').disabled=false;
                document.getElementById('BtnConciliar').value="Conciliar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnConciliar').disabled=false;
                document.getElementById('BtnConciliar').value="Conciliar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnConciliar').disabled=false;
            document.getElementById('BtnConciliar').value="Conciliar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ConfirmarAnulacion(){
    
    alertify.confirm('Está seguro que desea Anular esta Conciliación?',
        function (e) {
            if (e) {

                alertify.success("Enviando Formulario");                    
                AnularConciliacion();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function AnularConciliacion(){
    document.getElementById('btnGuardarAnulacion').disabled=true;
    document.getElementById('btnGuardarAnulacion').value="Anulando...";
    var TxtIdAnulacionConciliacion=document.getElementById('TxtIdAnulacionConciliacion').value;
    var CmbTipoAnulacion=document.getElementById('CmbTipoAnulacion').value;
    var TxtObservacionesAnulacion=document.getElementById('TxtObservacionesAnulacion').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 8);
        form_data.append('TxtIdAnulacionConciliacion', TxtIdAnulacionConciliacion);
        form_data.append('CmbTipoAnulacion', CmbTipoAnulacion);
        form_data.append('TxtObservacionesAnulacion', TxtObservacionesAnulacion);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
    $.ajax({
        //async:false,
        url: './procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                var TxtNumeroFactura=respuestas[2];
                VerHistorialFactura(TxtNumeroFactura,15);
                CambiePaginaCruce();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('btnGuardarAnulacion').disabled=false;
                document.getElementById('btnGuardarAnulacion').value="ANULAR";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('btnGuardarAnulacion').disabled=false;
                document.getElementById('btnGuardarAnulacion').value="ANULAR";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('btnGuardarAnulacion').disabled=false;
            document.getElementById('btnGuardarAnulacion').value="ANULAR";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function MarqueErrorElemento(idElemento){
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}

function HabiliteValores(){
    var TipoConciliacion=document.getElementById("CmbTipoConciliacion").value;
    if(TipoConciliacion==''){
        document.getElementById('ValorEPS').disabled=true;
        document.getElementById('ValorIPS').disabled=true;
        document.getElementById('ValorEPS').value='';
        document.getElementById('ValorIPS').value='';
    }
    
    if(TipoConciliacion=='1'){
        document.getElementById('ValorEPS').disabled=false;
        document.getElementById('ValorIPS').disabled=true;
        document.getElementById('ValorEPS').value='';
        document.getElementById('ValorIPS').value='NA';
    }
    
    if(TipoConciliacion=='2'){
        document.getElementById('ValorEPS').disabled=true;
        document.getElementById('ValorIPS').disabled=false;
        document.getElementById('ValorEPS').value='NA';
        document.getElementById('ValorIPS').value='';
    }
    
}
function ExportarTablaToExcel(idTabla){
    excel = new ExcelGen({
        "src_id": idTabla,
        "src": null,
        "show_header": true,
        "type": "table|normal"
    });
    excel.generate();
}

document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
$('#CmbEPS').select2();