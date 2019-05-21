<?php

session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];

include_once("../clases/ReciboOrdenCompra.class.php");
include_once("../../../constructores/paginas_constructor.php");

if( !empty($_REQUEST["Accion"]) ){
    $css =  new PageConstruct("", "", 1, "", 1, 0);
    $obCon = new ReciboOrdenCompra($idUser);
    
    switch ($_REQUEST["Accion"]) {
        case 1: //dibujar la lista de administradores
            $idOrden=$obCon->normalizar($_REQUEST["idOrden"]);
            
            $DatosOrden=$obCon->DevuelveValores("ordenesdecompra", "ID", $idOrden);
            
            $css->CrearTabla();
                $css->tr("", "", 1, 1, "", "");
                    $css->th("", "", 5, 1, "", "");
                        print("<strong>Orden de Compra No. $idOrden</strong>");
                    $css->Ctd();
                $css->Ctr();
                
                $css->FilaTabla(14);
                    $css->td("", "", 1, 1, "", "");
                        print("<strong>ID</strong>");                        
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print("<strong>Descripción</strong>");                        
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print("<strong>Referencia</strong>");                        
                    $css->Ctd();
                    
                    $css->td("", "", 1, 1, "", "");
                        print("<strong>Recibido</strong>");                        
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print("<strong>Solicitado</strong>");                        
                    $css->Ctd();
                $css->Ctr();
                
                $sql="SELECT * FROM ordenesdecompra_items WHERE NumOrden='$idOrden' ORDER BY Updated DESC";
                $Consulta=$obCon->Query($sql);
                while($DatosItemOrden=$obCon->FetchAssoc($Consulta)){
                    $idItem=$DatosItemOrden["ID"];
                    $CantidadSolicitada=$DatosItemOrden["Cantidad"];
                    $Back="";
                    if($DatosItemOrden["Recibido"]>=$DatosItemOrden["Cantidad"]){
                        $Back="background-color:#a6f7af;";
                    }
                    $css->tr("", "", 1, 1, "", "","style=font-size:1.5em;$Back");
                    $css->td("", "", 1, 1, "", "");
                        print($DatosItemOrden["idProducto"]);                        
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print($DatosItemOrden["Descripcion"]);                          
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print($DatosItemOrden["Referencia"]);                          
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        $css->input("number", "TxtRecibido_$idItem", "form-control", "TxtRecibido", "Recibido", $DatosItemOrden["Recibido"], "", "off", "", "onChange=EditarCantidadRecibida(`$idItem`,`$CantidadSolicitada`)", "style=font-size:1.2em;height:50px;", "step=any", "");                       
                    $css->Ctd();
                    $css->td("", "", 1, 1, "", "");
                        print($DatosItemOrden["Cantidad"]);                         
                    $css->Ctd();
                    
                $css->Ctr();
                } 
               
            $css->Ctable();
            
        break; 
        case 2: //dibuja los datos de la tabla
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $Condicion=$obCon->normalizar($_REQUEST["Condicion"]);
            $OrdenColumna=$obCon->normalizar($_REQUEST["OrdenColumna"]);
            $AscDesc=$obCon->normalizar($_REQUEST["Orden"]);
            $NumPage=$obCon->normalizar($_REQUEST["Page"]);
            $limit=$obCon->normalizar($_REQUEST["Limit"]);
            $Condicion= utf8_decode($Condicion);
            $DatosSubMenu=$obCon->DevuelveValores("menu_submenus", "TablaAsociada", $Tabla);
            $TituloTabla=$DatosSubMenu["Nombre"];
            $startpoint = ($NumPage * $limit) - $limit;
            
            $ColumnasSeleccionadas=$obCon->getColumnasVisibles($Tabla, "");            
            $DatosConsulta=$obCon->getConsultaTabla($Tabla,$ColumnasSeleccionadas, $Condicion, $OrdenColumna, $AscDesc, $NumPage, $limit,$startpoint);
           
            $TotalRegistros=$DatosConsulta["TotalRegistros"];
            $QueryCompleto=$DatosConsulta["QueryCompleto"];
            $QueryParcial=$DatosConsulta["QueryParcial"];
            
            if($TotalRegistros>$limit){                
                $css->PaginadorTablas($Tabla, $limit, $NumPage, $TotalRegistros, "");
            }
            $js="";
            $css->CrearTablaDB($Tabla, $Tabla, "100%", $js, "");
            
                $css->CabeceraTabla($Tabla,$limit,$TituloTabla,$ColumnasSeleccionadas, $js,$TotalRegistros,$NumPage, "");
                
                $consulta=$obCon->Query($QueryCompleto);
                 
                while($DatosConsulta=$obCon->FetchAssoc($consulta)){
                    $css->FilaTablaDB($Tabla,$DatosConsulta, "", "");
                }    
            $css->CerrarTablaDB();
            
            
        break; 
        case 3: //dibujar los filtros
            
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columnas=$obCon->getColumnasVisibles($Tabla, "");
                   
            $js="";
            
            $css->fieldset("fBuscar", "", "fBuscar", "", "Buscar", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivBusquedasTablas`)'>Buscar</a>");
            $css->Clegend();   
            $css->CrearDiv("DivBusquedasTablas", "", "", 0, 0);
            $css->select("CmbColumna", "form-control", "CmbColumna", "", "", $js,"");
            foreach ($Columnas["Field"] as $key => $value) {
                $css->option("", "", $value, $value, "", "");
                    print(utf8_encode($Columnas["Visualiza"][$key]));
                $css->Coption();
            }
            $css->Cselect();
            
            $css->select("CmbCondicion", "form-control", "CmbCondicion", " ", "", $js,"");
                $value="=";
                $Display="Igual";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="*";
                $Display="Contiene";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value=">";
                $Display="Mayor que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="<";
                $Display="Menor que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                
                $value=">=";
                $Display="Mayor o Igual que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                
                $value="<=";
                $Display="Menor o Igual que";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="#%";
                $Display="Empieza por";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                
                $value="<>";
                $Display="Diferente";
                $css->option("", "", $Display, $value , "", "");
                    print($Display." (".$value.")");
                $css->Coption();
                    
            $css->Cselect();
            //$css->input("text", "TxtBusquedaTablas", "form-control", "TxtBusquedaTablas", "Valor", "", "", "", "", "");
            $Script="";
            
            $ScriptButton="onclick='AgregaCondicional()'";
            //$Script="onchange='AgregaCondicional()'";
            $css->CrearInputTextButton("text", "TxtBusquedaTablas", "BtnBuscarEnTabla", "form-control", "TxtBusquedaTablas", "BtnBuscarEnTabla", "Buscar", "Buscar", "", "Buscar", "", "", "", $Script, $ScriptButton, "", "");
            
            $css->CrearDiv("DivFiltrosAplicados", "", "", 1, 1);
                    
            $css->CerrarDiv();
            
            $css->CerrarDiv();
            
            $css->Cfieldset();
            
            
        break; 
        
        case 4: //Acciones
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columnas=$obCon->getColumnasVisibles($Tabla, "");
                   
            $js="";
            
            $css->fieldset("fOperaciones", "", "fOperaciones", "", "Operaciones", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivAccionesTablas`)'>Operaciones</a>");
            $css->Clegend();   
            $css->CrearDiv("DivAccionesTablas", "", "", 0, 0);
            
            $css->select("CmbAccionTabla", "form-control", "CmbAccionTabla", " ", "", $js,"");
                $value="SUM";
                $Display="SUMAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="COUNT";
                $Display="CONTAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="AVG";
                $Display="PROMEDIAR";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="MAX";
                $Display="MAXIMO";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                $value="MIN";
                $Display="MINIMO";
                $css->option("", "", $Display,$value, "", "");
                    print($Display);
                $css->Coption();
                
                
            $css->Cselect();    
            $css->select("CmbColumnaAcciones", "form-control", "CmbColumnaAcciones", "", "", $js,"");
            foreach ($Columnas["Field"] as $key => $value) {
                $css->option("", "", $value, $value, "", "");
                    print(utf8_encode($Columnas["Visualiza"][$key]));
                $css->Coption();
            }
            $css->Cselect();
            
            $Script="";
            
            $ScriptButton="ConsultaAccionesTablas()";
            $css->CrearBotonEvento("BtnAccionTabla", "Ejecutar", 1, "onclick", $ScriptButton, "verde", "");
            $css->CrearDiv("DivResultadosAcciones", "", "", 1, 1);
                    
            $css->CerrarDiv();            
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;
        
        case 5: //Realiza las consultas solicitadas
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]); 
            $Columna=$obCon->normalizar($_REQUEST["Columna"]); 
            $AccionTabla=$obCon->normalizar($_REQUEST["AccionTabla"]); 
            $CondicionActual=$obCon->normalizar($_REQUEST["CondicionActual"]); 
            $ColumnaSeleccionada=$obCon->normalizar($_REQUEST["ColumnaSeleccionada"]); 
            $TxtAccionSeleccionada=$obCon->normalizar($_REQUEST["TxtAccionSeleccionada"]); 
            $Condicion="";
            if($CondicionActual<>''){
                $Condicion=" WHERE $CondicionActual";
            }
            $sql="SELECT $AccionTabla($Columna) AS Resultado FROM $Tabla $Condicion";
            //print($sql);
            
            $Consulta=$obCon->Query($sql);
            $DatosConsulta=$obCon->FetchAssoc($Consulta);
            if(is_numeric($DatosConsulta["Resultado"])){
                $Resultado=number_format($DatosConsulta["Resultado"],2);
            }else{
                $Resultado=$DatosConsulta["Resultado"];
            }
            $Mensaje='<i class="fa fa-circle-o text-red"></i><span> '.$TxtAccionSeleccionada;
            $Mensaje.=" ".$ColumnaSeleccionada." = ".$Resultado;
            $Mensaje.="</span><br>";
            print($Mensaje);
             
            break;
            
        case 6: //Ocultar o Muestra un campo
            $tbl=$obCon->normalizar($_REQUEST["Tabla"]);
            $Campo=$obCon->normalizar($_REQUEST["Campo"]);
            
            $consulta=$obCon->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$tbl' AND Campo='$Campo' AND Habilitado=1");
            $DatosCampo=$obCon->FetchAssoc($consulta);
            if($DatosCampo["Visible"]<>""){
                if($DatosCampo["Visible"]==1){
                    $obCon->ActualizaRegistro("tablas_campos_control", "Visible", 0, "ID", $DatosCampo["ID"]);
                    
                }
                if($DatosCampo["Visible"]==0){
                    $obCon->ActualizaRegistro("tablas_campos_control", "Visible", 1, "ID", $DatosCampo["ID"]);
                    
                }
            }else{
                $sql="INSERT INTO `tablas_campos_control` (`ID`, `NombreTabla`, `Campo`, `Visible`, `Editable`, `Habilitado`, `TipoUser`, `idUser`) 
                    VALUES ('', '$tbl', '$Campo', 0, 1, 1, 'administrador', 3);";
                $obCon->Query($sql);

            }
            print("OK");
        break;    
        
        case 7://Dibuja la tabla que activa o desactiva los campos
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            $ColumnasDisponibles=$obCon->getColumnasDisponibles($Tabla, "");
            
            $css->fieldset("fControlCampos", "", "fControlCampos", "", "Control de Campos", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivControlCamposTabla`)'>Campos</a>");
            $css->Clegend();   
            $css->CrearDiv("DivControlCamposTabla", "", "", 0, 0);
            
            $css->CrearTabla();
                $css->FilaTabla(14);
                    $css->ColTabla("<strong>Columna</strong>", 1);
                    $css->ColTabla("<strong>Estado</strong>", 1);
                $css->CierraFilaTabla();
                
                foreach ($ColumnasDisponibles["Field"] as $key => $value) {
                    if($key>0){
                        $idCampo="ck".$value;
                        $NombreCampo=$value;
                        $css->FilaTabla(13);
                            $css->ColTabla(utf8_encode($ColumnasDisponibles["Visualiza"][$key]), 1);
                            $css->td("", "", 1, 1, "", "","");
                                $js="onchange='OcultaMuestraCampoTabla(`$Tabla`,`$NombreCampo`);'";
                                $Estado=0;                                
                                if(array_search($value, $ColumnasVisibles["Field"])){
                                    $Estado=1; 
                                }                                                       
                                $css->CheckBoxTS($idCampo, $idCampo, "", $Estado, 1, $js,"", "", "");
                            $css->Ctd();
                        $css->CierraFilaTabla();
                    }
                }
                
            $css->CerrarTabla();
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;      
        
        case 8://Dibuja las acciones
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            $ColumnasDisponibles=$obCon->getColumnasDisponibles($Tabla, "");
            
            $css->fieldset("fAcciones", "", "fAcciones", "", "Acciones", "");
            $css->legend("", "");
                print("<a href='#' onclick='MuestraOcultaXID(`DivAcciones`)'>Acciones</a>");
            $css->Clegend();   
            $css->CrearDiv("DivAcciones", "", "", 0, 0);
            $js="onclick=DibujaFormularioNuevoRegistro(`$Tabla`)";
            $DatosControlTablas=$obCon->DevuelveValores("configuracion_control_tablas", "TablaDB", $Tabla);
            if($DatosControlTablas["Agregar"]<>0 or $DatosControlTablas["Agregar"]==''){
                $css->CrearBotonEvento("BtnNuevoRegistroTabla", "Agregar Registro", 1, "onclick", "DibujaFormularioNuevoRegistro(`$Tabla`)", "azulclaro", "");
            }
            $css->CerrarDiv();            
            $css->Cfieldset();
        break;  
    
        case 9://Dibuja el formulario para agregar un registro
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            
            $css->DibujaCamposFormularioInsert($Tabla,$ColumnasVisibles, "", "");
            
        break;  
    
        case 10://Dibuja el formulario para editar un registro
                        
            $Tabla=$obCon->normalizar($_REQUEST["Tabla"]);
            $idEditar=$obCon->normalizar($_REQUEST["idEditar"]);
            
            $ColumnasVisibles=$obCon->getColumnasVisibles($Tabla, "");
            
            $css->DibujaCamposFormularioEdit($Tabla,$idEditar,$ColumnasVisibles, "", "");
            
        break;  
        
    }
    
    
          
}else{
    print("No se enviaron parametros");
}
?>