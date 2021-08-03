/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var general_div="general_div";
var hoja_trabajo_activa="";

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



/*
 * 
 * Espacio para funciones de modulos externos
 * modulos usados actas y validaciones 
 * 
 * 
 */


function ModalRenombrarContrato(NumeroContrato){
        
    AbreModal('ModalAcciones');
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('CmbIPS', CmbIPS);   
        form_data.append('dibuje_rangos', 1);  
        form_data.append('NumeroContrato', NumeroContrato);
        
        
        $.ajax({
        url: '../actas/Consultas/ActasLiquidacion.draw.php',
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
    
    var ContratoNuevo=document.getElementById('TxtNumeroContratoRenombrar').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var FechaInicial=document.getElementById('FechaInicial').value;
    var FechaFinal=document.getElementById('FechaFinal').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 11);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('ContratoNuevo', ContratoNuevo);        
        form_data.append('NumeroContrato', NumeroContrato);
        form_data.append('FechaInicial', FechaInicial);
        form_data.append('FechaFinal', FechaFinal);
        form_data.append('idActaLiquidacion', "NA");
        form_data.append('hoja_trabajo_id', hoja_trabajo_activa);
        
        
    $.ajax({
        //async:false,
        url: '../actas/procesadores/actas_liquidacion.process.php',
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
                
                dibuje_contratos_disponibles_anexo(hoja_trabajo_activa); 
                dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_activa);
                
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


function AbreOpcionesMasivas(){
    OcultaXID('BntModalAcciones');
    AbreModal('ModalAcciones');
    document.getElementById("DivFrmModalAcciones").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 18);        
        
       
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: '../validaciones/Consultas/validaciones.draw.php',
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
        url: '../validaciones/procesadores/validaciones.process.php',
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
        url: '../validaciones/procesadores/validaciones.process.php',
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
        url: '../validaciones/procesadores/validaciones.process.php',
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
        url: '../validaciones/procesadores/validaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){   
                //CambiePaginaCruce();
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



/*
 *
 * Fin de funciones de modulos externos
 *   
 * 
 */



/**
 * funcion para dibujar el formulario para subir los anexos de una liquidacion
 * @returns {undefined}
 */
function frm_subir_anexos(){
    
    document.getElementById(general_div).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(general_div).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function create_tables_module(){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);
                        
    $.ajax({
        async:false,
        url: './procesadores/auditoria.process.php',
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

function create_table_hoja_de_trabajo_evento(){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('CmbIPS', CmbIPS);
                        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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


function confirma_inicializar_anexo(){
    
    alertify.confirm('Está seguro que desea Inicializar el Anexo?</strong>',
        function (e) {
            if (e) {

                                    
                inicializar_anexo();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function inicializar_anexo(){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
                        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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

function confirma_subir_anexo(){
    
    alertify.confirm('Está seguro que desea Subir el Anexo?</strong>',
        function (e) {
            if (e) {
               
                inicia_subir_anexo();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function inicia_subir_anexo(){ 
    
    document.getElementById("mensajes_div").innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_up', $('#anexo_up').prop('files')[0]); 
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                alertify.success(respuestas[1]);
                var anexo_id=respuestas[2];
                registra_anexo_temporal(anexo_id);
                
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

function registra_anexo_temporal(anexo_id){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_id', anexo_id);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                document.getElementById('LyProgresoUP').innerHTML="40%";
                alertify.success(respuestas[1]);
                document.getElementById("mensajes_div").innerHTML="";
                obtener_total_items_temporal(anexo_id);
                
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

function obtener_total_items_temporal(anexo_id){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 6);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_id', anexo_id);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
                $('.progress-bar').css('width','35%').attr('aria-valuenow', 35);  
                document.getElementById('LyProgresoUP').innerHTML="35%";
                alertify.success(respuestas[1]);
                var total_items=respuestas[2];
                document.getElementById("mensajes_div").innerHTML="copiando "+total_items;
                copiar_registros_anexo_real(anexo_id,total_items,1);
                
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

function copiar_registros_anexo_real(anexo_id,total_items,page=1){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_id', anexo_id);
        form_data.append('total_items', total_items);
        form_data.append('page', page);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                
                document.getElementById("mensajes_div").innerHTML="registros copiados";
                contar_registros_anexo_real(anexo_id);
            }else if(respuestas[0]==="UP"){
                document.getElementById("mensajes_div").innerHTML=respuestas[1];
                var next_page=respuestas[2];
                copiar_registros_anexo_real(anexo_id,total_items,next_page);
                
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

function contar_registros_anexo_real(anexo_id){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 8);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_id', anexo_id);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
                $('.progress-bar').css('width','75%').attr('aria-valuenow', 75);  
                document.getElementById('LyProgresoUP').innerHTML="75%";
                alertify.success(respuestas[1]);
                var total_items_actualizar=respuestas[2];
                document.getElementById("mensajes_div").innerHTML="actualizando "+total_items_actualizar;
                actualizar_registros_anexo_real(anexo_id,total_items_actualizar,1);
                
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


function actualizar_registros_anexo_real(anexo_id,total_items_actualizar,page=1){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var form_data = new FormData();
        form_data.append('Accion', 9);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('anexo_id', anexo_id);
        form_data.append('total_items', total_items_actualizar);
        form_data.append('page', page);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                
                document.getElementById("mensajes_div").innerHTML="Proceso Terminado";
                
            }else if(respuestas[0]==="UP"){
                document.getElementById("mensajes_div").innerHTML=respuestas[1];
                var next_page=respuestas[2];
                actualizar_registros_anexo_real(anexo_id,total_items_actualizar,next_page);
                
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


function module_init(){
    $('#CmbIPS').select2();
    $('#CmbEPS').select2();
        
}

function frm_construir_hoja_trabajo(hoja_trabajo_id){
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    hoja_trabajo_activa=hoja_trabajo_id;
    
    document.getElementById(general_div).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(general_div).innerHTML=data;
           dibuje_contratos_disponibles_anexo(hoja_trabajo_id); 
           dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_id);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function dibuje_contratos_disponibles_anexo(hoja_trabajo_id){
    var div_id="contratos_anexo_div";
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    if(!document.getElementById(div_id)){
        return;
    }
    document.getElementById(div_id).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(div_id).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function listar_hojas_trabajo(page=1){
    create_tables();
    
    var div_id=general_div;
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    var TxtBusquedas=document.getElementById('TxtBusquedas').value;
    document.getElementById(div_id).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('Page', page);
        form_data.append('TxtBusquedas', TxtBusquedas);
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(div_id).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function frm_crear_hoja_trabajo_nueva(){
    var tipo_anexo=document.getElementById('tipo_anexo').value;
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    document.getElementById(general_div).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('CmbIPS', CmbIPS);
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(general_div).innerHTML=data;
           
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function confirma_crear_hoja_trabajo(){
    
    alertify.confirm('Está seguro que desea Crear esta hoja de trabajo?</strong>',
        function (e) {
            if (e) {
              
                crear_hoja_de_trabajo();
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function crear_hoja_de_trabajo(){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var cmb_tipo_negociacion=document.getElementById('cmb_tipo_negociacion').value;
    var txt_descripcion=document.getElementById('txt_descripcion').value;
    var form_data = new FormData();
        form_data.append('Accion', 10);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('cmb_tipo_negociacion', cmb_tipo_negociacion);
        form_data.append('txt_descripcion', txt_descripcion);
                        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                listar_hojas_trabajo();
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


function agregar_contrato_hoja_trabajo(hoja_trabajo_id,contrato){ 
        
    var form_data = new FormData();
        form_data.append('Accion', 11);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        form_data.append('contrato', contrato);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                
                dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_id);
                
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


function dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_id){
    var div_id="contratos_anexo_agregados_div";
    
    if(!document.getElementById(div_id)){
        return;
    }
    document.getElementById(div_id).innerHTML='<div id="GifProcess">Procesando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var form_data = new FormData();
        form_data.append('Accion', 6);
        
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        $.ajax({
        url: './Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById(div_id).innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            LimpiarDivs();
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function eliminar_contrato_hoja_trabajo(hoja_trabajo_id,item_id){ 
        
    var form_data = new FormData();
        form_data.append('Accion', 12);
        form_data.append('item_id', item_id);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                
                dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_id);
                
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


function eliminar_ajustes_facturas(){ 
    var CmbIPS=document.getElementById('CmbIPS').value;    
    var form_data = new FormData();
        form_data.append('Accion', 13);
        form_data.append('CmbIPS', CmbIPS);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                
                dibuje_contratos_agregados_hoja_trabajo(hoja_trabajo_id);
                
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


function editar_campo(tabla,campo_id,campo_edit,table_id,item_id){ 
    var CmbIPS=document.getElementById('CmbIPS').value; 
    var nuevo_valor=document.getElementById(campo_id).value; 
    var form_data = new FormData();
        form_data.append('Accion', 14);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tabla', tabla);
        form_data.append('nuevo_valor', nuevo_valor);
        form_data.append('campo_edit', campo_edit);
        form_data.append('table_id', table_id);
        form_data.append('item_id', item_id);
        
        
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                if(tabla==2){
                    listar_hojas_trabajo();
                }
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



function confirma_construir_hoja_trabajo(hoja_trabajo_id){
    
    alertify.confirm('Está seguro que desea Inicializar el Anexo?</strong>',
        function (e) {
            if (e) {
             
                contar_registros_anexo(hoja_trabajo_id);
                
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function contar_registros_anexo(hoja_trabajo_id){ 
    var idBoton="btn_construir_hoja_trabajo";    
    document.getElementById(idBoton).disabled=true;
    document.getElementById("mensajes_div").innerHTML='<div id="GifProcess">Preparando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 15);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
                        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                var total_items=respuestas[2];
                var tipo_anexo=respuestas[3];                
                
                copiar_registros_anexo_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,1);
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


function copiar_registros_anexo_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,page){ 
    var idBoton="btn_construir_hoja_trabajo";
    var CmbIPS=document.getElementById('CmbIPS').value;
   
    var form_data = new FormData();
        form_data.append('Accion', 16);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        form_data.append('total_items', total_items);
        form_data.append('page', page);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
                $('#PgProgresoUp_insert').css('width','100%').attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP_insert').innerHTML="100% de los registros copiados";
                alertify.success(respuestas[1]);
                
                document.getElementById("mensajes_div").innerHTML="Copia terminada";
                document.getElementById(idBoton).disabled=false;
                contar_registros_hoja_trabajo(hoja_trabajo_id);
            }else if(respuestas[0]==="UP"){
                document.getElementById("mensajes_div").innerHTML=respuestas[1];
                var next_page=respuestas[2];
                var porcentaje=respuestas[3];
                $('#PgProgresoUp_insert').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoUP_insert').innerHTML=porcentaje+"% de registros insertados";
                
                copiar_registros_anexo_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,next_page);
                
            }else if(respuestas[0]==="E1"){
                document.getElementById(idBoton).disabled=false;
                alertify.alert(respuestas[1]);
                
                return;                
            }else{
                document.getElementById(idBoton).disabled=false;
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


function confirma_actualizar_hoja_trabajo(hoja_trabajo_id){
    
    alertify.confirm('Está seguro que desea Actualizar la hoja de trabajo?</strong>',
        function (e) {
            if (e) {
             
                contar_registros_hoja_trabajo(hoja_trabajo_id);
                
            }else{
                alertify.error("Se canceló el proceso");

                return;
            }
        });
}


function contar_registros_hoja_trabajo(hoja_trabajo_id){ 
    var idBoton="btn_actualizar_hoja_trabajo";    
    document.getElementById(idBoton).disabled=true;
    document.getElementById("mensajes_div").innerHTML='<div id="GifProcess">Preparando...<br><img   src="../../images/loader.gif" alt="Cargando" height="100" width="100"></div>';
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 17);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
                        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
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
                var total_items=respuestas[2];
                var tipo_anexo=respuestas[3];                
                
                actualizar_registros_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,1);
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


function actualizar_registros_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,page){ 
    var idBoton="btn_actualizar_hoja_trabajo";
    var CmbIPS=document.getElementById('CmbIPS').value;
   
    var form_data = new FormData();
        form_data.append('Accion', 18);
        form_data.append('CmbIPS', CmbIPS);
        form_data.append('tipo_anexo', tipo_anexo);
        form_data.append('hoja_trabajo_id', hoja_trabajo_id);
        form_data.append('total_items', total_items);
        form_data.append('page', page);
        
    $.ajax({
        //async:false,
        url: './procesadores/auditoria.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
           if(respuestas[0]==="OK"){ 
                $('#PgProgresoUp_update').css('width','100%').attr('aria-valuenow', 100);  
                document.getElementById('LyProgresoUP_update').innerHTML="100% de los registros actualizados";
                alertify.success(respuestas[1]);
                
                document.getElementById("mensajes_div").innerHTML="Actualización terminada";
                document.getElementById(idBoton).disabled=false;
            }else if(respuestas[0]==="UP"){
                document.getElementById("mensajes_div").innerHTML=respuestas[1];
                var next_page=respuestas[2];
                var porcentaje=respuestas[3];
                $('#PgProgresoUp_update').css('width',porcentaje+'%').attr('aria-valuenow', porcentaje);  
                document.getElementById('LyProgresoUP_update').innerHTML=porcentaje+"% de registros actualizados";
                
                actualizar_registros_hoja_trabajo(hoja_trabajo_id,tipo_anexo,total_items,next_page);
                
            }else if(respuestas[0]==="E1"){
                document.getElementById(idBoton).disabled=false;
                alertify.alert(respuestas[1]);
                
                return;                
            }else{
                document.getElementById(idBoton).disabled=false;
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

function frm_percapita(idContrato){
        
    AbreModal('ModalAcciones');
        
    var form_data = new FormData();
        form_data.append('Accion', 7);
        form_data.append('idContrato', idContrato);   
        
        
        $.ajax({
        url: 'Consultas/auditoria.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
           document.getElementById('DivFrmModalAcciones').innerHTML=data;
           $('#CmbMunicipioPercapita').select2({
		
                placeholder: 'Seleccione un municipio',
                ajax: {
                  url: 'buscadores/municipios.search.php',
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
            
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


function create_tables(){
    create_tables_module();
    
}

module_init();
listar_hojas_trabajo();
document.getElementById('BtnMuestraMenuLateral').click();

