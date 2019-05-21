/**
 * Controlador para realizar las cotizaciones
 * JULIAN ALVARAN 2019-02-21
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


/*
 * Abre el modal para registrar la nueva cotizacion
 * @returns {undefined}
 */

function AbrirModalNuevaCotizacion(Proceso="Nuevo"){
    $("#ModalAccionesCotizaciones").modal();
    var idCotizacion=document.getElementById('idCotizacion').value;
    
    var form_data = new FormData();
        if(Proceso=="Nuevo"){
            var Accion=1;
        }
        if(Proceso=="Editar"){
            var Accion=2;
            
        }
        form_data.append('Accion', Accion);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivFrmCrearCotizacion').innerHTML=data;
            $('#CmbTercero').select2({
		  
                placeholder: 'Selecciona un Tercero',
                ajax: {
                  url: 'buscadores/clientes.search.php',
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
      })  
}  
/**
 * Crear una compra
 * @returns {undefined}
 */
function CrearCotizacion(event){
    event.preventDefault();
    var idCotizacionActiva=document.getElementById('idCotizacion').value;
    var Accion=document.getElementById('idAccion').value;
    
    var Fecha = document.getElementById('TxtFecha').value;
    var Tercero = document.getElementById('CmbTercero').value;
    
    var Observaciones = document.getElementById('TxtObservaciones').value;
    
    
    if(Tercero==""){
        alertify.alert("Debe seleccionar un tercero");
        document.getElementById('select2-CmbTercero-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbTercero-container').style.backgroundColor="white";
    }
    
    if(Fecha==""){
        alertify.alert("Debe seleccionar una fecha");
        document.getElementById('TxtFecha').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtFecha').style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', Accion);
        
        form_data.append('Fecha', Fecha);
        form_data.append('Tercero', Tercero);
        form_data.append('idCotizacionActiva', idCotizacionActiva);
        form_data.append('Observaciones', Observaciones);
            
        document.getElementById('CmbTercero').value='';
        CierraModal('ModalAccionesCotizaciones');
    
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          var respuestas = data.split(';'); 
          if (respuestas[0] == "OK") { 
              if(Accion==1){
                var idCotizacion=respuestas[1];
                alertify.success("Cotizacion "+idCotizacion+" creada");
                var x = document.getElementById("idCotizacion");
                  var option = document.createElement("option");
                  option.text = idCotizacion;
                  option.value = idCotizacion;

                  x.add(option); 
                  $("#idCotizacion option:last").attr('selected','selected');
                  DibujeCotizacion();
              }  
              if(Accion==2){
                  var index = document.getElementById("idCotizacion").selectedIndex;
                  var TextoOpcion=idCotizacionActiva+" "+respuestas[1];
                  document.getElementById("idCotizacion").options[index].text=TextoOpcion;
                  alertify.success("Cotizacion "+idCotizacionActiva+" Editada");
              }
              
          }else{
              alertify.error("Error al crear o editar la cotizacion");
              document.getElementById('DivMensajesModulo').innerHTML=data;
          }
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
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
/**
 * Funcion para dibujar todos los componentes de una compra
 * @param {type} idCompra
 * @returns {undefined}
 */
function DibujeCotizacion(idCotizacion=""){
    if(document.getElementById('idCotizacion').value==""){
        document.getElementById('BtnEditar').disabled=true;
    }else{
        document.getElementById('BtnEditar').disabled=false;
    }
    if(idCotizacion==""){
        var idCotizacion = document.getElementById('idCotizacion').value;
        
    }
    
    DibujeItems(idCotizacion);
    DibujeTotales(idCotizacion);
}


/**
 * Se dibujan items de una cotizacion
 * @param {type} idCompra
 * @returns {undefined}
 */
function DibujeItems(idCotizacion=""){
    if(idCotizacion==""){
        var idCotizacion = document.getElementById('idCotizacion').value;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivItems').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
      
      
}


/**
 * cambia el select para realizar busquedas según el listado seleccionado
 * @returns {undefined}
 */
function ConvertirSelectBusquedas(){
    var Listado=document.getElementById('CmbListado').value;
    if(Listado==1){ //Opcion para buscar un producto
        document.getElementById('CmbBusquedas').value="";
        
        document.getElementById('ValorUnitario').value="";
        document.getElementById('Cantidad').value=1;
        
        $('#CmbBusquedas').select2({
		  
            placeholder: 'Selecciona un producto',
            ajax: {
              url: 'buscadores/productosventa.search.php',
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
    
    
    if(Listado==2){ //Opcion para buscar un servicio
        document.getElementById('CmbBusquedas').value="";
        
        document.getElementById('ValorUnitario').value="";
        document.getElementById('Cantidad').value=1;
        
        $('#CmbBusquedas').select2({
            
            placeholder: 'Selecciona un servicio',
            ajax: {
              url: 'buscadores/servicios.search.php',
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
    
    if(Listado==3){ //Opcion para buscar un producto en alquiler
        document.getElementById('CmbBusquedas').value="";        
        document.getElementById('ValorUnitario').value="";
        document.getElementById('Cantidad').value=1;
        
        $('#CmbBusquedas').select2({
            
            placeholder: 'Buscar producto para alquilar',
            ajax: {
              url: 'buscadores/productosalquiler.search.php',
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
}
/**
 * Agrega un item a una FC
 * @returns {undefined}
 */
function AgregarItem(){
    
    var idCotizacion=document.getElementById('idCotizacion').value;
    var CmbListado=document.getElementById('CmbListado').value;
    var CmbBusquedas=document.getElementById('CmbBusquedas').value;    
    
    var Cantidad = (document.getElementById('Cantidad').value);
    var ValorUnitario = (document.getElementById('ValorUnitario').value);
   console.log(ValorUnitario)
    if(idCotizacion==""){
        alertify.alert("Debe Seleccionar una cotizacion");
        document.getElementById('idCotizacion').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('idCotizacion').style.backgroundColor="white";
    }
    
    if(CmbBusquedas==""){
        alertify.alert("Debe Seleccionar un item");
        document.getElementById('select2-CmbBusquedas-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbBusquedas-container').style.backgroundColor="white";
    }
        
    if(!$.isNumeric(Cantidad) || Cantidad == "" || Cantidad <= 0){
        alertify.alert("El campo Cantidad debe ser un número mayor a cero");
        document.getElementById('Cantidad').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('Cantidad').style.backgroundColor="white";
    }
    
    if(!$.isNumeric(ValorUnitario) || ValorUnitario == "" || ValorUnitario<=0){
        alertify.alert("El campo Valor Unitario debe ser un número mayor a cero");
        document.getElementById('ValorUnitario').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('ValorUnitario').style.backgroundColor="white";
    }
       
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idCotizacion', idCotizacion);
        form_data.append('CmbListado', CmbListado);
        form_data.append('CmbBusquedas', CmbBusquedas);
        
        form_data.append('Cantidad', Cantidad);
        form_data.append('ValorUnitario', ValorUnitario);
         document.getElementById('ValorUnitario').value="";   
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          
          if (data == "OK") { 
              
                alertify.success("Item "+CmbListado+" Agregado");
                DibujeCotizacion(idCotizacion);
          
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

/**
 * Elimina un item de una factura de compra
 * @param {type} Tabla
 * @param {type} idItem
 * @returns {undefined}
 */
function EliminarItem(Tabla,idItem){
        
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('Tabla', Tabla);
        form_data.append('idItem', idItem);
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.error(data);
            DibujeCotizacion();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}


/**
 * Se dibujan los totales generales de una compra 
 * @param {type} idCompra
 * @returns {undefined}
 */
function DibujeTotales(idCotizacion=""){
    if(idCotizacion==""){
        var idCotizacion = document.getElementById('idCotizacion').value;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivTotales').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
      
      
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
 * Agrega los cargos al subtotal de los insumos
 * @param {type} event
 * @returns {undefined}
 */
function GuardarDocumento(idCotizacion=''){
    document.getElementById('BtnGuardarDocumento').disabled=true;
    if(idCotizacion==''){
        var idCotizacion = document.getElementById('idCotizacion').value;
    }
        
    
    var form_data = new FormData();
        form_data.append('Accion', '6'); 
        form_data.append('idCotizacion', idCotizacion);
        
        
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];
                LimpiarDivs();
                var x = document.getElementById("idCotizacion");
                x.remove(x.selectedIndex);
                document.getElementById('BtnEditar').disabled=true;
                alertify.alert(mensaje);
                
            }else{
                alertify.alert("Error: <br>"+data);
                document.getElementById('BtnGuardarDocumento').disabled=false;
            }
            
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}
/**
 * Limpia los divs de la compra despues de guardar
 * @returns {undefined}
 */
function LimpiarDivs(){
    document.getElementById('DivItems').innerHTML='';
    document.getElementById('DivTotales').innerHTML='';
}
/**
 * Busca el precio de venta de un producto
 * @returns {undefined}
 */
function BusquePrecioVenta(){
   
    var listado = document.getElementById('CmbListado').value;
    var CmbBusquedas = document.getElementById('CmbBusquedas').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('listado', listado);
        form_data.append('CmbBusquedas', CmbBusquedas);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            
            document.getElementById('ValorUnitario').value=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
      
}
/**
 * Edita un item
 * @param {type} idItem
 * @param {type} idCompra
 * @returns {undefined}
 */
function EditarItem(idItem){
    
    var idBotonEditar="BtnEditar_"+idItem;
    var idCantidad="TxtCantidad_"+idItem;
    var idMultiplicador="TxtMultiplicador_"+idItem;
    var idValorUnitario="TxtValorUnitario_"+idItem;
    
    document.getElementById(idBotonEditar).disabled=true;
    document.getElementById(idBotonEditar).value="Editando...";
    
    var idCotizacion = document.getElementById('idCotizacion').value;
    
        
    var Cantidad = document.getElementById(idCantidad).value;
    var Multiplicador = document.getElementById(idMultiplicador).value;
    var ValorUnitario = document.getElementById(idValorUnitario).value;
    
    
    
    if(!$.isNumeric(Cantidad) || Cantidad == "" || Cantidad<=0){
        alertify.alert("La Cantidad debe ser un número mayor a cero");
        document.getElementById(idCantidad).style.backgroundColor="pink";
        document.getElementById(idBotonEditar).disabled=false;
        document.getElementById(idBotonEditar).value="Editar";
        return;
    }else{
        document.getElementById(idCantidad).style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Multiplicador) || Multiplicador == "" || Multiplicador<=0){
        alertify.alert("El Multiplicador debe ser un número mayor a cero");
        document.getElementById(idMultiplicador).style.backgroundColor="pink";
        document.getElementById(idBotonEditar).disabled=false;
        document.getElementById(idBotonEditar).value="Editar";
        return;
    }else{
        document.getElementById(idMultiplicador).style.backgroundColor="white";
    }
    
    if(!$.isNumeric(ValorUnitario) || ValorUnitario == "" || ValorUnitario<=0){
        alertify.alert("El Valor Unitario debe ser un número mayor a cero");
        document.getElementById(idValorUnitario).style.backgroundColor="pink";
        document.getElementById(idBotonEditar).disabled=false;
        document.getElementById(idBotonEditar).value="Editar";
        return;
    }else{
        document.getElementById(idValorUnitario).style.backgroundColor="white";
    }
    
   
    var form_data = new FormData();
        form_data.append('Accion', '7'); 
        form_data.append('idItem', idItem);
        form_data.append('ValorUnitario', ValorUnitario);
        form_data.append('Multiplicador', Multiplicador);
        form_data.append('Cantidad', Cantidad);
        
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];
                
                alertify.success(mensaje);
                
            }else{
                alertify.alert("Error: <br>"+data);
                
            }
            
            DibujeCotizacion(idCotizacion);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/*
 * Abre el modal para registrar un anticipo a una cotizacion
 * @returns {undefined}
 */
function AbrirModalAnticipo(){
    
    $("#ModalAccionesGrande").modal();
    var idCotizacion=document.getElementById('idCotizacion').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 6);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivFrmModalAccionesGrande').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  

/*
 * Abre el modal para registrar un anticipo a una cotizacion
 * @returns {undefined}
 */
function AbrirModalFacturarCotizacion(){
    
    $("#ModalAccionesGrande").modal();
    var idCotizacion=document.getElementById('idCotizacion').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 7);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './Consultas/Cotizaciones.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivFrmModalAccionesGrande').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  
/**
 * Selecciona la accion que se realizará al momento de dar click en el boton de guardar en el modal
 * @returns {undefined}
 */
function SeleccioneAccionFormularios(){
    document.getElementById("BntModalAcciones").disabled=true;
    document.getElementById("BntModalAcciones").value="Guardando...";
    var idFormulario=document.getElementById('idFormulario').value; //determina el tipo de formulario que se va a guardar
    
    if(idFormulario==1){
        GuardarAnticipo();
    }
    
    if(idFormulario==2){
        ConviertaCotizacionEnFactura();
    }
    
    if(idFormulario==100){
        CrearTercero('ModalAccionesGrande','BntModalAcciones');
    }
}

/**
 * Guarda el anticipo a una cotizacion
 * @returns {undefined}
 */
function GuardarAnticipo(){
       
    var idCotizacion = document.getElementById('idCotizacion').value;       
    var TxtFechaAnticipo = document.getElementById('TxtFechaAnticipo').value;
    var TxtAnticipo = document.getElementById('TxtAnticipo').value;
    var CmbCuentaIngreso = document.getElementById('CmbCuentaIngreso').value;
    var TotalCotizacion = document.getElementById('TxtTotalDocumento').value;
    var TotalAnticipos = document.getElementById('TxtTotalAnticipos').value;
    var MaxAnticipo = TotalCotizacion-TotalAnticipos;
    //console.log("Anticipo "+TxtAnticipo+"; TotalCotizacion: "+TotalCotizacion+"; Total Anticipos: "+TotalAnticipos+";MaxAnticipo "+MaxAnticipo)
    if(!$.isNumeric(TxtAnticipo) || TxtAnticipo == "" || TxtAnticipo<=0){
        alertify.alert("El Anticipo debe ser un número mayor a cero");
        document.getElementById("TxtAnticipo").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        document.getElementById("BntModalAcciones").value="Guardar";
        return;
    }else{
        document.getElementById("TxtAnticipo").style.backgroundColor="white";
    }
    
    if( TxtAnticipo > MaxAnticipo){
        alertify.alert("La suma de los anticipos no puede ser mayor a "+TotalCotizacion);
        document.getElementById("TxtAnticipo").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        document.getElementById("BntModalAcciones").value="Guardar";
        return;
    }else{
        document.getElementById("TxtAnticipo").style.backgroundColor="white";
    }
    
    if( TxtFechaAnticipo == ''){
        alertify.alert("El campo Fecha no puede estar vacío");
        document.getElementById("TxtFechaAnticipo").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        document.getElementById("BntModalAcciones").value="Guardar";
        return;
    }else{
        document.getElementById("TxtFechaAnticipo").style.backgroundColor="white";
    }
    
    CierraModal('ModalAccionesGrande');
    var form_data = new FormData();
        form_data.append('Accion', '8'); 
        form_data.append('idCotizacion', idCotizacion);
        form_data.append('TxtFechaAnticipo', TxtFechaAnticipo);
        form_data.append('CmbCuentaIngreso', CmbCuentaIngreso);
        form_data.append('TxtAnticipo', TxtAnticipo);
        
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];
                
                alertify.alert(mensaje);
                document.getElementById("BntModalAcciones").disabled=false;
                document.getElementById("BntModalAcciones").value="Guardar";
            }else{
                alertify.alert("Error: <br>"+data);
                document.getElementById("BntModalAcciones").disabled=false;
                document.getElementById("BntModalAcciones").value="Guardar";
            }
            
            DibujeCotizacion(idCotizacion);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById("BntModalAcciones").disabled=false;
            document.getElementById("BntModalAcciones").value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}


/**
 * Convierte una cotizacion en una factura
 * @returns {undefined}
 */
function ConviertaCotizacionEnFactura(){
       
    var idCotizacion = document.getElementById('idCotizacion').value;       
    var TxtFechaFactura = document.getElementById('TxtFechaFactura').value;
    var CmbCentroCostosFactura = document.getElementById('CmbCentroCostosFactura').value;
    var CmbResolucion = document.getElementById('CmbResolucion').value;
    var CmbFormaPago = document.getElementById('CmbFormaPago').value;
    var CmbFrecuente = document.getElementById('CmbFrecuente').value;
    var CmbCuentaIngresoFactura = document.getElementById('CmbCuentaIngresoFactura').value;
    var CmbColaboradores = document.getElementById('CmbColaboradores').value;
    var TxtObservacionesFactura = document.getElementById('TxtObservacionesFactura').value;
    var AnticiposCruzados = parseFloat(document.getElementById('AnticiposCruzados').value);
    var TxtTotalFactura = parseFloat(document.getElementById('TxtTotalFactura').value);
    var TxtTotalAnticipos = parseFloat(document.getElementById('TxtTotalAnticiposFactura').value);
    
    var CmbEmpresa = document.getElementById('CmbEmpresa').value;
    var CmbSucursal = document.getElementById('CmbSucursal').value;
    //console.log(AnticiposCruzados+" "+TxtTotalFactura+" "+TxtTotalAnticipos)    
    if( TxtFechaFactura == ''){
        alertify.alert("El campo Fecha no puede estar vacío");
        document.getElementById("TxtFechaFactura").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        document.getElementById("BntModalAcciones").value="Guardar";
        return;
    }else{
        document.getElementById("TxtFechaFactura").style.backgroundColor="white";
    }
    
    if(!$.isNumeric(AnticiposCruzados) ||  AnticiposCruzados<0){
        
        alertify.alert("El Anticipo debe ser un número mayor o igual a cero");
        document.getElementById("AnticiposCruzados").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        return;
    }else{
        document.getElementById("AnticiposCruzados").style.backgroundColor="white";
    }
    
    if(TxtTotalAnticipos < AnticiposCruzados){
        
        alertify.alert("El Anticipo no puede ser mayor al total de anticipos realizados por el Cliente");
        document.getElementById("AnticiposCruzados").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        return;
    }else{
        document.getElementById("AnticiposCruzados").style.backgroundColor="white";
    }
    
    if( AnticiposCruzados > TxtTotalFactura){
        alertify.alert("El Anticipo no puede ser mayor al total de la Factura");
        document.getElementById("AnticiposCruzados").style.backgroundColor="pink";
        document.getElementById("BntModalAcciones").disabled=false;
        return;
    }else{
        document.getElementById("AnticiposCruzados").style.backgroundColor="white";
    }
    
    
    var form_data = new FormData();
        form_data.append('Accion', '9'); 
        form_data.append('idCotizacion', idCotizacion);
        form_data.append('TxtFechaFactura', TxtFechaFactura);
        form_data.append('CmbCentroCostosFactura', CmbCentroCostosFactura);
        form_data.append('CmbResolucion', CmbResolucion);
        form_data.append('CmbFormaPago', CmbFormaPago);
        form_data.append('CmbFrecuente', CmbFrecuente);
        form_data.append('CmbCuentaIngresoFactura', CmbCuentaIngresoFactura);
        form_data.append('CmbColaboradores', CmbColaboradores);
        form_data.append('TxtObservacionesFactura', TxtObservacionesFactura);
        form_data.append('CmbEmpresa', CmbEmpresa);
        form_data.append('CmbSucursal', CmbSucursal);
        form_data.append('AnticiposCruzados', AnticiposCruzados);
        AnticiposCruzados=0;
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="OK"){
                var mensaje=respuestas[1];                
                alertify.alert(mensaje);
                document.getElementById("BntModalAcciones").disabled=false;
                document.getElementById("BntModalAcciones").value="Guardar";
                CierraModal('ModalAccionesGrande');
                
            }else if(respuestas[0]=="E1"){
                alertify.error("Error: La Resolución seleccionada ya está Completada",0);
                document.getElementById("BntModalAcciones").disabled=false;
                document.getElementById("BntModalAcciones").value="Guardar";
            }else if(respuestas[0]=="E2"){
                alertify.error("Error: La Resolución seleccionada Está Ocupada, intentelo nuevamente",0);
                document.getElementById("BntModalAcciones").disabled=false;  
                document.getElementById("BntModalAcciones").value="Guardar";
            }else if(respuestas[0]=="E3"){
                alertify.error("Error: El Cliente ya no cuenta con el saldo en anticipos escrito",0);
                document.getElementById("BntModalAcciones").disabled=false;  
                document.getElementById("BntModalAcciones").value="Guardar";    
            }else{
                alertify.alert("Error: <br>"+data);
                document.getElementById("BntModalAcciones").disabled=false;
                document.getElementById("BntModalAcciones").value="Guardar";
            }
            
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            document.getElementById("BntModalAcciones").disabled=false;
            document.getElementById("BntModalAcciones").value="Guardar";
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/*
 * Abre una cotización
 * @returns {undefined}
 */
function AbrirCotizacion(){
    
    var idCotizacion=document.getElementById('idCotizacionAcciones').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 10);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="SD"){
                alertify.alert("La cotización digitada no existe");
            }
            if(respuestas[0]=="AB"){
                alertify.alert("La cotización ya está abierta");
            }
            if(respuestas[0]=="OK"){
                var x = document.getElementById("idCotizacion");
                var option = document.createElement("option");
                option.text = idCotizacion+" "+respuestas[1];
                option.value = idCotizacion;
                x.add(option); 
                $("#idCotizacion option:last").attr('selected','selected');
                DibujeCotizacion();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  


/*
 * Abre una cotización
 * @returns {undefined}
 */
function ClonarCotizacion(){
    
    var idCotizacion=document.getElementById('idCotizacionAcciones').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 11);
        form_data.append('idCotizacion', idCotizacion);
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="SD"){
                alertify.alert("La cotización digitada no existe");
            }
            
            if(respuestas[0]=="OK"){
                var x = document.getElementById("idCotizacion");
                var option = document.createElement("option");
                option.text = respuestas[1]+" "+respuestas[2];
                option.value = respuestas[1];
                x.add(option); 
                $("#idCotizacion option:last").attr('selected','selected');
                alertify.success("La cotización "+idCotizacion+" ha sido Clonada Satisfactoriamente");
                DibujeCotizacion();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  


/*
 * Copia los items de una cotización a otra
 * @returns {undefined}
 */
function CopiarCotizacion(){
    
    var idCotizacion=document.getElementById('idCotizacionAcciones').value;
    var idCotizacionActual=document.getElementById('idCotizacion').value;
    
    if(idCotizacionActual==""){
        alertify.alert("Debe Seleccionar una cotizacion");
        document.getElementById('idCotizacion').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('idCotizacion').style.backgroundColor="white";
    }
    var form_data = new FormData();
        
        form_data.append('Accion', 12);
        form_data.append('idCotizacion', idCotizacion);
        form_data.append('idCotizacionActual', idCotizacionActual);
        $.ajax({
        url: './procesadores/Cotizaciones.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';'); 
            if(respuestas[0]=="SD"){
                alertify.alert("La cotización digitada no existe");
            }
            
            if(respuestas[0]=="OK"){
                
                alertify.success("Los Items de la Cotización "+idCotizacion+" han sido Copiados Satisfactoriamente");
                DibujeCotizacion();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}  

ConvertirSelectBusquedas();

$('#CmbBusquedas').bind('change', function() {
    
    BusquePrecioVenta();
    
});


$('#idCotizacion').select2({
		  
    placeholder: 'Selecciona una Cotización',
    ajax: {
      url: 'buscadores/cotizaciones.search.php',
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

//$('#ValorUnitario').mask('1.999.999.##0,00', {reverse: true});
//$('#Cantidad').mask('9.999.##0,00', {reverse: true});