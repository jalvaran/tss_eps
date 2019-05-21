<?php
/**
 * Pagina para recibir y verificar ordenes de compras
 * 2018-11-27, Julian Alvaran Techno Soluciones SAS
 */
$myPage="ReciboOrdenCompra.php";
$myTitulo="Plataforma TS5";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");
$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);
    $css->div("", "container", "", "", "", "", "");
        $css->h3("", "", "", "");
            print("<strong>Recepción de Mercancía</strong>");
        $css->Ch3();
    
    
    $css->CrearDiv("DivSeleccionOrden", "col-sm-4", "left", 1, 1); //Selecciona la orden de compra
        $css->select("CmbOrdenCompra", "form-control", "CmbOrdenCompra", "", "", "onchange=DibujeOrdenCompra()", "");
            $sql="SELECT ID,Fecha,(SELECT RazonSocial FROM proveedores WHERE idProveedores=oc.Tercero LIMIT 1) AS Proveedor FROM ordenesdecompra oc WHERE Estado='CERRADA'";
            $consulta=$obCon->Query($sql);
            $css->option("", "", "", "", "", "", 0);
                print("Seleccione una orden");
            $css->Coption();
            while($DatosOrdenes=$obCon->FetchAssoc($consulta)){
                $css->option("", "", "", $DatosOrdenes["ID"], "", "", 0);
                    print($DatosOrdenes["ID"]." ".$DatosOrdenes["Fecha"]." ".$DatosOrdenes["Proveedor"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();

    $css->CrearDiv("DivBusquedaXCodigo", "col-sm-4", "left", 1, 1); //Busquedas por codigo
        $css->input("text", "TxtCodigoBarras", "form-control", "TxtCodigoBarras", "Código de Barras", "", "Código de Barras", "off", "", "onchange=BuscarXCodigo();", "", "", "");
    $css->CerrarDiv();
    $css->CrearDiv("DivOpciones", "col-sm-4", "left", 1, 1); //Guardar
        $css->CrearBotonEvento("BtnGuardar", "Guardar", 1, "onclick", "GuargarOrden();", "naranja", "");
    $css->CerrarDiv();
    $css->br();
    $css->br();
    $css->br();
    $css->CrearDiv("DivItemsOrden", "", "", 1, 1);
    
    $css->Cdiv();
    
    $css->Cdiv();    
$css->PageFin();

print('<script src="jsPages/ReciboOrdenCompra.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>