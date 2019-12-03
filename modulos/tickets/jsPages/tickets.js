/**
 * Controlador para realizar la administracion de los tickets
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
    document.getElementById('DivItemsCompra').innerHTML='';
    document.getElementById('DivTotalesCompra').innerHTML='';
}

/*
$('#CmbBusquedas').bind('change', function() {
    
    document.getElementById('CodigoBarras').value = document.getElementById('CmbBusquedas').value;
    BusquePrecioVentaCosto();
    
});

*/


function VerListadoTickets(Page=1){
    document.getElementById("DivDrawTickets").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEstadoTicketsListado=document.getElementById('CmbEstadoTicketsListado').value;  
    var CmbFiltroUsuario=document.getElementById('CmbFiltroUsuario').value; 
    var CmbProyectosTicketsListado=document.getElementById('CmbProyectosTicketsListado').value; 
    var CmbModulosTicketsListado=document.getElementById('CmbModulosTicketsListado').value; 
    var CmbTiposTicketsListado=document.getElementById('CmbTiposTicketsListado').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        form_data.append('CmbEstadoTicketsListado', CmbEstadoTicketsListado);
        form_data.append('CmbFiltroUsuario', CmbFiltroUsuario);
        form_data.append('CmbProyectosTicketsListado', CmbProyectosTicketsListado);
        form_data.append('CmbModulosTicketsListado', CmbModulosTicketsListado);
        form_data.append('CmbTiposTicketsListado', CmbTiposTicketsListado);
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivDrawTickets').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CambiePagina(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPage').value;
    }
    VerListadoTickets(Page);
}

function FormularioNuevoTicket(){
    document.getElementById("DivDrawTickets").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivDrawTickets').innerHTML=data;
           $("#CmbUsuarioDestino").select2();
           $("#TxtMensaje").wysihtml5(); 
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CrearTicket(){
    document.getElementById('BtnGuardarTicket').disabled=true;
    document.getElementById('BtnGuardarTicket').value="Guardando...";
    
    var CmbUsuarioDestino=document.getElementById('CmbUsuarioDestino').value;
    var CmbTipoTicket=document.getElementById('CmbTipoTicket').value;
    var CmbProyecto=document.getElementById('CmbProyecto').value;
    var CmbModuloProyecto=document.getElementById('CmbModuloProyecto').value;
    var CmbPrioridad=document.getElementById('CmbPrioridad').value;
    var TxtAsunto=document.getElementById('TxtAsunto').value;
    
    var TxtMensaje=document.getElementById('TxtMensaje').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbUsuarioDestino', CmbUsuarioDestino);
        form_data.append('CmbTipoTicket', CmbTipoTicket);
        form_data.append('CmbProyecto', CmbProyecto);
        form_data.append('CmbModuloProyecto', CmbModuloProyecto);
        form_data.append('TxtAsunto', TxtAsunto);        
        form_data.append('CmbPrioridad', CmbPrioridad);        
        
        form_data.append('TxtMensaje', TxtMensaje);
        form_data.append('upAdjuntosTickets1', $('#upAdjuntosTickets1').prop('files')[0]);
        form_data.append('upAdjuntosTickets2', $('#upAdjuntosTickets2').prop('files')[0]);
        form_data.append('upAdjuntosTickets3', $('#upAdjuntosTickets3').prop('files')[0]);
                
    $.ajax({
        //async:false,
        url: './procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerListadoTickets();
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnGuardarTicket').disabled=false;
            document.getElementById('BtnGuardarTicket').value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function VerTicket(idTicket){
    document.getElementById("DivDrawTickets").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idTicket', idTicket);
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivDrawTickets').innerHTML=data;
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function AgregarAdjunto(idMensaje,idTicket){
    var idBoton='BtnAgregarAdjunto_'+idMensaje;
    var UpFile='upAdjuntosMensajes_'+idMensaje;
    document.getElementById(idBoton).disabled=true;
    document.getElementById(idBoton).value="Guardando...";
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('idMensaje', idMensaje);
        form_data.append('idTicket', idTicket);
        form_data.append('upAdjuntosTickets', $('#'+UpFile).prop('files')[0]);
        
                
    $.ajax({
        //async:false,
        url: './procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerTicket(idTicket);
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Adjuntar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Adjuntar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            document.getElementById(idBoton).value="Adjuntar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function FormularioResponderTicket(idTicket){
    document.getElementById("DivDrawTickets").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idTicket', idTicket);
        $.ajax({
        url: './Consultas/tickets.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivDrawTickets').innerHTML=data;
           
           $("#TxtMensaje").wysihtml5(); 
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function GuardarRespuesta(idTicket){
    document.getElementById('BtnGuardarTicket').disabled=true;
    document.getElementById('BtnGuardarTicket').value="Guardando...";
    
    
    var CmbCerrarTicket=document.getElementById('CmbCerrarTicket').value;
    var TxtMensaje=document.getElementById('TxtMensaje').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idTicket', idTicket);
        form_data.append('CmbCerrarTicket', CmbCerrarTicket);
        form_data.append('TxtMensaje', TxtMensaje);
        form_data.append('upAdjuntosTickets1', $('#upAdjuntosTickets1').prop('files')[0]);
        form_data.append('upAdjuntosTickets2', $('#upAdjuntosTickets2').prop('files')[0]);
        form_data.append('upAdjuntosTickets3', $('#upAdjuntosTickets3').prop('files')[0]);
                
    $.ajax({
        //async:false,
        url: './procesadores/tickets.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                
                VerTicket(idTicket);
                
                alertify.success(respuestas[1]);
                
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById('BtnGuardarTicket').disabled=false;
                document.getElementById('BtnGuardarTicket').value="Guardar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById('BtnGuardarTicket').disabled=false;
            document.getElementById('BtnGuardarTicket').value="Guardar";
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

function CargarModulosProyectosEnSelect(SelectorACambiar){
    if(SelectorACambiar==1){
        var idSelector="CmbModuloProyecto";
        var SelectorPadre="CmbProyecto";
    }
    if(SelectorACambiar==2){
        var idSelector="CmbModulosTicketsListado";
        var SelectorPadre="CmbProyectosTicketsListado";
    }
    
    document.getElementById(idSelector).value='';
    if(document.getElementById("select2-"+idSelector+"-container")){
        document.getElementById("select2-"+idSelector+"-container").innerHTML='Seleccione un módulo';
    }
    
    var CmbProyectosTicketsListado=document.getElementById(SelectorPadre).value;
        $('#'+idSelector).select2({
            theme: "classic",
            placeholder: 'Seleccione un Módulo',
            ajax: {
              url: './buscadores/modulos_proyectos.search.php?idProyecto='+CmbProyectosTicketsListado,
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
}

document.getElementById('BtnMuestraMenuLateral').click();

VerListadoTickets();