/**
 * Controlador para realizar la administracion de los tickets
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

var general_div="general_div";

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

function create_tables_anexos_evento(){ 
    
    var CmbIPS=document.getElementById('CmbIPS').value;
    var form_data = new FormData();
        form_data.append('Accion', 2);
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
    create_tables_anexos_evento();
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
                
                document.getElementById("mensajes_div").innerHTML="registros actualizados";
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


module_init();

document.getElementById('BtnMuestraMenuLateral').click();

