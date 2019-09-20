/**
 * Controlador para las actas de liquidacion
 * JULIAN ALVARAN 2019-09-11
 * TECHNO SOLUCIONES SAS 
 * 
 */
function MuestraXID(id){
    
    
    document.getElementById(id).style.display="block";
    
    
}


function OcultaXID(id){
    
    
    document.getElementById(id).style.display="none";
    
    
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

function AbrirFormularioNuevo(){
        
    AbreModal('ModalAcciones');
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        $.ajax({
        url: './Consultas/ActasLiquidacion.draw.php',
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

function CrearActaLiquidacion(){
    var idBoton="BntModalAcciones";
    document.getElementById(idBoton).disabled=true;
    document.getElementById(idBoton).value="Enviando...";
    
    var TxtPrefijo=document.getElementById('TxtPrefijo').value;
    var TxtConsecutivo=document.getElementById('TxtConsecutivo').value;
    var TxtAnio=document.getElementById('TxtAnio').value;
    var NombreRepresentanteEPS=document.getElementById('NombreRepresentanteEPS').value;
    var NombreRepresentanteIPS=document.getElementById('NombreRepresentanteIPS').value;
    var ApellidosRepresentanteEPS=document.getElementById('ApellidosRepresentanteEPS').value;
    var ApellidosRepresentanteIPS=document.getElementById('ApellidosRepresentanteIPS').value;
    var IdentificacionRepresentanteEPS=document.getElementById('IdentificacionRepresentanteEPS').value;
    var IdentificacionRepresentanteIPS=document.getElementById('IdentificacionRepresentanteIPS').value;
    var DomicilioRepresentanteEPS=document.getElementById('DomicilioRepresentanteEPS').value;
    var DomicilioRepresentanteIPS=document.getElementById('DomicilioRepresentanteIPS').value;    
    var DireccionRepresentanteEPS=document.getElementById('DireccionRepresentanteEPS').value;
    var DireccionRepresentanteIPS=document.getElementById('DireccionRepresentanteIPS').value;
    var TelefonoRepresentanteEPS=document.getElementById('TelefonoRepresentanteEPS').value;
    var TelefonoRepresentanteIPS=document.getElementById('TelefonoRepresentanteIPS').value;
    var TipoActa=document.getElementById('TipoActa').value;
    var FechaInicial=document.getElementById('FechaInicial').value;
    var FechaFinal=document.getElementById('FechaFinal').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TxtPrefijo', TxtPrefijo);
        form_data.append('TxtConsecutivo', TxtConsecutivo);
        form_data.append('TxtAnio', TxtAnio);
        form_data.append('NombreRepresentanteEPS', NombreRepresentanteEPS);
        form_data.append('NombreRepresentanteIPS', NombreRepresentanteIPS);
        form_data.append('ApellidosRepresentanteEPS', ApellidosRepresentanteEPS);
        form_data.append('ApellidosRepresentanteIPS', ApellidosRepresentanteIPS);
        form_data.append('IdentificacionRepresentanteEPS', IdentificacionRepresentanteEPS);
        form_data.append('IdentificacionRepresentanteIPS', IdentificacionRepresentanteIPS);
        form_data.append('DomicilioRepresentanteEPS', DomicilioRepresentanteEPS);        
        form_data.append('DomicilioRepresentanteIPS', DomicilioRepresentanteIPS);
        form_data.append('DireccionRepresentanteEPS', DireccionRepresentanteEPS);
        form_data.append('DireccionRepresentanteIPS', DireccionRepresentanteIPS);
        form_data.append('TelefonoRepresentanteEPS', TelefonoRepresentanteEPS);
        form_data.append('TelefonoRepresentanteIPS', TelefonoRepresentanteIPS);
        form_data.append('TipoActa', TipoActa);
        form_data.append('FechaInicial', FechaInicial);
        form_data.append('FechaFinal', FechaFinal);
                
    $.ajax({
        //async:false,
        url: './procesadores/actas_liquidacion.process.php',
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
                document.getElementById(idBoton).value="Guardar";
                CierraModal('ModalAcciones');
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Guardar";
                return;                
            }else{
                
                alertify.alert(data);
                document.getElementById(idBoton).disabled=false;
                document.getElementById(idBoton).value="Guardar";
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
            document.getElementById(idBoton).disabled=false;
            document.getElementById(idBoton).value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      })
}


function SeleccioneAccionFormularios(){
    var idFormulario=document.getElementById('idFormulario').value;
    if(idFormulario==1){
        CrearActaLiquidacion();
    }
    
}

function MarqueErrorElemento(idElemento){
    console.log(idElemento);
    if(idElemento==undefined){
       return; 
    }
    document.getElementById(idElemento).style.backgroundColor="pink";
    document.getElementById(idElemento).focus();
}


function CambiePagina(Page=""){
    
    if(Page==""){
        Page = document.getElementById('CmbPage').value;
    }
    CargarHistorialActas(Page);
}


function DibujaSelectorActas(){
    document.getElementById('DivTab1').innerHTML="";
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        $.ajax({
        url: './Consultas/ActasLiquidacion.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab1SelectorActas').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function MostrarActa(){
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        
        form_data.append('idActaLiquidacion', idActaLiquidacion);
        $.ajax({
        url: './Consultas/ActasLiquidacion.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab1').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function EditeActaLiquidacion(idActaLiquidacion,idCampoTexto,CampoAEditar){
    
    var NuevoValor = document.getElementById(idCampoTexto).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
        form_data.append('NuevoValor', NuevoValor); 
        form_data.append('idCampoTexto', idCampoTexto); 
        form_data.append('CampoAEditar', CampoAEditar); 
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        
    $.ajax({
        //async:false,
        url: './procesadores/actas_liquidacion.process.php',
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


function AbreFormularioCrearContrato(idActaLiquidacion,Contrato){
        
    AbreModal('ModalAcciones');
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
        form_data.append('Contrato', Contrato);
        
        $.ajax({
        url: './Consultas/ActasLiquidacion.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           $('#CmbContratoPadre').select2();           
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function ValidarClasificacionContrato(){
    var Clasificacion = document.getElementById('CmbClasificacionContrato').value;
    
    if( Clasificacion=='ACUERDO' || Clasificacion=='CONTRATO' || Clasificacion=='SIN CONTRATO' || Clasificacion=='COTIZACION' || Clasificacion=='URGENCIAS'){
        OcultaXID('DivSelectoresOtroSI');
    }else{
        MuestraXID('DivSelectoresOtroSI');
    }
    
    if(Clasificacion=='ACUERDO' || Clasificacion=='OTRO SI' || Clasificacion=='SIN CONTRATO' || Clasificacion=='COTIZACION' || Clasificacion=='URGENCIAS'){
        OcultaXID('DivSelectorTipoContrato');
    }else{
        MuestraXID('DivSelectorTipoContrato');
    }
    
    if(Clasificacion==''){
        OcultaXID('DivSelectoresOtroSI');
        OcultaXID('DivSelectorTipoContrato');
    }
    
}

function ValidaOpcionesTipoContrato(){
    var TipoContrato = document.getElementById('CmbTipoContrato').value;
        if(TipoContrato == 'CAPITA' || TipoContrato == 'CAPITA MORVILIDAD' || TipoContrato == 'CAPITA PDYDT' || TipoContrato == 'CAPITA ACTIVIDADES MINIMAS' ){
            MuestraXID('DivUPCCapita');
        }else{
            OcultaXID('DivUPCCapita');
        }
    
    
}
document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
