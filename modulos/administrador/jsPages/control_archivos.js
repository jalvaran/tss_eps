/**
 * Controlador para realizar la administracion de la plataforma
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

SeleccioneTablaDB(`vista_consolidado_control_eps`);

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
document.getElementById('BtnMuestraMenuLateral').click();

function ListaIPSAsignar(idUsuario){
        
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('idUsuario', idUsuario);
        $.ajax({
        url: './Consultas/usuarios.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivOpcionesUsuarios').innerHTML=data;
            $('#ips').select2({
		  
                placeholder: 'Selecciona la o las IPS a Asociar',
                ajax: {
                  url: 'buscadores/ips.search.php',
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


function RegistrarIPSUsuario(){
    var idUsuario=document.getElementById('TxtUsuarioSeleccionado').value;
   
        if($('#ips').val()==null || $('#ips').val()==''){
              alertify.alert("por favor seleccione una o varias ips");          
              return;
        } 
   
    document.getElementById('btnAsigarIPS').disabled=true;
    
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('idUsuario', idUsuario);
        
        form_data.append('ips', $('#ips').val());
      
    $.ajax({
        //async:false,
        url: './procesadores/usuarios.process.php',
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
                ListaIPSAsignar(idUsuario);
            }else if(respuestas[0]==="E1"){
                alertify.error(respuestas[1]);
                
            }else{
                
                alertify.alert(data);
                
            }
            document.getElementById('btnAsigarIPS').disabled=false;
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById('btnAsigarIPS').disabled=false;
            alert(xhr.status);
            alert(thrownError);
          }
      })
}

function EliminarItem(Tabla,idItem){
    var idUsuario=document.getElementById('TxtUsuarioSeleccionado').value;    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('Tabla', Tabla);
        form_data.append('idItem', idItem);
        $.ajax({
        url: './procesadores/usuarios.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.error(data);
            ListaIPSAsignar(idUsuario);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
