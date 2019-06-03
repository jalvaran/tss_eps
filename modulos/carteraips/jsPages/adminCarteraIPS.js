/**
 * Controlador para cartera ips
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

function CargarAdminCarteraIPS(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.carteracargadaips`,`DivCatCartera`,`DivOpcionesCatCartera`)
}

function CargarAdminHistorialActualizacionesCarteraIPS(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.actualizacioncarteracargadaips`,`DivCatHisCartera`,`DivOpcionesCatHisCartera`)
}

function CargarAdminSinRelacionarASMET(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.vista_facturas_sr_ips`,`DivSRAsmet`,`DivOpcionesSRAsmet`)
}

function CargarAdminControlCargueIPS(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.controlcarguesips`,`DivControlCargue`,`DivOpcionesControlCargue`)
}
document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
