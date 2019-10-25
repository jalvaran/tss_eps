/**
 * Controlador para realizar la administracion de los archivos del ts_eps
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

function SeleccionarTablaTsEPS(Tabla){
    
    var db = document.getElementById('CmbIPS').value;
    Tabla=db+"."+Tabla;
    console.log(Tabla);
    if(db != ''){
        SeleccioneTablaDB(Tabla,'DivTablaTSEPS','DivOpcionesTablaTSEPS');
    }else{
        alertify.error("Seleccione una IPS");
    }

}

$('#CmbIPS').select2();
$('#CmbEPS').select2();

document.getElementById('BtnMuestraMenuLateral').click();

