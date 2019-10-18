/**
 * Controlador para cartera
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */
var w;

function startWorkerTotalesCruce(CmbIPS) {
  if (typeof(Worker) !== "undefined") {
    if (typeof(w) == "undefined") {
      document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Calculando Totales...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';  
      w = new Worker("jsPages/worker_validacion.js");
      w.postMessage(CmbIPS);
    }
    w.onmessage = function(event) {
      document.getElementById("DivTotalesCruce").innerHTML = event.data;
    };
  } else {
    document.getElementById("DivTotalesCruce").innerHTML = "Sorry! No Web Worker support.";
  }
}


function ObtenerHora(){
    var f=new Date();
    var HoraActual=f.getHours()+":"+f.getMinutes()+":"+f.getSeconds();
    return(HoraActual);
} 

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
    
    var CmbTipoNegociacion=document.getElementById('CmbTipoNegociacion').value;
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        form_data.append('CmbTipoNegociacion', CmbTipoNegociacion);
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
           $('.selector').select2({
		
                placeholder: 'Asociar a un contrato existente',
                ajax: {
                  url: 'buscadores/contratos.search.php?nit='+CmbIPS,
                  dataType: 'json',
                  delay: 250,
                                    
                  processResults: function (data) {
                      
                    return {                     
                      results: data
                    };
                  },
                 cache: true
                }
              });
           
           //DibujaTotalesCruce();
           //startWorkerTotalesCruce(CmbIPS);
            
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
        form_data.append('Opcion', 2); 
        
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

