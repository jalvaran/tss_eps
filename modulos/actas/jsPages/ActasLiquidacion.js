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
        document.getElementById('TabCuentas1').click();
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
              
              $('#CmbFirmaUsual').select2();
              DibujeFirmasActaConciliacion();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function AgregueFirma(TipoFirma){
    
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var CmbFirmaUsual=document.getElementById('CmbFirmaUsual').value;
    var NombreRepresentanteIPS=document.getElementById('NombreRepresentanteIPS').value;
    var ApellidosRepresentanteIPS=document.getElementById('ApellidosRepresentanteIPS').value;
    
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('idActaLiquidacion', idActaLiquidacion);        
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('TipoFirma', TipoFirma);
        form_data.append('CmbFirmaUsual', CmbFirmaUsual);
        form_data.append('NombreRepresentanteIPS', NombreRepresentanteIPS);
        form_data.append('ApellidosRepresentanteIPS', ApellidosRepresentanteIPS);
                
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
                
                DibujeFirmasActaConciliacion();                
                alertify.success(respuestas[1]);                
                
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
    
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
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
            
            document.getElementById('DivFirmasActaConciliacion').innerHTML=data;
                        
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

function EditeContrato(idContrato,idCampoTexto,CampoAEditar){
    
    var NuevoValor = document.getElementById(idCampoTexto).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 10);
        form_data.append('idContrato', idContrato);
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

function AgregarContratoActaLiquidacion(Contrato,idActaLiquidacion,idInput){
    
    var idCajaNombreContrato="TxtNombreContrato_"+idInput;
    var idCajaFechaInicial="FechaInicioContratoCapita_"+idInput;
    var idCajaFechaFinal="FechaFinalContratoCapita_"+idInput;
    var idCajaValor="TxtValorCapita_"+idInput;
    
    var NombreContrato=document.getElementById(idCajaNombreContrato).value;
    var FechaInicial=document.getElementById(idCajaFechaInicial).value;
    var FechaFinal=document.getElementById(idCajaFechaFinal).value;
    var ValorContrato=document.getElementById(idCajaValor).value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
        form_data.append('Contrato', Contrato); 
        form_data.append('NombreContrato', NombreContrato);
        form_data.append('FechaInicial', FechaInicial); 
        form_data.append('FechaFinal', FechaFinal);
        form_data.append('ValorContrato', ValorContrato); 
        
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
                MostrarActa();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                                
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

function EliminarContratoActa(idItem){
     
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idItem', idItem);
        
        
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
                
                alertify.error(respuestas[1]);                
                MostrarActa();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                                
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

function EliminarFirma(idItem){
     
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('idItem', idItem);
        
        
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
                
                alertify.error(respuestas[1]);                
                DibujeFirmasActaConciliacion();
            }else if(respuestas[0]==="E1"){
                
                alertify.alert(respuestas[1]);
                                
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


function EditeFirmaActaConciliacion(idFirma,idCajaFirma,CampoEditar){
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var TxtValorNuevo = document.getElementById(idCajaFirma).value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('idFirma', idFirma);
        form_data.append('TxtValorNuevo', TxtValorNuevo);  
        form_data.append('CampoEditar', CampoEditar);  
        form_data.append('idCajaFirma', idCajaFirma);  
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
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

function CerrarActaLiquidacion(){
    var idBoton="btnGuardarActaLiquidacion";    
    document.getElementById(idBoton).disabled=true;
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 8);
         
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
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
                
                //alertify.success(respuestas[1]); 
                CerrarActaLiquidacionRadicados();
                //document.getElementById('DivMensajesCerrarActa').innerHTML=respuestas[1];
                
            }else if(respuestas[0]==="E1"){
                document.getElementById(idBoton).disabled=false;
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
            }else{
                
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idBoton).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function CerrarActaLiquidacionRadicados(){
    var idBoton="btnGuardarActaLiquidacion";    
    document.getElementById(idBoton).disabled=true;
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 9);
         
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
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
                //document.getElementById('DivMensajesCerrarActa').innerHTML=document.getElementById('DivMensajesCerrarActa').innerHTML+"<br>"+respuestas[1];
                document.getElementById('DivTab1').innerHTML=respuestas[1];
                
                document.getElementById('DivTab1SelectorActas').innerHTML='';
                document.getElementById(idBoton).disabled=false;
            }else if(respuestas[0]==="E1"){
                document.getElementById(idBoton).disabled=false;
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
            }else{
                
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idBoton).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function DibujeConstanciaFirmaActa(){
    
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
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
            
            document.getElementById('DivConstanciaFirma').innerHTML=data;
                        
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function CargarHistorialActas(Page=1){
    document.getElementById("DivTab1").innerHTML='<div id="GifProcess">Cargando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var Busqueda=document.getElementById('TxtBusquedas').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('Page', Page);
        form_data.append('Busqueda', Busqueda);
        $.ajax({
        url: './Consultas/ActasLiquidacion.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivTab2').innerHTML=data;
            
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
    CargarHistorialActas(Page);
}


function ExportarExcel(db,Tabla,st){
    //document.getElementById("DivMensajes").innerHTML="Exportando...";
    document.getElementById("DivMensajes").innerHTML='<div id="GifProcess">Exportando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    var idBoton="BtnExportarExcelCruce";
    document.getElementById(idBoton).disabled=true; 
    
    //var CmbEPS=document.getElementById('CmbEPS').value;
    //var CmbIPS=document.getElementById('CmbIPS').value;
    
    
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

function ModalRenombrarContrato(NumeroContrato){
        
    AbreModal('ModalAcciones');
    
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('NumeroContrato', NumeroContrato);
        
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

function RenombrarContrato(NumeroContrato){
    var idBoton="BtnRenombrarContrato";    
    document.getElementById(idBoton).disabled=true;
    var idActaLiquidacion=document.getElementById('idActaLiquidacion').value;
    var ContratoNuevo=document.getElementById('TxtNumeroContratoRenombrar').value;
    var FechaInicial=document.getElementById('TxtFechaInicialActaLiquidacion').value;
    var FechaFinal=document.getElementById('TxtFechaFinalLiquidacion').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbEPS=document.getElementById('CmbEPS').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 11);
         
        form_data.append('CmbEPS', CmbEPS);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('ContratoNuevo', ContratoNuevo);
        form_data.append('idActaLiquidacion', idActaLiquidacion);
        form_data.append('NumeroContrato', NumeroContrato);
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
                //document.getElementById('DivMensajesCerrarActa').innerHTML=document.getElementById('DivMensajesCerrarActa').innerHTML+"<br>"+respuestas[1];
                //document.getElementById('DivTab1').innerHTML=respuestas[1];
                MostrarActa();
                
                document.getElementById(idBoton).disabled=false;
            }else if(respuestas[0]==="E1"){
                document.getElementById(idBoton).disabled=false;
                alertify.alert(respuestas[1]);
                MarqueErrorElemento(respuestas[2]);
                
                return;                
            }else{
                
                alertify.alert(data);
                
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById(idBoton).disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas2').click();
$('#CmbIPS').select2();
