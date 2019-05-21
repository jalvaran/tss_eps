/**
 * Controlador para realizar las compras
 * JULIAN ALVARAN 2018-12-05
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


/*
 * Abre el modal para registrar la nueva compra
 * @returns {undefined}
 */
function AbrirModalNuevaCompra(Proceso="Nuevo"){
    $("#ModalAccionesCompras").modal();
    var idCompra=document.getElementById('idCompra').value;
    
    var form_data = new FormData();
        if(Proceso=="Nuevo"){
            var Accion=1;
        }
        if(Proceso=="Editar"){
            var Accion=2;
            
        }
        form_data.append('Accion', Accion);
        form_data.append('idCompra', idCompra);
        $.ajax({
        url: './Consultas/Compras.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivFormularioCrearCompra').innerHTML=data;
            $('#CmbTerceroCrearCompra').select2({
		  
                placeholder: 'Selecciona un Tercero',
                ajax: {
                  url: 'buscadores/proveedores.search.php',
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
function CrearCompra(event){
    event.preventDefault();
    var idCompraActiva=document.getElementById('idCompra').value;
    var Accion=document.getElementById('idAccion').value;
    
    var Fecha = document.getElementById('TxtFecha').value;
    var Tercero = document.getElementById('CmbTerceroCrearCompra').value;
    var ControCosto = document.getElementById('CmbCentroCosto').value;
    var Sucursal = document.getElementById('idSucursal').value;
    var TipoCompra = document.getElementById('TipoCompra').value;
    var Concepto = document.getElementById('TxtConcepto').value;
    var NumFactura = document.getElementById('TxtNumFactura').value;
    
    if(Tercero==""){
        alertify.alert("Debe seleccionar un tercero");
        document.getElementById('select2-CmbTerceroCrearCompra-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbTerceroCrearCompra-container').style.backgroundColor="white";
    }
    
    if(NumFactura==""){
        alertify.alert("Debe escribir el numero de comprobante");
        document.getElementById('TxtNumFactura').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtNumFactura').style.backgroundColor="white";
    }
    
    if(Concepto==""){
        alertify.alert("Debe especificar un concepto");
        document.getElementById('TxtConcepto').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtConcepto').style.backgroundColor="white";
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
        form_data.append('Soporte', $('#UpSoporte').prop('files')[0]);
        form_data.append('Fecha', Fecha);
        form_data.append('Tercero', Tercero);
        form_data.append('ControCosto', ControCosto);
        form_data.append('Sucursal', Sucursal);
        form_data.append('TipoCompra', TipoCompra);
        form_data.append('Concepto', Concepto);
        form_data.append('NumFactura', NumFactura);
        form_data.append('idCompraActiva', idCompraActiva);
    
        document.getElementById('CmbTerceroCrearCompra').value='';
        CierraModal('ModalAccionesCompras');
    
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                var idCompra=respuestas[1];
                alertify.success("Compra "+idCompra+" creada");
                var x = document.getElementById("idCompra");
                  var option = document.createElement("option");
                  option.text = idCompra+" "+Concepto+" "+NumFactura;
                  option.value = idCompra;

                  x.add(option); 
                  $("#idCompra option:last").attr('selected','selected');
                  DibujeCompra();
              }  
              if(Accion==2){
                  var index = document.getElementById("idCompra").selectedIndex;
                  var TextoOpcion=idCompraActiva+" "+respuestas[1]+" "+respuestas[2]+" "+respuestas[3];
                  document.getElementById("idCompra").options[index].text=TextoOpcion;
                  alertify.success("Compra "+idCompraActiva+" Editada");
              }
          
          }else{
              alertify.error("Error al crear la compra");
              document.getElementById('DivDatosGeneralesCompra').innerHTML=data;
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
function DibujeCompra(idCompra=""){
    if(document.getElementById('idCompra').value==""){
        document.getElementById('BtnEditarCompra').disabled=true;
    }else{
        document.getElementById('BtnEditarCompra').disabled=false;
    }
    if(idCompra==""){
        var idCompra = document.getElementById('idCompra').value;
        
    }
    
    DibujeItemsCompra(idCompra);
    DibujeTotalesCompra(idCompra);
}


/**
 * Se dibujan los datos generales de una compra 
 * @param {type} idCompra
 * @returns {undefined}
 */
function DibujeItemsCompra(idCompra=""){
    if(idCompra==""){
        var idCompra = document.getElementById('idCompra').value;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idCompra', idCompra);
        $.ajax({
        url: './Consultas/Compras.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivItemsCompra').innerHTML=data;
            
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
        document.getElementById('CodigoBarras').value="";
        document.getElementById('TxtDescripcion').value="";
        document.getElementById('ValorUnitario').value="";
        document.getElementById('PrecioVenta').value="";
        document.getElementById('Cantidad').value=1;
        document.getElementById('Cantidad').disabled=false;
        document.getElementById('TxtDescripcion').disabled=true;
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
        document.getElementById('CodigoBarras').value="";
        document.getElementById('TxtDescripcion').value="";
        document.getElementById('ValorUnitario').value="";
        document.getElementById('PrecioVenta').value="";
        document.getElementById('Cantidad').value=1;
        document.getElementById('Cantidad').disabled=true;
        document.getElementById('TxtDescripcion').disabled=false;
        $('#CmbBusquedas').select2({
            
            placeholder: 'Selecciona una cuenta PUC para este servicio',
            ajax: {
              url: 'buscadores/CuentaPUC.search.php',
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
    
    if(Listado==3){ //Opcion para buscar un insumo
        document.getElementById('CmbBusquedas').value="";
        document.getElementById('CodigoBarras').value="";
        document.getElementById('TxtDescripcion').value="";
        document.getElementById('ValorUnitario').value="";
        document.getElementById('PrecioVenta').value="";
        document.getElementById('Cantidad').value=1;
        document.getElementById('Cantidad').disabled=false;
        document.getElementById('TxtDescripcion').disabled=true;
        $('#CmbBusquedas').select2({
            
            placeholder: 'Buscar insumo',
            ajax: {
              url: 'buscadores/insumos.search.php',
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
    
    var idCompra=document.getElementById('idCompra').value;
    var CmbListado=document.getElementById('CmbListado').value;
    var CmbBusquedas=document.getElementById('CmbBusquedas').value;    
    var CmbImpuestosIncluidos = document.getElementById('CmbImpuestosIncluidos').value;
    var CmbTipoImpuesto = document.getElementById('CmbTipoImpuesto').value;
    var CodigoBarras = document.getElementById('CodigoBarras').value;
    var TxtDescripcion = document.getElementById('TxtDescripcion').value;
    var Cantidad = (document.getElementById('Cantidad').value);
    var ValorUnitario = (document.getElementById('ValorUnitario').value);
    var PrecioVenta = (document.getElementById('PrecioVenta').value);
    
    if(idCompra==""){
        alertify.alert("Debe Seleccionar una compra");
        document.getElementById('idCompra').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('idCompra').style.backgroundColor="white";
    }
    
    if(TxtDescripcion=="" && CmbListado==2){
        alertify.alert("El campo descripción no puede estar vacío");
        document.getElementById('TxtDescripcion').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtDescripcion').style.backgroundColor="white";
    }
    
    if(CmbListado==2 && CodigoBarras.length<6){
        alertify.alert("No puedes seleccionar una Cuenta Padre");
        document.getElementById('select2-CmbBusquedas-container').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('select2-CmbBusquedas-container').style.backgroundColor="white";
    }
    
    if(CodigoBarras==""){
        alertify.alert("El campo código no puede estar vacío");
        document.getElementById('CodigoBarras').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CodigoBarras').style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Cantidad) || Cantidad == "" || Cantidad <= 0 ){
    
        alertify.alert("El campo Cantidad debe ser un número mayor a cero");
        document.getElementById('Cantidad').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('Cantidad').style.backgroundColor="white";
    }
    
    if(!$.isNumeric(ValorUnitario) || ValorUnitario == "" || ValorUnitario <= 0 ){
    
        alertify.alert("El campo Valor Unitario debe ser un número mayor a cero");
        document.getElementById('ValorUnitario').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('ValorUnitario').style.backgroundColor="white";
    }
    
    
    if(!$.isNumeric(PrecioVenta) || PrecioVenta == "" ){
    
        alertify.alert("El campo precio de venta debe ser un número mayor a cero");
        document.getElementById('PrecioVenta').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('PrecioVenta').style.backgroundColor="white";
    }
    /*
    if((PrecioVenta<ValorUnitario) && CmbListado==1 ){
    
        alertify.alert("El Precio de Venta debe ser mayor al Costo Unitario");
        document.getElementById('PrecioVenta').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('PrecioVenta').style.backgroundColor="white";
    }
       */
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idCompra', idCompra);
        form_data.append('CmbListado', CmbListado);
        form_data.append('CmbBusquedas', CmbBusquedas);
        form_data.append('CmbImpuestosIncluidos', CmbImpuestosIncluidos);
        form_data.append('CmbTipoImpuesto', CmbTipoImpuesto);
        form_data.append('CodigoBarras', CodigoBarras);
        form_data.append('TxtDescripcion', TxtDescripcion);
        form_data.append('Cantidad', Cantidad);
        form_data.append('ValorUnitario', ValorUnitario);
        form_data.append('PrecioVenta', PrecioVenta);
        
         document.getElementById('ValorUnitario').value=""; 
         document.getElementById('PrecioVenta').value="";  
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
          
          if (data == "OK") { 
              
                alertify.success("Item "+CodigoBarras+" Agregado");
                DibujeCompra(idCompra);
          
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
 * Imprime un codigo de barras
 * @param {type} idProducto
 * @param {type} Cantidad
 * @returns {undefined}
 */
function PrintEtiqueta(idProducto,Cantidad=''){
    if(Cantidad==""){
        var Cantidad = document.getElementById('CantidadTiquetes').value;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idProducto', idProducto);
        form_data.append('Cantidad', Cantidad);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.success(data);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
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
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.error(data);
            DibujeCompra();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Devuelve un item
 * @param {type} idItem
 * @param {type} Cantidad
 * @returns {undefined}
 */
function DevolverItem(idItem,Cantidad=""){
    if(Cantidad==""){
        var Cantidad = parseFloat(document.getElementById('CantidadDevolucion').value);
    }  
        
    if(isNaN(Cantidad) || Cantidad<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById('CantidadDevolucion').style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('CantidadDevolucion').style.backgroundColor="white";
    }
    var form_data = new FormData();
        form_data.append('Accion', 6);        
        form_data.append('idItem', idItem);
        form_data.append('Cantidad', Cantidad);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            alertify.error(data);
            DibujeCompra();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}
/**
 * Muestra las opciones para agregar retenciones, descuentos o impuestos 
 * adicionales a alguno de los montos del total de una factura
 * @param {type} Monto
 * @returns {undefined}
 */
function MuestreOpcionesEnTotales(Opcion){
    if(Opcion==1){//Selector de Subtotal de productos
        var Selector = document.getElementById('CmbImpRetDesProductos').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro2").style.display="none";
            document.getElementById("DivImpRetDesPro3").style.display="none";
            document.getElementById("DivImpRetDesPro4").style.display="none";
        }else{//Selector de Impuestos de productos
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro2").style.display="block";
            document.getElementById("DivImpRetDesPro3").style.display="block";
            document.getElementById("DivImpRetDesPro4").style.display="block";
        }
    }
    
    if(Opcion==2){//Selector de IVA en Productos
        var Selector = document.getElementById('CmbImpuestosProductos').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro5").style.display="none";
            document.getElementById("DivImpRetDesPro6").style.display="none";
            document.getElementById("DivImpRetDesPro7").style.display="none";
        }else{
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro5").style.display="block";
            document.getElementById("DivImpRetDesPro6").style.display="block";
            document.getElementById("DivImpRetDesPro7").style.display="block";
        }
    }
    
    if(Opcion==3){//Selector de Subtotal de insumos
        var Selector = document.getElementById('CmbImpRetDesInsumos').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro8").style.display="none";
            document.getElementById("DivImpRetDesPro9").style.display="none";
            document.getElementById("DivImpRetDesPro10").style.display="none";
        }else{//Selector de Impuestos de productos
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro8").style.display="block";
            document.getElementById("DivImpRetDesPro9").style.display="block";
            document.getElementById("DivImpRetDesPro10").style.display="block";
        }
    }
    
    if(Opcion==4){//Selector de IVA en insumos
        var Selector = document.getElementById('CmbImpuestosInsumos').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro11").style.display="none";
            document.getElementById("DivImpRetDesPro12").style.display="none";
            document.getElementById("DivImpRetDesPro13").style.display="none";
        }else{
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro11").style.display="block";
            document.getElementById("DivImpRetDesPro12").style.display="block";
            document.getElementById("DivImpRetDesPro13").style.display="block";
        }
    }
    
    if(Opcion==5){//Selector de Subtotal de servicios
        var Selector = document.getElementById('CmbImpRetDesServicios').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro14").style.display="none";
            document.getElementById("DivImpRetDesPro15").style.display="none";
            document.getElementById("DivImpRetDesPro16").style.display="none";
        }else{//Selector de Subtotal de servicios
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro14").style.display="block";
            document.getElementById("DivImpRetDesPro15").style.display="block";
            document.getElementById("DivImpRetDesPro16").style.display="block";
        }
    }
    
    if(Opcion==6){//Selector de IVA en servicios
        var Selector = document.getElementById('CmbImpuestosServicios').value;
        if(Selector == ''){
           //document.getElementById("DivImpRetDesPro1").style.display="none";
            document.getElementById("DivImpRetDesPro17").style.display="none";
            document.getElementById("DivImpRetDesPro18").style.display="none";
            document.getElementById("DivImpRetDesPro19").style.display="none";
        }else{
            //document.getElementById("DivImpRetDesPro1").style.display="block";
            document.getElementById("DivImpRetDesPro17").style.display="block";
            document.getElementById("DivImpRetDesPro18").style.display="block";
            document.getElementById("DivImpRetDesPro19").style.display="block";
        }
    }
    
}
/**
 * Calcula el valor o porcentaje de una retencion o descuento
 * @param {type} Opcion
 * @returns {undefined}
 */
function CalculeRetencionDescuento(Opcion){
    
    //Se Calcula el valor de la retencion o descuento en el subtotal del producto
    // dependiendo del porcentaje digitado
    if(Opcion==1){
        
        var idInputPorcentaje="TxtCargosPorcentajeProductos";
        var idInputValor="TxtCargosValorProductos";
        var idBaseCalculo="TxtSubtotalProductos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor de la retencion o descuento en el subtotal del producto
    // dependiendo del valor digitado
    if(Opcion==2){
        
        var idInputPorcentaje="TxtCargosPorcentajeProductos";
        var idInputValor="TxtCargosValorProductos";
        var idBaseCalculo="TxtSubtotalProductos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
     //Se Calcula el valor del reteiva 
    // dependiendo del porcentaje digitado
    if(Opcion==3){
        
        var idInputPorcentaje="TxtCargosPorcentajeProductosImpuestos";
        var idInputValor="TxtCargosValorProductosImpuestos";
        var idBaseCalculo="TxtImpuestosProductos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor del reteiva de los productos
    // dependiendo del valor digitado
    if(Opcion==4){
        
        var idInputPorcentaje="TxtCargosPorcentajeProductosImpuestos";
        var idInputValor="TxtCargosValorProductosImpuestos";
        var idBaseCalculo="TxtImpuestosProductos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
    //Se Calcula el valor de la retencion o descuento en el subtotal de los insumos
    // dependiendo del porcentaje digitado
    if(Opcion==5){
        
        var idInputPorcentaje="TxtCargosPorcentajeInsumos";
        var idInputValor="TxtCargosValorInsumos";
        var idBaseCalculo="TxtSubtotalInsumos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor de la retencion o descuento en el subtotal del insumo
    // dependiendo del valor digitado
    if(Opcion==6){
        
        var idInputPorcentaje="TxtCargosPorcentajeInsumos";
        var idInputValor="TxtCargosValorInsumos";
        var idBaseCalculo="TxtSubtotalInsumos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
     //Se Calcula el valor del reteiva 
    // dependiendo del porcentaje digitado
    if(Opcion==7){
        
        var idInputPorcentaje="TxtCargosPorcentajeInsumosImpuestos";
        var idInputValor="TxtCargosValorInsumosImpuestos";
        var idBaseCalculo="TxtImpuestosInsumos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor del reteiva
    // dependiendo del valor digitado
    if(Opcion==8){
        
        var idInputPorcentaje="TxtCargosPorcentajeInsumosImpuestos";
        var idInputValor="TxtCargosValorInsumosImpuestos";
        var idBaseCalculo="TxtImpuestosInsumos";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
    
     //Se Calcula el valor de la retencion o descuento en el subtotal de los servicios
    // dependiendo del porcentaje digitado
    if(Opcion==9){
        
        var idInputPorcentaje="TxtCargosPorcentajeServicios";
        var idInputValor="TxtCargosValorServicios";
        var idBaseCalculo="TxtSubtotalServicios";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor de la retencion o descuento en el subtotal del servicios
    // dependiendo del valor digitado
    if(Opcion==10){
        
        var idInputPorcentaje="TxtCargosPorcentajeServicios";
        var idInputValor="TxtCargosValorServicios";
        var idBaseCalculo="TxtSubtotalServicios";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
     //Se Calcula el valor del reteiva  de servicios
    // dependiendo del porcentaje digitado
    if(Opcion==11){
        
        var idInputPorcentaje="TxtCargosPorcentajeServiciosImpuestos";
        var idInputValor="TxtCargosValorServiciosImpuestos";
        var idBaseCalculo="TxtImpuestosServicios";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Porcentaje = document.getElementById(idInputPorcentaje).value;
        var Valor = Porcentaje/100 * BaseCalculo;
        document.getElementById(idInputValor).value=Valor.toFixed(2);
    }
    //Se Calcula el valor del reteiva de servicios
    // dependiendo del valor digitado
    if(Opcion==12){
        
        var idInputPorcentaje="TxtCargosPorcentajeServiciosImpuestos";
        var idInputValor="TxtCargosValorServiciosImpuestos";
        var idBaseCalculo="TxtImpuestosServicios";
        var BaseCalculo = document.getElementById(idBaseCalculo).value;
        var Valor = document.getElementById(idInputValor).value;
        var Porcentaje = 100/BaseCalculo * Valor;
        document.getElementById(idInputPorcentaje).value=Porcentaje.toFixed(1);
    }
    
    
        
}
/**
 * Se dibujan los totales generales de una compra 
 * @param {type} idCompra
 * @returns {undefined}
 */
function DibujeTotalesCompra(idCompra=""){
    if(idCompra==""){
        var idCompra = document.getElementById('idCompra').value;
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 4);
        form_data.append('idCompra', idCompra);
        $.ajax({
        url: './Consultas/Compras.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById('DivTotalesCompra').innerHTML=data;
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
      
      
}

/**
 * Agrega los cargos al subtotal de los productos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosProductos(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpRetDesProductos";
    var idPorcentaje="TxtCargosPorcentajeProductos";
    var idValor="TxtCargosValorProductos";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 7); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/**
 * Agrega los cargos al iva de los productos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosProductosImpuestos(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpuestosProductos";
    var idPorcentaje="TxtCargosPorcentajeProductosImpuestos";
    var idValor="TxtCargosValorProductosImpuestos";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero y menor a 100");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 8); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}


/**
 * Agrega los cargos al subtotal de los insumos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosSubtotalInsumos(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpRetDesInsumos";
    var idPorcentaje="TxtCargosPorcentajeInsumos";
    var idValor="TxtCargosValorInsumos";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 7); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/**
 * Agrega los cargos al iva de los insumos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosInsumosImpuestos(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpuestosInsumos";
    var idPorcentaje="TxtCargosPorcentajeInsumosImpuestos";
    var idValor="TxtCargosValorInsumosImpuestos";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero y menor a 100");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 8); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/**
 * Agrega los cargos al subtotal de los insumos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosSubtotalServicios(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpRetDesServicios";
    var idPorcentaje="TxtCargosPorcentajeServicios";
    var idValor="TxtCargosValorServicios";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 7); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

/**
 * Agrega los cargos al iva de los insumos
 * @param {type} event
 * @returns {undefined}
 */
function AgregarCargosServiciosImpuestos(event){
    event.preventDefault();
    
    var idCompra = document.getElementById('idCompra').value;
    var idSelector="CmbImpuestosServicios";
    var idPorcentaje="TxtCargosPorcentajeServiciosImpuestos";
    var idValor="TxtCargosValorServiciosImpuestos";
        
    var Selector = document.getElementById(idSelector).selectedIndex;
    var CuentaPUC = document.getElementById(idSelector).value;
    var Porcentaje = parseFloat(document.getElementById(idPorcentaje).value);
    var Valor = parseFloat(document.getElementById(idValor).value);
    
    if(isNaN(Porcentaje) || Porcentaje<=0 || Porcentaje>100){
        alertify.alert("El campo debe ser un valor numerico mayor a cero y menor a 100");
        document.getElementById(idPorcentaje).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idPorcentaje).style.backgroundColor="white";
    }
    
    if(isNaN(Valor) || Valor<=0){
        alertify.alert("El campo debe ser un valor numerico mayor a cero");
        document.getElementById(idValor).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById(idValor).style.backgroundColor="white";
    }
    document.getElementById(idValor).value='';
    var form_data = new FormData();
        form_data.append('Accion', 8); 
        form_data.append('idCompra', idCompra);
        form_data.append('Selector', Selector);
        form_data.append('CuentaPUC', CuentaPUC);
        form_data.append('Porcentaje', Porcentaje);
        form_data.append('Valor', Valor);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=="OK"){
                alertify.success("Registro realizado");
            }else{
                alertify.error(data,10000);
            }
            
            DibujeTotalesCompra(idCompra);
            
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

function MuestraOcultaXIDCompras(id){
    
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
function GuardarCompra(idCompra=''){
    document.getElementById('BtnGuardarCompra').disabled=true;
    if(idCompra==''){
        var idCompra = document.getElementById('idCompra').value;
    }
        
    var CmbTipoPago = document.getElementById("CmbTipoPago").value;
    var CmbCuentaOrigen = document.getElementById("CmbCuentaOrigen").value;
    var CmbCuentaPUCCXP = document.getElementById("CmbCuentaPUCCXP").value;
    var TxtFechaProgramada = document.getElementById("TxtFechaProgramada").value;
    var CmbTraslado = document.getElementById("CmbTraslado").value;
    
    
    if(TxtFechaProgramada==''){
        alertify.alert("El campo fecha programada no puede estar vacío");
        document.getElementById("TxtFechaProgramada").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("TxtFechaProgramada").style.backgroundColor="white";
    }
    
    
    document.getElementById("TxtFechaProgramada").value='';
    var form_data = new FormData();
        form_data.append('Accion', '9'); 
        form_data.append('idCompra', idCompra);
        form_data.append('CmbTipoPago', CmbTipoPago);
        form_data.append('CmbCuentaOrigen', CmbCuentaOrigen);
        form_data.append('CmbCuentaPUCCXP', CmbCuentaPUCCXP);
        form_data.append('TxtFechaProgramada', TxtFechaProgramada);
        form_data.append('CmbTraslado', CmbTraslado);
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                var x = document.getElementById("idCompra");
                x.remove(x.selectedIndex);
                document.getElementById('BtnEditarCompra').disabled=true;
                alertify.alert(mensaje);
                
            }else{
                alertify.error(data,10000);
                document.getElementById('BtnGuardarCompra').disabled=false;
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
 * Copia los items desde una orden de compra
 * @param {type} idOrdenCompra
 * @returns {undefined}
 */
function CopiarItemsDesdeOrden(idOrdenCompra=''){
    var idCompra = document.getElementById('idCompra').value;
    if(idOrdenCompra==''){
        var idOrdenCompra = document.getElementById('idCompraAcciones').value;
    }
        
        
    if(idCompra==''){
        alertify.alert("Debes seleccionar una compra");
        document.getElementById("idCompra").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompra").style.backgroundColor="white";
    }
    
    if(idOrdenCompra==''){
        alertify.alert("Debes digitar una valor");
        document.getElementById("idCompraAcciones").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompraAcciones").style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', '10'); 
        form_data.append('idCompra', idCompra);
        form_data.append('idOrdenCompra', idOrdenCompra);
                
        document.getElementById("idCompraAcciones").value='';
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                alertify.error(data,10000);
                
            }
            DibujeCompra();
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

function CopiarItemsDesdeOrdenVerificada(idOrdenCompra=''){
    var idCompra = document.getElementById('idCompra').value;
    if(idOrdenCompra==''){
        var idOrdenCompra = document.getElementById('idCompraAcciones').value;
    }
        
        
    if(idCompra==''){
        alertify.alert("Debes seleccionar una compra");
        document.getElementById("idCompra").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompra").style.backgroundColor="white";
    }
    
    if(idOrdenCompra==''){
        alertify.alert("Debes digitar una valor");
        document.getElementById("idCompraAcciones").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompraAcciones").style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', '15'); 
        form_data.append('idCompra', idCompra);
        form_data.append('idOrdenCompra', idOrdenCompra);
                
        document.getElementById("idCompraAcciones").value='';
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                alertify.alert(data);
                
            }
            DibujeCompra();
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
    document.getElementById('DivItemsCompra').innerHTML='';
    document.getElementById('DivTotalesCompra').innerHTML='';
}

/**
 * Busca el precio de venta y costo de un producto
 * @returns {undefined}
 */
function BusquePrecioVentaCosto(){
   
    var listado = document.getElementById('CmbListado').value;
    var Codigo = document.getElementById('CodigoBarras').value;
    
    var form_data = new FormData();
        form_data.append('Accion', 5);
        form_data.append('listado', listado);
        form_data.append('Codigo', Codigo);
        $.ajax({
        url: './Consultas/Compras.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            console.log(data)
            var respuestas = data.split(';');
            if(respuestas[0]=='OK'){
                document.getElementById('ValorUnitario').value=respuestas[1];
                document.getElementById('PrecioVenta').value=respuestas[2];
            }else{
                alertify.alert("Error "+ data);
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
      
}

function AplicarDescuentoItem(idItem){
    var idCaja="TxtDescuentoItem_"+idItem;
    var idBoton="BtnEditarDescuento_"+idItem;
    var Descuento=document.getElementById(idCaja).value;
    var idCompra = document.getElementById('idCompra').value;
    document.getElementById(idBoton).disabled=true;  
        
    if(Descuento==''){
        alertify.alert("El campo Descuento no puede estar vacío");
        document.getElementById(idCaja).style.backgroundColor="pink";
        document.getElementById(idBoton).disabled=false;
        return;
    }else{
        document.getElementById(idCaja).style.backgroundColor="white";
    }
            
    var form_data = new FormData();
        form_data.append('Accion', '11'); 
        form_data.append('idCompra', idCompra);
        form_data.append('idItem', idItem);
        form_data.append('Descuento', Descuento);
                
        
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                
                
            }else if(respuestas[0]=="E1"){
                var mensaje=respuestas[1];
                alertify.error(mensaje);             
                
            }else{
                alertify.alert(data);
            }
            document.getElementById(idBoton).disabled=false;
            DibujeCompra();
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
}
/**
 * Copia una factura de compra
 * @param {type} idOrdenCompra
 * @returns {undefined}
 */
function CopiarFacturaCompra(idFacturaCopiar=''){
    var idCompra = document.getElementById('idCompra').value;
    if(idFacturaCopiar==''){
        var idFacturaCopiar = document.getElementById('idCompraAcciones').value;
    }
        
        
    if(idCompra==''){
        alertify.alert("Debes seleccionar una compra");
        document.getElementById("idCompra").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompra").style.backgroundColor="white";
    }
    
    if(idFacturaCopiar==''){
        alertify.alert("Debe digitar una valor");
        document.getElementById("idCompraAcciones").style.backgroundColor="pink";
        return;
    }else{
        document.getElementById("idCompraAcciones").style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', '12'); 
        form_data.append('idCompra', idCompra);
        form_data.append('idFacturaCopiar', idFacturaCopiar);
                
        document.getElementById("idCompraAcciones").value='';
        $.ajax({
        url: './procesadores/Compras.process.php',
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
                alertify.error(data,10000);
                
            }
            DibujeCompra();
            //DibujeTotalesCompra(idCompra);
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
    
    
}

function EditarCostoUnitario(idCaja,idTabla,idItem){
    
    var Valor = document.getElementById(idCaja).value;
    
    if(Valor==''){
        
        alertify.error("El valor no puede estar vacío");
        document.getElementById(idCaja).style.backgroundColor="pink"; 
        
        return;
    }else{
        document.getElementById(idCaja).style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Valor) ||  Valor<=0){
        
        alertify.error("El Valor debe se un número mayor a Cero");
        document.getElementById(idCaja).style.backgroundColor="pink";   
        
        return;
    }else{
        document.getElementById(idCaja).style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 13);
        form_data.append('idItem', idItem);
        form_data.append('Valor', Valor);
        form_data.append('idTabla', idTabla);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Valor Editado");
            }else{
                alertify.alert(data);
            }
            
            DibujeCompra();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function EditarCantidadItem(idCaja,idTabla,idItem){
    
    var Valor = document.getElementById(idCaja).value;
    
    if(Valor==''){
        
        alertify.error("El valor no puede estar vacío");
        document.getElementById(idCaja).style.backgroundColor="pink"; 
        
        return;
    }else{
        document.getElementById(idCaja).style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Valor) ||  Valor<=0){
        
        alertify.error("El Valor debe se un número mayor a Cero");
        document.getElementById(idCaja).style.backgroundColor="pink";   
        
        return;
    }else{
        document.getElementById(idCaja).style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 14);
        form_data.append('idItem', idItem);
        form_data.append('Valor', Valor);
        form_data.append('idTabla', idTabla);
        $.ajax({
        url: './procesadores/Compras.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            if(data=='OK'){
                alertify.success("Cantidad Editada");
            }else{
                alertify.alert(data);
            }
            
            DibujeCompra();
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      });
}

function SeleccioneAccionFormularios(){
    var Accion = document.getElementById("idFormulario").value;
        
    if(Accion==100){
        CrearTercero('ModalAcciones','BntModalAcciones');
    }
}

ConvertirSelectBusquedas();

$('#CmbBusquedas').bind('change', function() {
    
    document.getElementById('CodigoBarras').value = document.getElementById('CmbBusquedas').value;
    BusquePrecioVentaCosto();
    
});

document.getElementById('BtnMuestraMenuLateral').click();

//$('#ValorUnitario').mask('1.999.999.##0,00', {reverse: true});
//$('#Cantidad').mask('9.999.##0,00', {reverse: true});