function ExportarHojaDeTrabajo(db,Tabla,st){
    
    document.getElementById("DivMensajes").innerHTML='<div id="GifProcess">Exportando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var idBoton="BtnExportarExcelCruce";
    document.getElementById(idBoton).disabled=true; 
    var TxtCondicional=document.getElementById('TxtCondicional').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var CmbTipoNegociacion=document.getElementById('CmbTipoNegociacion').value;
    
    
    var form_data = new FormData();
        form_data.append('Opcion', 7);
        
        form_data.append('Tabla', Tabla);
        form_data.append('db', db);
        form_data.append('TxtCondicional', TxtCondicional);
        form_data.append('CmbTipoNegociacion', CmbTipoNegociacion);
              
    $.ajax({
        
        url: '../../general/procesadores/GeneradorCSV.process.php',
        
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(idBoton).disabled=false; 
               //console.log(data)
                
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
    var CmbConceptoAGS=document.getElementById('CmbConceptoAGS').value;
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
        form_data.append('CmbConceptoAGS', CmbConceptoAGS);
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
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
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


function AbreOpcionesMasivas(){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 18);        
        
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

function ConfirmarConciliacionesMasivas(){
    
    alertify.confirm('Está seguro que desea realizar esta Conciliación Masiva?',
        function (e) {
            if (e) {

                alertify.success("Enviando Formulario");                    
                EnviarConciliacionMasiva();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function EnviarConciliacionMasiva(){
    document.getElementById("DivProcessConciliacionMasiva").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    document.getElementById('btnGuardarConciliacionesMasivas').disabled=true;
    document.getElementById('btnGuardarConciliacionesMasivas').value="Procesando...";
    var FechaConciliacionMasiva=document.getElementById('FechaConciliacionMasiva').value;
    var ConciliadorIPSMasivo=document.getElementById('ConciliadorIPSMasivo').value;
    var CmbMetodoConciliacionMasivo=document.getElementById('CmbMetodoConciliacionMasivo').value;
    var CmbConceptoConciliacion=document.getElementById('CmbConceptoConciliacion').value;
    var CmbConceptoConciliacionAGS=document.getElementById('CmbConceptoConciliacionAGS').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 9);
        form_data.append('FechaConciliacionMasiva', FechaConciliacionMasiva);
        form_data.append('ConciliadorIPSMasivo', ConciliadorIPSMasivo);
        form_data.append('CmbMetodoConciliacionMasivo', CmbMetodoConciliacionMasivo);
        form_data.append('CmbConceptoConciliacion', CmbConceptoConciliacion);
        form_data.append('UpConciliacionMasiva', $('#UpConciliacionMasiva').prop('files')[0]);
        form_data.append('UpSoporteConciliacionMasiva', $('#UpSoporteConciliacionMasiva').prop('files')[0]);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('CmbConceptoConciliacionAGS', CmbConceptoConciliacionAGS);
        
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
                
                LeerArchivoConciliacionesMasivas();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
            document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
            document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function LeerArchivoConciliacionesMasivas(){
    
    var FechaConciliacionMasiva=document.getElementById('FechaConciliacionMasiva').value;
    var ConciliadorIPSMasivo=document.getElementById('ConciliadorIPSMasivo').value;
    var CmbMetodoConciliacionMasivo=document.getElementById('CmbMetodoConciliacionMasivo').value;
    var CmbConceptoConciliacion=document.getElementById('CmbConceptoConciliacion').value;
    var CmbConceptoConciliacionAGS=document.getElementById('CmbConceptoConciliacionAGS').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 10);
        form_data.append('FechaConciliacionMasiva', FechaConciliacionMasiva);
        form_data.append('ConciliadorIPSMasivo', ConciliadorIPSMasivo);
        form_data.append('CmbMetodoConciliacionMasivo', CmbMetodoConciliacionMasivo);
        form_data.append('CmbConceptoConciliacion', CmbConceptoConciliacion);
        form_data.append('CmbConceptoConciliacionAGS', CmbConceptoConciliacionAGS);
        form_data.append('UpConciliacionMasiva', $('#UpConciliacionMasiva').prop('files')[0]);
        form_data.append('UpSoporteConciliacionMasiva', $('#UpSoporteConciliacionMasiva').prop('files')[0]);
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
                
                ActualiceConciliacionTemporalMasiva();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
            document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
            document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ActualiceConciliacionTemporalMasiva(){
    
    var FechaConciliacionMasiva=document.getElementById('FechaConciliacionMasiva').value;
    var ConciliadorIPSMasivo=document.getElementById('ConciliadorIPSMasivo').value;
    var CmbMetodoConciliacionMasivo=document.getElementById('CmbMetodoConciliacionMasivo').value;
    var CmbConceptoConciliacion=document.getElementById('CmbConceptoConciliacion').value;
    var CmbConceptoConciliacionAGS=document.getElementById('CmbConceptoConciliacionAGS').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 11);
        form_data.append('FechaConciliacionMasiva', FechaConciliacionMasiva);
        form_data.append('ConciliadorIPSMasivo', ConciliadorIPSMasivo);
        form_data.append('CmbMetodoConciliacionMasivo', CmbMetodoConciliacionMasivo);
        form_data.append('CmbConceptoConciliacion', CmbConceptoConciliacion);
        form_data.append('CmbConceptoConciliacionAGS', CmbConceptoConciliacionAGS);
        form_data.append('UpConciliacionMasiva', $('#UpConciliacionMasiva').prop('files')[0]);
        form_data.append('UpSoporteConciliacionMasiva', $('#UpSoporteConciliacionMasiva').prop('files')[0]);
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
                
                InserteConciliacionesMasivas();
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
            document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
            document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function InserteConciliacionesMasivas(){
    
    var FechaConciliacionMasiva=document.getElementById('FechaConciliacionMasiva').value;
    var ConciliadorIPSMasivo=document.getElementById('ConciliadorIPSMasivo').value;
    var CmbMetodoConciliacionMasivo=document.getElementById('CmbMetodoConciliacionMasivo').value;
    var CmbConceptoConciliacion=document.getElementById('CmbConceptoConciliacion').value;
    var CmbConceptoConciliacionAGS=document.getElementById('CmbConceptoConciliacionAGS').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 12);
        form_data.append('FechaConciliacionMasiva', FechaConciliacionMasiva);
        form_data.append('ConciliadorIPSMasivo', ConciliadorIPSMasivo);
        form_data.append('CmbMetodoConciliacionMasivo', CmbMetodoConciliacionMasivo);
        form_data.append('CmbConceptoConciliacion', CmbConceptoConciliacion);
        form_data.append('CmbConceptoConciliacionAGS', CmbConceptoConciliacionAGS);
        form_data.append('UpConciliacionMasiva', $('#UpConciliacionMasiva').prop('files')[0]);
        form_data.append('UpSoporteConciliacionMasiva', $('#UpSoporteConciliacionMasiva').prop('files')[0]);
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
                CambiePaginaCruce();
                document.getElementById("DivProcessConciliacionMasiva").innerHTML='';
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
                document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
                document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('btnGuardarConciliacionesMasivas').disabled=false;
            document.getElementById('btnGuardarConciliacionesMasivas').value="Ejecutar";
            document.getElementById("DivProcessConciliacionMasiva").innerHTML="";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function FacturasAFavor(Page=1){
    document.getElementById("DivTab6").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 19);
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
            
           document.getElementById('DivTab6').innerHTML=data;
           $('#CmbPageRetencionesSR').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaRetencionesSR(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPageRetencionesSR').value;
    }
    FacturasAFavor(Page);
}


function MuestreConciliaciones(Page=1){
    document.getElementById("DivCruce").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 20);
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
            
           document.getElementById('DivTab7').innerHTML=data;
           $('#CmbPageCruce').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePaginaConciliaciones(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPageConciliaciones').value;
    }
    MuestreConciliaciones(Page);
}


function ConfirmarCopiaFacturasSFNR(){
    
    alertify.confirm('Está seguro que desea Copiar las facturas de la vigencia seleccionada?',
        function (e) {
            if (e) {

                alertify.success("Enviando Solicitud");                    
                CopiaFacturasSFNR();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function CopiaFacturasSFNR(){
    document.getElementById("DivProcesoCopia").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    document.getElementById('BtnCopiarAlCruce').disabled=true;
    document.getElementById('BtnCopiarAlCruce').value="Copiando...";
    var VigenciaInicial=document.getElementById('VigenciaInicialFSF').value;
    var VigenciaFinal=document.getElementById('VigenciaFinalFSF').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 13);
        form_data.append('VigenciaInicial', VigenciaInicial);
        form_data.append('VigenciaFinal', VigenciaFinal);
        
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
               document.getElementById("DivProcesoCopia").innerHTML="";
                document.getElementById('TabCuentas6').click();
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                 document.getElementById("DivProcesoCopia").innerHTML="";
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnCopiarAlCruce').disabled=false;
                document.getElementById('BtnCopiarAlCruce').value="Copiar al Cruce";
                return;                
            }else{
                 document.getElementById("DivProcesoCopia").innerHTML="";
                alertify.alert(data);
                document.getElementById('BtnCopiarAlCruce').disabled=false;
                document.getElementById('BtnCopiarAlCruce').value="Copiar al Cruce";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
             document.getElementById("DivProcesoCopia").innerHTML="";
            document.getElementById('BtnCopiarAlCruce').disabled=false;
            document.getElementById('BtnCopiarAlCruce').value="Copiar al Cruce";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function ActasConciliaciones(){
    
    MuestraXID('DivOpcionesActasConciliacion');
    DibujeSelectorActas();
}


function AbreModalNuevaActaConciliacion(){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 25);        
        
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

function CargarActaConciliacion(){
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 20);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
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
            
           document.getElementById('DivTab7').innerHTML=data;
           $('#CmbPageCruce').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function FacturasSinRelacionarPorIPS(Page=1){
    document.getElementById("DivFacturasEPS").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 26);
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
           //$('#CmbPageRetencionesSR').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function ConfirmarCrearActa(){
    
    alertify.confirm('Está seguro que desea Crear una Nueva Acta de Conciliación?',
        function (e) {
            if (e) {
                
                CrearActaConciliacion();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function CrearActaConciliacion(){
    document.getElementById('BtnGuardarActa').disabled=true;
    document.getElementById('BtnGuardarActa').value="Creando...";
    var FechaActaConciliacion=document.getElementById('FechaActaConciliacion').value;
    var FechaActaInicial=document.getElementById('FechaActaInicial').value;
    var TxtRepresentanteLegalIPS=document.getElementById('TxtRepresentanteLegalIPS').value;
    var TxtEncargadoEPS=document.getElementById('TxtEncargadoEPS').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 14);
        form_data.append('FechaActaConciliacion', FechaActaConciliacion);
        form_data.append('FechaActaInicial', FechaActaInicial);
        form_data.append('TxtRepresentanteLegalIPS', TxtRepresentanteLegalIPS);
        form_data.append('TxtEncargadoEPS', TxtEncargadoEPS);
        
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
                
                alertify.success(respuestas[1]);
                document.getElementById('BtnGuardarActa').disabled=false;
                document.getElementById('BtnGuardarActa').value="Crear Acta";
                CierraModal("ModalAcciones");
                DibujeSelectorActas();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnGuardarActa').disabled=false;
                document.getElementById('BtnGuardarActa').value="Crear Acta";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnGuardarActa').disabled=false;
                document.getElementById('BtnGuardarActa').value="Crear Acta";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnGuardarActa').disabled=false;
            document.getElementById('BtnGuardarActa').value="Crear Acta";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function DibujeSelectorActas(){
    
   
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 27);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivSelectActas').innerHTML=data;
           //$('#idActaConciliacion').select2();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function MostrarActa(DibujeAreaContratos=1){
    var DivDraw='DivTab8';
    if(DibujeAreaContratos==0){
        DivDraw='DivDrawActaConciliacion';
    }
    document.getElementById(DivDraw).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 28);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('DibujeAreaContratos', DibujeAreaContratos);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(DivDraw).innerHTML=data;
           $('#CmbFirmaUsual').select2();
           setTextareaHeight($('textarea'));
           DibujeFirmasActaConciliacion();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function AgregueCompromiso(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var TxtCompromisoNuevo=document.getElementById('TxtCompromisoNuevo').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 15);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('TxtCompromisoNuevo', TxtCompromisoNuevo);  
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
                
                alertify.success(respuestas[1]);                
                DibujeCompromisosActaConciliacion(idActaConciliacion);
                document.getElementById('TxtCompromisoNuevo').value='';
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function EditeCompromiso(idCompromiso){
    var idCajaCompromiso = "TxtCompromiso_"+idCompromiso;
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var TxtCompromisoEditado = document.getElementById(idCajaCompromiso).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 16);
        form_data.append('idCompromiso', idCompromiso);
        form_data.append('TxtCompromisoEditado', TxtCompromisoEditado);  
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
                
                alertify.success(respuestas[1]);                
                DibujeCompromisosActaConciliacion(idActaConciliacion);
                document.getElementById('TxtCompromisoNuevo').value='';
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function DibujeCompromisosActaConciliacion(idActaCompromiso){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 29);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            document.getElementById('DivCompromisosActaConciliacion').innerHTML=data;
            setTextareaHeight($('textarea'));
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function setTextareaHeight(textareas) {
    textareas.each(function () {
        var textarea = $(this);
 
        if ( !textarea.hasClass('autoHeightDone') ) {
            textarea.addClass('autoHeightDone');
 
            var extraHeight = parseInt(textarea.css('padding-top')) + parseInt(textarea.css('padding-bottom')), // to set total height - padding size
                h = textarea[0].scrollHeight - extraHeight;
 
            // init height
            textarea.height('auto').height(h);
 
            textarea.bind('keyup', function() {
 
                textarea.removeAttr('style'); // no funciona el height auto
 
                h = textarea.get(0).scrollHeight - extraHeight;
 
                textarea.height(h+'px'); // set new height
            });
        }
    })
}


function EditeActaConciliacion(idActaConciliacion,idCampoTexto,CampoAEditar){
    
    var NuevoValor = document.getElementById(idCampoTexto).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 17);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('NuevoValor', NuevoValor); 
        form_data.append('idCampoTexto', idCampoTexto); 
        form_data.append('CampoAEditar', CampoAEditar); 
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
                
                alertify.success(respuestas[1]);                
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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


function CalcularDiferenciasActaConciliacion(){
    var HoraActual=ObtenerHora();
    var DivMensajes = document.getElementById('DivMensajesActaConciliacion');
    DivMensajes.innerHTML="<strong>("+HoraActual+") Iniciando los calculos para obtener las diferencias...</strong>";
    DivMensajes.innerHTML=DivMensajes.innerHTML+'<br><div id="GifProcess"><img src="../../images/process.gif" alt="Cargando" height="100" width="100"></div>';
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var ValorSegunEPS=document.getElementById('ACValorSegunEPS').value;
    var ValorSegunIPS=document.getElementById('ACValorSegunIPS').value;
    var Diferencia=document.getElementById('ACDiferencia').value;
    var form_data = new FormData();
        form_data.append('Accion', 18);
        form_data.append('idActaConciliacion', idActaConciliacion);        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('ValorSegunEPS', ValorSegunEPS);
        form_data.append('ValorSegunIPS', ValorSegunIPS);
        form_data.append('Diferencia', Diferencia);
        
        
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
                var HoraActual=ObtenerHora();
                DivMensajes.innerHTML=DivMensajes.innerHTML+"<br><strong>("+HoraActual+") 1 de 2 procesos terminado...</strong>";
                var DetalleDiferencias= JSON.parse(respuestas[2]);                
                
                //EscribaDiferenciasActas(DetalleDiferencias);
                //CalcularDiferenciasActaConciliacionProceso2(respuestas[2]);
                
                EscribaDiferenciasActas(DetalleDiferencias);
                EscribaValoresEnSpanDiferenciasActas();
                GuardarDiferenciasActaConciliacion();
                alertify.success(respuestas[1]);                
                document.getElementById('GifProcess').innerHTML='';
                
                //alertify.success(respuestas[1]);                
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function CalcularDiferenciasActaConciliacionProceso2(DetalleDiferencias){
    var HoraActual=ObtenerHora();
    var DivMensajes = document.getElementById('DivMensajesActaConciliacion');
    DivMensajes.innerHTML=DivMensajes.innerHTML+"<br><strong>("+HoraActual+") Iniciando el segundo proceso de calculos para obtener las diferencias...</strong>";
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var ValorSegunEPS=document.getElementById('ACValorSegunEPS').value;
    var ValorSegunIPS=document.getElementById('ACValorSegunIPS').value;
    var Diferencia=document.getElementById('ACDiferencia').value;
    var form_data = new FormData();
        form_data.append('Accion', 19);
        form_data.append('idActaConciliacion', idActaConciliacion);        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('ValorSegunEPS', ValorSegunEPS);
        form_data.append('ValorSegunIPS', ValorSegunIPS);
        form_data.append('Diferencia', Diferencia);
        form_data.append('DetalleDiferencias', DetalleDiferencias);
        
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
                var HoraActual=ObtenerHora();
                DivMensajes.innerHTML=DivMensajes.innerHTML+"<br><strong>("+HoraActual+") 2 de 2 procesos terminados...</strong>";
                var DetalleDiferencias= JSON.parse(respuestas[2]);                
                //console.log(DetalleDiferencias);
                EscribaDiferenciasActas(DetalleDiferencias);
                EscribaValoresEnSpanDiferenciasActas();
                GuardarDiferenciasActaConciliacion();
                alertify.success(respuestas[1]);                
                document.getElementById('GifProcess').innerHTML='';
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function EscribaDiferenciasActas(DetalleDiferencias){
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    document.getElementById('TxtACDiferenciaXPagos').value=DetalleDiferencias.DiferenciaXPagos;    
    document.getElementById('TxtACFacturasIPSNoRelacionadasEPS').value=DetalleDiferencias.FacturasIPSNoRelacionadasEPS;   
    document.getElementById('TxtACGlosasPendientesXConciliar').value=DetalleDiferencias.GlosasPendientesXConciliar;     
    document.getElementById('TxtACFacturasDevueltas').value=DetalleDiferencias.FacturasDevueltas;
    document.getElementById('TxtACDiferenciaXImpuestos').value=DetalleDiferencias.DiferenciaXImpuestos;  
    document.getElementById('TxtACDescuentoXRetefuente').value=DetalleDiferencias.DescuentoXRetefuente;     
    document.getElementById('TxtACFacturasNoRelacionadasXIPS').value=DetalleDiferencias.FacturasNoRelacionadasXIPS;     
    document.getElementById('TxtACRetencionesImpuestosNoProcedentes').value=DetalleDiferencias.RetencionesImpuestosNoProcedentes;    
    document.getElementById('TxtACAjustesDeCartera').value=DetalleDiferencias.AjustesDeCartera;   
    document.getElementById('TxtACDiferenciaXValorFacturado').value=DetalleDiferencias.DiferenciaXValorFacturado;     
    document.getElementById('TxtACDiferenciaXUPC').value=DetalleDiferencias.DiferenciaXUPC;    
    document.getElementById('TxtACGlosasPendientesXDescargarIPS').value=DetalleDiferencias.GlosasPendientesXDescargarIPS;    
    document.getElementById('TxtACAnticiposPendientesXCruzar').value=DetalleDiferencias.AnticiposPendientesXCruzar;    
    document.getElementById('TxtACDescuentosLMA').value=DetalleDiferencias.DescuentosLMA;  
    document.getElementById('TxtACPendientesAuditoria').value=DetalleDiferencias.PendientesAuditoria;
    document.getElementById('TxtACTotalDiferencias').value=DetalleDiferencias.TotalDiferencias;
    if(DetalleDiferencias.ValorSegunEPS){
        document.getElementById('ACValorSegunEPS').value=DetalleDiferencias.ValorSegunEPS;
    }
    
    if(DetalleDiferencias.ValorSegunIPS){
        document.getElementById('ACValorSegunIPS').value=DetalleDiferencias.ValorSegunIPS;
    }
    
    if(DetalleDiferencias.Diferencia){
        document.getElementById('ACDiferencia').value=DetalleDiferencias.Diferencia;
    }
    
    if(DetalleDiferencias.SaldoAcuerdoPago){
        document.getElementById('TxtACSaldoAcuerdoPago').value=DetalleDiferencias.SaldoAcuerdoPago;
    }
    
    
    
}

function EscribaValoresEnSpanDiferenciasActas(){
    
    var Valor=document.getElementById('TxtACDiferenciaXPagos').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDiferenciaXPagos').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACFacturasIPSNoRelacionadasEPS').value;
    var Numero = number_format(Valor);
    document.getElementById('spACFacturasIPSNoRelacionadasEPS').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACGlosasPendientesXConciliar').value;
    var Numero = number_format(Valor);
    document.getElementById('spACGlosasPendientesXConciliar').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACFacturasDevueltas').value;
    var Numero = number_format(Valor);
    document.getElementById('spACFacturasDevueltas').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACDiferenciaXImpuestos').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDiferenciaXImpuestos').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACDescuentoXRetefuente').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDescuentoXRetefuente').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACFacturasNoRelacionadasXIPS').value;
    var Numero = number_format(Valor);
    document.getElementById('spACFacturasNoRelacionadasXIPS').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACRetencionesImpuestosNoProcedentes').value;
    var Numero = number_format(Valor);
    document.getElementById('spACRetencionesImpuestosNoProcedentes').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACAjustesDeCartera').value;
    var Numero = number_format(Valor);
    document.getElementById('spACAjustesDeCartera').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACDiferenciaXValorFacturado').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDiferenciaXValorFacturado').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACDiferenciaXUPC').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDiferenciaXUPC').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACGlosasPendientesXDescargarIPS').value;
    var Numero = number_format(Valor);
    document.getElementById('spACGlosasPendientesXDescargarIPS').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACAnticiposPendientesXCruzar').value;
    var Numero = number_format(Valor);
    document.getElementById('spACAnticiposPendientesXCruzar').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACDescuentosLMA').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDescuentosLMA').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACPendientesAuditoria').value;
    var Numero = number_format(Valor);
    document.getElementById('spACPendientesAuditoria').innerHTML=Numero;
    
    var Valor=document.getElementById('ACValorSegunEPS').value;
    var Numero = number_format(Valor);
    document.getElementById('spValorSegunEPS').innerHTML=Numero;
    
    var Valor=document.getElementById('ACValorSegunIPS').value;
    var Numero = number_format(Valor);
    document.getElementById('spValorSegunIPS').innerHTML=Numero;
    
    var Valor=document.getElementById('ACDiferencia').value;
    var Numero = number_format(Valor);
    document.getElementById('spACDiferencia').innerHTML=Numero;
    
    
    var Valor=document.getElementById('TxtACTotalDiferencias').value;
    var Numero = number_format(Valor);
    document.getElementById('spACTotalDiferencias').innerHTML=Numero;
    
    var Valor=document.getElementById('TxtACSaldoAcuerdoPago').value;
    var Numero = number_format(Valor);
    document.getElementById('spACSaldoAcuerdoPago').innerHTML=Numero;
    
    
}


function GuardarDiferenciasActaConciliacion(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    
    EditeActaConciliacion(idActaConciliacion,'TxtACDiferenciaXPagos','DiferenciaXPagos');    
    EditeActaConciliacion(idActaConciliacion,'TxtACFacturasIPSNoRelacionadasEPS','FacturasNoRegistradasXEPS');
    EditeActaConciliacion(idActaConciliacion,'TxtACGlosasPendientesXConciliar','GlosasPendientesXConciliar');
    EditeActaConciliacion(idActaConciliacion,'TxtACFacturasDevueltas','TotalDevoluciones');
    EditeActaConciliacion(idActaConciliacion,'TxtACDiferenciaXImpuestos','ImpuestosNoRelacionadosIPS');
    EditeActaConciliacion(idActaConciliacion,'TxtACDescuentoXRetefuente','RetefuenteNoMerecida');
    EditeActaConciliacion(idActaConciliacion,'TxtACFacturasNoRelacionadasXIPS','FacturasSinRelacionIPS');
    EditeActaConciliacion(idActaConciliacion,'TxtACRetencionesImpuestosNoProcedentes','RetencionesImpuestosNoProcedentes');
    EditeActaConciliacion(idActaConciliacion,'TxtACAjustesDeCartera','AjustesDeCartera');
    EditeActaConciliacion(idActaConciliacion,'TxtACDiferenciaXValorFacturado','FacturasConValorDiferente');
    EditeActaConciliacion(idActaConciliacion,'TxtACDiferenciaXUPC','FacturasConReajusteUPC');
    EditeActaConciliacion(idActaConciliacion,'TxtACGlosasPendientesXDescargarIPS','GlosasConciliadasPendientesDescargaIPS');
    EditeActaConciliacion(idActaConciliacion,'TxtACAnticiposPendientesXCruzar','TotalAnticipos');
    EditeActaConciliacion(idActaConciliacion,'TxtACDescuentosLMA','DescuentosReconocimientosLMA');
    EditeActaConciliacion(idActaConciliacion,'TxtACPendientesAuditoria','FacturasPendienteAuditoria');
    
    EditeActaConciliacion(idActaConciliacion,'ACValorSegunEPS','ValorSegunEPS');
    EditeActaConciliacion(idActaConciliacion,'ACValorSegunIPS','ValorSegunIPS');
    EditeActaConciliacion(idActaConciliacion,'TxtACTotalDiferencias','Diferencia');
    EditeActaConciliacion(idActaConciliacion,'TxtACSaldoAcuerdoPago','SaldoConciliadoPago');
}

function AgregueFirma(TipoFirma){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var CmbFirmaUsual=document.getElementById('CmbFirmaUsual').value;
    var TxtNombreFirmaActa=document.getElementById('TxtNombreFirmaActa').value;
    var TxtCargoFirmaActa=document.getElementById('TxtCargoFirmaActa').value;
    var TxtEmpresaFirmaActa=document.getElementById('TxtEmpresaFirmaActa').value;
    var TxtRepresentanteActaConciliacion=document.getElementById('TxtRepresentanteActaConciliacion').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 20);
        form_data.append('idActaConciliacion', idActaConciliacion);        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TipoFirma', TipoFirma);
        form_data.append('CmbFirmaUsual', CmbFirmaUsual);
        form_data.append('TxtNombreFirmaActa', TxtNombreFirmaActa);
        form_data.append('TxtCargoFirmaActa', TxtCargoFirmaActa);
        form_data.append('TxtEmpresaFirmaActa', TxtEmpresaFirmaActa);
        form_data.append('TxtRepresentanteActaConciliacion', TxtRepresentanteActaConciliacion);
        
        
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
                
                DibujeFirmasActaConciliacion();                
                alertify.success(respuestas[1]);                
                document.getElementById('TxtNombreFirmaActa').value='';
                document.getElementById('TxtCargoFirmaActa').value='';
                document.getElementById('TxtEmpresaFirmaActa').value='';
                document.getElementById('CmbFirmaUsual').value='';
                document.getElementById('select2-CmbFirmaUsual-container').innerHTML='Seleccione una Firma';
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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


function DibujeFirmasActaConciliacion(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 30);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            document.getElementById('DivFirmasActaConciliacion').innerHTML=data;
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function EditeFirmaActaConciliacion(idFirma,idCajaFirma,CampoEditar){
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var TxtValorNuevo = document.getElementById(idCajaFirma).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 21);
        form_data.append('idFirma', idFirma);
        form_data.append('TxtValorNuevo', TxtValorNuevo);  
        form_data.append('CampoEditar', CampoEditar);  
        form_data.append('idCajaFirma', idCajaFirma);  
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
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
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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


function EliminarItem(Tabla,idItem){
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 22);
        form_data.append('Tabla', Tabla);
        form_data.append('idItem', idItem);  
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
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
                
                alertify.error(respuestas[1]);   
                if(Tabla==1){
                    DibujeFirmasActaConciliacion();
                }
                if(Tabla==2){
                    DibujeContratosActaConciliacion();
                    InicializarValoresGeneralesActaConciliacion();
                }
                
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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


function DibujeConstanciaFirmaActaConciliacion(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 31);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            document.getElementById('DivConstanciaFirma').innerHTML=data;
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function AgregarContratoActaConciliacion(idActaConciliacion,NumeroContrato){
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 23);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('NumeroContrato', NumeroContrato);  
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
                
                alertify.error(respuestas[1]);                
                DibujeContratosActaConciliacion();
                InicializarValoresGeneralesActaConciliacion();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function DibujeContratosActaConciliacion(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 32);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            document.getElementById('DivContratosAgregadosActaConciliacion').innerHTML=data;
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function InicializarValoresGeneralesActaConciliacion(){
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 24);
        form_data.append('idActaConciliacion', idActaConciliacion);
        
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
                
                alertify.error(respuestas[1]);                
                DibujeContratosActaConciliacion();
                MostrarActa(0);
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
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

function DibujaTotalesCruce(){
    document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Calculando Totales...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';
    
    var CmbTipoNegociacion=document.getElementById('CmbTipoNegociacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 33);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);   
        form_data.append('CmbTipoNegociacion', CmbTipoNegociacion);     
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTotalesCruce').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CerrarActaConciliacion(){
    
    alertify.confirm('Está seguro que desea Cerrar este documento?',
        function (e) {
            if (e) {

                alertify.success("Iniciando Proceso");                    
                EnviarSoporteActaConciliacion();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}

function EnviarSoporteActaConciliacion(){
    var idDivOutput="DivMensajesCerrarActa";
    document.getElementById(idDivOutput).innerHTML='<div id="GifProcess">Enviando Soporte...<br><img   src="../../images/loading.gif" alt="Procesando" height="50" width="50"></div>';
    
    var idBoton="btnGuardarConciliacion";
    
    document.getElementById(idBoton).disabled=true;   
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 25);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('UpSoporteActaConciliacionCierre', $('#UpSoporteActaConciliacionCierre').prop('files')[0]);
      
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
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                ObtengaTotalRegistrosCruce();
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function ObtengaTotalRegistrosCruce(){
    var idDivOutput="DivMensajesCerrarActa";
    document.getElementById(idDivOutput).innerHTML='<div id="GifProcess">Obteniengo el Total de Registros...<br><img   src="../../images/loading.gif" alt="Procesando" height="50" width="50"></div>';
    
    var idBoton="btnGuardarConciliacion";
    
    document.getElementById(idBoton).disabled=true;   
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 26);
        form_data.append('idActaConciliacion', idActaConciliacion);
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
               
                alertify.success(respuestas[1]);
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                CopiarRegistrosCruce(respuestas[2]);
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function CopiarRegistrosCruce(TotalRegistros){
    var idDivOutput="DivMensajesCerrarActa";
    
    var idBoton="btnGuardarConciliacion";
    
    document.getElementById(idBoton).disabled=true;   
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 27);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TotalRegistros', TotalRegistros);
        
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
            //console.log(data)
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){
               var porcentaje = respuestas[3];
               
                $("#PgProgresoCruce").css('width',porcentaje+"%").attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoCruce').innerHTML=porcentaje+"%";
                //alertify.success(respuestas[1]);
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                
                CopiarRegistrosCruce(TotalRegistros);
                
            }else if(respuestas[0]==="FIN"){
                 var porcentaje = 100;
               
                $("#PgProgresoCruce").css('width',porcentaje+"%").attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoCruce').innerHTML=porcentaje+"%";
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                //alertify.alert(respuestas[1]);
                ObtengaTotalRegistrosQueNoCruzan();
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function ObtengaTotalRegistrosQueNoCruzan(){
    var idDivOutput="DivMensajesCerrarActa";
    document.getElementById(idDivOutput).innerHTML='<div id="GifProcess">Obteniengo el Total de Registros...<br><img   src="../../images/loading.gif" alt="Procesando" height="50" width="50"></div>';
    
    var idBoton="btnGuardarConciliacion";
    
    document.getElementById(idBoton).disabled=true;   
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 28);
        form_data.append('idActaConciliacion', idActaConciliacion);
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
               
                alertify.success(respuestas[1]);
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                CopiarRegistrosQueNoCruzan(respuestas[2]);
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function CopiarRegistrosQueNoCruzan(TotalRegistros){
    var idDivOutput="DivMensajesCerrarActa";
    
    var idBoton="btnGuardarConciliacion";
        
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 29);
        form_data.append('idActaConciliacion', idActaConciliacion);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TotalRegistros', TotalRegistros);
        
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
            //console.log(data)
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
               var porcentaje = respuestas[3];
               
                $("#PgProgresoNoCruce").css('width',porcentaje+"%").attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoNoCruce').innerHTML=porcentaje+"%";
                //alertify.success(respuestas[1]);
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                //alertify.success(respuestas[1]);
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                
                CopiarRegistrosQueNoCruzan(TotalRegistros);
                
            }else if(respuestas[0]==="FIN"){
                var porcentaje = 100;
               
                $("#PgProgresoNoCruce").css('width',porcentaje+"%").attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoNoCruce').innerHTML=porcentaje+"%";
                
                document.getElementById(idDivOutput).innerHTML=respuestas[1];
                //alertify.alert(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                ActualiceRegistroCierreActaConciliacion();
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function ActualiceRegistroCierreActaConciliacion(){
    var idDivOutput="DivMensajesCerrarActa";
    document.getElementById(idDivOutput).innerHTML='<div id="GifProcess">Actualizando Registros para el cierre...<br><img   src="../../images/loading.gif" alt="Procesando" height="50" width="50"></div>';
    
    var idBoton="btnGuardarConciliacion";
    
    document.getElementById(idBoton).disabled=true;   
    
    var idActaConciliacion=document.getElementById('idActaConciliacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 30);
        form_data.append('idActaConciliacion', idActaConciliacion);
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
               
                //alertify.success(respuestas[1]);
                //alertify.alert(respuestas[1]);
                var x = document.getElementById("idActaConciliacion");
                x.remove(x.selectedIndex);
                document.getElementById('DivTab8').innerHTML=respuestas[1];
                document.getElementById(idBoton).disabled=false;
                
                
            }else if(respuestas[0]==="E1"){
                document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               document.getElementById(idDivOutput).innerHTML='';
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idDivOutput).innerHTML='';
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function MostrarCamposDiferencias(){
    MuestraOcultaXID('spACDiferenciaXPagos');
    MuestraOcultaXID('TxtACDiferenciaXPagos');
    
    MuestraOcultaXID('TxtACFacturasIPSNoRelacionadasEPS');
    MuestraOcultaXID('spACFacturasIPSNoRelacionadasEPS');
    
    MuestraOcultaXID('TxtACGlosasPendientesXConciliar');
    MuestraOcultaXID('spACGlosasPendientesXConciliar');
    
    MuestraOcultaXID('TxtACFacturasDevueltas');
    MuestraOcultaXID('spACFacturasDevueltas');
    
    MuestraOcultaXID('TxtACDiferenciaXImpuestos');
    MuestraOcultaXID('spACDiferenciaXImpuestos');
    
    MuestraOcultaXID('TxtACDescuentoXRetefuente');
    MuestraOcultaXID('spACDescuentoXRetefuente');
    
    MuestraOcultaXID('TxtACFacturasNoRelacionadasXIPS');
    MuestraOcultaXID('spACFacturasNoRelacionadasXIPS');
    
    MuestraOcultaXID('TxtACRetencionesImpuestosNoProcedentes');
    MuestraOcultaXID('spACRetencionesImpuestosNoProcedentes');
    
    MuestraOcultaXID('TxtACAjustesDeCartera');
    MuestraOcultaXID('spACAjustesDeCartera');
    
    MuestraOcultaXID('TxtACDiferenciaXValorFacturado');
    MuestraOcultaXID('spACDiferenciaXValorFacturado');
    
    MuestraOcultaXID('TxtACDiferenciaXUPC');
    MuestraOcultaXID('spACDiferenciaXUPC');
    
    MuestraOcultaXID('TxtACGlosasPendientesXDescargarIPS');
    MuestraOcultaXID('spACGlosasPendientesXDescargarIPS');
    
    MuestraOcultaXID('TxtACAnticiposPendientesXCruzar');
    MuestraOcultaXID('spACAnticiposPendientesXCruzar');
    
    MuestraOcultaXID('TxtACDescuentosLMA');
    MuestraOcultaXID('spACDescuentosLMA');
    
    MuestraOcultaXID('TxtACPendientesAuditoria');
    MuestraOcultaXID('spACPendientesAuditoria');
    
    MuestraOcultaXID('DivBotonActualizarManual');
    
}

function ActualizarDiferenciasManualmente(){
       
    var idBoton="BtnActualizarDiferenciasManualmente";
    
    document.getElementById(idBoton).disabled=true;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var ACDiferencia=document.getElementById('ACDiferencia').value;
    var TxtACDiferenciaXPagos=document.getElementById('TxtACDiferenciaXPagos').value;
    var TxtACFacturasIPSNoRelacionadasEPS=document.getElementById('TxtACFacturasIPSNoRelacionadasEPS').value;
    var TxtACGlosasPendientesXConciliar=document.getElementById('TxtACGlosasPendientesXConciliar').value;
    var TxtACFacturasDevueltas=document.getElementById('TxtACFacturasDevueltas').value;
    var TxtACDiferenciaXImpuestos=document.getElementById('TxtACDiferenciaXImpuestos').value;
    var TxtACDescuentoXRetefuente=document.getElementById('TxtACDescuentoXRetefuente').value;
    var TxtACFacturasNoRelacionadasXIPS=document.getElementById('TxtACFacturasNoRelacionadasXIPS').value;
    var TxtACRetencionesImpuestosNoProcedentes=document.getElementById('TxtACRetencionesImpuestosNoProcedentes').value;
    var TxtACAjustesDeCartera=document.getElementById('TxtACAjustesDeCartera').value;
    var TxtACDiferenciaXValorFacturado=document.getElementById('TxtACDiferenciaXValorFacturado').value;
    var TxtACDiferenciaXUPC=document.getElementById('TxtACDiferenciaXUPC').value;
    var TxtACGlosasPendientesXDescargarIPS=document.getElementById('TxtACGlosasPendientesXDescargarIPS').value;
    var TxtACAnticiposPendientesXCruzar=document.getElementById('TxtACAnticiposPendientesXCruzar').value;
    var TxtACDescuentosLMA=document.getElementById('TxtACDescuentosLMA').value;
    var TxtACPendientesAuditoria=document.getElementById('TxtACPendientesAuditoria').value;
        
    var form_data = new FormData();
        form_data.append('Accion', 31);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
        form_data.append('ACDiferencia', ACDiferencia);
        form_data.append('TxtACDiferenciaXPagos', TxtACDiferenciaXPagos);
        form_data.append('TxtACFacturasIPSNoRelacionadasEPS', TxtACFacturasIPSNoRelacionadasEPS);
        form_data.append('TxtACGlosasPendientesXConciliar', TxtACGlosasPendientesXConciliar);
        form_data.append('TxtACFacturasDevueltas', TxtACFacturasDevueltas);
        form_data.append('TxtACDiferenciaXImpuestos', TxtACDiferenciaXImpuestos);
        form_data.append('TxtACDescuentoXRetefuente', TxtACDescuentoXRetefuente);
        form_data.append('TxtACFacturasNoRelacionadasXIPS', TxtACFacturasNoRelacionadasXIPS);
        form_data.append('TxtACRetencionesImpuestosNoProcedentes', TxtACRetencionesImpuestosNoProcedentes);
        form_data.append('TxtACAjustesDeCartera', TxtACAjustesDeCartera);
        form_data.append('TxtACDiferenciaXValorFacturado', TxtACDiferenciaXValorFacturado);
        form_data.append('TxtACDiferenciaXUPC', TxtACDiferenciaXUPC);
        form_data.append('TxtACGlosasPendientesXDescargarIPS', TxtACGlosasPendientesXDescargarIPS);
        form_data.append('TxtACAnticiposPendientesXCruzar', TxtACAnticiposPendientesXCruzar);
        form_data.append('TxtACDescuentosLMA', TxtACDescuentosLMA);
        form_data.append('TxtACPendientesAuditoria', TxtACPendientesAuditoria);
              
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
                EscribaValoresEnSpanDiferenciasActas();
                GuardarDiferenciasActaConciliacion();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
    
    
}

function ObtenerNumeroRegistrosACopiarEnHoja(){
    document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Obteniendo el total de registros a copiar...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';
     
    var idBoton="BtnConstruirHojaTrabajo";
    
    document.getElementById(idBoton).disabled=true;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 34);
        
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
               
                alertify.success(respuestas[1]);  
                var TotalRegistros=respuestas[2];
                document.getElementById("DivTotalesCruce").innerHTML=respuestas[1];
                CronstruirHojaDeTrabajo(TotalRegistros);
                
               
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function CronstruirHojaDeTrabajo(TotalRegistros){
    document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Construyendo hoja de trabajo...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';
    
    var idBoton="BtnConstruirHojaTrabajo";
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 32);//Construirla completa
        //form_data.append('Accion', 36); //Construirla por partes
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TotalRegistros', TotalRegistros);
                    
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
               
                console.log(data);               
                document.getElementById("DivTotalesCruce").innerHTML=respuestas[1];
                var porcentaje=respuestas[2];
                $('.progress-bar').css('width',porcentaje+"%").attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoUP').innerHTML=porcentaje+"%";
                CronstruirHojaDeTrabajo(TotalRegistros);
                
            }else if(respuestas[0]==="FIN"){
                $('.progress-bar').css('width',"100%").attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP').innerHTML="100%";
                alertify.success(respuestas[1]);
                document.getElementById("DivTotalesCruce").innerHTML=respuestas[1];
                document.getElementById(idBoton).disabled=false;
                document.getElementById("DivMensajes").innerHTML=respuestas[1];
                //CalcularDiferenciasVaridas();
                MuestreCruce();
                return;     
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
    
    
}

function ActualizarHojaDeTrabajo(){
    document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Actualizando la hoja de trabajo...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';
     
    var idBoton="BtnActualizarCruce";
    
    document.getElementById(idBoton).disabled=true;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 33);
        
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
               
                alertify.success(respuestas[1]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById("DivTotalesCruce").innerHTML=respuestas[1];
                MuestreCruce();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
    
    
}

function CalcularDiferenciasVaridas(){
    document.getElementById("DivTotalesCruce").innerHTML='<div id="GifProcess">Calculando diferencias...<br><img   src="../../images/loading.gif" alt="Cargando" height="50" width="50"></div>';
     
    var idBoton="BtnCalcularDiferencias";
    
    document.getElementById(idBoton).disabled=true;
    var CmbTipoNegociacion=document.getElementById('CmbTipoNegociacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 35);
        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('CmbTipoNegociacion', CmbTipoNegociacion);
                    
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
                document.getElementById("DivTotalesCruce").innerHTML=respuestas[1];
                MuestreCruce();               
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                
                return;                
            }else{
               
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function VerContratos(){
    document.getElementById("DivTab9").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 34);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        $.ajax({
        url: './Consultas/validaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab9').innerHTML=data;
            $('.selector').select2({
		
                placeholder: 'Selecciona un Contrato',
                ajax: {
                  url: 'buscadores/contratos.search.php?nit='+CmbIPS,
                  dataType: 'json',
                  delay: 250,
                                    
                  processResults: function (data) {
                      
                    return {                     
                      results: data
                    };
                  },
                 cache: true
                }
              });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
$('#CmbEPS').select2();