/**
 * Controlador para cartera eps
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

function CargarAdminCarteraEPS(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.carteraeps`,`DivCatCartera`,`DivOpcionesCatCartera`)
}

function HistorialGlosas(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.glosaseps_asmet`,`DivCatHisCartera`,`DivOpcionesCatHisCartera`)
}

function CargarHistorialPagos(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.pagos_asmet`,`DivSRAsmet`,`DivOpcionesSRAsmet`)
}

function HistorialAnticipos(){
    var dbIPS=document.getElementById('CmbIPS').value; 
    SeleccioneTablaDB(dbIPS+`.anticipos_asmet`,`DivControlCargue`,`DivOpcionesControlCargue`)
}
document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();
$('#CmbIPS').select2();
