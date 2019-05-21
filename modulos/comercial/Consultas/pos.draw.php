<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/Facturacion.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new Facturacion($idUser);
    
    switch ($_REQUEST["Accion"]) {
                
        case 1://Dibuja los items de una preventa
            $idPreventa=$obCon->normalizar($_REQUEST["idPreventa"]);
            $css->CrearTabla();
                $css->FilaTabla(16);
                    
                    $css->ColTabla("<strong>Nombre</strong>", 1, "C");
                    $css->ColTabla("<strong>Referencia</strong>", 1, "C");
                    $css->ColTabla("<strong>Cantidad</strong>", 1, "C");                    
                    $css->ColTabla("<strong>Valor_Unitario</strong>", 1, "C");                    
                    $css->ColTabla("<strong>Total</strong>", 1, "C");                    
                    $css->ColTabla("<strong>Eliminar</strong>", 1, "C");
                    
                $css->CierraFilaTabla();
                //Dibujo los productos
                $sql="SELECT * FROM preventa WHERE VestasActivas_idVestasActivas='$idPreventa' ORDER BY idPrecotizacion DESC";
                $Consulta=$obCon->Query($sql);
                while ($DatosItems = $obCon->FetchAssoc($Consulta)) {
                    $idItem=$DatosItems["idPrecotizacion"];
                    
                    $css->FilaTabla(16);                        
                        
                        $css->ColTabla($DatosItems["Nombre"], 1, "C");                   
                        
                        $css->ColTabla($DatosItems["Referencia"], 1, "C");
                        print("<td>");
                            print('<div class="input-group input-group-md" style=width:100px>');
                            
                                $css->input("number", "TxtCantidad_$idItem", "form-control", "TxtCantidad_$idItem", "Cantidad", $DatosItems["Cantidad"], "", "off", "", "");
                                print('<span class="input-group-btn">');
                                    print('<button type="button" id="BtnEditarCantidad_'.$idItem.'" class="btn btn-info btn-flat" onclick=EditarItemCantidad('.$idItem.')>E</button>');
                                    
                                print('</span>');
                            print('</div>');
                            
                            
                        print("</td>");
                        
                                              
                        print("<td>");
                            
                            print('<div class="input-group input-group-md">
                                <input type="text" id="TxtValorUnitario_'.$idItem.'" value="'.$DatosItems["ValorAcordado"].'" class="form-control" placeholder="Valor Unitario">
                                <div class="input-group-btn">
                                  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >Precio
                                    <span class="fa fa-caret-down"></span></button>
                                    <ul class="dropdown-menu">');
                            
                            print('<li><a href="#" onclick="EditarPrecioVenta(`'.$idItem.'`,`0`)" title="Valor Libre">Valor Libre</a></li>');
                            print('<li><a href="#" onclick="EditarPrecioVenta(`'.$idItem.'`,`1`)" title="Precio Mayorista">Mayorista</a></li>');            

                            print('</ul></div></div>');
                                

                         print("</td>");
                        
                        
                        $css->ColTabla(number_format($DatosItems["TotalVenta"],2,",","."), 1, "C");
                        
                        print("<td style='font-size:16px;text-align:center;color:red' title='Borrar'>");   
                            
                            $css->li("", "fa  fa-remove", "", "onclick=EliminarItem(`$idItem`) style=font-size:16px;cursor:pointer;text-align:center;color:red");
                            $css->Cli();
                        print("</td>");
                        
                    $css->CierraFilaTabla();
                }
                
                
            $css->CerrarTabla();
        break;// fin caso 1
        
        case 2://Dibujo los Totales
            $idPreventa=$obCon->normalizar($_REQUEST["idPreventa"]);
            
            $sql="SELECT COUNT(*) AS CantidadRegistros,SUM(Cantidad) as TotalItems,SUM(Subtotal) AS Subtotal, SUM(Impuestos) as IVA, round(SUM(TotalVenta)) as Total FROM preventa WHERE VestasActivas_idVestasActivas = '$idPreventa'";
            $Consulta=$obCon->Query($sql);
            $Totales=$obCon->FetchAssoc($Consulta);
            
            $Subtotal=$Totales["Subtotal"];
            $IVA=$Totales["IVA"];
            $Total=$Totales["Total"];
            $CantidadRegistros=$Totales["CantidadRegistros"];
            $sql="SELECT Devuelve FROM facturas WHERE Usuarios_idUsuarios='$idUser' ORDER BY idFacturas DESC LIMIT 1";
            $consulta=$obCon->Query($sql);
            $DatosDevuelta=$obCon->FetchArray($consulta);
    
            //$css->input("hidden", "TxtTotalDocumento", "", "TxtTotalDocumento", "", $Total, "", "", "", "");
           
                
                $css->CrearTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>TOTALES</strong>", 2,'C');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Última Devuelta:</strong>", 1,'L'); 
                        $css->ColTabla(number_format($DatosDevuelta["Devuelve"]), 1,'R'); 
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Items:</strong>", 1,'L'); 
                        $css->ColTabla(($Totales["TotalItems"]), 1,'R');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Subtotal:</strong>", 1,'L'); 
                        $css->ColTabla(number_format($Subtotal), 1,'R');  
                    $css->CierraFilaTabla();
                    $css->FilaTabla(16);
                        $css->ColTabla("<strong>Impuestos:</strong>", 1,'L');
                        $css->ColTabla(number_format($IVA), 1,'R');
                    $css->CierraFilaTabla();
                    $css->FilaTabla(30);
                        $css->ColTabla("<strong>Total:</strong>", 1,'L');
                        $css->ColTabla("<strong>".number_format($Total)."</strong>", 1,'R');
                    $css->CierraFilaTabla();
                    
                    
                                        
                    $css->FilaTabla(16);
                        print("<td colspan=3 style='text-align:center'>");
                            $habilitaBotones=0;
                             if($CantidadRegistros>0){ //Verifico que hayan productos, servicios o insumos agregados
                                 $habilitaBotones=1;
                             }
                             $css->CrearBotonEvento("BtnFacturar", "Facturar", $habilitaBotones, "onclick", "AbrirModalFacturarPOS()", "naranja", "");
                            
                            print("<br><br>");
                            $css->CrearBotonEvento("BtnCotizar", "Cotizar", $habilitaBotones, "onclick", "CotizarPOS()", "verde", "");
                        print("</td>");
                    $css->CierraFilaTabla();
                $css->CerrarTabla(); 
            
                   
            
            
        break; // fin caso 4
        
        case 5: 
            $Listado=$obCon->normalizar($_REQUEST["listado"]);
            $idBusqueda=$obCon->normalizar($_REQUEST["CmbBusquedas"]);
            
            if($Listado==1){
                $tab="productosventa";
            }
            if($Listado==2){
                $tab="servicios";
            }
            if($Listado==3){
                $tab="productosalquiler";
            }
            $Datos=$obCon->ValorActual($tab, "PrecioVenta", " idProductosVenta='$idBusqueda'");
            print($Datos["PrecioVenta"]);
        break;//Fin caso 5
        
        case 6: //Dibujo el formulario para facturar 
            $idPreventa=$obCon->normalizar($_REQUEST["idPreventa"]);
            $idCliente=$obCon->normalizar($_REQUEST["idCliente"]);
            
            $DatosCliente=$obCon->DevuelveValores("clientes", "idClientes", $idCliente);
            $NIT=$DatosCliente["Num_Identificacion"];
            $Parametros=$obCon->DevuelveValores("parametros_contables", "ID", 20);//Aqui se encuentra la cuenta para los anticipos
            $CuentaAnticipos=$Parametros["CuentaPUC"];
            $sql="SELECT SUM(Debito) as Debito, SUM(Credito) AS Credito FROM librodiario WHERE CuentaPUC='$CuentaAnticipos' AND Tercero_Identificacion='$NIT'";
            $Consulta=$obCon->Query($sql);
            $DatosAnticipos=$obCon->FetchAssoc($Consulta);
            $SaldoAnticiposTercero=$DatosAnticipos["Credito"]-$DatosAnticipos["Debito"];
            
            $sql="SELECT round(SUM(TotalVenta)) as Total FROM preventa WHERE VestasActivas_idVestasActivas = '$idPreventa'";
            $Consulta=$obCon->Query($sql);
            $Totales=$obCon->FetchAssoc($Consulta);
            
            $TotalFactura=$Totales["Total"];
            $css->input("hidden", "TxtTotalFactura", "", "TxtTotalFactura", "", $TotalFactura, "", "", "", ""); 
            $css->input("hidden", "TxtTotalAnticiposFactura", "", "TxtTotalAnticiposFactura", "", $SaldoAnticiposTercero, "", "", "", "");  
            
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 1, "", "", "", ""); //1 sirve para indicarle al sistema que debe guardar el formulario de crear una factura
            
            $css->CrearTabla();
                $css->FilaTabla(22);
                    $css->ColTabla("Facturar Esta preventa al Cliente: $DatosCliente[RazonSocial] $DatosCliente[Num_Identificacion], por un total de:<strong> ". number_format($TotalFactura)."</strong>", 5);
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(14);
                    
                    $css->ColTabla("<strong>Efectivo</strong>", 1);
                    $css->ColTabla("<strong>Tarjetas</strong>", 1);
                    $css->ColTabla("<strong>Cheques</strong>", 1);
                    $css->ColTabla("<strong>Otros</strong>", 1);
                    $css->ColTabla("<strong>Devolver</strong>", 1);
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                    
                    print("<td >");
                        
                        $css->input("number", "Efectivo", "form-control input-lg", "Efectivo", "Efectivo", $TotalFactura, "Efectivo", "off", "", "onKeyUp=CalculeDevuelta()");
                    print("</td>");
                    print("<td >");
                        
                        $css->input("number", "Tarjetas", "form-control input-lg", "Tarjetas", "Tarjetas", 0, "Tarjetas", "off", "", "onKeyUp=CalculeDevuelta()");
                    print("</td>");
                    print("<td>");
                        
                        $css->input("number", "Cheque", "form-control input-lg", "Cheque", "Cheque", 0, "Cheque", "off", "", "onKeyUp=CalculeDevuelta()");
                    print("</td>");
                    print("<td >");
                        
                        $css->input("number", "Otros", "form-control input-lg", "Otros", "Otros", 0, "Otros", "off", "", "onKeyUp=CalculeDevuelta()");
                    print("</td>");
                   
                    print("<td >");                        
                        $css->input("number", "Devuelta", "form-control input-lg", "Devuelta", "Devuelta", 0, "Efectivo", "off", "", " disabled");
                    print("</td>");
                    
                
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Forma de Pago</strong>", 1);
                    $css->ColTabla("<strong>Asignar</strong>", 1);
                    $css->ColTabla("<strong>Imprimir</strong>", 1);
                    $css->ColTabla("<strong>Observaciones</strong>", 1);
                    $css->ColTabla("<strong>Anticipos del Cliente: $".number_format($SaldoAnticiposTercero)."</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                print("<td>");
                        $css->select("CmbFormaPago", "form-control", "CmbFormaPago", "", "", "", "");

                            $sql="SELECT * FROM repuestas_forma_pago";
                            $Consulta=$obCon->Query($sql);
                            if($idCliente==1){
                                $css->option("", "",'' , "Contado", "", "", "", "");
                                    print("Contado");
                                $css->Coption();
                            }
                            while($DatosFormaPago=$obCon->FetchAssoc($Consulta)){
                                if($idCliente<>1){
                                    $css->option("", "",'' , $DatosFormaPago["DiasCartera"], "", "", "", "");
                                        print($DatosFormaPago["Etiqueta"]);
                                    $css->Coption();
                                }
                            }


                        $css->Cselect();
                    print("</td>");
                    
                print("<td>");
                        $css->select("CmbColaboradores", "form-control", "CmbColaboradores", "", "", "", "");

                            $sql="SELECT * FROM colaboradores WHERE Activo='SI'";
                            $Consulta=$obCon->Query($sql);
                                $css->option("", "",'' , '', "", "", "", "");
                                    print("Seleccione un colaborador");
                                $css->Coption();
                            while($DatosColaboradores=$obCon->FetchAssoc($Consulta)){
                                $css->option("", "",'' , $DatosColaboradores["Identificacion"], "", "", "", "");
                                    print($DatosColaboradores["Nombre"]." ".$DatosColaboradores["Identificacion"]);
                                $css->Coption();
                            }


                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $DatosImpresion=$obCon->DevuelveValores("configuracion_general", "ID", 2);  //aqui está almaceda la info para saber si debe imprimir o no el tikete por defecto
                        $Imprime=$DatosImpresion["Valor"];
                        $css->select("CmbPrint", "form-control", "CmbPrint", "", "", "", "");
                            $Defecto=0;
                            if($Imprime==1){
                                $Defecto=1;
                            }
                            
                            $css->option("", "",'' , 'SI', "", "", $Defecto, "");
                                print("SI");
                            $css->Coption();
                            $Defecto=0;
                            if($Imprime==0){
                                $Defecto=1;
                            }
                            $css->option("", "",'' , 'NO', "", "", $Defecto, "");
                                print("NO");
                            $css->Coption();
                            
                            
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->textarea("TxtObservacionesFactura", "form-control", "TxtObservacionesFactura", "Observaciones", "Observaciones", "", "");
                        $css->Ctextarea();
                    print("</td>"); 
                    
                    print("<td>");
                        
                        $css->input("number", "AnticiposCruzados", "form-control input-lg", "AnticiposCruzados", "Cruzar Anticipos", 0, "", "", "", "");
                    print("</td>");
                    
                    
                    $css->CierraFilaTabla();
                    
            $css->CerrarTabla();
            
        break;//Fin caso 6
        
        case 7://Dibuja las opciones al momento de autorizar
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<strong>Descuento General</strong><br>");
                
                $css->input("text", "TxtPorcentajeDescuento", "form-control", "TxtPorcentajeDescuento", "Porcentaje descuento", "", "Porcentaje", "off", "", "");
                $css->CrearBotonEvento("BtnDescuentoPorcentaje", "Aplicar", 1, "onclick", "DescuentoPorcentaje()", "naranja", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<strong>Listas de Precios</strong><br>");
                $css->select("CmbListaPrecio", "form-control", "CmbListaPrecio", "", "", "", "");
                    $Consulta=$obCon->ConsultarTabla("productos_lista_precios", "");
                    while($DatosListas=$obCon->FetchAssoc($Consulta)){
                        $css->option("", "", "", $DatosListas["ID"], "", "");
                            print($DatosListas["Nombre"]);
                        $css->Coption();
                    }
                $css->Cselect();
                $css->CrearBotonEvento("BtnListaPrecio", "Aplicar", 1, "onclick", "DescuentoListaPrecio()", "azul", "");
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<strong>Descuento Costo</strong><br>");                
                $css->CrearBotonEvento("BtnDescuentoCosto", "Aplicar", 1, "onclick", "DescuentoCosto()", "rojo", "");
            $css->CerrarDiv();
            print("<br><br><br><br><br>");
        break;//Fin caso 7
        
        case 8: //Dibuja las opciones para crear un tercero
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 2, "", "", "", ""); //2 sirve para indicarle al sistema que debe guardar el formulario de crear un tercero
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Tipo de Documento</strong>", 1);
                    $css->ColTabla("<strong>Identificación</strong>", 1);
                    $css->ColTabla("<strong>Ciudad</strong>", 1);
                    $css->ColTabla("<strong>Teléfono</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("TipoDocumento", "form-control", "TipoDocumento", "", "", "", "style=width:300px");
                        $Consulta=$obCon->ConsultarTabla("cod_documentos", "");
                        while($DatosTipoDocumento=$obCon->FetchAssoc($Consulta)){
                            $sel=0;
                            if($DatosTipoDocumento["Codigo"]==13){
                                $sel=1;
                            }
                            $css->option("", "", "", $DatosTipoDocumento["Codigo"], "", "", $sel);
                                print($DatosTipoDocumento["Codigo"]." ".$DatosTipoDocumento["Descripcion"]);
                            $css->Coption();
                        }    
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Num_Identificacion", "form-control", "Num_Identificacion", "", "", "Identificación", "off", "", "onchange=VerificaNIT()");
                    print("</td>");
                    print("<td>");
                        $css->select("CodigoMunicipio", "form-control", "CodigoMunicipio", "", "", "", "");
                            $Consulta=$obCon->ConsultarTabla("cod_municipios_dptos", "");
                            while($DatosMunicipios=$obCon->FetchAssoc($Consulta)){
                                $sel=0;
                                if($DatosMunicipios["ID"]==1011){
                                    $sel=1;
                                }
                                $css->option("", "", "", $DatosMunicipios["ID"], "", "", $sel);
                                    print($DatosMunicipios["Ciudad"]." ".$DatosMunicipios["Cod_mcipio"]);
                                $css->Coption();
                            }    
                        $css->Cselect();
                    print("</td>");
                    
                    print("<td>");
                        $css->input("text", "Telefono", "form-control", "Telefono", "", "", "Teléfono", "off", "", "");
                    print("</td>");
                    
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Nombres</strong>", 4,"C");
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "PrimerNombre", "form-control", "PrimerNombre", "Primer Nombre", "", "Primer Nombre", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "OtrosNombres", "form-control", "OtrosNombres", "Otros Nombres", "", "Otros Nombres", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "PrimerApellido", "form-control", "PrimerApellido", "Primer Apellido", "", "Primer Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "SegundoApellido", "form-control", "SegundoApellido", "Segundo Apellido", "", "Segundo Apellido", "off", "", "onkeyup=CompletaRazonSocial()", "");
                    print("</td>");
                    $css->FilaTabla(16);
                        print("<td colspan=4>");
                            $css->input("text", "RazonSocial", "form-control", "RazonSocial", "Razon Social", "", "RazonSocial", "off", "", "", "");
                        print("</td>");
                    $css->CierraFilaTabla(); 
                    
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Dirección</strong>", 1);
                    $css->ColTabla("<strong>Email</strong>", 1);
                    $css->ColTabla("<strong>Cupo</strong>", 1);
                    $css->ColTabla("<strong>Código Tarjeta</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->input("text", "Direccion", "form-control", "Direccion", "Direccion", "", "Dirección", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("text", "Email", "form-control", "Email", "Email", "", "Email", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "Cupo", "form-control", "Cupo", "Cupo", 0, "Cupo Crédito", "off", "", "", "");
                    print("</td>");
                    print("<td>");
                        $css->input("number", "CodigoTarjeta", "form-control", "CodigoTarjeta", "Codigo Tarjeta", "", "Código Tarjeta", "off", "", "", "onchange=VerificaCodigoTarjeta()");
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//Fin caso 8
        
        case 9://Crear un separado
            
            $css->CrearDiv("", "col-md-8", "center", 1, 1);
                print("<strong>Abono</strong><br>");
                
                $css->input("number", "TxtAbonoCrearSeparado", "form-control", "TxtAbonoCrearSeparado", "Abono a Separado", "", "Abono a Separado", "off", "", "");
                
            $css->CerrarDiv();
            
            $css->CrearDiv("", "col-md-4", "center", 1, 1);
                print("<strong>Crear Separado</strong><br>");
                $css->CrearBotonEvento("BtnCrearSeparado", "Ejecutar", 1, "onclick", "CrearSeparado()", "rojo", "");
            $css->CerrarDiv();
            
            
            print("<br><br><br><br><br>");
            
        break;//Fin caso 9
    
        case 10://Formulario para crear un egreso
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 3, "", "", "", ""); //3 sirve para indicarle al sistema que debe guardar el formulario de crear un egreso
            
            $css->CrearTabla();
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Tipo de Egreso</strong>", 1);
                    $css->ColTabla("<strong>Tercero</strong>", 1);
                    
                    $css->ColTabla("<strong>Número de Soporte</strong>", 1);
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td>");
                        $css->select("TipoEgreso", "form-control", "TipoEgreso", "", "", "", "style=width:300px");
                        $Consulta=$obCon->ConsultarTabla("subcuentas", " WHERE (PUC LIKE '5135%' or PUC LIKE '5105%' or PUC LIKE '5195%') AND LENGTH(PUC)>4 ");
                        while($DatosCuenta=$obCon->FetchAssoc($Consulta)){
                                                       
                            $css->option("", "", "", $DatosCuenta["PUC"], "", "", 0);
                                print($DatosCuenta["PUC"]." ".$DatosCuenta["Nombre"]);
                            $css->Coption();
                        }    
                        $css->Cselect();
                    print("</td>");
                    print("<td>");
                        $css->select("CmbTerceroEgreso", "form-control", "CmbTerceroEgreso", "", "", "", "style=width:300px");
                            $css->option("", "", "", "", "", "");
                                print("Seleccione un tercero");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    
                    
                    print("<td>");
                        $css->input("text", "TxtNumeroSoporteEgreso", "form-control", "TxtNumeroSoporteEgreso", "", "", "Número de Soporte", "off", "", "");
                    print("</td>");
                    
                    
                $css->CierraFilaTabla();
                                
                $css->FilaTabla(16);
                    $css->ColTabla("<strong>Concepto</strong>", 2);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                    
                $css->CierraFilaTabla();
                
                $css->FilaTabla(16);
                    print("<td colspan=2>");
                        $css->textarea("TxtConcepto", "form-control", "TxtConcepto", "Concepto", "Concepto", "", "");
                        $css->Ctextarea();
                    print("</td>");
                    
                    print("<td>");
                        $css->input("number", "SubtotalEgreso", "form-control", "SubtotalEgreso", "SubtotalEgreso", "", "Subtotal", "off", "", "", "onkeyup=CalculeTotalEgreso()");                    
                        $css->input("number", "IVAEgreso", "form-control", "IVAEgreso", "IVAEgreso", 0, "IVA", "off", "", "", "onkeyup=CalculeTotalEgreso()");
                        $css->input("number", "TotalEgreso", "form-control", "TotalEgreso", "TotalEgreso", "", "Total", "off", "", "", "","disabled");
                        
                    print("</td>");
                $css->CierraFilaTabla();
                
            $css->CerrarTabla();
        break;//fin caso 10
        
        case 11://Crea el formulario para abonar a un separado
            $key=$obCon->normalizar($_REQUEST["TxtBuscarSeparado"]);
            if(strlen($key)<4){
                print("Digite una palabra de más de 3 carácteres");
                exit();
            }
            $sql="SELECT sp.Fecha,sp.ID, cl.RazonSocial, cl.Num_Identificacion, sp.Total, sp.Saldo, sp.idCliente FROM separados sp"
                    . " INNER JOIN clientes cl ON sp.idCliente = cl.idClientes "
                    . " WHERE (sp.Estado<>'Cerrado' AND sp.Estado<>'ANULADO' AND sp.Saldo>0) AND (cl.RazonSocial LIKE '%$key%' OR cl.Num_Identificacion LIKE '%$key%') LIMIT 20";
            $Datos=$obCon->Query($sql);
            if($obCon->NumRows($Datos)){
                $css->CrearTabla();

                while($DatosSeparado=$obCon->FetchArray($Datos)){
                    $css->FilaTabla(14);
                    $css->ColTabla("<strong>Separado No. $DatosSeparado[ID]</strong>, del <strong>$DatosSeparado[Fecha]</strong>", 6);
                    $css->CierraFilaTabla();
                    $css->FilaTabla(14);
                    print("<td>");
                    //$css->CrearForm2("FormAbonosSeparados$DatosSeparado[ID]", $myPage, "post", "_self");
                    $idSeparado=$DatosSeparado["ID"];
                    
                    $css->input("number", "TxtAbonoSeparado_$idSeparado", "form-control", "TxtAbono", "Abonar", $DatosSeparado["Saldo"], "Abonar", "off", "", "");
                    
                    $css->CrearBotonEvento("BtnAbono_$idSeparado", "Abonar", 1, "onclick", "AbonarSeparado($idSeparado)", "rojo", "");
                    
                    print("</td>");
                    $css->ColTabla($DatosSeparado["ID"], 1);
                    $css->ColTabla($DatosSeparado["RazonSocial"], 1);
                    $css->ColTabla($DatosSeparado["Num_Identificacion"], 1);
                    $css->ColTabla("<strong>Total: </strong>".number_format($DatosSeparado["Total"]), 1);
                    $css->ColTabla("<strong>Abonos: </strong>".number_format($DatosSeparado["Total"]-$DatosSeparado["Saldo"]), 1);
                    $css->ColTabla("<strong>Saldo: </strong>".number_format($DatosSeparado["Saldo"]), 1);
                    $css->CierraFilaTabla();

                    $css->FilaTabla(16);
                    $css->ColTabla("ID Separado", 1);
                    $css->ColTabla("Referencia", 1);
                    $css->ColTabla("Nombre", 2);
                    $css->ColTabla("Cantidad", 1);
                    $css->ColTabla("TotalItem", 1);
                    $css->ColTabla("Opciones", 1);
                    $css->CierraFilaTabla();
                    $TotalAbonos=$DatosSeparado["Total"]-$DatosSeparado["Saldo"];
                    $ConsultaItems=$obCon->ConsultarTabla("separados_items", "WHERE idSeparado='$DatosSeparado[ID]'");
                    while($DatosItemsSeparados=$obCon->FetchArray($ConsultaItems)){
                        $CantidadMaxima=$DatosItemsSeparados["Cantidad"];
                        $ValorUnitarioItem=$DatosItemsSeparados["ValorUnitarioItem"];
                        $idItemSeparado=$DatosItemsSeparados["ID"];
                        $css->FilaTabla(14);
                        $css->ColTabla($DatosItemsSeparados["idSeparado"], 1);
                        $css->ColTabla($DatosItemsSeparados["Referencia"], 1);

                        $css->ColTabla($DatosItemsSeparados["Nombre"], 2);
                        print("<td>");
                        $css->input("number", "TxtCantidadItemSeparado_$idItemSeparado", "form-control", "Cantidad", "Cantidad", $DatosItemsSeparados["Cantidad"], "Cantidad", "off", "", "");
                        
                        print("</td>");

                        $css->ColTabla(number_format($DatosItemsSeparados["TotalItem"]), 1);
                        print("<td>");

                            $css->CrearBotonEvento("BtnFactItemSeparado_$idItemSeparado", "Facturar Item", 1, "onClick", "FacturarItemSeparado('$idItemSeparado','$TotalAbonos','$CantidadMaxima','$ValorUnitarioItem')", "naranja", "");
                        print("</td>");
                        $css->CierraFilaTabla();
                    }           



                }
                $css->CerrarTabla();
            }else{
                print("No hay resultados");
            }
        break;//Fin Caso 11
        
        case 12://Dibujo el formulario para abonar a un credito
            
            $key=$obCon->normalizar($_REQUEST["TxtBuscarCredito"]);
            if(strlen($key)<=3){

                print("Escriba mas de 3 caracteres");
                exit();  
            }
            $sql="SELECT cart.idCartera,cart.TipoCartera,cart.Facturas_idFacturas, cl.RazonSocial, cl.Num_Identificacion, cart.TotalFactura, cart.Saldo,cart.TotalAbonos, cl.idClientes FROM cartera cart"
                    . " INNER JOIN clientes cl ON cart.idCliente = cl.idClientes "
                    . " WHERE (cl.RazonSocial LIKE '%$key%' OR cl.Num_Identificacion LIKE '%$key%') AND cart.Saldo>1 LIMIT 40";
            $Datos=$obCon->Query($sql);
            if($obCon->NumRows($Datos)){
                $css->CrearTabla();

                while($DatosCredito=$obCon->FetchArray($Datos)){
                    $DatosFactura=$obCon->DevuelveValores("facturas", "idFacturas", $DatosCredito["Facturas_idFacturas"]);
                    $idCartera=$DatosCredito["idCartera"];
                    $idFactura=$DatosFactura["idFacturas"];
                            
                    $css->FilaTabla(14);
                    if($DatosFactura["FormaPago"]=='SisteCredito'){

                        print("<td colspan=6 style='background-color:#ff391a; color:white'>");
                    }else{
                        print("<td colspan=6 style='background-color:#daeecf;'>");
                    }

                    print("<strong>Factura No. ".$DatosFactura["Prefijo"]." - ".$DatosFactura["NumeroFactura"]." TIPO DE CREDITO: $DatosFactura[FormaPago] Fecha: $DatosFactura[Fecha]<strong>");
                    print("</td>");
                    $css->CierraFilaTabla();
                    $css->FilaTabla(14);
                    print("</td>");
                    
                    $css->ColTabla("<strong>".$DatosCredito["RazonSocial"]."</strong>", 1);
                    $css->ColTabla("<strong>".$DatosCredito["Num_Identificacion"]."</strong>", 1);
                    $css->ColTabla("<strong>Total: </strong>".($DatosCredito["TotalFactura"]), 1);
                    $css->ColTabla("<strong>Abonos: </strong>".($DatosCredito["TotalFactura"]-$DatosCredito["Saldo"]), 1);
                    $css->ColTabla("<strong>Saldo: </strong>".($DatosFactura["SaldoFact"]), 1);
                    $css->CierraFilaTabla();
                    
                    $css->FilaTabla(14);
                    
                    print("<td>");
                        print("<strong>Efectivo:</strong>"); 
                        $css->input("number", "TxtAbonoCredito_$idCartera", "form-control", "TxtAbonoCredito_$idCartera", "Abono", $DatosFactura["SaldoFact"], "Efectivo: ", "off", "", "", "");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Intereses:</strong>"); 
                        $css->input("number", "TxtInteresCredito_$idCartera", "form-control", "TxtInteresCredito_$idCartera", "Intereses", 0, "Intereses: ", "off", "", "", "");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Tarjetas:</strong>"); 
                        $css->input("number", "TxtTarjetasCredito_$idCartera", "form-control", "TxtTarjetasCredito_$idCartera", "Tarjetas", 0, "Tarjetas: ", "off", "", "", "");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Cheques:</strong>"); 
                        $css->input("number", "TxtChequesCredito_$idCartera", "form-control", "TxtChequesCredito_$idCartera", "Cheques", 0, "Cheques: ", "off", "", "", "");
                    print("</td>");
                    
                    print("<td>");
                        print("<strong>Otros:</strong>"); 
                        $css->input("number", "TxtOtrosCredito_$idCartera", "form-control", "TxtOtrosCredito_$idCartera", "Otros", 0, "Otros: ", "off", "", "", "");
                    print("</td>");
                    $css->FilaTabla(16);
                        print("<td colspan=3>");
                            $css->CrearBotonEvento("BtnItemFactura_$idCartera", "Ver Items de la Factura", 1, "onclick", "MostrarItemsFacturaCredito(`$idFactura`,`DivCredito_Items_$idCartera`)", "naranja", "");
                        print("</td>");
                        print("<td colspan=2>");
                            $css->CrearBotonEvento("BtnAbonoCredito_$idCartera", "Abonar a Credito", 1, "onclick", "AbonarCredito(`$idCartera`,`$idFactura`)", "rojo", "");
                        print("</td>");
                        
                    $css->CierraFilaTabla();
                    
                    $css->FilaTabla(16);
                    print("<td colspan='5' style='text-align:center'>");
                        $css->CrearDiv("DivCredito_Items_$idCartera", "", "center", 1, 1);
                        $css->CerrarDiv();
                    print("</td>");   
                    $css->CierraFilaTabla();

                }
                $css->CerrarTabla();

            }else{
                print("No se encontraron datos");
            }
            
        break;//Fin caso 12
        
        case 13://Dibujo los items de una factura
            
            $idFactura=$obCon->normalizar($_REQUEST["idFactura"]);
            
            $css->CrearTabla();
            $css->FilaTabla(12);
                $css->ColTabla("<strong>REFERENCIA</strong>", 1);
                $css->ColTabla("<strong>NOMBRE</strong>", 1);
                $css->ColTabla("<strong>VALOR UNITARIO</strong>", 1);
                $css->ColTabla("<strong>CANTIDAD</strong>", 1);
                $css->ColTabla("<strong>SUBTOTAL</strong>", 1);
                $css->ColTabla("<strong>IVA</strong>", 1);
                $css->ColTabla("<strong>TOTAL</strong>", 1);
            $css->CierraFilaTabla();
            
            $sql="SELECT Referencia,Nombre,ValorUnitarioItem,Cantidad,SubtotalItem,IVAItem,TotalItem FROM facturas_items "
                    . " WHERE idFactura='$idFactura' LIMIT 100";
            $Consulta=$obCon->Query($sql);
            while ($DatosFactura=$obCon->FetchArray($Consulta)){
                $css->FilaTabla(12);
                    $css->ColTabla($DatosFactura["Referencia"], 1);
                    $css->ColTabla($DatosFactura["Nombre"], 1);
                    $css->ColTabla($DatosFactura["ValorUnitarioItem"], 1);
                    $css->ColTabla($DatosFactura["Cantidad"], 1);
                    $css->ColTabla($DatosFactura["SubtotalItem"], 1);
                    $css->ColTabla($DatosFactura["IVAItem"], 1);
                    $css->ColTabla($DatosFactura["TotalItem"], 1);
                $css->CierraFilaTabla();
            }
            $css->CerrarTabla();
        break;    //fin caso 13
        
        case 14://Formulario para recibir el ingreso de una plataforma de pagos
        
            $css->input("hidden", "idFormulario", "", "idFormulario", "", 4, "", "", "", ""); //4 sirve para indicarle al sistema que debe guardar el formulario de crear un ingreso de una plataforma
            
            $css->CrearTabla();
                $css->FilaTabla(22);
                    $css->ColTabla("<strong>INGRESOS POR PLATAFORMAS</strong>", 5);
                $css->CierraFilaTabla();
                
                
                $css->FilaTabla(14);
                    
                    $css->ColTabla("<strong>Plataforma</strong>", 1);
                    $css->ColTabla("<strong>Tercero</strong>", 3);
                    $css->ColTabla("<strong>Valor</strong>", 1);
                                        
                $css->CierraFilaTabla();
                
                $css->FilaTabla(14);
                print("<td>");
                        $css->select("CmbPlataforma", "form-control", "CmbPlataforma", "", "", "", "");

                            $sql="SELECT * FROM comercial_plataformas_pago WHERE Activa=1";
                            $Consulta=$obCon->Query($sql);
                            
                            while($DatosFormaPago=$obCon->FetchAssoc($Consulta)){
                                
                                $css->option("", "",'' , $DatosFormaPago["ID"], "", "", "", "");
                                    print($DatosFormaPago["Nombre"]);
                                $css->Coption();
                                
                            }


                        $css->Cselect();
                    print("</td>");
                    
                print("<td colspan=3>");
                        $css->select("CmbTerceroIngresoPlataformas", "form-control", "CmbTerceroIngresoPlataformas", "", "", "", "style=width:400px");

                            
                            $css->option("", "",'' , '', "", "", "", "");
                                print("Seleccione un Tercero");
                            $css->Coption();
                        $css->Cselect();
                    print("</td>");
                    
                    
                    print("<td>");
                        
                        $css->input("number", "TxtIngresoPlataforma", "form-control input-md", "TxtIngresoPlataforma", "", 0, "", "", "", "style=width:300px");
                    print("</td>");
                    
                    
                    $css->CierraFilaTabla();
                    
            $css->CerrarTabla();
            
        break;//Fin caso 14
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>