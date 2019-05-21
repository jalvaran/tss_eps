/**
 * Controlador para el Recibo de las Ordenes de Compra
 * JULIAN ALVARAN 2018-12-05
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */


/*
 * Dibuja los Items de una Orden de Compra
 * @returns {undefined}
 */
function DibujeOrdenCompra(){
    var idOrden = document.getElementById('CmbOrdenCompra').value;
    if(idOrden==""){
        alertify.alert("Debe seleccionar una Orden de Compra");
        return;
    }
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('idOrden', idOrden);
        $.ajax({
        url: './Consultas/ReciboOrdenCompra.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data != "") { 
              document.getElementById('DivItemsOrden').innerHTML=data;
              
          }else {
            alertify.alert("No hay resultados para la consulta");
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })        
}  
/**
 * Busca un articulo por codigo de barras y si lo encuentra lo agrega
 * @returns {undefined}
 */
function BuscarXCodigo(){
    var idOrden = document.getElementById('CmbOrdenCompra').value;
    if(idOrden==""){
        alertify.alert("Debe seleccionar una Orden de Compra");
        return;
    }
    var TxtCodigoBarras = document.getElementById('TxtCodigoBarras').value;
    var form_data = new FormData();
        form_data.append('Accion', 1);
        form_data.append('idOrden', idOrden);
        form_data.append('TxtCodigoBarras', TxtCodigoBarras);
        $.ajax({
        url: './procesadores/ReciboOrdenCompra.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           document.getElementById('TxtCodigoBarras').value="";
          if (data == "OK") { 
              alertify.success("Cantidad Agregada");
              DibujeOrdenCompra();
          }else if(data == "Error1"){ //No encuentra el producto en la orden de compra
              alertify.alert("El producto no está en la orden de compra, por favor no recibir");
          }else if(data == "Error2"){ //No encuentra el producto en la orden de compra
              alertify.alert("Ya se recibió la cantidad solicitada de este producto, por favor no recibir");    
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
 * Se edita una cantidad
 * @returns {undefined}
 */
function EditarCantidadRecibida(idItem,Max){
    var Cantidad = parseFloat(document.getElementById('TxtRecibido_'+idItem).value);
    if(isNaN(Cantidad) ){
        alertify.alert("La cantidad digitada No es un número, por favor digite un número válido");
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="white";
    }
    if(Cantidad<0){
        alertify.alert("La cantidad digitada no puede ser menor a cero");
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="white";
    }
    if(Cantidad>Max){
        alertify.alert("La cantidad digitada no puede ser mayor a la solicitada");
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="pink";
        return;
    }else{
        document.getElementById('TxtRecibido_'+idItem).style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        form_data.append('Accion', 2);
        form_data.append('idItem', idItem);
        form_data.append('Cantidad', Cantidad);
        $.ajax({
        url: './procesadores/ReciboOrdenCompra.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data == "OK") { 
              alertify.success("Cantidad Editada");
              DibujeOrdenCompra();
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
 * Guardar la Orden
 * @returns {undefined}
 */
function GuargarOrden(){
    var idOrden = document.getElementById('CmbOrdenCompra').value;
    if(idOrden==""){
        alertify.alert("Debe seleccionar una Orden de Compra");
        return;
    }
    
    alertify.confirm("Está seguro que desea Gurdar esta orden?",
    function (e) {
        if (e) {
    
    
    var form_data = new FormData();
        form_data.append('Accion', 3);
        form_data.append('idOrden', idOrden);
        
        $.ajax({
        url: './procesadores/ReciboOrdenCompra.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
           
          if (data == "OK") { 
              alertify.success("Orden de Compra Recibida");
              document.getElementById('DivItemsOrden').innerHTML="";
              BorrarSeleccionActual();
          
          }else{
              alertify.alert(data);
          }
          
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
  
    }else{
        alertify.error("Accion cancelada");
    }
    });
}

/**
 * Borra la opcion del Select de la orden seleccionada actualmente, 
 * @returns {undefined}
 */
function BorrarSeleccionActual() {

  var sel = document.getElementById("CmbOrdenCompra");
  sel.remove(sel.selectedIndex);

}