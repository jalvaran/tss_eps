/**
 * Controlador para cartera eps
 * JULIAN ALVARAN 2019-05-20
 * TECHNO SOLUCIONES SAS 
 * 
 */

function CargarComparacionRadicados(){
    var dbIPS="ts_eps_resoluciones_glosas"; 
    SeleccioneTablaDB(dbIPS+`.vista_coincidencia_radicados`,`DivTab1`,`DivOpcionesTab1`)
}

function CargarContratosVSHIDRA(){
    var dbIPS="ts_eps_resoluciones_glosas"; 
    SeleccioneTablaDB(dbIPS+`.vista_comparacion_glosas_contratos_hidra`,`DivTab2`,`DivOpcionesTab2`)
}


document.getElementById('BtnMuestraMenuLateral').click();
document.getElementById('TabCuentas1').click();

