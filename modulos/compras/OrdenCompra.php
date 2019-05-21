<?php
/**
 * Pagina para la creacion de Ordenes de Compra 
 * 2018-11-27, Julian Alvaran Techno Soluciones SAS
 */
$myPage="OrdenCompra.php";
$myTitulo="Ordenes de Compra";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");
$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html
$obCon = new conexion($idUser); //Conexion a la base de datos

$css->PageInit($myTitulo);
    $css->Modal("ModalAccionesCompras", "Ordenes de compra", "", 1);
        $css->div("DivFormularioCrearCompra", "", "", "", "", "", "");
        $css->Cdiv();
        
    $css->CModal("BntModalCompras", "onclick=CrearCompra(event)", "button", "Guardar");
    
    $css->Modal("ModalAcciones", "TS5", "", 1);
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();
        
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    //$css->div("", "container", "", "", "", "", "");
    $css->CrearDiv("", "col-md-10", "left", 1, 1); 
        $css->h3("", "", "", "");
                print("<strong>Ordenes de Compra</strong>");
        $css->Ch3();
    $css->CerrarDiv(); 
    $css->CrearDiv("", "col-md-2", "right", 1, 1); 
        $css->h3("", "", "", "");
            print("<a onclick=MuestraOcultaXID('DivOpcionesGenerales') style='cursor:pointer'><strong>Opciones</strong>");
            print('<i class="fa fa-fw fa-bars"></i></a>');
                
        $css->Ch3();
    $css->CerrarDiv();
    
    $css->CrearDiv("DivOpcionesGenerales", "col-md-12", "left", 0, 0);
        $css->fieldset("", "", "FieldDatosCotizacion", "DatosCotizacion", "", "");
            $css->legend("", "");
                print("<a href='#'>Opciones Adicionales:</a>");
            $css->Clegend();   
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->CrearBotonEvento("BtnCrearTercero", "Crear Tercero", 1, "onclick", "ModalCrearTercero(`ModalAcciones`,`DivFrmModalAcciones`);", "azul", "");
                
                
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-3", "left", 1, 1);
                $css->CrearBotonEvento("BtnHistorialDocumentos", "Historial de Ordenes de Compra", 1, "onclick", "SeleccioneTablaDB(`ordenesdecompra`);", "azul", "");
            $css->CerrarDiv();
            
            
            
        $css->Cfieldset();
        print("<br><br>");
    $css->CerrarDiv();  
    
    
    $css->CrearDiv("DivDatosGeneralesCompra", "col-md-12", "left", 1, 1);
    $css->CerrarDiv();
    $css->CrearDiv("DivOpcionesCrearCompras", "col-md-12", "left", 1, 1); 
        
        $css->fieldset("", "", "FieldDatosCompra", "DatosCompra", "", "");
            $css->legend("", "");
                print("<a href='#'>Cree o Seleccione una Orden de Compra</a>");
            $css->Clegend();   
            
        $css->CrearDiv("DivBtnCrear", "col-md-2", "left", 1, 1); 
            $css->CrearBotonEvento("BtnNuevaCompra", "Crear Orden", 1, "onClick", "AbrirModalNuevaCompra()", "azul", "");
        $css->CerrarDiv();
        $css->CrearDiv("DivDatosCompras", "col-md-5", "left", 1, 1); 
            print('<div class="input-group input-group-md">');
            $css->select("idCompra", "form-control", "idCompra", "", "", "onchange=DibujeCompra()", "");
            $css->option("", "", "","", "", "");
                print("Seleccione una Orden de Compra");
            $css->Coption();
            $consulta = $obCon->ConsultarTabla("ordenesdecompra","WHERE Estado='ABIERTA'");
            while($DatosComprobante=$obCon->FetchArray($consulta)){
                
                $DatosTercero=$obCon->DevuelveValores("proveedores", "idProveedores", $DatosComprobante['Tercero']);
                $css->option("", "", "", $DatosComprobante['ID'], "", "");
                    print($DatosComprobante['ID']." ".$DatosTercero["RazonSocial"]." ".$DatosComprobante['Descripcion']);
                $css->Coption();
            }
            $css->Cselect();
            print('<span class="input-group-btn">
                      <button type="button" class="btn btn-info btn-flat" onclick=ConvertirEnBuscadorSelectOrden()><i class="fa fa-fw fa-search-plus"></i></button>
                    </span>');
           print("</div>");
        $css->CerrarDiv();
        $css->CrearDiv("DivBtnEditar", "col-md-2", "left", 1, 1); 
            $css->CrearBotonEvento("BtnEditarCompra", "Editar Datos", 0, "onClick", "AbrirModalNuevaCompra('Editar')", "azul", "");
        $css->CerrarDiv();
        
        print('<div class="input-group input-group-md">
                
                <div class="input-group-btn">
                  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >Acción
                    <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        
                        <li><a href="#" onclick="CopiarOrden()">Copiar una Orden de Compra</a></li>
                       
                  </ul>
                </div>
               <input type="text" id="idCompraAcciones" class="form-control" placeholder="ID opción">
              </div>');
        
        $css->Cfieldset(); 
    $css->CerrarDiv();
    print("<br><br><br><br><br>");
    $css->CrearDiv("DivDatosCompras", "col-md-12", "left", 1, 1); //Datos para la creacion de la compra
        $css->fieldset("", "", "FieldDatosCompra", "DatosCompra", "", "");
                    $css->legend("", "");
                        print("<a href='#'>Agregar items a esta Orden de Compra</a>");
                    $css->Clegend();    
        $css->CrearDiv("DivAgregarItems", "", "center", 1, 1);   
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->select("CmbListado", "form-control", "CmbListado", "Listado", "", "", "onchange=ConvertirSelectBusquedas()");
                    $css->option("", "", "", 1, "", "");
                        print("Productos para la venta");
                    $css->Coption();
                    /*
                    $css->option("", "", "", 2, "", "");
                        print("Servicios y otros");
                    $css->Coption();
                    $css->option("", "", "", 3, "", "");
                        print("Insumos");
                    $css->Coption();
                    //$css->option("", "", "", 4, "", "");
                      //  print("Otros");
                    //$css->Coption();
                     * 
                     */
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-6", "center", 1, 1);
                $css->select("CmbBusquedas", "form-control", "CmbBusquedas", "Búsquedas<br>", "", "", "style=width:100%");
                   
                    $css->option("", "", "", "", "", "");
                        print("Buscar");
                    $css->Coption();
                    
                    
                $css->Cselect();
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                $css->select("CmbImpuestosIncluidos", "form-control", "CmbImpuestosIncluidos", "Impuestos incluidos?", "", "", "");
                    $css->option("", "", "", 1, "", "");
                        print("SI");
                    $css->Coption();
                    $css->option("", "", "", 2, "", "");
                        print("NO");
                    $css->Coption();
                    
                $css->Cselect();
            $css->CerrarDiv();
            
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
            
                $css->select("CmbTipoImpuesto", "form-control", "CmbTipoImpuesto", "Tipo de Impuesto", "", "", "");
                    
                    $Consulta=$obCon->ConsultarTabla("porcentajes_iva", " WHERE Habilitado='SI'");
                    while($DatosTipoImpuestos=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosTipoImpuestos["ID"], "", "");
                            print($DatosTipoImpuestos["Nombre"]);
                        $css->Coption();
                    }
                    
                $css->Cselect();   
            $css->CerrarDiv();
            print("<br><br><br><br>");
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
                print("<strong>Código</strong>");
               $css->input("text", "CodigoBarras", "form-control", "CodigoBarras", "Codigo de barras", "", "Código", "off", "", "onchange=AgregaItemPorCodigo()");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
               print("<strong>Descripción</strong>");
               $css->input("text", "TxtDescripcion", "form-control", "TxtDescripcion", "Descripción", "", "Descripción", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-2", "center", 1, 1);
               print("<strong>Cantidad</strong>");
               $css->input("text", "Cantidad", "form-control", "Cantidad", "Cantidad", "", "Cantidad", "off", "", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
               print("<strong>Valor Unitario</strong>");
               $css->input("text", "ValorUnitario", "form-control", "ValorUnitario", "Valor Unitario", "", "Valor Unitario", "off", "", "");
            $css->CerrarDiv();
            
            print("<br><br><br><br>");
            
            
            
            $css->CrearDiv("DivBtnEditar", "col-md-8", "left", 1, 1); 
                
            $css->CerrarDiv();
            $css->CrearDiv("DivBtnEditar", "col-md-4", "left", 1, 1); 
                $css->CrearBotonEvento("BtnAgregarItem", "Agregar Item", 1, "onClick", "AgregarItem(event)", "verde", "");
            $css->CerrarDiv();
            
        
        $css->CerrarDiv();       
            $css->Cfieldset();
    $css->CerrarDiv();
    //$css->CerrarDiv();

    print("<br>");
    $css->CrearDiv("DivDatosCompras", "col-md-12", "left", 1, 1); //Datos para la creacion de la compra
        $css->fieldset("", "", "FieldDatosCompra", "items en esta compra", "", "");
            $css->legend("", "");
                print("<a href='#'>Items Agregados a esta Compra</a>");
            $css->Clegend();    
            $css->CrearDiv("DivItemsCompra", "", "center", 1, 1);   

            $css->CerrarDiv();       
        $css->Cfieldset();
            
        $css->fieldset("", "", "FieldDatosCompra", "Totales, Retenciones y opciones del documento", "", "");
            $css->legend("", "");
                print("<a href='#'>Totales, Retenciones y opciones del documento</a>");
            $css->Clegend();    
           
            $css->CrearDiv("DivTotalesCompra", "", "center", 1, 1);   
                
            $css->CerrarDiv(); 
        $css->Cfieldset();    
    $css->CerrarDiv();
    //$css->CerrarDiv();
    
    $css->Cdiv();

$css->PageFin();

print('<script src="jsPages/OrdenCompra.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>