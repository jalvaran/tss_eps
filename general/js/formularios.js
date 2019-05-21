/**
 * Controlador para realizar cargar y guardar formularios que se usaran en varios modulos
 * JULIAN ALVARAN 2019-03-27
 * TECHNO SOLUCIONES SAS 
 * 317 774 0609
 */

/**
 * Abre el modal para crear un tercero
 * @returns {undefined}
 */
function ModalCrearTercero(idModal,idDivFormulario){
    $("#"+idModal).modal();
    
    var form_data = new FormData();
        
        form_data.append('Accion', 1);
        
        $.ajax({
        url: '../../general/Consultas/formularios.draw.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            document.getElementById(idDivFormulario).innerHTML=data;
            $('#CodigoMunicipio').select2();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}

/**
 * Crear un tercero
 * @returns {undefined}
 */
function CrearTercero(idModal,idBotonModal){
    
    var TipoDocumento=document.getElementById('TipoDocumento').value;
    var Num_Identificacion=document.getElementById('Num_Identificacion').value;    
    var CodigoMunicipio=document.getElementById('CodigoMunicipio').value;
    var Telefono=document.getElementById('Telefono').value;
    var PrimerNombre=document.getElementById('PrimerNombre').value;
    var OtrosNombres=document.getElementById('OtrosNombres').value;
    var PrimerApellido=document.getElementById('PrimerApellido').value;
    var SegundoApellido=document.getElementById('SegundoApellido').value;
    var RazonSocial=document.getElementById('RazonSocial').value;
    var Direccion=document.getElementById('Direccion').value;
    var Email=document.getElementById('Email').value;
    var Cupo=document.getElementById('Cupo').value;
    var CodigoTarjeta=document.getElementById('CodigoTarjeta').value;
    
    if(!$.isNumeric(Num_Identificacion) || Num_Identificacion <= 0){
        alertify.error("El Campo Identificacion debe ser un número mayor a Cero y no puede estar en blanco");
        document.getElementById("Num_Identificacion").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("Num_Identificacion").style.backgroundColor="white";
    }
    
    if(Telefono==''){
        alertify.error("El Campo Teléfono no puede estar vacío");
        document.getElementById("Telefono").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("Telefono").style.backgroundColor="white";
    }
    
    if(RazonSocial==''){
        alertify.error("El Campo Razón Social no puede estar vacío");
        document.getElementById("RazonSocial").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("RazonSocial").style.backgroundColor="white";
    }
    
    
    if(Direccion==''){
        alertify.error("El Campo Dirección no puede estar vacío");
        document.getElementById("Direccion").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("Direccion").style.backgroundColor="white";
    }
    
    if(Email==''){
        alertify.error("El Campo Email no puede estar vacío");
        document.getElementById("Email").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("Email").style.backgroundColor="white";
    }
    
    if(!$.isNumeric(Cupo) || Cupo < 0){
        alertify.error("El Campo Cupo debe ser un número mayor o igual a Cero y no puede estar en blanco");
        document.getElementById("Cupo").style.backgroundColor="pink";
        
        return;
    }else{
        document.getElementById("Cupo").style.backgroundColor="white";
    }
    
    var form_data = new FormData();
        
        form_data.append('Accion', 1);
        form_data.append('TipoDocumento', TipoDocumento);
        form_data.append('Num_Identificacion', Num_Identificacion);
        form_data.append('CodigoMunicipio', CodigoMunicipio);
        form_data.append('Telefono', Telefono);
        form_data.append('TipoDocumento', TipoDocumento);
        form_data.append('PrimerNombre', PrimerNombre);
        form_data.append('OtrosNombres', OtrosNombres);
        form_data.append('PrimerApellido', PrimerApellido);
        form_data.append('SegundoApellido', SegundoApellido);
        form_data.append('RazonSocial', RazonSocial);
        form_data.append('Direccion', Direccion);
        form_data.append('Email', Email);
        form_data.append('Cupo', Cupo);
        form_data.append('CodigoTarjeta', CodigoTarjeta);
        
        document.getElementById("RazonSocial").value='';
        
        $.ajax({
        url: '../../general/procesadores/formularios.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="E1"){
                alertify.alert(respuestas[1]);
                
            }else if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                CierraModal(idModal);
                
            }else{
                alertify.alert(data);
            }
            document.getElementById(idBotonModal).disabled=false;
            document.getElementById(idBotonModal).value="Guardar";
                      
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
    
}

/**
 * Verifica si existe un nit
 * @returns {undefined}
 */
function VerificaNIT(){
    var Num_Identificacion=document.getElementById('Num_Identificacion').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 2);
        form_data.append('Num_Identificacion', Num_Identificacion);
        
        $.ajax({
        url: '../../general/procesadores/formularios.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
                document.getElementById("Num_Identificacion").style.backgroundColor="pink";
                posiciona('Num_Identificacion');
                document.getElementById("BntModalPOS").disabled=true;
            }else if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                document.getElementById("Num_Identificacion").style.backgroundColor="white";
                document.getElementById("BntModalPOS").disabled=false;
            }else{
                alertify.alert(data);
                document.getElementById("BntModalPOS").disabled=false;
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}

/**
 * Verifica si el codigo de una tarjeta ya existe
 * @returns {undefined}
 */
function VerificaCodigoTarjeta(){
    var CodigoTarjeta=document.getElementById('CodigoTarjeta').value;
    
    var form_data = new FormData();
        
        form_data.append('Accion', 3);
        form_data.append('CodigoTarjeta', CodigoTarjeta);
        
        $.ajax({
        url: '../../general/procesadores/formularios.process.php',
        //dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data){
            var respuestas = data.split(';');
            if(respuestas[0]=="E1"){
                alertify.error(respuestas[1]);
                document.getElementById("CodigoTarjeta").style.backgroundColor="pink";
                posiciona('CodigoTarjeta');
                document.getElementById("BntModalPOS").disabled=true;
            }else if(respuestas[0]=="OK"){
                alertify.success(respuestas[1]);
                document.getElementById("CodigoTarjeta").style.backgroundColor="white";
                document.getElementById("BntModalPOS").disabled=false;
            }else{
                alertify.alert(data);
                document.getElementById("BntModalPOS").disabled=false;
            }
            
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          }
      })  
}
/**
 * Crea la razon social
 * @returns {undefined}
 */
function CompletaRazonSocial() {

    var PrimerApellido=document.getElementById('PrimerApellido').value;
    var SegundoApellido=document.getElementById('SegundoApellido').value;
    var PrimerNombre=document.getElementById('PrimerNombre').value;
    var OtrosNombres=document.getElementById('OtrosNombres').value;
	

    var RazonSocial=PrimerNombre+" "+OtrosNombres+" "+PrimerApellido+" "+SegundoApellido;

    document.getElementById('RazonSocial').value=RazonSocial;


}