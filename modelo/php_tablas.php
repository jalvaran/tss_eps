<?php
include_once("php_conexion.php");
/*
 * Esta clase contiene los datos necesarios para tratar y dibujar las tablas
 * 
 */
class Tabla{
    public $DataBase;
    public $obCon;
    public $css;
    /*
     * Se utilizará para seleccionar las columnas de la exportacion a excel
     */
    public $Campos = array("A","B","C","D","E","F","G","H","I","J","K","L",
    "M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP");
    public $Condicionales = array(" ","=","*",">","<",">=","<=","<>","#%");
    function __construct($db){
        $this->DataBase=$db;
        $this->obCon=new ProcesoVenta(1);
        //$this->css=new CssIni("");
    }
   
    
/*
 *Funcion devolver los nombres de las columnas de una tabla
 */
    
public function Columnas($Vector){
    
    $Tabla=$Vector["Tabla"];
    $sql="SHOW COLUMNS FROM `$this->DataBase`.`$Tabla`;";
    $Results=$this->obCon->Query($sql);
    $i=0;
    while($Columnas = $this->obCon->FetchArray($Results) ){
        if($Columnas["Field"]<>''){
            $Nombres[$i]=$Columnas["Field"];
            $i++;
        }
    }
    return($Nombres);
}
   
/*
 *Funcion devolver todas los atributos de las columnas de una tablas
 */
    
public function ColumnasInfo($Vector){
    
    $Tabla=$Vector["Tabla"];
    $sql="SHOW COLUMNS FROM `$this->DataBase`.`$Tabla`;";
    $Results=$this->obCon->Query($sql);
    $i=0;
    while($Columnas = $this->obCon->FetchArray($Results) ){
        $Nombres["Field"][$i]=$Columnas["Field"];
        $Nombres["Type"][$i]=$Columnas["Type"];
        $Nombres["Null"][$i]=$Columnas["Null"];
        $Nombres["Key"][$i]=$Columnas["Key"];
        $Nombres["Default"][$i]=$Columnas["Default"];
        $Nombres["Extra"][$i]=$Columnas["Extra"];
        $i++;
        
    }
    return($Nombres);
}
/*
 *Funcion devolver el ultimo autoincremento
 */
    
public function ObtengaAutoIncrement($Vector){
    
    $Tabla=$Vector["Tabla"];
    $sql="SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='$this->DataBase' and TABLE_NAME='$Tabla'";
    $Results=$this->obCon->Query($sql);
    $Results=$this->obCon->FetchArray($Results);
    return($Results["AUTO_INCREMENT"]);
}
/*
 *Funcion devolver un ID Unico
 */
    
public function ObtengaID(){
    
    $ID=date("YmdHis").microtime(false);
    return($ID);
}
/*
 * Funcion arme filtros
 */
    
public function CreeFiltro($Vector){
    if(!isset($_REQUEST["st"])){   
        $Columnas=$this->Columnas($Vector);
        $Tabla=$Vector["Tabla"];
        $Filtro=" $Tabla";
        $z=0;
        $NumCols=count($Columnas);
        foreach($Columnas as $NombreCol){
            $IndexFiltro="Filtro_".$NombreCol;  //Campo que trae el valor del filtro a aplicar
            $IndexCondicion="Cond_".$NombreCol; // Condicional para aplicacion del filtro
            $IndexTablaVinculo="TablaVinculo_".$NombreCol; // Si hay campos vinculados se encontra la tabla vinculada aqui 
            $IndexIDTabla="IDTabla_".$NombreCol;           // Id de la tabla vinculada
            $IndexDisplay="Display_".$NombreCol;           // Campo que se quiere ver
            if(!empty($_REQUEST[$IndexFiltro])){
                $Valor=$this->obCon->normalizar($_REQUEST[$IndexFiltro]);
                if(!empty($_REQUEST[$IndexTablaVinculo])){
                    
                    switch ($_REQUEST[$IndexCondicion]){
                case 1:
                    $FiltroVinculo=" = '$Valor'";
                    break;
                case 2:
                    $FiltroVinculo=" LIKE '%$Valor%'";
                    break;
                case 3:
                    $FiltroVinculo=" > '$Valor'";
                    break;
                case 4:
                    $FiltroVinculo=" < '$Valor'";
                    break;
                case 5:
                    $FiltroVinculo=" >= '$Valor'";
                    break;
                case 6:
                    $FiltroVinculo=" <= '$Valor'";
                    break;
                case 7:
                    $FiltroVinculo=" <> '$Valor'";
                    break;
                case 8:
                    $FiltroVinculo=" LIKE '$Valor%'";
                    break;
            }
            
                    $sql="SELECT $_REQUEST[$IndexIDTabla] FROM $_REQUEST[$IndexTablaVinculo] "
                            . "WHERE $_REQUEST[$IndexDisplay] $FiltroVinculo";
                    $DatosVinculados=$this->obCon->Query($sql);
                    $DatosVinculados=$this->obCon->FetchArray($DatosVinculados);
                    //print($sql);
                    $Valor=$DatosVinculados[$_REQUEST[$IndexIDTabla]];
                }
                if($z==0){
                    $Filtro.=" WHERE ";
                    $z=1;
                }
                $Filtro.=$NombreCol;
                switch ($_REQUEST[$IndexCondicion]){
                    case 1:
                        $Filtro.=" = '$Valor'";
                        break;
                    case 2:
                        $Filtro.=" LIKE '%$Valor%'";
                        break;
                    case 3:
                        $Filtro.=" > '$Valor'";
                        break;
                    case 4:
                        $Filtro.=" < '$Valor'";
                        break;
                    case 5:
                        $Filtro.=" >= '$Valor'";
                        break;
                    case 6:
                        $Filtro.=" <= '$Valor'";
                        break;
                    case 7:
                        $Filtro.=" <> '$Valor'";
                        break;
                                    case 8:
                        $Filtro.=" LIKE '$Valor%'";
                        break;
                }
                $And=" AND ";
                $Filtro.=$And;
            }
        }
        if($z>0){
            $Filtro=substr($Filtro, 0, -4);
        }
    }else{
        $Filtro=  base64_decode($_REQUEST["st"]);
    }
    return($Filtro);
}
/*
 * Funcion arme filtros
 */
    
public function CreeFiltroCuentas($Vector){
       
    $Columnas=$this->Columnas($Vector);
    $Tabla=$Vector["Tabla"];
    $Filtro=" $Tabla WHERE (`CuentaPUC` like '2205%' or `CuentaPUC` like '2380%' or `CuentaPUC` like '21%') AND Estado ='' AND Neto < 0 ";
    $z=0;
    
    $NumCols=count($Columnas);
    foreach($Columnas as $NombreCol){
        $IndexFiltro="Filtro_".$NombreCol;  //Campo que trae el valor del filtro a aplicar
        $IndexCondicion="Cond_".$NombreCol; // Condicional para aplicacion del filtro
        $IndexTablaVinculo="TablaVinculo_".$NombreCol; // Si hay campos vinculados se encontra la tabla vinculada aqui 
        $IndexIDTabla="IDTabla_".$NombreCol;           // Id de la tabla vinculada
        $IndexDisplay="Display_".$NombreCol;           // Campo que se quiere ver
        if(!empty($_REQUEST[$IndexFiltro])){
            
            $Valor=$this->obCon->normalizar($_REQUEST[$IndexFiltro]);
            if(!empty($_REQUEST[$IndexTablaVinculo])){
                $sql="SELECT $_REQUEST[$IndexIDTabla] FROM $_REQUEST[$IndexTablaVinculo] "
                        . "WHERE $_REQUEST[$IndexDisplay] = '$Valor'";
                $DatosVinculados=$this->obCon->Query($sql);
                $DatosVinculados=$this->obCon->FetchArray($DatosVinculados);
                //print($sql);
                $Valor=$DatosVinculados[$_REQUEST[$IndexIDTabla]];
            }
            
            if($z==0){
                $Filtro.=" AND ";
                $z=1;
            }
            $Filtro.=$NombreCol;
            switch ($_REQUEST[$IndexCondicion]){
                case 1:
                    $Filtro.="='$Valor'";
                    break;
                case 2:
                    $Filtro.=" LIKE '%$Valor%'";
                    break;
                case 3:
                    $Filtro.=">'$Valor'";
                    break;
                case 4:
                    $Filtro.="<'$Valor'";
                    break;
                case 5:
                    $Filtro.=">='$Valor'";
                    break;
                case 6:
                    $Filtro.="<='$Valor'";
                    break;
                case 7:
                    $Filtro.="<>'$Valor'";
                    break;
				case 8:
                    $Filtro.=" LIKE '$Valor%'";
                    break;
            }
            $And=" AND ";
            
            
            $Filtro.=$And;
           
        }
       
    }
    
    if($z>0){
        $Filtro=substr($Filtro, 0, -4);
    }
    
    //$Filtro.=" GROUP BY `CuentaPUC`, `Tercero_Identificacion`";
    return($Filtro);
}
/*
 * Cuentas por cobrar
 * 
 */
public function CreeFiltroCobros($Vector){
       
    $Columnas=$this->Columnas($Vector);
    $Tabla=$Vector["Tabla"];
    $Filtro=" $Tabla WHERE `CuentaPUC` like '1305%' AND Estado ='' AND Neto > 0";
    $z=0;
    
    $NumCols=count($Columnas);
    foreach($Columnas as $NombreCol){
        $IndexFiltro="Filtro_".$NombreCol;  //Campo que trae el valor del filtro a aplicar
        $IndexCondicion="Cond_".$NombreCol; // Condicional para aplicacion del filtro
        $IndexTablaVinculo="TablaVinculo_".$NombreCol; // Si hay campos vinculados se encontra la tabla vinculada aqui 
        $IndexIDTabla="IDTabla_".$NombreCol;           // Id de la tabla vinculada
        $IndexDisplay="Display_".$NombreCol;           // Campo que se quiere ver
        if(!empty($_REQUEST[$IndexFiltro])){
            
            $Valor=$this->obCon->normalizar($_REQUEST[$IndexFiltro]);
            if(!empty($_REQUEST[$IndexTablaVinculo])){
                
                switch ($_REQUEST[$IndexCondicion]){
                case 1:
                    $FiltroVinculo="='$Valor'";
                    break;
                case 2:
                    $FiltroVinculo=" LIKE '%$Valor%'";
                    break;
                case 3:
                    $FiltroVinculo=">'$Valor'";
                    break;
                case 4:
                    $FiltroVinculo="<'$Valor'";
                    break;
                case 5:
                    $FiltroVinculo=">='$Valor'";
                    break;
                case 6:
                    $FiltroVinculo="<='$Valor'";
                    break;
                case 7:
                    $FiltroVinculo="<>'$Valor'";
                    break;
                case 8:
                    $FiltroVinculo=" LIKE '$Valor%'";
                    break;
            }
                
                $sql="SELECT $_REQUEST[$IndexIDTabla] FROM $_REQUEST[$IndexTablaVinculo] "
                        . "WHERE $_REQUEST[$IndexDisplay] $FiltroVinculo";
                $DatosVinculados=$this->obCon->Query($sql);
                $DatosVinculados=$this->obCon->FetchArray($DatosVinculados);
                //print($sql);
                $Valor=$DatosVinculados[$_REQUEST[$IndexIDTabla]];
            }
            
            if($z==0){
                $Filtro.=" AND ";
                $z=1;
            }
            $Filtro.=$NombreCol;
            switch ($_REQUEST[$IndexCondicion]){
                case 1:
                    $Filtro.="='$Valor'";
                    break;
                case 2:
                    $Filtro.=" LIKE '%$Valor%'";
                    break;
                case 3:
                    $Filtro.=">'$Valor'";
                    break;
                case 4:
                    $Filtro.="<'$Valor'";
                    break;
                case 5:
                    $Filtro.=">='$Valor'";
                    break;
                case 6:
                    $Filtro.="<='$Valor'";
                    break;
                case 7:
                    $Filtro.="<>'$Valor'";
                    break;
                case 8:
                    $Filtro.=" LIKE '$Valor%'";
                    break;
            }
            $And=" AND ";
            
            
            $Filtro.=$And;
           
        }
       
    }
    
    if($z>0){
        $Filtro=substr($Filtro, 0, -4);
    }
    
    //$Filtro.=" GROUP BY `CuentaPUC`, `Tercero_Identificacion`";
    //print("<pre>$Filtro</pre>");
    return($Filtro);
}
/*
 * 
 * Funcion para crear una tabla con los datos de una tabla
 * 
 */
  
public function DibujeTabla($Vector){
    //print_r($Vector);
    $this->css=new CssIni("");
    $Tabla["Tabla"]=$Vector["Tabla"];
    $tbl=$Tabla["Tabla"];
    $Titulo=$Vector["Titulo"];
    $VerDesde=$Vector["VerDesde"];
    $Limit=$Vector["Limit"];
    $Order=$Vector["Order"];
    $statement=$Vector["statement"];
    
    $Columnas=$this->Columnas($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
    
    $myPage="$Tabla[Tabla]".".php";
    if(isset($Vector["MyPage"])){
        $myPage=$Vector["MyPage"];
    }
    $NumCols=count($Columnas);
    $Compacto= urlencode(json_encode($Vector));
    //$Compacto=urlencode($Compacto);
    if(!isset($Vector["NuevoRegistro"]["Deshabilitado"])){
        $this->css->CrearFormularioEvento("FrmAgregar", "InsertarRegistro.php", "post", "_self", "");
        $this->css->CrearInputText("TxtParametros", "hidden", "", $Compacto, "", "", "", "", "", "", "", "");
        $this->css->CrearBotonNaranja("BtnAgregar", "Agregar Nuevo Registro");
        $this->css->CerrarForm();
    }
    $this->css->CrearFormularioEvento("FrmFiltros", $myPage, "post", "_self", "");
    $this->css->CrearInputText("TxtSql", "hidden", "", $statement, "", "", "", "", "", "", "", "");
    $ColFiltro=$NumCols-1;
    $this->css->CrearTabla();
    $this->css->FilaTabla(18);
    print("<td ><strong>$Titulo</strong>");
    print("</td>");
    print("<td style='text-align: left' colspan=$ColFiltro>");
    $this->css->CrearLink("$myPage","_self","Limpiar ");
    $this->css->CrearBotonVerde("BtnFiltrar", "Filtrar");
    $TxtSt=urlencode($statement);
    $TxtTabla= base64_encode($Tabla["Tabla"]);
    
    $imagerute="../images/excel.png";    
    $this->css->CrearImageLink("$myPage?BtnExportarExcel=1&TxtT=$TxtTabla&TxtL=$TxtSt", $imagerute, "_blank",50,50);
    $imagerute="../images/csv2.png";    
    $this->css->CrearImageLink("ProcesadoresJS/GeneradorCSV.php?Opcion=1&TxtT=$TxtTabla&TxtL=$TxtSt", $imagerute, "_blank",50,50);
    
    //$this->css->CrearBoton("BtnExportarExcel", "Exportar a Excel");
    //$this->css->CrearBotonNaranja("BtnVerPDF", "Exportar a PDF");
    $imagerute="../images/pdf2.png";
    
    $this->css->CrearImageLink("CreePDFFromTabla.php?BtnVerPDF=1&TxtT=$TxtTabla&TxtL=$TxtSt", $imagerute, "_blank",50,50);
    if($_SESSION["tipouser"]=='administrador'){
        $Titulo="Ajustes";
        $Nombre="ImgShowMenu";
        $RutaImage="../images/options.gif";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialTabla";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","left:10px;top:50",$VectorBim);
        
        $this->css->CrearCuadroDeDialogo("DialTabla", "Opciones para $tbl");
            $this->css->CrearDiv("DivUpdateCampo", "", "center", 1, 1);
            $this->css->CerrarDiv();
            $this->css->CrearTabla();
            
                $this->css->FilaTabla(16);
                    $this->css->ColTabla("<strong>Columna</strong>", 1);
                    $this->css->ColTabla("<strong>Visualizar</strong>", 1);
                    //$this->css->ColTabla("<strong>Editar</strong>", 1);
                    
                $this->css->CierraFilaTabla();
                
                    foreach ($Columnas as $Campo){
                        $consulta=$this->obCon->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$tbl' AND Campo='$Campo'");
                        $DatosCampo=$this->obCon->FetchArray($consulta);
                        if($DatosCampo["Habilitado"]=='' or $DatosCampo["Habilitado"]=='1'){
                            $this->css->FilaTabla(16);
                                $this->css->ColTabla($Campo, 1);
                                print("<td>");


                                $Page="Consultas/ControlCamposTablas.php?idElement=Act_$Campo&Tbl=$tbl&Campo=$Campo&Ret=";
                                $js="OnClick=EnvieObjetoConsulta2(`$Page`,`Act_$Campo`,`DivUpdateCampo`,`5`);return false;";
                                $Act=0;
                                if($DatosCampo["Visible"]=='' or $DatosCampo["Visible"]=='1'){
                                    $Act=1;
                                }
                                
                                $this->css->CheckOnOff("Act_$Campo", $js, $Act, "");
                                print("</td>");
                            $this->css->CierraFilaTabla();
                        }
                    }
                    
                
            $this->css->CerrarTabla();
            
        $this->css->CerrarCuadroDeDialogo();
    }
    print("</td>");
    $this->css->CierraFilaTabla();
    
        $this->css->FilaTabla(14);
        $i=0;
        $this->css->ColTabla("<strong>Acciones</strong>","");
        if(isset($Vector["ProductosVenta"])){
           $this->css->ColTabla("<strong>Imprimir</strong>","");
        }
        if(isset($Vector["Abonos"])){
            $this->css->ColTabla("<strong>Abonar</strong>","");
        }
        foreach($Columnas as $NombreCol){
            $consulta=$this->obCon->ConsultarTabla("tablas_campos_control", "WHERE NombreTabla='$tbl' AND Campo='$NombreCol'");
            $DatosCampo=$this->obCon->FetchArray($consulta);
            if($DatosCampo["Visible"]<>''){
                if($DatosCampo["Visible"]==0 or $DatosCampo["Habilitado"]==0){
                    $Vector["Excluir"][$NombreCol]=1;
                }
                
            } 
            
            if(isset($Vector[$NombreCol]["Link"])){
                $Colink[$i]=1;
            }
            $Ancho=50;
            if(!isset($Vector["Excluir"][$NombreCol])){
                
                print("<td><strong>$NombreCol</strong><br>");
                $Ancho=strlen($NombreCol)."0";
                if($Ancho<50){
                    $Ancho=50;
                }
                $DatosSel["Nombre"]="Cond_".$NombreCol;
                $DatosSel["Evento"]="";
                $DatosSel["Ancho"]=50;
                $DatosSel["Alto"]=30;
                $this->css->CrearSelectPers($DatosSel);
                    $IndexCondicion="Cond_".$NombreCol; // Condicional para aplicacion del filtro
                    $Activo=0;
                    for($h=1;$h<=8;$h++){
                        if(isset($_REQUEST[$IndexCondicion])){
                            if($_REQUEST[$IndexCondicion]==$h){
                               $Activo=1; 
                            }else{
                               $Activo=0; 
                            }
                              
                        }
                        
                       $this->css->CrearOptionSelect($h, $this->Condicionales[$h], $Activo);
                    }
                $this->css->CerrarSelect();
                $ValorFiltro="";
                if(!empty($_REQUEST["Filtro_".$NombreCol])){
                    $ValorFiltro=$_REQUEST["Filtro_".$NombreCol];
                }
                print("<br>");
                $this->css->CrearInputText("Filtro_".$NombreCol, "Text", "", $ValorFiltro, "Filtrar", "black", "", "", $Ancho, 30, 0, 0);
                
                print("</td>");
                $VisualizarRegistro[$i]=1;
            }
            if(isset($Vector[$NombreCol]["Vinculo"])){
                $VinculoRegistro[$i]["Vinculado"]=1;
                $VinculoRegistro[$i]["TablaVinculo"]=$Vector[$NombreCol]["TablaVinculo"];
                $VinculoRegistro[$i]["IDTabla"]=$Vector[$NombreCol]["IDTabla"];  
                $VinculoRegistro[$i]["Display"]=$Vector[$NombreCol]["Display"];
                $this->css->CrearInputText("TablaVinculo_".$NombreCol, "hidden", "", $Vector[$NombreCol]["TablaVinculo"], "", "black", "", "", $Ancho, 30, 0, 0);
                $this->css->CrearInputText("IDTabla_".$NombreCol, "hidden", "", $Vector[$NombreCol]["IDTabla"], "", "black", "", "", $Ancho, 30, 0, 0);
                $this->css->CrearInputText("Display_".$NombreCol, "hidden", "", $Vector[$NombreCol]["Display"], "", "black", "", "", $Ancho, 30, 0, 0);
            }
            
            if(isset($Vector[$NombreCol]["NewLink"]) ){
                $NewLink[$i]["Link"]=$Vector[$NombreCol]["NewLink"];
                $NewLink[$i]["Titulo"]=$Vector[$NombreCol]["NewLinkTitle"];  
            }
                
            if(isset($Vector["NewText"][$NombreCol]) ){
               $NewText[$i]["NewText"]=$Vector["NewText"][$NombreCol];
            }
            $i++;
            
        }
        
        $this->css->CierraFilaTabla();
        $this->css->CerrarForm();
        $this->css->CrearForm2("FrmItemsTabla", $myPage, "post", "_self");
        if(isset($Vector["idComprobante"])){
        $this->css->CrearInputText("idComprobante", "hidden", "", $Vector["idComprobante"], "", "", "", "", "", "", 0, 0);
        }
        $sql="SELECT * FROM $statement ORDER BY $Order LIMIT $VerDesde,$Limit ";
        $Consulta=  $this->obCon->Query($sql);
        $Parametros=urlencode(json_encode($Vector));
        while($DatosProducto=$this->obCon->FetchArray($Consulta)){
            $this->css->FilaTabla(12);
            print("<td>");
            if(!isset($Vector["VerRegistro"]["Deshabilitado"])){
                
                $Ruta="";
                if(isset($Vector["VerRegistro"]["Link"]) and isset($Vector["VerRegistro"]["ColumnaLink"])){
                    $Ruta=$Vector["VerRegistro"]["Link"];
                    $ColumnaLink=$Vector["VerRegistro"]["ColumnaLink"];
                    $Ruta.=$DatosProducto[$ColumnaLink];
                }
                
                
                
                $this->css->CrearLink($Ruta,"_blank", "Ver // ");
            }
            if(!isset($Vector["EditarRegistro"]["Deshabilitado"])){
                $Ruta="EditarRegistro.php?&TxtIdEdit=$DatosProducto[0]&TxtTabla=$Tabla[Tabla]&Others=".base64_encode($statement);
                $this->css->CrearLink($Ruta, "_self", "Editar // ");
            }
            /*
             * Espacio para nuevas acciones
             */
            if(isset($Vector["NuevaAccion"])){
                //$NumAcciones=count($Vector["NuevaAccion"]["Titulo"]);
                foreach($Vector["NuevaAccionLink"] as $NuevaAccion){
                    $TituloLink=$Vector["NuevaAccion"][$NuevaAccion]["Titulo"];
                    if($NuevaAccion=="ChkID"){
                        echo "$TituloLink: <input type='checkbox' name='ChkID[]' value=$DatosProducto[0]></input><br><br>";
                        echo "<input type='submit' name='BtnEnviarChk' value='Agregar' class='btn btn-danger'></input>";
                    }else{
                    $Target=$Vector["NuevaAccion"][$NuevaAccion]["Target"];
                    $Ruta=$Vector["NuevaAccion"][$NuevaAccion]["Link"];
                    $ColumnaLink=$Vector["NuevaAccion"][$NuevaAccion]["ColumnaLink"];
                    $Ruta.=$DatosProducto[$ColumnaLink];
                    $this->css->CrearLink($Ruta,$Target, " // $TituloLink // ");
                    }
                }
                
                
            }
            
            print("</td>");
            
            if(isset($Vector["Abonos"])){
                print("<td>");
                $idLibro=$DatosProducto[0];
                $TipoAbono=$Vector["Abonos"];
                $AbonosActuales=$this->obCon->Sume("abonos_libro", "Cantidad", "WHERE idLibroDiario='$idLibro' AND TipoAbono='$TipoAbono'");
                
                $Procesador=$Vector["Procesador"];
                $TablaAbono=$Vector["TablaAbono"];
                if($TipoAbono=="CuentasXCobrar"){
                    $Factor=1;
                }
                if($TipoAbono=="CuentasXPagar"){
                    $Factor="-1";
                }
                $Saldo=$DatosProducto["Neto"]*$Factor;
                $Saldo=$Saldo-$AbonosActuales;
                print("Saldo: $".number_format($Saldo)."<br>");
                $idFecha="TxtFecha".$DatosProducto[0];
                $idCantidad="TxtAbono".$DatosProducto[0];
                $idLink="LinkAbono".$DatosProducto[0];
                $idSelect="CmbAbono".$DatosProducto[0];
                $Page=$Vector["MyPage"];
                $this->css->CrearInputText($idFecha, "text", "Fecha: ", date("Y-m-d"), "Fecha", "black", "onchange", "CambiaLinkAbono('$idFecha',$idLibro','$idLink','$idCantidad','$idSelect','$Page','$Page','$TablaAbono')", 100, 30, 0, 0);
                print("<br>");
                $this->css->CrearInputNumber($idCantidad, "number", "Abono:", 0, "Cantidad", "black", "onchange", "CambiaLinkAbono('$idFecha','$idLibro','$idLink','$idCantidad','$idSelect','$Page','$Page','$TablaAbono')", 100, 30, 0, 0, "", $Saldo, "any");
                
                $this->css->CrearSelect($idSelect, "CambiaLinkAbono('$idFecha','$idLibro','$idLink','$idCantidad','$idSelect','$Page','$Page','$TablaAbono')");
                    $ConsultaCuentasFrecuentes=$this->obCon->ConsultarTabla("cuentasfrecuentes", "");
                    //$this->css->CrearOptionSelect(0, "Cuenta ingreso", 1);
                    while($DatosCuenta=  $this->obCon->FetchArray($ConsultaCuentasFrecuentes)){
                        $this->css->CrearOptionSelect($DatosCuenta["CuentaPUC"], $DatosCuenta["Nombre"], 0);
                    }
                $this->css->CerrarSelect();
                
                $VectorDatosExtra["ID"]=$idLink;
                $VectorDatosExtra["JS"]=' onclick="ConfirmarLink('.$idLink.');return false" ';
                //$this->css->CrearLinkID($Procesador, "_self", "Abonar",$VectorDatosExtra);
                $this->css->CrearBotonConfirmado("BtnAbonar", $DatosProducto[0]);
                print("</td>");
                
            }
            
            if(isset($Vector["ProductosVenta"])){
                print("<td>");
                $idProducto=$DatosProducto[0];
                
                $this->css->CrearInputNumber("TxtCantidadCodigos$idProducto", "number", "Cantidad:", 1, "Cantidad", "black", "", "", 100, 30, 0, 0, 1, 1000, 1);
                if(isset($Vector["Enabled_PrinterCB"])){ //productosventa Codigo de barras normal
                    $RutaPrint="ProcesadoresJS/PrintCodigoBarras.php?TipoCodigo=1&idProducto=$idProducto&TxtCantidad=";
                    $this->css->CrearBotonEvento("BtnPrintCB$idProducto", "BARRAS", 1, "onclick", "EnvieObjetoConsulta(`$RutaPrint`,`TxtCantidadCodigos$idProducto`,`DivRespuestasJS`,`0`)", "naranja", "");
                }
                if(isset($Vector["Enabled_PrinterLB"])){ //productosventa Label
                    $RutaPrint="ProcesadoresJS/PrintCodigoBarras.php?TipoCodigo=2&idProducto=$idProducto&TxtCantidad=";
                    $this->css->CrearBotonEvento("BtnPrintLB$idProducto", "LABEL", 1, "onclick", "EnvieObjetoConsulta(`$RutaPrint`,`TxtCantidadCodigos$idProducto`,`DivRespuestasJS`,`0`)", "verde", "");
                }
                if(isset($Vector["Enabled_PrinterCC"])){ //productosventa Codigo de Barras Corto
                    $RutaPrint="ProcesadoresJS/PrintCodigoBarras.php?TipoCodigo=3&idProducto=$idProducto&TxtCantidad=";
                    $this->css->CrearBotonEvento("BtnPrintMU$idProducto", "CORTO", 1, "onclick", "EnvieObjetoConsulta(`$RutaPrint`,`TxtCantidadCodigos$idProducto`,`DivRespuestasJS`,`0`)", "rojo", "");
                }
                if(isset($Vector["PrinterCB_Sistemas"])){
                    $RutaPrint="ProcesadoresJS/PrintCodigoBarras.php?TipoCodigo=4&idProducto=$idProducto&TxtCantidad=";
                    $this->css->CrearBotonEvento("BtnPrintCBS$idProducto", "BARRAS", 1, "onclick", "EnvieObjetoConsulta(`$RutaPrint`,`TxtCantidadCodigos$idProducto`,`DivRespuestasJS`,`0`)", "naranja", "");
                }
                print("</td>");
                
            }
            
            for($i=0;$i<$NumCols;$i++){
                
                
                if(isset($VisualizarRegistro[$i])){
                    
                    if(!isset($VinculoRegistro[$i]["Vinculado"])){
                        print("<td>");
                        if(isset($Colink[$i])){
                            
                            $this->css->CrearLink("../".$DatosProducto[$i], "_blank", $DatosProducto[$i]);
                        }else{
                            if(isset($NewLink[$i]["Link"])){
                                $Page=$Vector["Kit"]["Page"];
                                
                                $idProducto=$DatosProducto[0];
                                $idLink="LinkCol".$DatosProducto[0];
                                $idCantidad="TxtCantidad".$DatosProducto[0];
                                $idSelect="CmbKit".$DatosProducto[0];
                                $this->css->CrearSelect($idSelect, "CambiaLinkKit('$idProducto','$idLink','$idCantidad','$idSelect','$Page')");
                                    $ConsultaKits=$this->obCon->ConsultarTabla("kits", "");
                                    $this->css->CrearOptionSelect(0, "Seleccione un kit", 1);
                                    while($DatosKits=  $this->obCon->FetchArray($ConsultaKits)){
                                        $this->css->CrearOptionSelect($DatosKits["ID"], $DatosKits["Nombre"], 0);
                                    }
                                $this->css->CerrarSelect();
                                $this->css->CrearInputNumber($idCantidad, "number", "", 0, "Cantidad", "black", "onchange", "CambiaLinkKit('$idProducto','$idLink','$idCantidad','$idSelect','$Page')", 100, 30, 0, 0, 0, "", "any");
                                $VectorDatosExtra["ID"]=$idLink;
                                $this->css->CrearLinkID($NewLink[$i]["Link"], "_self", $NewLink[$i]["Titulo"],$VectorDatosExtra);
                            }else{
                                include_once("../VAtencion/ConfiguracionesGenerales/Edicion.Conf.php");
                                $NomCol=$Columnas[$i];
                                $idTabla=$Columnas[0];
                                if($i>0 and $Columnas[$i]<>"Sync" and $Columnas[$i]<>"Updated" and !isset($Vector[$tbl]["Excluir"][$NomCol]) and !isset($Vector["EditarRegistro"]["Deshabilitado"])){
                                    $idElement="TxtDatos_".$tbl."_".$Columnas[$i]."_".$DatosProducto[0];
                                    $idEdit=$DatosProducto[0];
                                    $this->css->CrearTextArea($idElement, "", $DatosProducto[$i], "", "", "onChange", "EditeRegistro(`$tbl`,`$NomCol`,`$idTabla`,`$idEdit`,`$idElement`)", "", "", 0, 1,0);
                                }else{
                                //$this->css->CrearInputText("TxtDatos$DatosProducto[0]", "text", "", $DatosProducto[$i], "", "", "Evento", "JS", "", "", 0, 1);
                                    print("$DatosProducto[$i]"); 
                                }
                            }
                        }
                        print("</td>");
                       
                    }else{
                        $TablaVinculo=$VinculoRegistro[$i]["TablaVinculo"];
                        $ColDisplay=$VinculoRegistro[$i]["Display"];
                        $idTablaVinculo=$VinculoRegistro[$i]["IDTabla"];
                        $ID=$DatosProducto[$i];
                        //print("datos: $TablaVinculo $ColDisplay $idTablaVinculo $ID");                    
                        $sql1="SELECT $ColDisplay FROM $TablaVinculo WHERE $idTablaVinculo='$ID'";
                        $Consul=$this->obCon->Query($sql1);
                        $DatosVinculo=$this->obCon->FetchArray($Consul);
                        
                        print("<td>");
                        if(isset($Colink[$i])){
                            
                            $this->css->CrearLink("../".$DatosVinculo[$ColDisplay], "_blank", $DatosVinculo[$ColDisplay]);
                        }else{
                            
                            print("$DatosVinculo[$ColDisplay]");
                            
                        }
                        print("</td>");
                        
                    }
                }
            }
            print("</tr>");
        }
        $this->css->CierraFilaTabla();
    $this->css->CerrarForm();
    $this->css->CerrarTabla();
    
    
    //return($sql);
}
 
/*
 * Verificamos si hay peticiones de exportacion
 */
    
public function VerifiqueExport($Vector)  {
    
    if(isset($_REQUEST["BtnExportarExcel"])){
       $statement= urldecode($_REQUEST["TxtL"]);
    require_once '../librerias/Excel/PHPExcel.php';
   $objPHPExcel = new PHPExcel();    
     
     //$Tabla["Tabla"]=$Vector["Tabla"];
    $tbl= base64_decode($_REQUEST["TxtT"]);
    $Tabla["Tabla"]=$tbl;
    $Titulo=$Vector["Titulo"];
    $VerDesde=$Vector["VerDesde"];
    $Limit=$Vector["Limit"];
    $Order=$Vector["Order"];
    
    $tbl=$Vector["Tabla"];
    
    
    $Columnas=$this->Columnas($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
   
    $i=0;
 $a=0;
 foreach($Columnas as $NombreCol){ 
     if(!isset($Vector["Excluir"][$NombreCol])){
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$a]."1",$NombreCol);
        $VisualizarRegistro[$i]=1;
        $a++;	
     }
     if(isset($Vector[$NombreCol]["Vinculo"])){
                $VinculoRegistro[$i]["Vinculado"]=1;
                $VinculoRegistro[$i]["TablaVinculo"]=$Vector[$NombreCol]["TablaVinculo"];
                $VinculoRegistro[$i]["IDTabla"]=$Vector[$NombreCol]["IDTabla"];  
                $VinculoRegistro[$i]["Display"]=$Vector[$NombreCol]["Display"];
     }
     $i++;
 }
    
    
   $IndexFiltro="Filtro_".$NombreCol;  //Campo que trae el valor del filtro a aplicar
    $IndexCondicion="Cond_".$NombreCol; // Condicional para aplicacion del filtro
    $IndexTablaVinculo="TablaVinculo_".$NombreCol; // Si hay campos vinculados se encontra la tabla vinculada aqui 
    $IndexIDTabla="IDTabla_".$NombreCol;           // Id de la tabla vinculada
    $IndexDisplay="Display_".$NombreCol;           // Campo que se quiere ver
        
   
    
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar $tbl  desde base de datos")
        ->setSubject("$tbl")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones")
        ->setCategory("$tbl");    
 $NumCols=count($Columnas);
 
 
 $i=0;
 $a=0;
 $c=2;
    $sql="SELECT * FROM $statement ";
 
        $Consulta=  $this->obCon->Query($sql);
        while($DatosTabla=$this->obCon->fetch_object($Consulta)){
            foreach($Columnas as $NombreCol){
                if(isset($VisualizarRegistro[$i])){
                    if(!isset($VinculoRegistro[$i]["Vinculado"])){
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[$a].$c,$DatosTabla->$NombreCol);
                    }else{
                        $TablaVinculo=$VinculoRegistro[$i]["TablaVinculo"];
                        $ColDisplay=$VinculoRegistro[$i]["Display"];
                        $idTablaVinculo=$VinculoRegistro[$i]["IDTabla"];
                        $ID=$DatosTabla->$NombreCol;
                        //print("datos: $TablaVinculo $ColDisplay $idTablaVinculo $ID");                    
                        $sql1="SELECT $ColDisplay  FROM $TablaVinculo WHERE $idTablaVinculo ='$ID'";
                        $Consul=$this->obCon->Query($sql1);
                        $DatosVinculo=  $this->obCon->FetchArray($Consul);
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($this->Campos[$a].$c,$DatosVinculo[$ColDisplay]);
                    }
                    $a++;
                    
                }
                
                $i++;
                if($i==$NumCols){
                    $i=0;
                    $c++;
                    $a=0;
                }
            }
        }
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$tbl.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
   
    }    
     
    
}
/*
 * Verificamos si hay peticiones de exportacion para el kardex
 */
    
public function VerifiqueExportKardex($Vector)  {
    
    if(isset($_REQUEST["BtnExportarExcel"])){
       $statement= base64_decode($_REQUEST["TxtL"]);
    require_once '../librerias/Excel/PHPExcel.php';
   $objPHPExcel = new PHPExcel();    
        
   $Tabla["Tabla"]=$Vector["Tabla"];
    $tbl=$Tabla["Tabla"];
    $Titulo=$Vector["Titulo"];
    $VerDesde=$Vector["VerDesde"];
    $Limit=$Vector["Limit"];
    $Order=$Vector["Order"];
    
    $tbl=$Vector["Tabla"];
    $Columnas=$this->Columnas($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
   
    $i=0;
 $a=0;
 
 $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$a]."1","Fecha")
            ->setCellValue($this->Campos[$a++]."1","Movimiento")
            ->setCellValue($this->Campos[$a++]."1","Detalle")
            ->setCellValue($this->Campos[$a++]."1","idDocumento")
            ->setCellValue($this->Campos[$a++]."1","Cantidad")    
            ->setCellValue($this->Campos[$a++]."1","ValorUnitario")
            ->setCellValue($this->Campos[$a++]."1","ValorTotal")
            ->setCellValue($this->Campos[$a++]."1","idProductosVenta")    
            ->setCellValue($this->Campos[$a++]."1","Referencia")
          ->setCellValue($this->Campos[$a++]."1","Nombre")
         ->setCellValue($this->Campos[$a++]."1","Departamento")
         ->setCellValue($this->Campos[$a++]."1","Sub1")
         ->setCellValue($this->Campos[$a++]."1","Sub2")
         ->setCellValue($this->Campos[$a++]."1","Sub3")
         ->setCellValue($this->Campos[$a++]."1","Sub4")
         ->setCellValue($this->Campos[$a++]."1","Sub5");
    
    
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar $tbl  desde base de datos")
        ->setSubject("$tbl")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones")
        ->setCategory("$tbl");    
  
 $i=1;
 $a=0;
 $c=2;
    $statement=str_replace("kardexmercancias", " ", $statement);
    $sql="SELECT * FROM kardexmercancias INNER JOIN productosventa ON  kardexmercancias.ProductosVenta_idProductosVenta=productosventa.idProductosVenta $statement";
 
    $Consulta=  $this->obCon->Query($sql);
    while($DatosKardex=  $this->obCon->FetchArray($Consulta)){
        
        $i++;
        $a=0;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$a].$i,$DatosKardex["Fecha"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Movimiento"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Detalle"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["idDocumento"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Cantidad"])    
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["ValorUnitario"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["ValorTotal"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["idProductosVenta"])    
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Referencia"])
            ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Nombre"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Departamento"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Sub1"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Sub2"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Sub3"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Sub4"])
         ->setCellValue($this->Campos[$a++].$i,$DatosKardex["Sub5"]);
    }
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$tbl.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
   
    }    
     
    
}
/*
 * Verificamos si hay peticiones de exportacion
 */
    
public function GenereInformeDepartamento($Mes,$Anio,$Vector)  {
   $FechaIni=$Anio."-".$Mes."01";
   $FechaFin=$Anio."-".$Mes."31";
    require_once '../librerias/Excel/PHPExcel.php';
   $objPHPExcel = new PHPExcel();    
   $Consulta=  $this->obCon->ConsultarTabla("prod_departamentos", "");   
   $i=0;
   while($DatosDepartamentos=$this->obCon->FetchArray($Consulta)){
       $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$i]."1",$DatosDepartamentos["Nombre"]);
       $i++;
   }
   
   
    
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar Informe  desde base de datos")
        ->setSubject("Informe")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones")
        ->setCategory("Informe Departamentos");    
 
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'."Informe_Departamentos".'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
   
      
}
/*
 * 
 * Funcion para crear un formulario para crear un nuevo registro en una tabla
 * 
 */
    
public function FormularioInsertRegistro($Parametros,$VarInsert)  {
    //print_r($Vector);
    $this->css=new CssIni("");
    $Tabla["Tabla"]=$Parametros->Tabla;
    $tbl=$Tabla["Tabla"];
    $Titulo=$Parametros->Titulo;
    
    $Columnas=$this->Columnas($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
    $ColumnasInfo=$this->ColumnasInfo($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
    
    $myPage="$Tabla[Tabla]".".php";
    $NumCols=count($Columnas);
    
    $this->css->CrearFormularioEvento("FrmGuardarRegistro", "procesadores/procesaInsercion.php", "post", "_self", "");
    $this->css->CrearInputText("TxtTablaInsert", "hidden", "", $tbl, "", "", "", "", "", "", "", "");
    $this->css->CrearTabla();
    $this->css->FilaTabla(18);
    print("<td style='text-align: center'><strong>$Titulo</strong>");
    print("</td>");
    $this->css->CierraFilaTabla();
    
    
    $i=0;
        
    foreach($Columnas as $NombreCol){
        $this->css->FilaTabla(14);
        $excluir=0;
        
        if(isset($VarInsert[$tbl][$NombreCol]["Excluir"]) or $NombreCol=="Updated" or $NombreCol=="Sync"){
            $excluir=1;
        }
        $TipoText="text";
        if(isset($VarInsert[$tbl][$NombreCol]["TipoText"])){
            $TipoText=$VarInsert[$tbl][$NombreCol]["TipoText"];
        }
        if(!$excluir){  //Si la columna no está excluida
           $DateBox=0;
           $lengCampo=preg_replace('/[^0-9]+/', '', $ColumnasInfo["Type"][$i]); //Determinamos la longitud del campo
           if($lengCampo<1){
               $lengCampo=45;
           }
           if($ColumnasInfo["Type"][$i]=="text"){
               $lengCampo=100;
           }
           if($ColumnasInfo["Type"][$i]=="date"){
               $DateBox=1;
           }
           $Value=$ColumnasInfo["Default"][$i];
           $Required=0;
           $ReadOnly=0;
           if($ColumnasInfo["Key"][$i]=="PRI"){ //Verificamos si la llave es primaria
                
                $Required=1;
                if(!$ColumnasInfo["Extra"][$i]=="auto_increment"){ //Verificamos si tiene auto increment
                   $Value = $this->ObtengaID(); //Obtiene un timestamp para crear un id unico
                }else{
                   $ReadOnly=1; 
                }
           }else{
                $ReadOnly=0;
           }
           
           if(isset($VarInsert[$tbl][$NombreCol]["Required"])){
               $Required=1;
           }
            
            print("<td style='text-align: center'>");
            
            print($NombreCol."<br>");
            if(property_exists($Parametros,$NombreCol) and $NombreCol<>"Soporte"){
                $Display=$Parametros->$NombreCol->Display;
                $IDTabla=$Parametros->$NombreCol->IDTabla;
                $TablaVinculo=$Parametros->$NombreCol->TablaVinculo;
                if($Display<>"CodigoBarras"){
                    $sql="SELECT * FROM $TablaVinculo";
                    //print($sql);
                    $Consulta=$this->obCon->Query($sql);
                    $VectorSel["Nombre"]="$NombreCol";
                    $VectorSel["Evento"]="";
                    $VectorSel["Funcion"]="";
                    $VectorSel["Required"]=$Required;
                    $VarSelect["Ancho"]=100;
                    $VarSelect["PlaceHolder"]="Seleccione una opcion";
                    //$this->css->CrearSelect2($VectorSel);
                    $this->css->CrearSelectChosen($NombreCol, $VarSelect);
                    $this->css->CrearOptionSelect("", "Seleccione Una Opcion", 0);
                    while($Opciones=$this->obCon->FetchArray($Consulta)){
                        $pre=0;
                        if($Parametros->$NombreCol->Predeterminado==$Opciones[$IDTabla]){
                            $pre=1;
                        }
                        $this->css->CrearOptionSelect($Opciones[$IDTabla], $Opciones[$IDTabla]." - ".$Opciones[$Display]." - ".$Opciones[2], $pre);              
                    }
                    $this->css->CerrarSelect(); 
                }else{
                    
                        $this->css->CrearInputText("$NombreCol", $TipoText, "", "", "$NombreCol", "black", "", "", $lengCampo."0", 30, 1, $Required);
                        
                }
            }else{
                if($lengCampo<100){
                    if($NombreCol=="RutaImagen" or $NombreCol=="Soporte"){
                        $this->css->CrearUpload($NombreCol);
                    }else{
                        if($DateBox==0){
                            $this->css->CrearInputText("$NombreCol", $TipoText, "", $Value, "$NombreCol", "black", "", "", $lengCampo."0", 30, $ReadOnly, $Required);    
                        }
                        if($DateBox==1){
                            $this->css->CrearInputFecha("", $NombreCol, date("Y-m-d"), 100, 30, "");
                        }
                    }
                }else{
                    if($NombreCol=="RutaImagen" or $NombreCol=="Soporte"){
                        $this->css->CrearUpload($NombreCol);
                    }else{    
                        $this->css->CrearTextArea("$NombreCol", "", $Value, "", "$NombreCol", "black", "", "","100",$lengCampo."0", $ReadOnly, 1);
                    }
                    
                }
            }
                print("<td></tr>");    
        }
        $i++;
    }
    $this->css->FilaTabla(18);
    print("<td style='text-align: center'>");
    $this->css->CrearBotonConfirmado("BtnGuardarRegistro", "Guardar Registro"); 
    print("</td>");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla();
    $this->css->CerrarForm();    
    //return($sql);
}
/*
 * 
 * Funcion para crear un formulario de edicion de un registro
 * 
 */
    
public function FormularioEditarRegistro($Parametros,$VarEdit,$TablaEdit)  {
    //print_r($Vector);
    $this->css=new CssIni("");
    $Tabla["Tabla"]=$TablaEdit;
    $tbl=$Tabla["Tabla"];
    $Titulo=$TablaEdit;
    $IDEdit=$VarEdit["ID"];
    $stament=$VarEdit["stament"];
    $Columnas=$this->Columnas($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
    $ColumnasInfo=$this->ColumnasInfo($Tabla); //Se debe disenar la base de datos colocando siempre la llave primaria de primera
    
    $myPage="$Tabla[Tabla]".".php";
    if(isset($VarEdit[$tbl]["MyPage"])){
        $myPage=$VarEdit[$tbl]["MyPage"];
    }
    $NumCols=count($Columnas);
    
    $this->css->CrearFormularioEvento("FrmGuardarRegistro", "procesadores/procesaEdicion.php", "post", "_self", "");
    $this->css->CrearInputText("TxtTablaEdit", "hidden", "", $tbl, "", "", "", "", "", "", "", "");
    $this->css->CrearInputText("TxtIDEdit", "hidden", "", $IDEdit, "", "", "", "", "", "", "", "");
    $this->css->CrearInputText("TxtMyPage", "hidden", "", $myPage, "", "", "", "", "", "", "", "");
    $this->css->CrearInputText("TxtStament", "hidden", "", $stament, "", "", "", "", "", "", "", "");
    $this->css->CrearTabla();
    $this->css->FilaTabla(18);
    print("<td style='text-align: center'><strong>$Titulo</strong>");
    print("</td>");
    $this->css->CierraFilaTabla();
    
    
    $i=0;
       
    foreach($Columnas as $NombreCol){
        $this->css->FilaTabla(14);
        $excluir=0;
        $TipoText="text";
        if(isset($VarEdit[$tbl][$NombreCol]["TipoText"])){
            $TipoText=$VarEdit[$tbl][$NombreCol]["TipoText"];
        }
        if(isset($VarEdit[$tbl]["Excluir"][$NombreCol])){
            $excluir=1;
        }
        if(!$excluir){  //Si la columna no está excluida
           $lengCampo=preg_replace('/[^0-9]+/', '', $ColumnasInfo["Type"][$i]); //Determinamos la longitud del campo
           if($lengCampo<1){
               $lengCampo=45;
           }
           if($ColumnasInfo["Type"][$i]=="text"){
               $lengCampo=100;
           }
           $ColID=$Columnas[0];
           $Condicion=" $ColID='$IDEdit'";
           $SelColumnas=$NombreCol;
           $DatosRegistro =  $this->obCon->ValorActual($tbl, $SelColumnas, $Condicion);
           $Value=$DatosRegistro[$NombreCol];
           $Required=0;
           $ReadOnly=0;
           if($ColumnasInfo["Key"][$i]=="PRI"){ //Verificamos si la llave es primaria
                $Required=1;
                if($ColumnasInfo["Extra"][$i]=="auto_increment"){ 
                    $ReadOnly=1;
                }
                    
           }else{
                $ReadOnly=0;
           }
           
           if(isset($VarEdit[$tbl]["Required"][$NombreCol])){
               $Required=1;
           }
            
            print("<td style='text-align: center'>");
            
            print($NombreCol."<br>");
            if(isset($VarEdit[$tbl][$NombreCol]["Vinculo"])){
                $Display=$VarEdit[$tbl][$NombreCol]["Display"];
                $IDTabla=$VarEdit[$tbl][$NombreCol]["IDTabla"];
                $TablaVinculo=$VarEdit[$tbl][$NombreCol]["TablaVinculo"];
                
                $sql="SELECT * FROM $TablaVinculo";
                $Consulta=$this->obCon->Query($sql);
                $VectorSel["Nombre"]="$NombreCol";
                $VectorSel["Evento"]="";
                $VectorSel["Funcion"]="";
                $VectorSel["Required"]=$Required;
                
                //$this->css->CrearSelectChosen($NombreCol,$VectorSel);
                $this->css->CrearSelect2($VectorSel);
                $this->css->CrearOptionSelect("", "Seleccione Una Opcion", 0);
                while($Opciones=$this->obCon->FetchArray($Consulta)){
                    $pre=0;
                    if($Value==$Opciones[$IDTabla]){
                        $pre=1;
                    }
                    $this->css->CrearOptionSelect($Opciones[$IDTabla], $Opciones[$IDTabla]."-".$Opciones[$Display]."-".$Opciones[2], $pre);              
                }
                $this->css->CerrarSelect(); 
                
            }else{
                if($NombreCol=="RutaImagen" or $NombreCol=="Soporte"){
                    $this->css->CrearUpload($NombreCol);
                }else{
                    if($lengCampo<100){
                        $this->css->CrearInputText("$NombreCol", $TipoText, "", $Value, "$NombreCol", "black", "", "", $lengCampo."0", 30, $ReadOnly, $Required);
                    }else{
                        $this->css->CrearTextArea("$NombreCol", "", $Value, "", "$NombreCol", "black", "", "","100",$lengCampo."0", $ReadOnly, 1);
                    }
                }
            }
                print("<td></tr>");    
        }
        $i++;
    }
    $this->css->FilaTabla(18);
    print("<td style='text-align: center'>");
    $this->css->CrearBotonConfirmado("BtnEditarRegistro", "Editar Registro"); 
    print("</td>");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla();
    $this->css->CerrarForm();    
    //return($sql);
}
/*
 * 
 * Funcion para dibujar las cuentas por pagar
 * 
 */
    
public function DibujaCuentasXPagar($VarCuentas)  {
    $this->css=new CssIni("");
    $TipoAbono=$VarCuentas["Abonos"];
    $sql="SELECT `Neto` as Saldos, `Tercero_Identificacion` as Tercero, CuentaPUC, "
            . "`NombreCuenta`,`Tipo_Documento_Intero`,`Num_Documento_Interno`,`idLibroDiario`  FROM `librodiario` "
            . "WHERE `CuentaPUC` like '2%' AND `Estado`='' AND `Neto`<0 GROUP BY `CuentaPUC`, `Tercero_Identificacion` ";
    $Datos=$this->obCon->Query($sql);
    
    $this->css->CrearTabla();
    $this->css->FilaTabla(14);
    echo "<td><strong>CUENTA</strong></td>";
    echo "<td><strong>NOMBRE</strong></td>";
    echo "<td><strong>DOCUMENTO</strong></td>"; 
    echo "<td><strong>NUMERO</strong></td>";
    echo "<td><strong>TERCERO</strong></td>";
    echo "<td><strong>SALDO</strong></td>";
    echo "<td><strong>AGREGAR</strong></td>";
    
    while($DatosCuentas=$this->obCon->FetchArray($Datos)){
        $idLibro=$DatosCuentas["idLibroDiario"];
        $AbonosActuales=$this->obCon->Sume("abonos_libro", "Cantidad", "WHERE idLibroDiario='$idLibro' AND TipoAbono='$TipoAbono'");
        
        $SaldoTotal=($DatosCuentas["Saldos"]*(-1))-$AbonosActuales;
        $this->css->FilaTabla(12);
        
        echo"<td>$DatosCuentas[CuentaPUC]</td>";
        echo"<td>$DatosCuentas[NombreCuenta]</td>";
        echo"<td>$DatosCuentas[Tipo_Documento_Intero]</td>";
        echo"<td>$DatosCuentas[Num_Documento_Interno]</td>";
        echo"<td>$DatosCuentas[Tercero]</td>";
        echo"<td>$SaldoTotal</td>";
        echo"<td>$DatosCuentas[idLibroDiario]</td>";
    }
    
    $this->css->CerrarTabla();
}
///Esta funcion permite dibujar un cuadro de dialogo para crear un cliente
public function CrearCuadroClientes ($id,$titulo,$myPage,$VectorCDC){
        $this->css=new CssIni("");
    /////////////////Cuadro de dialogo de Clientes create
	$this->css->CrearCuadroDeDialogo($id,$titulo); 
	 
        $this->css->CrearForm2("FrmCrearCliente",$myPage,"post","_self");
        $this->css->CrearSelect("CmbTipoDocumento","Oculta()");
        $this->css->CrearOptionSelect('13','Cedula',1);
        $this->css->CrearOptionSelect('31','NIT',0);
        $this->css->CerrarSelect();
        //$css->CrearInputText("CmbPreVentaAct","hidden","",$idPreventa,"","","","",0,0,0,0);
        $this->css->CrearInputText("TxtNIT","number","","","Identificacion","black","","",200,30,0,1);
        $this->css->CrearInputText("TxtPA","text","","","Primer Apellido","black","onkeyup","CreaRazonSocial()",200,30,0,0);
        $this->css->CrearInputText("TxtSA","text","","","Segundo Apellido","black","onkeyup","CreaRazonSocial()",200,30,0,0);
        $this->css->CrearInputText("TxtPN","text","","","Primer Nombre","black","onkeyup","CreaRazonSocial()",200,30,0,0);
        $this->css->CrearInputText("TxtON","text","","","Otros Nombres","black","onkeyup","CreaRazonSocial()",200,30,0,0);
        $this->css->CrearInputText("TxtRazonSocial","text","","","Razon Social","black","","",200,30,0,1);
        $this->css->CrearInputText("TxtDireccion","text","","","Direccion","black","","",200,30,0,1);
        $this->css->CrearInputText("TxtTelefono","text","","","Telefono","black","","",200,30,0,1);
        $this->css->CrearInputText("TxtEmail","text","","","Email","black","","",200,30,0,1);
        $VarSelect["Ancho"]="200";
        $VarSelect["PlaceHolder"]="Seleccione el municipio";
        $this->css->CrearSelectChosen("CmbCodMunicipio", $VarSelect);
        $sql="SELECT * FROM cod_municipios_dptos";
        $Consulta=$this->obCon->Query($sql);
           while($DatosMunicipios=$this->obCon->FetchArray($Consulta)){
               $Sel=0;
               if($DatosMunicipios["ID"]==1011){
                   $Sel=1;
               }
               $this->css->CrearOptionSelect($DatosMunicipios["ID"], $DatosMunicipios["Ciudad"], $Sel);
           }
        $this->css->CerrarSelect();
        echo '<br><br>';
        $this->css->CrearBoton("BtnCrearCliente", "Crear Cliente");
        $this->css->CerrarForm();
	$this->css->CerrarCuadroDeDialogo(); 
}
//Funcion para Crear un Cuadro de dialogo que permita crear un servicio nuevo
public function CrearCuadroCrearServicios($id,$titulo,$myPage,$idClientes,$VectorCDSer){
        $this->css=new CssIni("");
        $DatosTabla=$this->obCon->DevuelveValores("tablas_ventas", "NombreTabla", "servicios");
    /////////////////Cuadro de dialogo de Clientes create
	$this->css->CrearCuadroDeDialogo($id,$titulo); 
	 
        $this->css->CrearForm2("FrmCrearItemServicio",$myPage,"post","_self");
        $this->css->CrearInputText("TxtIdCliente","hidden","",$idClientes,"Precio Venta","black","","",200,30,0,1);
        if(isset($VectorCDSer["servitorno"])){
            $this->css->CrearInputText("TxtServitorno","hidden","",$VectorCDSer["servitorno"],"Precio Venta","black","","",200,30,0,1);
        }
        
        $this->css->CrearTextArea("TxtNombre", "", "", "Descripcion", "", "", "", 200, 100, 0, 1);
        
        echo '<br>';
        
        if(isset($VectorCDSer["servitorno"])){
            $TotalCostos=$this->obCon->Sume("costos", "ValorCosto", "");
            $TotalCostos=$TotalCostos/192;
            $this->css->CrearInputNumber("TxtCantidadPiezas","number","Cantidad de piezas:<br>",1,"Piezas","black","onkeyup","Servitorno_CalculePrecioVenta('$TotalCostos')",200,30,0,1,1,"",1);
            $this->css->CrearInputNumber("TxtNumMaquinas","number","Maquinas:<br>",3,"Maquinas","black","onkeyup","Servitorno_CalculePrecioVenta('$TotalCostos')",200,30,0,1,1,"",1);
            $this->css->CrearInputNumber("TxtMargen","number","Margen:<br>","0.58825","Margen","black","onkeyup","Servitorno_CalculePrecioVenta('$TotalCostos')",200,30,0,1,0,"","any");
            $this->css->CrearInputNumber("TxtTiempoMaquinas","number","Tiempo Maquina:<br>",1,"Tiempo en Maquina","black","onkeyup","Servitorno_CalculePrecioVenta('$TotalCostos')",200,30,0,1,0,"","any");
            $this->css->CrearInputNumber("TxtValorMateriales","number","<br>Valor de Materiales:<br>","","Valor de Materiales","black","onkeyup","Servitorno_CalculePrecioVenta('$TotalCostos')",200,30,0,1,0,"","any");
        }
        print("</br>");
        
        $this->css->CrearInputNumber("TxtPrecioVenta","number","PrecioVenta:<br>","","Precio Venta","black","","",200,30,0,1,1,"","");
        print("</br>");
        $this->css->CrearInputNumber("TxtCostoUnitario","number","CostoUnitario:<br>","","Costo Unitario","black","","",200,30,0,1,1,"","");
        print("</br>");
        //$this->css->CrearInputText("TxtCuentaPUC","number","","","Cuenta Contable","black","","",200,30,0,1);
        
        $VarSelect["Ancho"]="200";
        $VarSelect["PlaceHolder"]="Seleccione el Departamento";
        $VarSelect["Required"]=1;
        $this->css->CrearSelectChosen("CmbDepartamento", $VarSelect);
        $sql="SELECT * FROM prod_departamentos";
        $Consulta=$this->obCon->Query($sql);
        $this->css->CrearOptionSelect("", "Seleccione un Departamento", 0);
           while($DatosDepartamentos=$this->obCon->FetchArray($Consulta)){
                              
               $this->css->CrearOptionSelect($DatosDepartamentos["idDepartamentos"], $DatosDepartamentos["Nombre"], 0);
           }
        $this->css->CerrarSelect();
        print("</br></br>");
        $VarSelect["Ancho"]="200";
        $VarSelect["PlaceHolder"]="Seleccione la cuenta contable";
        $VarSelect["Required"]=1;
        $this->css->CrearSelectChosen("TxtCuentaPUC", $VarSelect);
        $sql="SELECT * FROM subcuentas WHERE PUC LIKE '41%'";
        $Consulta=$this->obCon->Query($sql);
        $this->css->CrearOptionSelect("", "Seleccione una cuenta contable", 0);
           while($DatosCuenta=$this->obCon->FetchArray($Consulta)){
               $sel=0;
               if($DatosTabla["CuentaPUCDefecto"]==$DatosCuenta["PUC"]){
                   $sel=1;
               }               
               $this->css->CrearOptionSelect($DatosCuenta["PUC"],"$DatosCuenta[PUC] $DatosCuenta[Nombre]", $sel);
           }
        $this->css->CerrarSelect();
        echo '<br>';
        print("</br>");
        $DatosEmpresa=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro",1 );
        $IVA="";
        if($DatosEmpresa["Regimen"]=="COMUN"){
            $IVA=0.19;
        }else if($DatosEmpresa["Regimen"]=="SIMPLIFICADO"){
            $IVA=0;
        }
        $VarSelect["Ancho"]="200";
        $VarSelect["PlaceHolder"]="Seleccione el IVA";
        $VarSelect["Required"]=1;
        $this->css->CrearSelectChosen("CmbIVA", $VarSelect);
        $sql="SELECT * FROM porcentajes_iva";
        $Consulta=$this->obCon->Query($sql);
        $this->css->CrearOptionSelect("", "Seleccione el IVA", 0);
           while($DatosIVA=$this->obCon->FetchArray($Consulta)){
               $sel=0;
               if($IVA==$DatosIVA["Valor"]){
                   $sel=1;
               }               
               $this->css->CrearOptionSelect($DatosIVA["Valor"], $DatosIVA["Nombre"], $sel);
           }
        $this->css->CerrarSelect();
               
        $this->css->CrearBoton("BtnCrearServicios", "Crear Servicio");
        $this->css->CerrarForm();
	$this->css->CerrarCuadroDeDialogo(); 
}
//Funcion para Crear un Cuadro de dialogo que permita crear un servicio nuevo
public function CrearCuadroBusqueda($myPage,$Hidden1,$ValHiden1,$Hidden2,$ValHiden2,$VectorCuaBus){
    $this->css=new CssIni("");
    $this->css->CrearForm2("FrmBuscarItem","$myPage","post","_self");
            
    $this->css->CrearInputText($Hidden1,"hidden","",$ValHiden1,"","","","",0,0,0,0);
    $this->css->CrearInputText($Hidden2,"hidden","",$ValHiden2,"","","","",0,0,0,0);
    $this->css->CrearInputText("TxtBusqueda", "text", "", "", "Buscar Item", "black", "", "", 200, 30, 0, 0);
    $this->css->CerrarForm();
}
//Funcion para Dibujar un item buscado en las tablas de ventas
public function DibujeItemsBuscadosVentas($key,$PageReturn,$Variable){
    $this->css=new CssIni("");
    
    $Titulo="Crear Item En servicios";
    $Nombre="ShowItemsBusqueda";
    $RutaImage="";
    $javascript="";
    $VectorBim["f"]=0;
    $target="#DialBusquedaItems";
    $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",0,0,"fixed","left:10px;top:100",$VectorBim);
    
    $VectorDialogo["F"]=0;
    $this->css->CrearCuadroDeDialogo("DialBusquedaItems", "Resultados");
    $this->css->CrearDiv("DivBusqueda", "", "center", 1, 1);
    $this->css->CrearTabla();
    $tab="productosventa";
    $Condicion=" WHERE idProductosVenta='$key' OR Nombre LIKE '%$key%' OR Referencia LIKE '%$key%'";
    $consulta=$this->obCon->ConsultarTabla($tab,$Condicion);
    if($this->obCon->NumRows($consulta)){
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>Agregar</strong>", 1);
        $this->css->ColTabla("<strong>ID</strong>", 1);
            $this->css->ColTabla("<strong>Referencia</strong>", 1);
            $this->css->ColTabla("<strong>Nombre</strong>", 1);
            $this->css->ColTabla("<strong>PrecioVenta</strong>", 1);
            $this->css->ColTabla("<strong>Mayorista</strong>", 1);
            $this->css->ColTabla("<strong>Existencias</strong>", 1);
            $this->css->CierraFilaTabla();
        while($DatosProducto=$this->obCon->FetchArray($consulta)){
            $this->css->FilaTabla(16);
             print("<td>");
            //$Titulo="";
            //$Nombre="Agregar";
            //$RutaImage="../images/add.png";
            //$javascript="";
            //$VectorBim["f"]=0;
            $target="$PageReturn$DatosProducto[idProductosVenta]&TxtIdCliente=$Variable&TxtTablaItem=$tab";
            //$this->css->CrearLinkImagen($Titulo,$Nombre,$target,$RutaImage,"",50,50,"relative","",$VectorBim);
            $this->css->CrearLink($target, "_self", "Agregar");
            print("</td>");
            $this->css->ColTabla($DatosProducto["idProductosVenta"], 1);
            $this->css->ColTabla($DatosProducto["Referencia"], 1);
            $this->css->ColTabla($DatosProducto["Nombre"], 1);
            $this->css->ColTabla($DatosProducto["PrecioVenta"], 1);
            $this->css->ColTabla($DatosProducto["PrecioMayorista"], 1);
            $this->css->ColTabla($DatosProducto["Existencias"], 1);
           
            $this->css->CierraFilaTabla();
        }
    }
    
    $tab="servicios";
    $Condicion=" WHERE idProductosVenta='$key' OR Nombre LIKE '%$key%' OR Referencia LIKE '%$key%'";
    $consulta=$this->obCon->ConsultarTabla($tab,$Condicion);
    if($this->obCon->NumRows($consulta)){
        while($DatosProducto=$this->obCon->FetchArray($consulta)){
            $this->css->FilaTabla(16);
            print("<td>Agregar");
            $Titulo="";
            $Nombre="Agregar";
            $RutaImage="../images/add.png";
            $javascript="";
            $VectorBim["f"]=0;
            $target="$PageReturn$DatosProducto[idProductosVenta]&TxtIdCliente=$Variable&TxtTablaItem=$tab";
            $this->css->CrearLinkImagen($Titulo,$Nombre,$target,$RutaImage,"",50,50,"relative","",$VectorBim);
            print("</td>");
            $this->css->ColTabla($DatosProducto["idProductosVenta"], 1);
            $this->css->ColTabla($DatosProducto["Referencia"], 1);
            $this->css->ColTabla($DatosProducto["Nombre"], 1);
            $this->css->ColTabla($DatosProducto["PrecioVenta"], 1);
            $this->css->ColTabla($DatosProducto["PrecioMayorista"], 1);
            $this->css->CierraFilaTabla();
        }
    }
    
    $tab="productosalquiler";
    $Condicion=" WHERE idProductosVenta='$key' OR Nombre LIKE '%$key%' OR Referencia LIKE '%$key%'";
    $consulta=$this->obCon->ConsultarTabla($tab,$Condicion);
    if($this->obCon->NumRows($consulta)){
        while($DatosProducto=$this->obCon->FetchArray($consulta)){
            $this->css->FilaTabla(16);
            print("<td>Agregar");
            $Titulo="";
            $Nombre="Agregar";
            $RutaImage="../images/add.png";
            $javascript="";
            $VectorBim["f"]=0;
            $target="$PageReturn$DatosProducto[idProductosVenta]&TxtIdCliente=$Variable&TxtTablaItem=$tab";
            $this->css->CrearLinkImagen($Titulo,$Nombre,$target,$RutaImage,"",50,50,"relative","",$VectorBim);
            print("</td>");
            $this->css->ColTabla($DatosProducto["idProductosVenta"], 1);
            $this->css->ColTabla($DatosProducto["Referencia"], 1);
            $this->css->ColTabla($DatosProducto["Nombre"], 1);
            $this->css->ColTabla($DatosProducto["PrecioVenta"], 1);
            $this->css->ColTabla($DatosProducto["PrecioMayorista"], 1);
            
            $this->css->CierraFilaTabla();
        }
    }
    
    $this->css->CerrarTabla();
    $this->css->CerrarDiv();
    $this->css->CerrarCuadroDeDialogo();
   
}
//Funcion para Dibujar un item buscado en las tablas de ventas
public function DibujeItemsBuscadosVentas2($key,$PageReturn,$Variable){
    $this->css=new CssIni("");
    $key= $this->obCon->normalizar($key);
    $idPre=$Variable["idPre"];
    $Titulo="Crear Item En servicios";
    $Nombre="ShowItemsBusqueda";
    $RutaImage="";
    $javascript="";
    $VectorBim["f"]=0;
    $target="#DialBusquedaItems";
    $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",0,0,"fixed","left:10px;top:100",$VectorBim);
    
    $VectorDialogo["F"]=0;
    $this->css->CrearCuadroDeDialogo("DialBusquedaItems", "Resultados");
    $this->css->CrearDiv("DivBusqueda", "", "center", 1, 1);
    $this->css->CrearTabla();
    $sql="SELECT * FROM productosventa pv INNER JOIN prod_codbarras cb ON pv.idProductosVenta=cb.ProductosVenta_idProductosVenta"
            . " WHERE cb.CodigoBarras='$key' OR pv.idProductosVenta='$key' OR pv.Nombre LIKE '%$key%' OR pv.Referencia LIKE '%$key%' LIMIT 50";
    $consulta= $this->obCon->Query($sql);
    
    $tab="productosventa";
    /*
    $Condicion=" WHERE idProductosVenta='$key' OR Nombre LIKE '%$key%' OR Referencia LIKE '%$key%'";
    $consulta=$this->obCon->ConsultarTabla($tab,$Condicion);
     * 
     */
    
    if($this->obCon->NumRows($consulta)){
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>Agregar</strong>", 1);
        $this->css->ColTabla("<strong>ID</strong>", 1);
            $this->css->ColTabla("<strong>Referencia</strong>", 1);
            $this->css->ColTabla("<strong>Nombre</strong>", 1);
            $this->css->ColTabla("<strong>PrecioVenta</strong>", 1);
            $this->css->ColTabla("<strong>Mayorista</strong>", 1);
            $this->css->ColTabla("<strong>Existencias</strong>", 1);
            $this->css->CierraFilaTabla();
        while($DatosProducto=$this->obCon->FetchArray($consulta)){
            $this->css->FilaTabla(16);
            print("<td>");
            $this->css->CrearForm2("FrmAgregarItem$DatosProducto[idProductosVenta]", $PageReturn, "post", "_self");
            $this->css->CrearInputText("TxtIdItem", "hidden", "", $DatosProducto["idProductosVenta"], "", "", "", "", "", "", 0, 1);
            $this->css->CrearInputText("TxtidPre", "hidden", "", $idPre, "", "", "", "", "", "", 0, 1);
            $this->css->CrearInputText("TxtTablaItem", "hidden", "", $tab, "", "", "", "", "", "", 0, 1);
            $this->css->CrearInputNumber("TxtCantidad", "number", "", 1, "Cantidad", "", "", "", 80, 30, 0, 1, 1, "", 1);
            $this->css->CrearBotonNaranja("BtnAgregarItem", "Agregar");
            $this->css->CerrarForm();
            print("</td>");
            $this->css->ColTabla($DatosProducto["idProductosVenta"], 1);
            $this->css->ColTabla($DatosProducto["Referencia"], 1);
            $this->css->ColTabla($DatosProducto["Nombre"], 1);
            $this->css->ColTabla($DatosProducto["PrecioVenta"], 1);
            $this->css->ColTabla($DatosProducto["PrecioMayorista"], 1);
            $this->css->ColTabla($DatosProducto["Existencias"], 1);
            
            $this->css->CierraFilaTabla();
        }
    }
    
    
    
    $this->css->CerrarTabla();
    $this->css->CerrarDiv();
    $this->css->CerrarCuadroDeDialogo();
   
}
//Verifico si hay peticiones de busqueda de separados
public function DibujaSeparado($myPage,$idPreventa,$Vector) {
    $this->css=new CssIni("");
    //Dibujo una busqueda de un separado
if(!empty($_REQUEST["TxtBuscarSeparado"])){
    $key=$this->obCon->normalizar($_REQUEST["TxtBuscarSeparado"]);
    $sql="SELECT sp.ID, cl.RazonSocial, cl.Num_Identificacion, sp.Total, sp.Saldo, sp.idCliente FROM separados sp"
            . " INNER JOIN clientes cl ON sp.idCliente = cl.idClientes "
            . " WHERE (sp.Estado<>'Cerrado' AND sp.Saldo>0) AND (cl.RazonSocial LIKE '%$key%' OR cl.Num_Identificacion LIKE '%$key%') LIMIT 10";
    $Datos=$this->obCon->Query($sql);
    if($this->obCon->NumRows($Datos)){
        $this->css->CrearTabla();
        
        while($DatosSeparado=$this->obCon->FetchArray($Datos)){
            $this->css->FilaTabla(14);
            $this->css->ColTabla("<strong>Separado No. $DatosSeparado[ID]<strong>", 6);
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(14);
            print("<td>");
            $this->css->CrearForm2("FormAbonosSeparados$DatosSeparado[ID]", $myPage, "post", "_self");
            $this->css->CrearInputText("CmbPreVentaAct","hidden","",$idPreventa,"","","","",0,0,0,0);
            $this->css->CrearInputText("TxtIdSeparado","hidden","",$DatosSeparado["ID"],"","","","",0,0,0,0);
            $this->css->CrearInputText("TxtIdClientes","hidden","",$DatosSeparado["idCliente"],"","","","",0,0,0,0);
            $this->css->CrearInputNumber("TxtAbonoSeparado$DatosSeparado[ID]", "number", "Abonar: ", $DatosSeparado["Saldo"], "Abonar", "black", "", "", 200, 30, 0, 1, 1, $DatosSeparado["Saldo"], "any");
            $this->css->CrearBotonConfirmado("BtnAbono$DatosSeparado[ID]", "Abonar");
            $this->css->CerrarForm();
            print("</td>");
            $this->css->ColTabla($DatosSeparado["ID"], 1);
            $this->css->ColTabla($DatosSeparado["RazonSocial"], 1);
            $this->css->ColTabla($DatosSeparado["Num_Identificacion"], 1);
            $this->css->ColTabla(number_format($DatosSeparado["Total"]), 1);
            $this->css->ColTabla(number_format($DatosSeparado["Saldo"]), 1);
            $this->css->CierraFilaTabla();
            
            $this->css->FilaTabla(16);
            $this->css->ColTabla("ID Separado", 1);
            $this->css->ColTabla("Referencia", 1);
            $this->css->ColTabla("Nombre", 2);
            $this->css->ColTabla("Cantidad", 1);
            $this->css->ColTabla("TotalItem", 1);
            $this->css->CierraFilaTabla();
        
            $ConsultaItems=$this->obCon->ConsultarTabla("separados_items", "WHERE idSeparado='$DatosSeparado[ID]'");
            while($DatosItemsSeparados=$this->obCon->FetchArray($ConsultaItems)){
                
                $this->css->FilaTabla(14);
                $this->css->ColTabla($DatosItemsSeparados["idSeparado"], 1);
                $this->css->ColTabla($DatosItemsSeparados["Referencia"], 1);
                $this->css->ColTabla($DatosItemsSeparados["Nombre"], 2);
                $this->css->ColTabla($DatosItemsSeparados["Cantidad"], 1);
                $this->css->ColTabla($DatosItemsSeparados["TotalItem"], 1);
                $this->css->CierraFilaTabla();
            }           
            
             
            
        }
        $this->css->CerrarTabla();
    }else{
        $this->css->CrearNotificacionRoja("No se encontraron datos", 16);
    }
}
}
//Verifico si hay peticiones de busqueda de creditos
public function DibujaCredito($myPage,$idPreventa,$Vector) {
    $this->css=new CssIni("");
    //Dibujo una busqueda de un separado
if(!empty($_REQUEST["TxtBuscarCredito"])){
    
    $key=$this->obCon->normalizar($_REQUEST["TxtBuscarCredito"]);
    if(strlen($key)<=3){
        
        $this->css->CrearNotificacionNaranja("Escriba mas de 4 caracteres", 16);
        return;  
    }
    $sql="SELECT cart.idCartera,cart.TipoCartera,cart.Facturas_idFacturas, cl.RazonSocial, cl.Num_Identificacion, cart.TotalFactura, cart.Saldo,cart.TotalAbonos, cl.idClientes FROM cartera cart"
            . " INNER JOIN clientes cl ON cart.idCliente = cl.idClientes "
            . " WHERE (cl.RazonSocial LIKE '%$key%' OR cl.Num_Identificacion LIKE '%$key%') LIMIT 40";
    $Datos=$this->obCon->Query($sql);
    if($this->obCon->NumRows($Datos)){
        $this->css->CrearTabla();
        
        while($DatosCredito=$this->obCon->FetchArray($Datos)){
            $DatosFactura=$this->obCon->DevuelveValores("facturas", "idFacturas", $DatosCredito["Facturas_idFacturas"]);
            
            $this->css->FilaTabla(14);
            if($DatosFactura["FormaPago"]=='SisteCredito'){
                
                print("<td colspan=6 style='background-color:#ff391a; color:white'>");
            }else{
                print("<td colspan=6 style='background-color:#daeecf;'>");
            }
            
            print("<strong>Factura No. ".$DatosFactura["Prefijo"]." - ".$DatosFactura["NumeroFactura"]." TIPO DE CREDITO: $DatosFactura[FormaPago] Fecha: $DatosFactura[Fecha]<strong>");
            print("</td>");
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(14);
            print("<td>");
            $this->css->CrearForm2("FormCartera$DatosCredito[idCartera]", $myPage, "post", "_self");
            $this->css->CrearInputText("CmbPreVentaAct","hidden","",$idPreventa,"","","","",0,0,0,0);
            $this->css->CrearInputText("TxtIdFactura","hidden","",$DatosCredito["Facturas_idFacturas"],"","","","",0,0,0,0);
            $this->css->CrearInputText("TxtIdCartera","hidden","",$DatosCredito["idCartera"],"","","","",0,0,0,0);
            $CarteraAct=0;
            if(isset($Vector["HabilitaCmbCuentaDestino"])){
                $this->css->CrearInputFecha("Fecha: ", "TxtFecha", date("Y-m-d"), 100, 30, "");
                $VectorCuentas["Nombre"]="CmbCuentaDestino";
                $VectorCuentas["Evento"]="";
                $VectorCuentas["Funcion"]="";
                $VectorCuentas["Required"]=1;
                print("<strong>Cuenta:</strong>");
                $this->css->CrearSelect2($VectorCuentas);
                $this->css->CrearOptionSelect("", "Seleccione una cuenta destino", 0);
                $ConsultaCuentas=$this->obCon->ConsultarTabla("subcuentas", "WHERE PUC LIKE '11%'");
                while($DatosCuentaFrecuentes=$this->obCon->FetchArray($ConsultaCuentas)){
                    $this->css->CrearOptionSelect($DatosCuentaFrecuentes["PUC"], $DatosCuentaFrecuentes["Nombre"]." ".$DatosCuentaFrecuentes["PUC"], 0);
                }
                $this->css->CerrarSelect();
                print("<br>");
                $CarteraAct=1;
            }
            
            $this->css->CrearInputNumber("TxtAbonoCredito$DatosCredito[idCartera]", "number", "Efectivo: ", $DatosCredito["Saldo"], "Abonar", "black", "", "", 200, 30, 0, 1, 1, $DatosCredito["Saldo"], "any");
            print("<br>");
            if($CarteraAct==0){
                print("<strong>Opciones de Pago:</strong><image name='imgHidde' id='imgHidde' src='../images/hidde.png' onclick=MuestraOculta('DivCredito$DatosCredito[idCartera]');><br>");
            }    
            $this->css->CrearDiv("DivCredito$DatosCredito[idCartera]", "", "left", 0, 1);
                print("<br>");  
                $this->css->CrearInputNumber("TxtAbonoTarjeta$DatosCredito[idCartera]", "number", "Tarjetas: ", 0, "Abonar", "black", "", "", 200, 30, 0, 1, 0, $DatosCredito["Saldo"], 1);  
                print("<br>");
                $this->css->CrearInputNumber("TxtAbonoCheques$DatosCredito[idCartera]", "number", "Cheques: ", 0, "Abonar", "black", "", "", 200, 30, 0, 1, 0, $DatosCredito["Saldo"], 1);  
                print("<br>");
                $this->css->CrearInputNumber("TxtAbonoOtros$DatosCredito[idCartera]", "number", "Otros: ", 0, "Abonar", "black", "", "", 200, 30, 0, 1, 0, $DatosCredito["Saldo"], 1);  
            print("<br>");
            $this->css->CerrarDiv();
            $this->css->CrearBotonConfirmado("BtnAbono$DatosCredito[idCartera]", "Abonar a Credito");
            $this->css->CerrarForm();
            print("</td>");
            $this->css->ColTabla($DatosFactura["Prefijo"]." - ".$DatosFactura["NumeroFactura"], 1);
            $this->css->ColTabla($DatosCredito["RazonSocial"], 1);
            $this->css->ColTabla($DatosCredito["Num_Identificacion"], 1);
            $this->css->ColTabla(number_format($DatosCredito["TotalFactura"]), 1);
            $this->css->ColTabla(number_format($DatosCredito["Saldo"]), 1);
            $this->css->CierraFilaTabla();
            
            $this->css->FilaTabla(16);
            $this->css->ColTabla("Factura", 1);
            $this->css->ColTabla("Referencia", 1);
            $this->css->ColTabla("Nombre", 2);
            $this->css->ColTabla("Cantidad", 1);
            $this->css->ColTabla("TotalItem", 1);
            $this->css->CierraFilaTabla();
        
            $ConsultaItems=$this->obCon->ConsultarTabla("facturas_items", "WHERE idFactura='$DatosCredito[Facturas_idFacturas]'");
            while($DatosItemsFactura=$this->obCon->FetchArray($ConsultaItems)){
                
                $this->css->FilaTabla(14);
                $this->css->ColTabla($DatosFactura["Prefijo"]." - ".$DatosFactura["NumeroFactura"], 1);
                $this->css->ColTabla($DatosItemsFactura["Referencia"], 1);
                $this->css->ColTabla($DatosItemsFactura["Nombre"], 2);
                $this->css->ColTabla($DatosItemsFactura["Cantidad"], 1);
                $this->css->ColTabla($DatosItemsFactura["TotalItem"], 1);
                $this->css->CierraFilaTabla();
            }           
            
          
            
        }
        $this->css->CerrarTabla();
        
    }else{
        $this->css->CrearNotificacionRoja("No se encontraron datos", 16);
    }
}
}
//Clase para dibujar el cronograma de Produccion por horas
    public function DibujCronogramaProduccionHoras($Titulo,$FechaActual, $myPage,$idOT,$Vector) {
        //Creamos la interfaz del Cronograma
        
    $ColorLibre="#FFFFFF";
    $ColorPausaOperativa="red";
    $ColorPausaNoOperativa="orange";
    $ColorEjecucion="green";
    $ColorTerminada="black";
    $ColorNoIniciada="blue";
    $this->css=new CssIni("");
    
    $this->css->CrearTabla();
    //Agrego Titulo
    $this->css->FilaTabla(14);
    print("<td style='background-color:$ColorEjecucion'>");
    $this->css->ColTabla("En Ejecucion", 1);
    print("</td>");
    
    
    print("<td style='background-color:$ColorPausaOperativa'>");
    $this->css->ColTabla("Pausa Operativa", 1);
    print("</td>");
    print("<td style='background-color:$ColorPausaNoOperativa'>");
    $this->css->ColTabla("Pausa NO Operativa", 1);
    print("</td>");
    print("<td style='background-color:$ColorTerminada'>");
    $this->css->ColTabla("Terminada", 1);
    print("</td>");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla();
    $this->css->CrearTabla();
    //Agrego Titulo
    $this->css->FilaTabla(18);
    $this->css->ColTabla($Titulo, 5);
    
    $this->css->CierraColTabla();
    $this->css->CierraFilaTabla();
    //Agrego Horas
    $this->css->FilaTabla(16);
    $this->css->ColTabla("Maquina", 1);
    $this->css->CierraColTabla();
    $Datos=$this->obCon->ConsultarTabla("produccion_horas_cronograma", "");
    while($HorasCronograma=$this->obCon->FetchArray($Datos)){
        $this->css->ColTabla($HorasCronograma["Hora"], 1);
        $this->css->CierraColTabla();
    }
    $this->css->CierraFilaTabla();
    //Agrego las filas con cada maquina
    
    $Datos=$this->obCon->ConsultarTabla("maquinas", "");
    
    while($DatosMaquinas=$this->obCon->FetchArray($Datos)){
        $this->css->FilaTabla(14);
        echo("<td rowspan='2'>");
        print($DatosMaquinas["Nombre"]);
        echo("</td>");
        
        
        $DatosHoras=$this->obCon->ConsultarTabla("produccion_horas_cronograma", "");
        
        while($HorasCronograma=$this->obCon->FetchArray($DatosHoras)){
            print("<td>");
            $Page=$myPage;
            $Page.="?TxtFechaCronograma=$FechaActual&TxtHoraIni=$HorasCronograma[Hora]&idMaquina=$DatosMaquinas[ID]&idOT=$idOT";
            $Color="";
            $idActividad="";
            $Condicion="WHERE Fecha_Planeada_Inicio='$FechaActual' AND (Hora_Planeada_Inicio <='$HorasCronograma[Hora]' AND Hora_Planeada_Fin >'$HorasCronograma[Hora]') AND idMaquina='$DatosMaquinas[ID]'";
            $DatosActividades=$this->obCon->ConsultarTabla("produccion_actividades", $Condicion);
                    
            $DatosActividades=$this->obCon->FetchArray($DatosActividades);
            
            if($DatosActividades["ID"]>0){
                $idActividad=$DatosActividades["ID"];
                switch ($DatosActividades["Estado"]){
                    case "NO_INICIADA":
                        $ColorBG=$ColorNoIniciada;
                        $VectorDatosExtra["Color"] = $ColorNoIniciada;
                        break;
                    case "EJECUCION":
                        $ColorBG=$ColorEjecucion;
                        $VectorDatosExtra["Color"] = $ColorEjecucion;
                        break;
                    case "PAUSA_OPERATIVA":
                        $ColorBG=$ColorPausaOperativa;
                        $VectorDatosExtra["Color"] = $ColorPausaOperativa;
                        break;
                    case "PAUSA_NO_OPERATIVA":
                        $ColorBG=$ColorPausaNoOperativa;
                        $VectorDatosExtra["Color"] = $ColorPausaNoOperativa;
                        break;
                    case "TERMINADA":
                        $ColorBG=$ColorTerminada;
                        $VectorDatosExtra["Color"] = $ColorTerminada;
                        break;
                }
                $Color="background-color: $ColorBG";
                
                $Page.="&idEdit=$idActividad";
                $VectorDatosExtra["ID"]="LinkP".$idActividad;
                $this->css->CrearLinkID($Page, "_self", "$idActividad",$VectorDatosExtra);
            }
            
            
            if($Color=="" and $idOT>0){
                $this->css->CrearLink($Page, "_self", "+...");
                
            }
            
            print("</td>");
            
            
            
        }
        $this->css->CierraFilaTabla();
        $this->css->FilaTabla(12);
        
        $DatosHoras=$this->obCon->ConsultarTabla("produccion_horas_cronograma", "");
        
        while($HorasCronograma=$this->obCon->FetchArray($DatosHoras)){
            print("<td>");
            
            $Condicion="WHERE Fecha_Inicio<='$FechaActual'  AND idMaquina='$DatosMaquinas[ID]' AND Estado<>'NO_INICIADA' limit 100";
            $ConsultaActividades=$this->obCon->ConsultarTabla("produccion_actividades", $Condicion);
            
            while($DatosActividades=$this->obCon->FetchArray($ConsultaActividades)){
            $Page=$myPage;
            $Page.="?TxtFechaCronograma=$FechaActual&TxtHoraIni=$HorasCronograma[Hora]&idMaquina=$DatosMaquinas[ID]&idOT=$idOT";
            $Color="";
            $idActividad="";
            //$DatosActividades=$this->obCon->FetchArray($DatosActividades);
            $FechaHoraCalendario=date("$FechaActual $HorasCronograma[Hora]:00");
            $FechaTerminacion=date("Y-m-d H:i:00");
            
            if($DatosActividades["Estado"]=="TERMINADA"){
                $FechaTerminacion="$DatosActividades[Fecha_Fin] $DatosActividades[Hora_Fin]";
                    
            }
            
            
            $Hora1=strtotime($HorasCronograma["Hora"]);
            $Hora2=strtotime(substr($DatosActividades["Hora_Inicio"],0,2)."00");
            if($FechaActual==$DatosActividades["Fecha_Inicio"] AND $Hora1<$Hora2){
                $FechaTerminacion="2000-01-01 00:00:00";
            }
            
            $Fecha1=strtotime($FechaHoraCalendario);
            $Fecha2=strtotime($FechaTerminacion);
            
            if($DatosActividades["ID"]>0 and $Fecha2>$Fecha1){
                $idActividad=$DatosActividades["ID"];
                switch ($DatosActividades["Estado"]){
                    case "NO_INICIADA":
                        $ColorBG=$ColorNoIniciada;
                        $VectorDatosExtra["Color"] = $ColorNoIniciada;
                        break;
                    case "EJECUCION":
                        $ColorBG=$ColorEjecucion;
                        $VectorDatosExtra["Color"] = $ColorEjecucion;
                        break;
                    case "PAUSA_OPERATIVA":
                        $ColorBG=$ColorPausaOperativa;
                        $VectorDatosExtra["Color"] = $ColorPausaOperativa;
                        break;
                    case "PAUSA_NO_OPERATIVA":
                        $ColorBG=$ColorPausaNoOperativa;
                        $VectorDatosExtra["Color"] = $ColorPausaNoOperativa;
                        break;
                    case "TERMINADA":
                        $ColorBG=$ColorTerminada;
                        $VectorDatosExtra["Color"] = $ColorTerminada;
                        break;
                }
                $Color="background-color: $ColorBG";
            $VectorDatosExtra["ID"] = "Link".$idActividad;
            
            $Page.="&idEdit=$idActividad";
            $this->css->CrearLinkID($Page, "_self", "$idActividad<br>",$VectorDatosExtra);
            }
            
           // print("<td style='$Color'>");
            
            
            
            
            }
            print("</td>");
        }
        
        $this->css->CierraFilaTabla();
    }
    
    
    $this->css->CerrarTabla();
   
    }
    ///Arme una tabla con los datos de ventas por rangos
    
    public function ArmeTablaVentaRangos($Titulo,$CondicionItems,$Vector) {
             
        $sql="SELECT MAX(`TotalItem`/`Cantidad`) as Mayor, MIN(`TotalItem`/`Cantidad`) as Menor, SUM(`Cantidad`) as TotalItems FROM `facturas_items` WHERE `TotalItem`>1 $CondicionItems";
        
        $Consulta=$this->obCon->Query($sql);
        $Datos=$this->obCon->FetchArray($Consulta);
        $Mayor=$Datos["Mayor"];
        
        if($Mayor>1){
        
        $Menor=$Datos["Menor"];
        $TotalItems=$Datos["TotalItems"];
        
        $Rango=$Mayor-$Menor;
        $NoIntervalos=4;
        $Amplitud=$Rango/$NoIntervalos;
        
        $Intervalo[1]["LimiteInferior"]=$Menor;
        $Intervalo[1]["LimiteSuperior"]=$Menor+$Amplitud;
        $Intervalo[1]["Media"]=($Intervalo[1]["LimiteInferior"]+$Intervalo[1]["LimiteSuperior"])/2;
        $LimiteInferior=$Intervalo[1]["LimiteInferior"];
        $LimiteSuperior=$Intervalo[1]["LimiteSuperior"];
        $sql="SELECT SUM(`Cantidad`) as Items FROM `facturas_items` WHERE `TotalItem`>='$LimiteInferior' AND `TotalItem`<='$LimiteSuperior' $CondicionItems";
        $Consulta=$this->obCon->Query($sql);
        $Datos=$this->obCon->FetchArray($Consulta);
        $Intervalo[1]["FrecuenciaABS"]=$Datos["Items"];
        $Intervalo[1]["FrecuenciaAcumulada"]=$Intervalo[1]["FrecuenciaABS"];
        if($TotalItems>0){
            $Intervalo[1]["FrecuenciaABSPorcentual"]=$Intervalo[1]["FrecuenciaABS"]/$TotalItems*100;
        }else{
            $Intervalo[1]["FrecuenciaABSPorcentual"]=0;
        }
        
        $Intervalo[1]["FrecuenciaAcumuladaPorcentual"]=$Intervalo[1]["FrecuenciaABSPorcentual"];
        
        for($i=2;$i<=$NoIntervalos;$i++){
            $Intervalo[$i]["LimiteInferior"]=$Intervalo[$i-1]["LimiteSuperior"];
            $Intervalo[$i]["LimiteSuperior"]=$Intervalo[$i]["LimiteInferior"]+$Amplitud;
            $Intervalo[$i]["Media"]=($Intervalo[$i]["LimiteInferior"]+$Intervalo[$i]["LimiteSuperior"])/2;
            $LimiteInferior=$Intervalo[$i]["LimiteInferior"];
            $LimiteSuperior=$Intervalo[$i]["LimiteSuperior"];
            $sql="SELECT SUM(`Cantidad`) as Items FROM `facturas_items` WHERE `TotalItem`>='$LimiteInferior' AND `TotalItem`<='$LimiteSuperior' $CondicionItems";
            $Consulta=$this->obCon->Query($sql);
            $Datos=$this->obCon->FetchArray($Consulta);
            $Intervalo[$i]["FrecuenciaABS"]=$Datos["Items"];
            $Intervalo[$i]["FrecuenciaAcumulada"]=$Intervalo[$i-1]["FrecuenciaAcumulada"]+$Intervalo[$i]["FrecuenciaABS"];
            $Intervalo[$i]["FrecuenciaABSPorcentual"]=$Intervalo[$i]["FrecuenciaABS"]/$TotalItems*100;
            $Intervalo[$i]["FrecuenciaAcumuladaPorcentual"]=$Intervalo[$i-1]["FrecuenciaAcumuladaPorcentual"]+$Intervalo[$i]["FrecuenciaABSPorcentual"];
        }
        
        
        //$sql="SELECT SUM(`Cantidad`) as Items FROM `facturas_items` WHERE `TotalItem`>1 AND $CondicionItems";
        $tbl ='  
        <span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>'.$Titulo.':
        </em></strong></span><BR><BR>
        <table cellspacing="1" cellpadding="2" border="1"  align="center" >
          <tr> 
            <th><h3>MAYOR</h3></th>
                <th><h3>MINIMO</h3></th>
                <th><h3>RANGO</h3></th>
            <th><h3>INTERVALOS</h3></th>
                <th><h3>AMPLITUD</h3></th>
          </tr >
          <tr> 
            <td>'.number_format($Mayor).'</td>
            <td>'.number_format($Menor).'</td>
            <td>'.number_format($Rango).'</td>
            <td>'.number_format($NoIntervalos).'</td>
            <td>'.number_format($Amplitud).'</td>
          </tr >
        </table>
        <br><br>
        <table cellspacing="1" cellpadding="2" border="0" align="center" >
          <tr> 
            <th><h3>No.</h3></th>
            <th><h3>LIM INF</h3></th>
            <th><h3>LIM SUP</h3></th>
            <th><h3>MEDIA</h3></th>
            <th><h3>fabs</h3></th>
            <th><h3>Frec</h3></th>
            <th><h3>fabs %</h3></th>
            <th><h3>Frec %</h3></th>
          </tr >
        ';
        $h=0;
        for($i=1;$i<=$NoIntervalos;$i++){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
            $tbl.='<tr align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
            <td >'.number_format($i).'</td>
            <td>'.number_format($Intervalo[$i]["LimiteInferior"]).'</td>
            <td>'.number_format($Intervalo[$i]["LimiteSuperior"]).'</td>
            <td>'.number_format($Intervalo[$i]["Media"]).'</td>
            <td>'.number_format($Intervalo[$i]["FrecuenciaABS"]).'</td>
            <td>'.number_format($Intervalo[$i]["FrecuenciaAcumulada"]).'</td>
            <td>'.round($Intervalo[$i]["FrecuenciaABSPorcentual"],2).'%</td>
            <td>'.round($Intervalo[$i]["FrecuenciaAcumuladaPorcentual"],2).'%</td>
          </tr >';
        }
        $tbl.="</table><br><br><br><br>";
        }else{
           $tbl=""; 
        }
        
    return($tbl);
}
   
///Arme una tabla con el balance de comprobacion 2016-11-18  JAAV
    
    public function ArmeTablaBalanceComprobacion($Titulo,$Condicion,$Condicion2,$Vector) {
             
        
        
        $tbl='<table cellspacing="1" cellpadding="2" border="1"  align="center" >
          <tr> 
            <th><h3>CUENTA</h3></th>
            <th><h3>NOMBRE</h3></th>
            <th><h3>SALDO ANTERIOR</h3></th>
            <th><h3>DEBITO</h3></th>
            <th><h3>CREDITO</h3></th>
            <th><h3>NUEVO SALDO</h3></th>
          </tr >
          </table>
        ';
        
        //Guardo en un Vector los resultados de la consulta por Clase
        
        
        $sql="SELECT SUBSTRING(`CuentaPUC`,1,1) AS Clase ,sum(`Debito`) as Debitos, sum(`Credito`) as Creditos, sum(`Neto`) as Neto, (SELECT SUM(`Neto`) as SaldoTotal FROM `librodiario` $Condicion2  SUBSTRING(`CuentaPUC`,1,1)=Clase) AS Total FROM `librodiario` $Condicion GROUP BY SUBSTRING(`CuentaPUC`,1,1)";
        $Consulta=$this->obCon->Query($sql);
        $i=0;
        $DebitosGeneral=0;
        $CreditosGeneral=0;
        $TotalDebitos=0;
        $TotalCreditos=0;
        $Total=0;
        while($ClaseCuenta=$this->obCon->FetchArray($Consulta)){
            $DebitosGeneral=$DebitosGeneral+$ClaseCuenta["Debitos"];
            $CreditosGeneral=$CreditosGeneral+$ClaseCuenta["Creditos"];
            $TotalDebitos=$TotalDebitos+$ClaseCuenta["Debitos"];
            $TotalCreditos=$TotalCreditos+$ClaseCuenta["Creditos"];
            $Total=$Total+$ClaseCuenta["Total"];
            $i++;
            $Clase=$ClaseCuenta["Clase"];
            $NoClasesCuentas[$i]=$ClaseCuenta["Clase"];
            $DatosCuenta=  $this->obCon->DevuelveValores("clasecuenta", "PUC", $Clase);
            $Balance["ClaseCuenta"][$Clase]["Nombre"]=$DatosCuenta["Clase"];
            $Balance["ClaseCuenta"][$Clase]["Clases"]=$ClaseCuenta["Clase"];
            $Balance["ClaseCuenta"][$Clase]["Debitos"]=$ClaseCuenta["Debitos"];
            $Balance["ClaseCuenta"][$Clase]["Creditos"]=$ClaseCuenta["Creditos"];
            $Balance["ClaseCuenta"][$Clase]["NuevoSaldo"]=$ClaseCuenta["Debitos"]-$ClaseCuenta["Creditos"]+$ClaseCuenta["Total"];
            $Balance["ClaseCuenta"][$Clase]["SaldoAnterior"]=$ClaseCuenta["Total"];
        }
        $Diferencia=$TotalDebitos-$TotalCreditos;
        //Guardo en un Vector los resultados de la consulta por Grupo
        
        $sql="SELECT SUBSTRING(`CuentaPUC`,1,2) AS Grupo ,sum(`Debito`) as Debitos, sum(`Credito`) as Creditos,(SELECT SUM(`Neto`) as SaldoTotal FROM `librodiario` $Condicion2  SUBSTRING(`CuentaPUC`,1,2)=Grupo) AS Total FROM `librodiario` $Condicion GROUP BY SUBSTRING(`CuentaPUC`,1,2)";
        $Consulta=$this->obCon->Query($sql);
        $i=0;
        
        while($ClaseCuentaGrupo=$this->obCon->FetchArray($Consulta)){
            $i++;
            $Grupo=$ClaseCuentaGrupo["Grupo"];
            $NoGrupos[$i]=$ClaseCuentaGrupo["Grupo"];
            $DatosCuenta=  $this->obCon->DevuelveValores("gupocuentas", "PUC", $Grupo);
            $Balance["GrupoCuenta"][$Grupo]["Nombre"]=$DatosCuenta["Nombre"];
            $Balance["GrupoCuenta"][$Grupo]["Grupos"]=$ClaseCuentaGrupo["Grupo"];
            $Balance["GrupoCuenta"][$Grupo]["Debitos"]=$ClaseCuentaGrupo["Debitos"];
            $Balance["GrupoCuenta"][$Grupo]["Creditos"]=$ClaseCuentaGrupo["Creditos"];
            $Balance["GrupoCuenta"][$Grupo]["NuevoSaldo"]=$ClaseCuentaGrupo["Debitos"]-$ClaseCuentaGrupo["Creditos"]+$ClaseCuentaGrupo["Total"];
            $Balance["GrupoCuenta"][$Grupo]["SaldoAnterior"]=$ClaseCuentaGrupo["Total"];
        }
        
        //Guardo en un Vector los resultados de la consulta por Cuenta
        
        $sql="SELECT SUBSTRING(`CuentaPUC`,1,4) AS Cuenta ,sum(`Debito`) as Debitos, sum(`Credito`) as Creditos,(SELECT SUM(`Neto`) as SaldoTotal FROM `librodiario` $Condicion2  SUBSTRING(`CuentaPUC`,1,4)=Cuenta) as Total FROM `librodiario` $Condicion GROUP BY SUBSTRING(`CuentaPUC`,1,4)";
        $Consulta=$this->obCon->Query($sql);
        $i=0;
        
        while($ClaseCuentaCuenta=$this->obCon->FetchArray($Consulta)){
            $i++;
            $Cuenta=$ClaseCuentaCuenta["Cuenta"];
            $NoCuentas[$i]=$ClaseCuentaCuenta["Cuenta"];
            $DatosCuenta=  $this->obCon->DevuelveValores("cuentas", "idPUC", $Cuenta);
            $Balance["Cuenta"][$Cuenta]["Nombre"]=$DatosCuenta["Nombre"];
            $Balance["Cuenta"][$Cuenta]["Cuentas"]=$ClaseCuentaCuenta["Cuenta"];
            $Balance["Cuenta"][$Cuenta]["Debitos"]=$ClaseCuentaCuenta["Debitos"];
            $Balance["Cuenta"][$Cuenta]["Creditos"]=$ClaseCuentaCuenta["Creditos"];
            $Balance["Cuenta"][$Cuenta]["NuevoSaldo"]=$ClaseCuentaCuenta["Debitos"]-$ClaseCuentaCuenta["Creditos"]+$ClaseCuentaCuenta["Total"];
            $Balance["Cuenta"][$Cuenta]["SaldoAnterior"]=$ClaseCuentaCuenta["Total"];
        }
        
        //Guardo en un Vector los resultados de la consulta por SubCuenta
        
        $sql="SELECT `CuentaPUC` AS Subcuenta , sum(`Debito`) as Debitos, sum(`Credito`) as Creditos, sum(`Neto`) as NuevoSaldo,(SELECT SUM(`Neto`) as SaldoTotal FROM `librodiario` $Condicion2  `CuentaPUC` = Subcuenta) as Total FROM `librodiario` $Condicion AND LENGTH(`CuentaPUC`)>=5 GROUP BY `CuentaPUC` ";
        $Consulta=$this->obCon->Query($sql);
        $i=0;
        
        while($ClaseCuentaSub=$this->obCon->FetchArray($Consulta)){
            $i++;
            $SubCuenta=$ClaseCuentaSub["Subcuenta"];
            $NoSubCuentas[$i]=$ClaseCuentaSub["Subcuenta"];
            $sql="SELECT Nombre FROM subcuentas WHERE PUC='$SubCuenta' LIMIT 1";
            $Datos=  $this->obCon->Query($sql);
            $DatosCuenta=$this->obCon->FetchArray($Datos);
            //$DatosCuenta=  $this->obCon->DevuelveValores("subcuentas", "PUC", `$SubCuenta`);
            $Balance["SubCuenta"][$SubCuenta]["Nombre"]=$DatosCuenta["Nombre"];
            $Balance["SubCuenta"][$SubCuenta]["Subcuenta"]=$ClaseCuentaSub["Subcuenta"];
            $Balance["SubCuenta"][$SubCuenta]["Debitos"]=$ClaseCuentaSub["Debitos"];
            $Balance["SubCuenta"][$SubCuenta]["Creditos"]=$ClaseCuentaSub["Creditos"];
            $Balance["SubCuenta"][$SubCuenta]["NuevoSaldo"]=$ClaseCuentaSub["Debitos"]-$ClaseCuentaSub["Creditos"]+$ClaseCuentaSub["Total"];
            $Balance["SubCuenta"][$SubCuenta]["SaldoAnterior"]=$ClaseCuentaSub["Total"];
        }
        
        $h=0;
        $tbl.='<table cellspacing="1" cellpadding="2" border="0"  align="center" >';
        if(isset($NoClasesCuentas)){
            foreach($NoClasesCuentas as $ClasesCuentas){
                if($Balance["ClaseCuenta"][$ClasesCuentas]["Creditos"]>0 or $Balance["ClaseCuenta"][$ClasesCuentas]["Debitos"]>0){
                    if($h==0){
                    $Back="#f2f2f2";
                        $h=1;
                    }else{
                        $Back="white";
                        $h=0;
                    }
                    $tbl.='<tr align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
                    <td align="left"><strong><h1>'.$Balance["ClaseCuenta"][$ClasesCuentas]["Clases"].'</h1></strong></td>
                    <td align="center"><strong><h1>'.$Balance["ClaseCuenta"][$ClasesCuentas]["Nombre"].'</h1></strong></td>    
                    <td><strong><h1>'.number_format($Balance["ClaseCuenta"][$ClasesCuentas]["SaldoAnterior"]).'</h1></strong></td>  
                    <td><strong><h1>'.number_format($Balance["ClaseCuenta"][$ClasesCuentas]["Debitos"]).'</h1></strong></td>
                    <td><strong><h1>'.number_format($Balance["ClaseCuenta"][$ClasesCuentas]["Creditos"]).'</h1></strong></td>
                    <td><strong><h1>'.number_format($Balance["ClaseCuenta"][$ClasesCuentas]["NuevoSaldo"]).'</h1></strong></td>
                    </tr >';
                
               //Consulto los valores dentro del Grupo
                        
               foreach($NoGrupos as $GruposCuentas){
                   if(substr($Balance["GrupoCuenta"][$GruposCuentas]["Grupos"], 0, 1)==$Balance["ClaseCuenta"][$ClasesCuentas]["Clases"]){
                       if($h==0){
                            $Back="#f2f2f2";
                            $h=1;
                        }else{
                            $Back="white";
                            $h=0;
                        }
                        $tbl.='<tr align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
                        <td align="left"><h2>'.$Balance["GrupoCuenta"][$GruposCuentas]["Grupos"].'</h2></td>
                        <td align="center"><strong>'.$Balance["GrupoCuenta"][$GruposCuentas]["Nombre"].'</strong></td>
                        <td><h2>'.number_format($Balance["GrupoCuenta"][$GruposCuentas]["SaldoAnterior"]).'</h2></td>
                        <td><h2>'.number_format($Balance["GrupoCuenta"][$GruposCuentas]["Debitos"]).'</h2></td>
                        <td><h2>'.number_format($Balance["GrupoCuenta"][$GruposCuentas]["Creditos"]).'</h2></td>
                        <td><h2>'.number_format($Balance["GrupoCuenta"][$GruposCuentas]["NuevoSaldo"]).'</h2></td>
                        </tr >';
                   
                   
                   //Consulto los valores dentro de la Cuenta
                   
                   foreach($NoCuentas as $Cuentas){
                    if(substr($Balance["Cuenta"][$Cuentas]["Cuentas"], 0, 2)==$Balance["GrupoCuenta"][$GruposCuentas]["Grupos"]){
                        if($h==0){
                             $Back="#f2f2f2";
                             $h=1;
                         }else{
                             $Back="white";
                             $h=0;
                         }
                         $tbl.='<tr align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
                         <td align="left"><h3>'.$Balance["Cuenta"][$Cuentas]["Cuentas"].'</h3></td>
                         <td align="center"><strong>'.$Balance["Cuenta"][$Cuentas]["Nombre"].'</strong></td>
                         <td><h3>'.number_format($Balance["Cuenta"][$Cuentas]["SaldoAnterior"]).'</h3></td> 
                         <td><h3>'.number_format($Balance["Cuenta"][$Cuentas]["Debitos"]).'</h3></td>
                         <td><h3>'.number_format($Balance["Cuenta"][$Cuentas]["Creditos"]).'</h3></td>
                         <td><h3>'.number_format($Balance["Cuenta"][$Cuentas]["NuevoSaldo"]).'</h3></td>
                         </tr >';
                         
                         //Consulto los valores dentro de la Cuenta
                   
                   foreach($NoSubCuentas as $SubCuentas){
                    if(substr($Balance["SubCuenta"][$SubCuentas]["Subcuenta"], 0, 4)==$Balance["Cuenta"][$Cuentas]["Cuentas"]){
                        if($h==0){
                             $Back="#f2f2f2";
                             $h=1;
                         }else{
                             $Back="white";
                             $h=0;
                         }
                         $tbl.='<tr align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
                         <td align="left">'.$Balance["SubCuenta"][$SubCuentas]["Subcuenta"].'</td>
                         <td align="center"><strong>'.$Balance["SubCuenta"][$SubCuentas]["Nombre"].'</strong></td>
                         <td>'.number_format($Balance["SubCuenta"][$SubCuentas]["SaldoAnterior"]).'</td>
                         <td>'.number_format($Balance["SubCuenta"][$SubCuentas]["Debitos"]).'</td>
                         <td>'.number_format($Balance["SubCuenta"][$SubCuentas]["Creditos"]).'</td>
                         <td>'.number_format($Balance["SubCuenta"][$SubCuentas]["NuevoSaldo"]).'</td>
                         </tr >';
                    }
                   }
                         
                    }
                   }
                   }
               } 
            }
            }
        }
       $tbl.='<tr >  <td colspan="3" align="rigth"><h2>TOTALES:</h2></td>'
               . '<td align="rigth"><h2>'.  number_format($DebitosGeneral)."</h2> </td>"
               . '<td align="rigth"><h2>'.  number_format($CreditosGeneral)."</h2> </td>"
               . "<td>NA</td>"
               . "</tr ></table>";
    return($tbl);
}
   
//Clase para dibujar una caja de texto para verificar si un titulo ya fue vendido
//
//Funcion para Dibujar un item buscado en las tablas de ventas
public function DibujeVerificacionTitulo($myPage,$Vector){
    $this->css=new CssIni("");
    $this->css->CrearDiv("DivBusquedaTitulo", "", "center", 1, 1);
    $this->css->CrearForm2("FrmVerDisponibilidadTitulo", $myPage, "post", "_self");
    $this->css->CrearTabla();
    $this->css->FilaTabla(16);
    $this->css->ColTabla("<strong>Verificar un Titulo</strong>", 3);
    $this->css->CierraFilaTabla();
    $this->css->FilaTabla(16);
    print("<td>");
    $VectorSelect["Nombre"]="CmbPromocion";
    $VectorSelect["Evento"]="";
    $VectorSelect["Funcion"]="";
    $VectorSelect["Required"]=1;
    $this->css->CrearSelect2($VectorSelect);
    $this->css->CrearOptionSelect("", "Seleccione una promocion", 0);
        $Datos=$this->obCon->ConsultarTabla("titulos_promociones", " WHERE Activo='SI'");
        while($DatosPromociones=$this->obCon->FetchArray($Datos)){
            $javascript="onclick='CambiarMaxMin(`TxtMayor`,`$DatosPromociones[MayorInicial]`,`$DatosPromociones[MayorFinal]`);'";
            $this->css->CrearOptionSelect2($DatosPromociones["ID"],$DatosPromociones["ID"]." ".$DatosPromociones["Nombre"],$javascript, 0);
        }
    print("</td>");
    print("<td>");
    $this->css->CrearInputNumber("TxtMayor", "number", "", "", "Titulo", "Black", "", "", 100, 30, 0, 1, 0, 9999, 1); 
    print("</td>");
    print("<td>");
    $this->css->CrearBotonVerde("BtnVerificarTitulo", "Verificar");
    print("</td>");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla();
    $this->css->CerrarForm();
    
    if(isset($_REQUEST["TxtMayor"])) {
        $Mayor=$this->obCon->normalizar($_REQUEST["TxtMayor"]);
        $Promocion=$this->obCon->normalizar($_REQUEST["CmbPromocion"]);
        
    $tab="titulos_ventas";
    $Condicion=" WHERE Mayor1='$Mayor' AND Promocion='$Promocion'";
    $consulta=$this->obCon->ConsultarTabla($tab,$Condicion);
    if($this->obCon->NumRows($consulta)){
        $this->css->CrearNotificacionRoja("Este Titulo no esta disponible", 16);
        $this->css->CrearTabla();
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>ID Venta</strong>", 1);
        $this->css->ColTabla("<strong>Fecha</strong>", 1);
            $this->css->ColTabla("<strong>Promocion</strong>", 1);
            $this->css->ColTabla("<strong>Mayor1</strong>", 1);
            $this->css->ColTabla("<strong>Mayor2</strong>", 1);
            $this->css->ColTabla("<strong>Adicional</strong>", 1);
            $this->css->ColTabla("<strong>IdCliente</strong>", 1);
            $this->css->ColTabla("<strong>Nombre Cliente</strong>", 1);
            $this->css->ColTabla("<strong>IdColaborador</strong>", 1);
            $this->css->ColTabla("<strong>Nombre Colaborador</strong>", 1);
            $this->css->CierraFilaTabla();
        while($DatosVentas=$this->obCon->FetchArray($consulta)){
            $this->css->FilaTabla(16);
            
            $this->css->ColTabla($DatosVentas["ID"], 1);
            $this->css->ColTabla($DatosVentas["Fecha"], 1);
            $this->css->ColTabla($DatosVentas["Promocion"], 1);
            $this->css->ColTabla($DatosVentas["Mayor1"], 1);
            $this->css->ColTabla($DatosVentas["Mayor2"], 1);
            $this->css->ColTabla($DatosVentas["Adicional"], 1);
            $this->css->ColTabla($DatosVentas["idCliente"], 1);
            $this->css->ColTabla($DatosVentas["NombreCliente"], 1);
            $this->css->ColTabla($DatosVentas["idColaborador"], 1);
            $this->css->ColTabla($DatosVentas["NombreColaborador"], 1);
                       
            $this->css->CierraFilaTabla();
        }
        $this->css->CerrarTabla();
    }else{
        $this->css->CrearNotificacionVerde("Este Titulo esta disponible", 16);
    }
    
    
    
    
    }   
   
    
    $this->css->CerrarDiv();
      
}
//Funcion para Dibujar el area de ventas de un titulo
public function DibujeAreaVentasTitulos($myPage,$Vector){
    $this->css=new CssIni("");
    $this->css->CrearDiv("DivVentasTitulos", "", "center", 1, 1);
    $this->css->CrearNotificacionVerde("Vender un Titulo", 16);
    $this->css->CrearForm2("FrmVentasTitulos", $myPage, "post", "_self");
    $this->css->CrearTabla();
    $this->css->FilaTabla(16);
    $this->css->ColTabla("<strong>Vender un Titulo</strong>", 3);
    $this->css->CierraFilaTabla();
    
    $this->css->FilaTabla(16);
    $this->css->ColTabla("<strong>Fecha</strong>", 1);
    $this->css->ColTabla("<strong>Cliente</strong>", 1);
    $this->css->ColTabla("<strong>Vendedor</strong>", 1);
    $this->css->ColTabla("<strong>Promocion</strong>", 1);
    //$this->css->ColTabla("<strong>Titulo</strong>", 1);
    //$this->css->ColTabla("<strong>Abono a Titulo</strong>", 1);
    //$this->css->ColTabla("<strong>Ciclo de Pago</strong>", 1);
    $this->css->CierraFilaTabla();
    
    $this->css->FilaTabla(16);
    print("<td>");
    $this->css->CrearInputFecha("", "TxtFechaVenta", date("Y-m-d"), 100, 30, "");
    print("</td>");
    print("<td>");
    $VarSelect["Ancho"]="200";
    $VarSelect["PlaceHolder"]="Busque un Cliente";
    $VarSelect["Required"]=1;
    $this->css->CrearSelectChosen("TxtCliente", $VarSelect);
    $this->css->CrearOptionSelect("", "Seleccione un Cliente" , 0);
        $sql="SELECT * FROM clientes";
        $Consulta=$this->obCon->Query($sql);
        while($DatosCliente=$this->obCon->FetchArray($Consulta)){
               
               $this->css->CrearOptionSelect("$DatosCliente[Num_Identificacion]", "$DatosCliente[Num_Identificacion] / $DatosCliente[RazonSocial] / $DatosCliente[Telefono]" , 0);
           }
           
    $this->css->CerrarSelect();
    print("</td>");
    print("<td>");
    $VarSelect["Ancho"]="200";
    $VarSelect["PlaceHolder"]="Busque un Colaborador";
    $VarSelect["Required"]=1;
    $this->css->CrearSelectChosen("TxtColaborador", $VarSelect);
    $this->css->CrearOptionSelect("", "Seleccione un Colaborador" , 0);
        $sql="SELECT * FROM colaboradores";
        $Consulta=$this->obCon->Query($sql);
        while($DatosColaborador=$this->obCon->FetchArray($Consulta)){
               
               $this->css->CrearOptionSelect("$DatosColaborador[Identificacion]", "$DatosColaborador[Identificacion] / $DatosColaborador[Nombre]" , 0);
           }
           
    $this->css->CerrarSelect();
    print("</td>");
    print("<td>");
    $VectorSelect["Nombre"]="CmbPromocion";
    $VectorSelect["Evento"]="";
    $VectorSelect["Funcion"]="";
    $VectorSelect["Required"]=1;
    $this->css->CrearSelect2($VectorSelect);
    $this->css->CrearOptionSelect("", "Seleccione una promocion", 0);
        $Datos=$this->obCon->ConsultarTabla("titulos_promociones", " WHERE Activo='SI'");
        
        while($DatosPromociones=$this->obCon->FetchArray($Datos)){
            $javascript="onclick='CambiarMaxMin(`TxtTitulo`,`$DatosPromociones[MayorInicial]`,`$DatosPromociones[MayorFinal]`);CambiarMaxMin(`TxtAbonoTitulo`,`0`,`$DatosPromociones[Valor]`);'";
                    
            $this->css->CrearOptionSelect2($DatosPromociones["ID"],$DatosPromociones["ID"]." ".$DatosPromociones["Nombre"],$javascript, 0);
        }
    $this->css->CerrarSelect();    
    print("</td>");
    print("<tr>"); 
    print("<td>");
    print("<strong>Titulo: </strong>");
    $VectorCuadro["Variable"][0]="CmbPromocion";
    $this->css->DibujeCuadroBusqueda("TxtTitulo", "Consultas/DatosTitulos.php?Titulo", "idPromocion=", "DivInfoTitulo", "onKeyup", 30, 100, $VectorCuadro);
    //$this->css->CrearInputNumber("TxtTitulo", "number", "", "", "Titulo", "Black", "onKeyup", "CargueValoresAdicionalesTitulo();", 100, 30, 0, 1, 0, 9999, 1); 
    
    print("</td>");
    print("<td>");
    $this->css->CrearInputNumber("TxtAbonoTitulo", "number", "", "", "Abono", "Black", "", "", 100, 30, 0, 1, 0, 1000000, 1);
    $this->css->CrearTextArea("TxtObservaciones", "<br>", "", "Observaciones", "", "", "", 100, 60, 0, 0);
    print("</td>");
    print("<td>");
    $this->css->CrearSelect("CmbCicloPago", "");
        $this->css->CrearOptionSelect("", "Seleccione un ciclo de pago", 0);
        $this->css->CrearOptionSelect(7, "Semanal", 0);
        $this->css->CrearOptionSelect(15, "Quincenal", 0);
        $this->css->CrearOptionSelect(30, "Mensual", 0);
    $this->css->CerrarSelect();    
    print("</td>");
    //print("<tr>");
    print("<td style='text-align:center'>");
    $this->css->CrearBotonConfirmado("BtnVenderTitulo", "Vender");
    print("</td>");
    print("</tr>");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla();
    $this->css->CerrarForm();
       
    $this->css->CerrarDiv();
      
}
/*
 * Genera informe de compras comparativo con las ventas
 */
    
public function GenerarInformeComprasComparativo($TipoReporte,$FechaInicial,$FechaFinal,$FechaCorte,$Vector)  {
   
    require_once '../librerias/Excel/PHPExcel.php';
   $objPHPExcel = new PHPExcel();    
   
   if($TipoReporte=="Corte"){
      $sql="SELECT Movimiento, Detalle, idProductosVenta, Referencia, Nombre, Departamento, Sub1, Sub2,Sub3,Sub4,Sub5, SUM(Cantidad) as Cantidad FROM `kardexmercancias` INNER JOIN `productosventa` ON "
              . " `kardexmercancias`.`ProductosVenta_idProductosVenta`=`productosventa`.`idProductosventa` "
              . " WHERE `Fecha`<='$FechaCorte' GROUP BY Movimiento,Detalle, idProductosventa ORDER BY idProductosventa";
      
      $TituloInforme="Informe Comparativo a $FechaCorte";
   }else{
       
      $sql="SELECT Movimiento, Detalle, idProductosVenta, Referencia, Nombre, Departamento, Sub1, Sub2,Sub3,Sub4,Sub5, SUM(Cantidad) as Cantidad FROM `kardexmercancias` INNER JOIN `productosventa` ON "
              . " `kardexmercancias`.`ProductosVenta_idProductosVenta`=`productosventa`.`idProductosventa` "
              . " WHERE `Fecha`>='$FechaInicial' AND `Fecha`<='$FechaFinal'  GROUP BY Movimiento,Detalle, idProductosventa ORDER BY idProductosventa"; 
      $TituloInforme="Informe Comparativo de $FechaInicial a $FechaFinal";
   }
   
   $Consulta=  $this->obCon->Query($sql);
   $f=0;
   $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$f++]."1","idProductosVenta")
            ->setCellValue($this->Campos[$f++]."1","Referencia")
            ->setCellValue($this->Campos[$f++]."1","Nombre")
            ->setCellValue($this->Campos[$f++]."1","Saldo Inicial")
            ->setCellValue($this->Campos[$f++]."1","Entradas por Compras")
            ->setCellValue($this->Campos[$f++]."1","Entradas por Traslados")
            ->setCellValue($this->Campos[$f++]."1","Entradas por Altas")
            ->setCellValue($this->Campos[$f++]."1","Salidas por Ventas")
            ->setCellValue($this->Campos[$f++]."1","Salidas por Traslados")
            ->setCellValue($this->Campos[$f++]."1","Salidas por Bajas")
            ->setCellValue($this->Campos[$f++]."1","Saldo")
            ->setCellValue($this->Campos[$f++]."1","Saldo Final")
            ->setCellValue($this->Campos[$f++]."1","Departamento")
            ->setCellValue($this->Campos[$f++]."1","Sub1")
            ->setCellValue($this->Campos[$f++]."1","Sub2")
            ->setCellValue($this->Campos[$f++]."1","Sub3")
            ->setCellValue($this->Campos[$f++]."1","Sub4")
            ->setCellValue($this->Campos[$f++]."1","Sub5")
            ->setCellValue($this->Campos[$f++]."1",$TituloInforme);
            
   
   while($DatosKardex=$this->obCon->FetchArray($Consulta)){
       $id=$DatosKardex["idProductosVenta"];
      if($DatosKardex["Movimiento"]=='ENTRADA' or $DatosKardex["Movimiento"]=='SALIDA') {
        
        $Movimiento=$DatosKardex["Movimiento"];
        $Detalle=$DatosKardex["Detalle"];
        $idProductos[$id]=$DatosKardex["idProductosVenta"];
        $Producto[$id]["Referencia"]=$DatosKardex["Referencia"];
        $Producto[$id]["Nombre"]=$DatosKardex["Nombre"];
        $Producto[$id][$Movimiento][$Detalle]["Cantidad"]=$DatosKardex["Cantidad"];
        $Producto[$id]["Departamento"]=$DatosKardex["Departamento"];
        $Producto[$id]["Sub1"]=$DatosKardex["Sub1"];
        $Producto[$id]["Sub2"]=$DatosKardex["Sub2"];
        $Producto[$id]["Sub3"]=$DatosKardex["Sub3"];
        $Producto[$id]["Sub4"]=$DatosKardex["Sub4"];
        $Producto[$id]["Sub5"]=$DatosKardex["Sub5"];
      }  
      $Producto[$id]["SaldoFinal"]=0; 
      if($DatosKardex["Movimiento"]=='SALDOS'){
        $Producto[$id]["SaldoFinal"]=$DatosKardex["Cantidad"];  
        
      }
   }
   
   $i=2;
   foreach($idProductos as $id){
       $Entradas=0;
       $EntradasXTraslados=0;
       $EntradasXAltas=0;
       $SalidasXTraslados=0;
       $SalidasXBajas=0;
       $Salidas=0;
       $Saldos=0;
       $SaldoInicial=0;
       if(isset($Producto[$id]["ENTRADA"]["FACTURA"]["Cantidad"])){
           $Entradas=$Producto[$id]["ENTRADA"]["FACTURA"]["Cantidad"];
       }
       if(isset($Producto[$id]["ENTRADA"]["Traslado"]["Cantidad"])){
           $EntradasXTraslados=$Producto[$id]["ENTRADA"]["Traslado"]["Cantidad"];
       }
       if(isset($Producto[$id]["ENTRADA"]["ALTA"]["Cantidad"])){
           $EntradasXAltas=$Producto[$id]["ENTRADA"]["ALTA"]["Cantidad"];
       }
       if(isset($Producto[$id]["SALIDA"]["Factura"]["Cantidad"])){
           $Salidas=$Producto[$id]["SALIDA"]["Factura"]["Cantidad"];
       }
       if(isset($Producto[$id]["SALIDA"]["Traslado"]["Cantidad"])){
           $SalidasXTraslados=$Producto[$id]["SALIDA"]["Traslado"]["Cantidad"];
       }
       if(isset($Producto[$id]["SALIDA"]["BAJA"]["Cantidad"])){
           $SalidasXBajas=$Producto[$id]["SALIDA"]["BAJA"]["Cantidad"];
       }
       $Saldos=$Entradas+$EntradasXTraslados+$EntradasXAltas-$Salidas-$SalidasXTraslados-$SalidasXBajas;
       $SaldoInicial=$Producto[$id]["SaldoFinal"]-$Saldos;
       $Departamentos=  $this->obCon->DevuelveValores("prod_departamentos", "idDepartamentos", $Producto[$id]["Departamento"]);
       $Sub1=  $this->obCon->DevuelveValores("prod_sub1", "idSub1", $Producto[$id]["Sub1"]);
       $Sub2=  $this->obCon->DevuelveValores("prod_sub2", "idSub2", $Producto[$id]["Sub2"]);
       $Sub3=  $this->obCon->DevuelveValores("prod_sub3", "idSub3", $Producto[$id]["Sub3"]);
       $Sub4=  $this->obCon->DevuelveValores("prod_sub4", "idSub4", $Producto[$id]["Sub4"]);
       $Sub5=  $this->obCon->DevuelveValores("prod_sub5", "idSub5", $Producto[$id]["Sub5"]);
       if($Entradas<>0 OR $EntradasXTraslados<>0 OR $EntradasXAltas<>0 OR $Salidas<>0 OR $SalidasXTraslados<>0 OR $SalidasXBajas<>0){
       
           $f=0;
           $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[$f++].$i,$id)
            ->setCellValue($this->Campos[$f++].$i,$Producto[$id]["Referencia"])
            ->setCellValue($this->Campos[$f++].$i,$Producto[$id]["Nombre"])
            ->setCellValue($this->Campos[$f++].$i,$SaldoInicial)       
            ->setCellValue($this->Campos[$f++].$i,$Entradas)
            ->setCellValue($this->Campos[$f++].$i,$EntradasXTraslados)
            ->setCellValue($this->Campos[$f++].$i,$EntradasXAltas)
            ->setCellValue($this->Campos[$f++].$i,$Salidas)
            ->setCellValue($this->Campos[$f++].$i,$SalidasXTraslados)
            ->setCellValue($this->Campos[$f++].$i,$SalidasXBajas)
            ->setCellValue($this->Campos[$f++].$i,$Saldos)
            ->setCellValue($this->Campos[$f++].$i,$Producto[$id]["SaldoFinal"])
            ->setCellValue($this->Campos[$f++].$i,$Departamentos["Nombre"])
            ->setCellValue($this->Campos[$f++].$i,$Sub1["NombreSub1"])
            ->setCellValue($this->Campos[$f++].$i,$Sub2["NombreSub2"])
            ->setCellValue($this->Campos[$f++].$i,$Sub3["NombreSub3"])
            ->setCellValue($this->Campos[$f++].$i,$Sub4["NombreSub4"])
            ->setCellValue($this->Campos[$f++].$i,$Sub5["NombreSub5"]);
       $i++; 
       }     
      
   }
   
    
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar Informe  desde base de datos")
        ->setSubject("Informe")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones")
        ->setCategory("Informe Departamentos");    
 
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'."Informe_Comparativo".'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
   
      
}
//Crea los inputs para un concepto
    public function CrearInputsMontos($idConcepto,$Vector) {
        $this->css=new CssIni("");
        $DatosConcepto=$this->obCon->DevuelveValores("conceptos", "ID", $idConcepto);
        $consulta=  $this->obCon->ConsultarTabla("conceptos_montos", " WHERE idConcepto='$idConcepto'");
        $idSumatoria="";
        while($DatosMontos=$this->obCon->FetchArray($consulta)){
            $idMonto=$DatosMontos["ID"];
            $idMontoDependiente=$DatosMontos["Depende"];
            $Montos[$idMonto]["Dependencia"] = $DatosMontos["Depende"];
            $Montos[$idMonto]["Operacion"] = $DatosMontos["Operacion"];
            $Montos[$idMonto]["ValorDependencia"] = $DatosMontos["ValorDependencia"];
            $Montos[$idMonto]["NombreObjeto"]="Monto$idMonto";
            if($idMontoDependiente>0){
                $Operacion=$DatosMontos["Operacion"];
                $ValorDependencia=$DatosMontos["ValorDependencia"];
                $Montos[$idMontoDependiente]["FuncionJS"]="CalculeValorDependencia('Monto$idMontoDependiente','Monto$idMonto','$Operacion','Dependencia$idMonto')";
                                
            }
            if($DatosMontos["Operacion"]=="S"){
                $idSumatoria="Monto$idMonto";
            }
        }
        
        $consulta=  $this->obCon->ConsultarTabla("conceptos_montos", " WHERE idConcepto='$idConcepto'");
        $this->css->CrearDiv("DivMontos", "", "center", 1, 1);
        while($DatosMontos=$this->obCon->FetchArray($consulta)){
            $disabled=0;
            $idMonto=$DatosMontos["ID"];
            $EventoJS="onKeyup";
            $FuncionJS="";
            $idObjetos[$idMonto]=$idMonto;
            if(isset($Montos[$idMonto]["FuncionJS"])){
                
                $FuncionJS=$Montos[$idMonto]["FuncionJS"];
            }
            $FuncionJS.=";CalculeSumatoria('$idSumatoria')";
            if($Montos[$idMonto]["Operacion"]=="S"){
                $disabled=1;
            }
            
            $this->css->CrearInputNumber("Monto$DatosMontos[ID]", "number", "", "",$DatosMontos["NombreMonto"] , "", $EventoJS, $FuncionJS, 100, 30, $disabled, 1, 0, "", "any");
            if($Montos[$idMonto]["Dependencia"]>0 and $Montos[$idMonto]["Operacion"]<>"" and $Montos[$idMonto]["ValorDependencia"]>0){
                print("<-->");
                $this->css->CrearInputNumber("Dependencia$DatosMontos[ID]", "number", "", $Montos[$idMonto]["ValorDependencia"],"" , "", "", "", 100, 30, 0, 1, 0, "", "any");
            }
            print("<br>");            
        }
        $this->css->CerrarDiv();
    }
    
    ///Arme una tabla con un auxiliar 2016-12-27  JAAV
    
    public function ArmeTablaAuxiliarDetallado($Titulo,$Condicion,$TipoReporte,$FechaIni,$Vector) {
             
        
        
        $tbl='
            
<table cellspacing="1" cellpadding="2" border="1"  align="center" >
          <tr> 
            <th><h3>TERCERO</h3></th>
            <th><h3>FECHA</h3></th>
            <th><h3>CUENTA</h3></th>
            <th><h3>TIPO DOC</h3></th>
            <th><h3>#DOC</h3></th>
            <th><h3>SOPORTE</h3></th>
            
            <th><h3>DETALLE</h3></th>
            <th><h3>DEBITO</h3></th>
            <th><h3>CREDITO</h3></th>
            <th><h3>SALDO</h3></th>
          </tr >
          </table>
        ';
        
        //Guardo en un Vector los resultados de la consulta por Clase
        
        
        $sql="SELECT * FROM `librodiario` $Condicion ORDER BY `Fecha`";
        $Consulta=$this->obCon->Query($sql);
        $i=0;
        $TotalDebitos=0;
        $TotalCreditos=0;
        $TotalSaldos=0;
        $h=0;
        
        $tbl.='<table cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $Saldo=0;
        while($DatosLibro=$this->obCon->FetchArray($Consulta)){
            if($TipoReporte=="Corte"){
                $Saldo=0;
            }else{
                $idTercero=$DatosLibro["Tercero_Identificacion"];
                $Cuenta=$DatosLibro["CuentaPUC"];
                $FechaMovimiento=$DatosLibro["Fecha"];
                $sql="SELECT SUM(Neto) as Neto,SUM(Debito) Debito,SUM(Credito) Credito FROM librodiario WHERE Tercero_Identificacion='$idTercero' AND Fecha<'$FechaMovimiento' "
                        . " AND CuentaPUC='$Cuenta'";
                $Datos=$this->obCon->Query($sql);
                $Resultado=$this->obCon->FetchArray($Datos);
                $Saldo=$Resultado["Neto"]; //Neto
            }
                        
            $TotalDebitos=$TotalDebitos+$DatosLibro["Debito"];
            $TotalCreditos=$TotalCreditos+$DatosLibro["Credito"];
            $TotalSaldos=$TotalSaldos+$Saldo;
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
            $tbl.='<tr align="rigth" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> 
            <td align="center">'.$DatosLibro["Tercero_Identificacion"].'<br>'.$DatosLibro["Tercero_Razon_Social"].'</td>
            <td align="center">'.$DatosLibro["Fecha"].'</td>
            <td align="center"><strong>'.$DatosLibro["CuentaPUC"].'</strong></td>
            <td align="left">'.$DatosLibro["Tipo_Documento_Intero"].'</td>
            <td align="center">'.$DatosLibro["Num_Documento_Interno"].'</td>
            <td align="center">'.$DatosLibro["Num_Documento_Externo"].'</td>
            <td align="center">'.$DatosLibro["Concepto"].'</td>
            <td align="center">'.number_format($DatosLibro["Debito"]).'</td>
            <td align="center">'.number_format($DatosLibro["Credito"]).'</td>
            <td align="center">'.number_format($Saldo).'</td>
            </tr >';
        }
        
        $tbl.="</table>";
             
        $tbl.='<table cellspacing="1" cellpadding="2" border="1"  align="center" >
          <tr> 
            <th colspan="7"><h3>TOTALES:</h3></th>
            
            <th align="rigth"><h3>'.number_format($TotalDebitos).'</h3></th>
            <th align="rigth"><h3>'.number_format($TotalCreditos).'</h3></th>
            <th align="rigth"><h3>'.number_format($TotalSaldos).'</h3></th>
          </tr >
          </table>   
        ';
    return($tbl);
}
   
//Arme Balance General
//
    public function ArmeTablaBalanceGeneral($Titulo,$Condicion,$Vector) {
        
        
        $tbl='<table cellspacing="1" cellpadding="2" border="1"  align="center" >
            <tr> 
            <th colspan="3"><h3>ACTIVOS</h3></th>
            <th colspan="3"><h3>PASIVOS Y PATRIMONIO</h3></th>
            </tr> 
            <tr> 
            <th><h3>Cuenta</h3></th>
            <th><h3>Nombre</h3></th>
            <th><h3>Valor</h3></th>
            <th><h3>Cuenta</h3></th>
            <th><h3>Nombre</h3></th>
            <th><h3>Valor</h3></th>
            
          </tr >
          
        ';
        
        $sql="SELECT SUBSTRING(`CuentaPUC`,1,4) AS Cuenta ,sum(`Debito`) as Debitos, sum(`Credito`) as Creditos FROM `librodiario` $Condicion AND SUBSTRING(`CuentaPUC`,1,4) LIKE '1%' GROUP BY SUBSTRING(`CuentaPUC`,1,4)";
        $Consulta=$this->obCon->Query($sql);
        $h=0;
        while($DatosCuenta=$this->obCon->FetchArray($Consulta)){
            $Debitos=$DatosCuenta["Debitos"];
            $Creditos=$DatosCuenta["Creditos"];
            $Valor=$Debitos-$Creditos;
            
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
            $DatosSubcuentas=  $this->obCon->DevuelveValores("cuentas", "idPUC", $DatosCuenta["Cuenta"]);
            $tbl.='</table><table cellspacing="1" cellpadding="2" border="0"  align="center" >';
            $tbl.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">' ;
            $tbl.="
            <th>$DatosCuenta[Cuenta]</th>
            <th>$DatosSubcuentas[Nombre]</th>
            <th>".number_format($Valor)."</th>
            <th>0</th>
            <th>0</th>
            <th><h3>0</h3></th>
            
          </tr >";
        }
        $tbl.="</table>";
        return($tbl);
        
    }
    //Funcion para generar una interface de ingresos (TECNOAGRO)
    public function GenereInterfaceIngresosEgresos($Tipo,$FechaIni,$FechaFin,$Vector) {
        require_once '../librerias/Excel/PHPExcel.php';
   $objPHPExcel = new PHPExcel();    
   $objPHPExcel->getActiveSheet()->getStyle('H:I')->getNumberFormat()->setFormatCode('#');
   $objPHPExcel->getActiveSheet()->getStyle("A:AL")->getFont()->setSize(8);
   $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[0]."1","empresa")
            ->setCellValue($this->Campos[1]."1","clase")
            ->setCellValue($this->Campos[2]."1","vinkey")
            ->setCellValue($this->Campos[3]."1","tipodoc")
            ->setCellValue($this->Campos[4]."1","numedoc")
            ->setCellValue($this->Campos[5]."1","reg")
            ->setCellValue($this->Campos[6]."1","fecha")
            ->setCellValue($this->Campos[7]."1","cuenta")
            ->setCellValue($this->Campos[8]."1","vinculado")
            ->setCellValue($this->Campos[9]."1","sucvin")
            ->setCellValue($this->Campos[10]."1","sucurs")
            ->setCellValue($this->Campos[11]."1","ccosto")
            ->setCellValue($this->Campos[12]."1","destino")
           ->setCellValue($this->Campos[13]."1","vende")
            ->setCellValue($this->Campos[14]."1","cobra")
            ->setCellValue($this->Campos[15]."1","zona")
            ->setCellValue($this->Campos[16]."1","bodega")
            ->setCellValue($this->Campos[17]."1","producto")
            ->setCellValue($this->Campos[18]."1","unimed")
            ->setCellValue($this->Campos[19]."1","lotepro")
            ->setCellValue($this->Campos[20]."1","cantidad")
            ->setCellValue($this->Campos[21]."1","claseinv")
            ->setCellValue($this->Campos[22]."1","clacru1")
            ->setCellValue($this->Campos[23]."1","tipcru1")
            ->setCellValue($this->Campos[24]."1","numcru1")
            ->setCellValue($this->Campos[25]."1","cuocru1")
           ->setCellValue($this->Campos[26]."1","fecini")
            ->setCellValue($this->Campos[27]."1","plazo")
            ->setCellValue($this->Campos[28]."1","clacru2")
            ->setCellValue($this->Campos[29]."1","tipcru2")
            ->setCellValue($this->Campos[30]."1","numcru2")
            ->setCellValue($this->Campos[31]."1","cuocru2")
            ->setCellValue($this->Campos[32]."1","valdebi")
            ->setCellValue($this->Campos[33]."1","valcred")
            ->setCellValue($this->Campos[34]."1","parci_o")
            ->setCellValue($this->Campos[35]."1","tpreg")
            ->setCellValue($this->Campos[36]."1","detalle")
            ->setCellValue($this->Campos[37]."1","serial")
            ;
   if($Tipo=="I"){
       $Filtro=" CuentaPUC LIKE '41%' OR CuentaPUC LIKE '42%' ";
   }
   if($Tipo=="E"){
       $Filtro=" CuentaPUC LIKE '51%' OR CuentaPUC LIKE '52%' ";
   }
   
   $sql="SELECT * FROM librodiario WHERE (Fecha>='$FechaIni' AND Fecha<='$FechaFin') ORDER BY Fecha";
   //print($sql);
   $Consulta=  $this->obCon->Query($sql);
   $i=2;
   $TipoDocumentoOLD="";
   $Num_DocumentoOLD="";
   while($DatosIngreso=$this->obCon->FetchArray($Consulta)){
       
       $FechaVacia=str_replace("-", "", $DatosIngreso["Fecha"]);
     
       $TipoDocumento=$DatosIngreso["Tipo_Documento_Intero"];
       $Num_Documento=$DatosIngreso["Num_Documento_Interno"];
       if($TipoDocumento<>$TipoDocumentoOLD OR $Num_DocumentoOLD<>$Num_Documento){
       $TipoDocumentoOLD=$TipoDocumento;
       $Num_DocumentoOLD=$Num_Documento;
       $CuentaPUC=$DatosIngreso["CuentaPUC"];
       $sql="SELECT Sum(Debito) as Debito, Sum(Credito) as Credito,Tipo_Documento_Intero,Fecha,CuentaPUC,Tercero_Identificacion, Num_Documento_Interno "
               . " FROM librodiario WHERE Tipo_Documento_Intero='$TipoDocumento' AND Num_Documento_Interno='$Num_Documento' GROUP BY Tipo_Documento_Intero, CuentaPUC";
       $Consulta2=$this->obCon->Query($sql);
       //$Consulta2=$this->obCon->ConsultarTabla("librodiario", "WHERE Tipo_Documento_Intero='$TipoDocumento' AND Num_Documento_Interno='$Num_Documento' ");
       while($DatosMovimiento=$this->obCon->FetchArray($Consulta2)){
           $NumDoc=$DatosMovimiento["Num_Documento_Interno"];
           if($DatosMovimiento["Tipo_Documento_Intero"]=="FACTURA"){
               $DatosDocumento=$this->obCon->DevuelveValores("facturas", "idFacturas", $DatosMovimiento["Num_Documento_Interno"]);
           
               $NumDoc=$DatosDocumento["NumeroFactura"];
           }
           $DatosDocumento=$this->obCon->DevuelveValores("facturas", "idFacturas", $DatosMovimiento["Num_Documento_Interno"]);
           $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[0].$i,"101")
            ->setCellValue($this->Campos[1].$i," 0000 ")
            ->setCellValue($this->Campos[2].$i,".")
            ->setCellValue($this->Campos[3].$i,$DatosMovimiento["Tipo_Documento_Intero"])
            ->setCellValue($this->Campos[4].$i,$NumDoc)
            ->setCellValue($this->Campos[5].$i,$i-1)
            ->setCellValue($this->Campos[6].$i,$DatosMovimiento["Fecha"])
            ->setCellValue($this->Campos[7].$i,$DatosMovimiento["CuentaPUC"])
            ->setCellValue($this->Campos[8].$i,$DatosMovimiento["Tercero_Identificacion"])
            ->setCellValue($this->Campos[9].$i,".")
            ->setCellValue($this->Campos[10].$i,".")
            ->setCellValue($this->Campos[11].$i,".")
            ->setCellValue($this->Campos[12].$i,".")
            ->setCellValue($this->Campos[13].$i,".")
            ->setCellValue($this->Campos[14].$i,".")
            ->setCellValue($this->Campos[15].$i,".")
            ->setCellValue($this->Campos[16].$i,".")
            ->setCellValue($this->Campos[17].$i,".")
            ->setCellValue($this->Campos[18].$i,".")
            ->setCellValue($this->Campos[19].$i,".")
            ->setCellValue($this->Campos[20].$i,"0")
            ->setCellValue($this->Campos[21].$i,"E")
            ->setCellValue($this->Campos[22].$i,".")
            ->setCellValue($this->Campos[23].$i,".")
            ->setCellValue($this->Campos[24].$i,"0")
            ->setCellValue($this->Campos[25].$i,"0")
            ->setCellValue($this->Campos[26].$i,$DatosMovimiento["Fecha"])
            ->setCellValue($this->Campos[27].$i,"0")
            ->setCellValue($this->Campos[28].$i,".")
            ->setCellValue($this->Campos[29].$i,".")
            ->setCellValue($this->Campos[30].$i,"0")
            ->setCellValue($this->Campos[31].$i,"0")
            ->setCellValue($this->Campos[32].$i,$DatosMovimiento["Debito"])
            ->setCellValue($this->Campos[33].$i,$DatosMovimiento["Credito"])
            ->setCellValue($this->Campos[34].$i,"0")
            ->setCellValue($this->Campos[35].$i,"1")
            ->setCellValue($this->Campos[36].$i,".")
            ->setCellValue($this->Campos[37].$i,".")
               ;
           $i++;
       }
       }
   }
   
   
    
   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar Ingresos")
        ->setSubject("Informe")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones sas")
        ->setCategory("Informe Ingresos");    
 
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'."Interface_Ingresos".'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
   
      
    }
    
    
    //Genera un excel con el auxiliar detallado
    public function ExcelAuxiliarDetallado($TipoReporte,$FechaInicial,$FechaFinal,$FechaCorte,$CuentaPUC,$TipoFiltro,$Tercero,$Vector) {
        require_once '../librerias/Excel/PHPExcel.php';
        $Condicion=" WHERE ";
        $Condicion2=" WHERE ";
        if($TipoReporte=="Corte"){
            $FechaCalculoAnterior=$FechaCorte;
            $Condicion.=" Fecha <= '$FechaCorte' ";
            $Condicion2.=" Fecha > '5000-01-01' AND  ";
            $Rango="Corte a $FechaCorte";
        }else{
            $FechaCalculoAnterior=$FechaInicial;
            $Condicion.=" Fecha >= '$FechaInicial' AND Fecha <= '$FechaFinal' "; 
            $Condicion2.= " Fecha < '$FechaInicial' AND ";
            $Rango="De $FechaInicial a $FechaFinal";
        }
        if($Tercero<>"All"){
                $Condicion.="  AND Tercero_Identificacion='$Tercero' ";
                $Condicion2.="  AND Tercero_Identificacion='$Tercero' ";
        }
        if($CuentaPUC<>"" AND $TipoFiltro=="Igual"){
                $Condicion.="  AND CuentaPUC='$CuentaPUC' ";
                $Condicion2.="  AND CuentaPUC='$CuentaPUC' ";
        }
        
        if($CuentaPUC<>"" AND $TipoFiltro=="Inicia"){
                $Condicion.="  AND CuentaPUC LIKE '$CuentaPUC%' ";
                $Condicion2.="  AND CuentaPUC LIKE '$CuentaPUC%' ";
        }
        
        
        $objPHPExcel = new PHPExcel();  
        
        $objPHPExcel->getActiveSheet()->getStyle('A:H')->getNumberFormat()->setFormatCode('#');
        $objPHPExcel->getActiveSheet()->getStyle('I:M')->getNumberFormat()->setFormatCode('#,##0');
        $objPHPExcel->getActiveSheet()->getStyle("A:N")->getFont()->setSize(10);
        
        $f=1;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[2].$f,"LIBRO AUXILIAR $Rango")
                        
            ;
                
        $sql="SELECT `Fecha`,`Num_Documento_Externo`,`Tercero_Razon_Social`, SUM(`Debito`) AS Debitos,SUM(`Credito`) AS Creditos,`CuentaPUC`,`NombreCuenta`,`Tercero_Identificacion`,`Tipo_Documento_Intero`,`Num_Documento_Interno`,`Concepto`,`Detalle` "
                . " FROM `librodiario` $Condicion GROUP BY `Tercero_Identificacion`,`Tipo_Documento_Intero`,`Num_Documento_Interno`,`CuentaPUC` ORDER BY `Tercero_Identificacion`,`Fecha`,`CuentaPUC` ";
        $Datos=$this->obCon->Query($sql);
        $Tercero='';
        $SaldoAnterior=0;
        $NuevoSaldo=0;
        $Totales[]=0;
        $Cuenta=0;
        $TotalSaldo=0;
        while($DatosLibro=$this->obCon->FetchArray($Datos)){
            $f++;
            $FechaLibro=$DatosLibro["Fecha"];
            $CuentaPUC=$DatosLibro["CuentaPUC"];
            $CuentaComp=substr($CuentaPUC,0,4);
            if(!isset($Totales["$CuentaComp"]["Valor"])){
                //$Totales["$CuentaComp"]["Valor"]=0;
                $Totales[$CuentaComp]=0;
            }
            $CambiaRazonSocial=0;
            if($Tercero<>$DatosLibro["Tercero_Identificacion"] or $CuentaComp<>$Cuenta){
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($this->Campos[11].$f,"Saldo Final")    
                    ->setCellValue($this->Campos[12].$f,$NuevoSaldo);
                $f++;  
                $TotalSaldo=$TotalSaldo+$NuevoSaldo;
                $CambiaRazonSocial=1;
                $SaldoAnterior=0;
                $NuevoSaldo=0;
            }
            $Tercero=$DatosLibro["Tercero_Identificacion"];
                       
            $DatosAbreviaturas=$this->obCon->DevuelveValores("documentos_generados", "Libro", $DatosLibro["Tipo_Documento_Intero"]);
            $Doc_Interno=$DatosLibro["Num_Documento_Interno"];
            if($DatosAbreviaturas["Abreviatura"]=="FV"){
                $sql="SELECT NumeroFactura FROM facturas WHERE idFacturas='$Doc_Interno'";
                $consultaF= $this->obCon->Query($sql);
                $Num= $this->obCon->FetchArray($consultaF);
                $Doc_Interno=$Num["NumeroFactura"];
            }
            if($CambiaRazonSocial==1){
                
                $Cuenta=substr($CuentaPUC,0,4);
                if($TipoReporte<>"Corte"){
                    $sql="SELECT SUM(`Neto`) AS SaldoAnterior FROM librodiario WHERE Fecha<'$FechaCalculoAnterior' AND CuentaPUC LIKE '$CuentaPUC%' AND Tercero_Identificacion='$Tercero'";
                    $ConsultaAnterior=  $this->obCon->Query($sql);
                    $DatosConsultaAnterior=$this->obCon->FetchArray($ConsultaAnterior);
                    $SaldoAnterior=$DatosConsultaAnterior["SaldoAnterior"];
                    if($SaldoAnterior==''){
                        $SaldoAnterior=0;
                    }
                }else{
                    $SaldoAnterior=0;
                }
                $f++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($this->Campos[0].$f,$DatosLibro["Tercero_Identificacion"])
                    ->setCellValue($this->Campos[1].$f,$DatosLibro["Tercero_Razon_Social"]) ; 
                $f++;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($this->Campos[0].$f,"FECHA")
                    ->setCellValue($this->Campos[1].$f,"DOC")
                    ->setCellValue($this->Campos[2].$f,"NOMBRE DOCUMENTO")
                    ->setCellValue($this->Campos[3].$f,"NUM_DOC")
                    ->setCellValue($this->Campos[4].$f,"DETALLE")
                    ->setCellValue($this->Campos[5].$f,"DOC_REF")
                    ->setCellValue($this->Campos[6].$f,"CUENTA")
                    ->setCellValue($this->Campos[7].$f,"NOMBRE")
                    ->setCellValue($this->Campos[8].$f,"SALDO ANTERIOR")
                    ->setCellValue($this->Campos[9].$f,"DEBITO")
                    ->setCellValue($this->Campos[10].$f,"CREDITO")
                    ->setCellValue($this->Campos[11].$f,"NUEVO SALDO");
                $f++;
                               
            }
            
            $NuevoSaldo=$SaldoAnterior+$DatosLibro["Debitos"]-$DatosLibro["Creditos"];
            $Totales[$CuentaComp]=$Totales[$CuentaComp]+$NuevoSaldo;
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($this->Campos[0].$f,$DatosLibro["Fecha"])
            ->setCellValue($this->Campos[1].$f,$DatosAbreviaturas["Abreviatura"])
            ->setCellValue($this->Campos[2].$f,$DatosAbreviaturas["Nombre"])
            ->setCellValue($this->Campos[3].$f,$Doc_Interno)
            ->setCellValue($this->Campos[4].$f,$DatosLibro["Detalle"]." ".$DatosLibro["Concepto"])
            ->setCellValue($this->Campos[5].$f,$DatosLibro["Num_Documento_Externo"])
            ->setCellValue($this->Campos[6].$f,$DatosLibro["CuentaPUC"])
            ->setCellValue($this->Campos[7].$f,$DatosLibro["NombreCuenta"])
            ->setCellValue($this->Campos[8].$f,$SaldoAnterior)
            ->setCellValue($this->Campos[9].$f,$DatosLibro["Debitos"])
            ->setCellValue($this->Campos[10].$f,$DatosLibro["Creditos"])
            ->setCellValue($this->Campos[11].$f,$NuevoSaldo)
            ;
            $SaldoAnterior=$NuevoSaldo;
            
        }
        $TotalSaldo=$TotalSaldo+$NuevoSaldo;
        $f++;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($this->Campos[11].$f,"Saldo Final")    
                    ->setCellValue($this->Campos[12].$f,$NuevoSaldo)        
                    ->setCellValue($this->Campos[13]."2","Saldo Total")    
                    ->setCellValue($this->Campos[14]."2",$TotalSaldo);
        /*
        while (list($clave, $valor) = each($Totales)) {
            $f++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($this->Campos[0].$f,"Total Cuenta $clave :")
                ->setCellValue($this->Campos[1].$f,$Totales[$clave]) ;
        }
         
        foreach($Totales["Cuenta"] as $NumeroCuenta){
            $f++;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($this->Campos[0].$f,"Total Cuenta $NumeroCuenta :")
                ->setCellValue($this->Campos[1].$f,$Totales["$NumeroCuenta"]["Valor"]) ; 
            
        }
          * 
          */
        //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("www.technosoluciones.com")
        ->setLastModifiedBy("www.technosoluciones.com")
        ->setTitle("Exportar Ingresos")
        ->setSubject("Informe")
        ->setDescription("Documento generado con PHPExcel")
        ->setKeywords("techno soluciones sas")
        ->setCategory("Informe Ingresos");    
 
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'."Auxiliar".'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    $objWriter->save('php://output');
    exit; 
    }
        
    //Inicia la creacion de un pdf
    public function PDF_Ini($TituloFormato,$FontSize,$VectorPDF,$Margenes=1) {
        
        require_once('../tcpdf/examples/config/tcpdf_config_alt.php');
        $tcpdf_include_dirs = array(realpath('../tcpdf/tcpdf.php'), '/usr/share/php/tcpdf/tcpdf.php', '/usr/share/tcpdf/tcpdf.php', '/usr/share/php-tcpdf/tcpdf.php', '/var/www/tcpdf/tcpdf.php', '/var/www/html/tcpdf/tcpdf.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf.php');
        foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
                if (@file_exists($tcpdf_include_path)) {
                        require_once($tcpdf_include_path);
                        break;
                }
        }
        // create new PDF document
        $this->PDF = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'ISO 8859-1', false);
        // set document information
        $this->PDF->SetCreator(PDF_CREATOR);
        $this->PDF->SetAuthor('Techno Soluciones');
        $this->PDF->SetTitle($TituloFormato);
        $this->PDF->SetSubject($TituloFormato);
        $this->PDF->SetKeywords('Techno Soluciones, PDF, '.$TituloFormato.' , CCTV, Alarmas, Computadores, Software');
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, 60, PDF_HEADER_TITLE.'', "");
        // set header and footer fonts
        $this->PDF->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->PDF->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $this->PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        if($Margenes==1){
            $this->PDF->SetMargins(10, 10, PDF_MARGIN_RIGHT);
            $this->PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->PDF->SetFooterMargin(10);
        }
        
        // set auto page breaks
        $this->PDF->SetAutoPageBreak(TRUE, 10);
        // set image scale factor
        $this->PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/spa.php')) {
                require_once(dirname(__FILE__).'/lang/spa.php');
                $this->PDF->setLanguageArray($l);
        }
        
        // ---------------------------------------------------------
        // set font
        //$pdf->SetFont('helvetica', 'B', 6);
        // add a page
        $this->PDF->AddPage();
        $this->PDF->SetFont('helvetica', '', $FontSize);
        
}
//encabezado de un  formato de calidad en un pdf
    public function PDF_Encabezado($Fecha,$idEmpresa,$idFormatoCalidad,$VectorEncabezado,$NumeracionDocumento="") {
        $DatosEmpresaPro=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro", $idEmpresa);
        $DatosFormatoCalidad=$this->obCon->DevuelveValores("formatos_calidad", "ID", $idFormatoCalidad);
        
        $RutaLogo="../$DatosEmpresaPro[RutaImagen]";
///////////////////////////////////////////////////////
//////////////encabezado//////////////////
////////////////////////////////////////////////////////
//////
//////
$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr border="1">
        <td rowspan="3" border="1" style="text-align: center;"><img src="$RutaLogo" style="width:110px;height:60px;"></td>
        
        <td rowspan="3" width="290px" style="text-align: center; vertical-align: center;"><h2><br>$DatosFormatoCalidad[Nombre]</h2></td>
        <td width="70px" style="text-align: center;">Versión<br></td>
        <td width="130px"> $DatosFormatoCalidad[Version]</td>
    </tr>
    <tr>
    	
    	<td style="text-align: center;" >Código<br></td>
        <td> $DatosFormatoCalidad[Codigo]</td>
        
    </tr>
    <tr>
       <td style="text-align: center;" >Fecha<br></td>
       <td style="font-size:6px;"> $DatosFormatoCalidad[Fecha]</td> 
    </tr>
</table>
EOD;
$this->PDF->writeHTML($tbl, true, false, false, false, '');
$this->PDF->SetFillColor(255, 255, 255);
$txt="<h3>".$DatosEmpresaPro["RazonSocial"]."<br>NIT ".$DatosEmpresaPro["NIT"]."</h3>";
$this->PDF->MultiCell(62, 5, $txt, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');
$txt=$DatosEmpresaPro["Direccion"]."<br>".$DatosEmpresaPro["Telefono"]."<br>".$DatosEmpresaPro["Ciudad"]."<br>".$DatosEmpresaPro["WEB"];
$this->PDF->MultiCell(62, 5, $txt, 0, 'C', 1, 0, '', '', true,0, true, true, 10, 'M');
$Documento="<strong>$NumeracionDocumento</strong><br><h5>Impreso por TS5, Techno Soluciones SAS <BR>NIT 900.833.180 3177740609</h5><br>";
$this->PDF->MultiCell(62, 5, $Documento, 0, 'R', 1, 0, '', '', true,0, true ,true, 10, 'M');
$this->PDF->writeHTML("<br>", true, false, false, false, '');
//Close and output PDF document
    }
//Crear el documento PDF
    public function PDF_Write($html) {
        $this->PDF->writeHTML($html, true, false, false, false, '');
    } 
//Agregar pagina en PDF
    public function PDF_Add() {
        $this->PDF->AddPage();
    }     
//Crear el documento PDF
    public function PDF_Output($NombreArchivo,$TipoSalida="I") {
        $this->PDF->Output("$NombreArchivo".".pdf", $TipoSalida);
    } 
    
    //Crear Estados Financieros en PDF
 
    public function ArmeTemporalMayor($FechaCorte,$CentroCostos,$EmpresaPro,$Vector){
        //obtener activos
        $Condicion=" WHERE Fecha<='$FechaCorte'";
        if($CentroCostos<>"ALL"){
            $Condicion.=" AND idCentroCosto='$CentroCostos'";
        }
        if($EmpresaPro<>"ALL"){
            $Condicion.=" AND idEmpresa='$EmpresaPro'";
        }
        $Clase=0;
        $this->obCon->VaciarTabla("estadosfinancieros_mayor_temporal");
        $sql="SELECT SUBSTRING(`CuentaPUC`,1,4) AS Cuenta ,sum(`Neto`) as TotalCuenta FROM `librodiario` $Condicion GROUP BY SUBSTRING(`CuentaPUC`,1,4) ORDER BY SUBSTRING(`CuentaPUC`,1,4)";
        $Consulta=$this->obCon->Query($sql);
        
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($DatosMayor["Cuenta"]>0){
                $Clase=substr($DatosMayor["Cuenta"], 0, 1);
                $DatosCuenta=$this->obCon->DevuelveValores("cuentas", "idPUC", $DatosMayor["Cuenta"]);
                $tab="estadosfinancieros_mayor_temporal";
                $NumRegistros=5;
                $Columnas[0]="FechaCorte";        $Valores[0]=$FechaCorte;
                $Columnas[1]="Clase";             $Valores[1]=$Clase;
                $Columnas[2]="CuentaPUC";         $Valores[2]=$DatosMayor["Cuenta"];
                $Columnas[3]="NombreCuenta";      $Valores[3]=$DatosCuenta["Nombre"];
                $Columnas[4]="Neto";              $Valores[4]=$DatosMayor["TotalCuenta"];
                $this->obCon->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
            }
        }
        
        $Activos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='1'");
        $Pasivos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='2'");
        $Patrimonio=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='3'");
        $Ingresos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='4'");
        $GastosOperativos=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='5'");
        $CostosVentas=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='6'");
        $CostosProduccion=$this->obCon->Sume("estadosfinancieros_mayor_temporal", "Neto", "WHERE Clase='7'");
        
        $TotalClases[1]=$Activos;
        $TotalClases[2]=$Pasivos*(-1);    //Es naturaleza credito por lo tanto debe multiplicarse por -1
        $TotalClases[3]=$Patrimonio*(-1);
        $TotalClases[4]=$Ingresos*(-1);
        $TotalClases[5]=$GastosOperativos;
        $TotalClases[6]=$CostosVentas;
        $TotalClases[7]=$CostosProduccion;
        $TotalClases["RE"]=($TotalClases[1]-$TotalClases[2]-$TotalClases[3])*(-1);//resultado del ejercicio
        $TotalClases["UB"]=$TotalClases[4]-$TotalClases[6]-$TotalClases[7];//Utilidad Bruta
        $TotalClases["UO"]=$TotalClases["UB"]-$TotalClases[5]; //Utilidad de la Operacion
        
        if($TotalClases["RE"]>=0){
            $DatosCuentaRE=  $this->obCon->DevuelveValores("parametros_contables", "ID", 12);
            
        }else{
            $DatosCuentaRE=  $this->obCon->DevuelveValores("parametros_contables", "ID", 11);
            
        }
        $tab="estadosfinancieros_mayor_temporal";
        $NumRegistros=5;
        $Columnas[0]="FechaCorte";        $Valores[0]=$FechaCorte;
        $Columnas[1]="Clase";             $Valores[1]=3;
        $Columnas[2]="CuentaPUC";         $Valores[2]=$DatosCuentaRE["CuentaPUC"];
        $Columnas[3]="NombreCuenta";      $Valores[3]=$DatosCuentaRE["NombreCuenta"];
        $Columnas[4]="Neto";              $Valores[4]=$TotalClases["RE"];
        $this->obCon->InsertarRegistro($tab,$NumRegistros,$Columnas,$Valores);
        
        return($TotalClases);
    }
    
    //Armar el html para el balance General
    public function ArmeHTMLBalanceGeneral($TotalClases,$FechaCorte) {
        $Back="#CEE3F6";
        $html='<table cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="6"><strong>Estado de Situacion Financiera <br>A '.$FechaCorte.'</strong></td></tr>'; 
        //$Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="3"><strong>ACTIVOS</strong></td><td colspan="3"><strong>PASIVOS Y PATRIMONIO</strong></td></tr>';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td><strong>Cuenta</strong></td><td><strong>Nombre</strong></td><td><strong>Valor</strong></td>';
        $html.='<td><strong>Cuenta</strong></td><td><strong>Nombre</strong></td><td><strong>Valor</strong></td></tr>';
        //Calculo el total de filas que necesitare
        $FilasActivo=$this->obCon->Count("estadosfinancieros_mayor_temporal", "Clase", " WHERE Clase=1");
        $FilasPasivoPatrimonio=$this->obCon->Count("estadosfinancieros_mayor_temporal", "Clase", " WHERE Clase=2 OR Clase=3");
        $FilasPasivoPatrimonio;
        if($FilasActivo>=$FilasPasivoPatrimonio){
            $TotalFilas=$FilasActivo;
            $M=1;
        }else{
            $TotalFilas=$FilasPasivoPatrimonio;
            $M=0;
        }
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " ORDER BY CuentaPUC");
        $f=0;
        $flag=0;
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            
            
            if($DatosMayor["Clase"]==1){
                
                $Fila[$f]["CodigoA"]=$DatosMayor["CuentaPUC"];
                $Fila[$f]["CuentaA"]=$DatosMayor["NombreCuenta"];
                $Fila[$f]["ValorA"]=$DatosMayor["Neto"];
            }
            
            if($DatosMayor["Clase"]==2 OR $DatosMayor["Clase"]==3){
                if($f>=0 and $flag==0){
                    $f=0;
                    $flag=1;
                }
                $Fila[$f]["CodigoPP"]=$DatosMayor["CuentaPUC"];
                $Fila[$f]["CuentaPP"]=$DatosMayor["NombreCuenta"];
                $Fila[$f]["ValorPP"]=$DatosMayor["Neto"]*(-1);
            }
            $f++;
                       
        }
        if($TotalClases["RE"]>=0){
            $DatosParametros=$this->obCon->DevuelveValores("parametros_contables", "ID", 11);
        }else{
            $DatosParametros=$this->obCon->DevuelveValores("parametros_contables", "ID", 12);
        }
        $ResultadoEjercicio["CodigoPP"]= $DatosParametros["CuentaPUC"] ;
        $ResultadoEjercicio["CuentaPP"]=$DatosParametros["NombreCuenta"] ;
        $ResultadoEjercicio["ValorPP"]=$TotalClases["RE"];
        
        $h=1;
        for($i=0;$i<=$TotalFilas;$i++){
           
           $PUCA="";
           $NombreA="";
           $ValorA="";
           $PUCPP="";
           $NombrePP="";
           $ValorPP="";
           if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           if(isset($Fila[$i]["CodigoA"])){
               $PUCA=$Fila[$i]["CodigoA"];
               $NombreA=$Fila[$i]["CuentaA"];
               $ValorA=$Fila[$i]["ValorA"];
           }
           if(isset($Fila[$i]["CodigoPP"])){
               
                $PUCPP=$Fila[$i]["CodigoPP"];
                $NombrePP=$Fila[$i]["CuentaPP"];
                $ValorPP=$Fila[$i]["ValorPP"];
               
           }
           if($ValorA<>""){
               $ValorA=number_format($ValorA);
           }
           if($ValorPP<>""){
               $ValorPP=number_format($ValorPP);
           }
           $html.='<td>'.$PUCA.'</td><td>'.$NombreA.'</td><td align="right">'.$ValorA.'</td>';
           $html.='<td>'.$PUCPP.'</td><td>'.$NombrePP.'</td><td align="right">'.$ValorPP.'</td>';
           $html.="</tr>";
        }
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="2"><strong>Total Activos</strong></td><td align="right"><strong>'.number_format($TotalClases[1]).'</strong></td>';
        $html.='<td colspan="2"><strong>Total Pasivo y Patrimonio</strong></td><td align="right"><strong>'.number_format($TotalClases[2]+$TotalClases[3]-$TotalClases["RE"]).'</strong></td></tr>';
        $html.="</table>";
        return($html);
    }
    
    //Armar el html para el estado de resultados
    public function ArmeHTMLEstadoResultados($TotalClases,$FechaCorte) {
        $Back="#CEE3F6";
        $html='<table cellspacing="1" cellpadding="2" border="0"  align="center" >';
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="3"><strong>Estado del Resultado Integral <br>A '.$FechaCorte.'</strong></td></tr>'; 
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="3"><strong>INGRESOS</strong></td></tr>';
        
        ///Se dibujan los ingresos
        $h=1;     
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=4");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]*(-1));
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$Valor.'</td>'; 
           $html.='</tr>'; 
        }
        
        $TotalIngresos=0;
        if($TotalClases[4]<>""){
            $TotalIngresos=  number_format($TotalClases[4]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="2"><strong>Total de Ingresos:</strong></td><td><strong>'.$TotalIngresos.'</strong></td>'; 
        $html.='</tr>'; 
        
         ///Se dibujan los costos de venta y produccion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="3"><strong>COSTOS DE VENTA Y/O PRODUCCION</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=6 OR Clase=7");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$Valor.'</td>'; 
           $html.='</tr>'; 
        }
        
        
        $TotalCostos=$TotalClases[6]+$TotalClases[7];
        $TotalCostosN=0;
        if($TotalCostos<>""){
            $TotalCostosN=  number_format($TotalCostos);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="2"><strong>Total Costos de Venta y/o Produccion:</strong></td><td><strong>'.$TotalCostosN.'</strong></td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        
        if($TotalClases["UB"]<>""){
            $UtilidadBruta=  number_format($TotalClases["UB"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="2"><strong>Utilidad Bruta:</strong></td><td><strong>'.$UtilidadBruta.'</strong></td>'; 
        $html.='</tr>'; 
        
        
        ///Se dibujan los gastos y utilidad de la operacion
        $Back="#CEE3F6";
        $html.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $html.='<td colspan="3"><strong>GASTOS</strong></td></tr>';
        $h=1; 
        $Consulta=$this->obCon->ConsultarTabla("estadosfinancieros_mayor_temporal", " WHERE Clase=5");
              
        while($DatosMayor=$this->obCon->FetchArray($Consulta)){
            if($h==0){
                $Back="#f2f2f2";
                $h=1;
            }else{
                $Back="white";
                $h=0;
            }
           $Valor=  number_format($DatosMayor["Neto"]);
           $html.='<tr align="left" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
           $html.='<td>'.$DatosMayor["CuentaPUC"].'</td><td>'.$DatosMayor["NombreCuenta"].'</td><td align="right">'.$Valor.'</td>'; 
           $html.='</tr>'; 
        }
        
        
        if($TotalClases[5]<>""){
            $TotalGastos=  number_format($TotalClases[5]);
        }
        $Back="#fef9e7";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="2"><strong>Total Gastos:</strong></td><td><strong>'.$TotalGastos.'</strong></td>'; 
        $html.='</tr>'; 
        
        ///Dibujamos Utilidad Bruta
        
        
        if($TotalClases["UO"]<>""){
            $UtilidadOperacional=  number_format($TotalClases["UO"]);
        }
        $Back="#f9e79f";
        $html.='<tr align="right" border="0" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';"> ';
        $html.='<td colspan="2"><strong>Utilidad de la Operacion:</strong></td><td><strong>'.$UtilidadOperacional.'</strong></td>'; 
        $html.='</tr>'; 
        
        $html.="</table>";
        return($html);
    }
    
 //Crear Estados Financieros en PDF
 
    public function GenereEstadosFinancierosPDF($FechaCorte,$CentroCostos,$EmpresaPro,$Vector){
        if($EmpresaPro=="ALL"){
            $EmpresaPro=1;
        }
        $TotalClases=$this->ArmeTemporalMayor($FechaCorte, $CentroCostos, $EmpresaPro, $Vector);
        //print("<pre>");
        //print_r($TotalClases);
        //print("</pre>");
        
        $htmlBG=$this->ArmeHTMLBalanceGeneral($TotalClases,$FechaCorte);
        //print($htmlBG);
        $htmlER=$this->ArmeHTMLEstadoResultados($TotalClases,$FechaCorte);
        $Back="#f2f2f2";
        $htmlFirmas='<table cellspacing="1" cellpadding="1" border="0"  align="center" >';
        $htmlFirmas.='<tr style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">';
        $htmlFirmas.='<td height="60"><strong>Gerente</strong></td><td height="60"><strong>Contador</strong></td></tr>'; 
        $htmlFirmas.='</table>';      
        $this->PDF_Ini("Estados Financieros", 8, "");
        //print($htmlBG);
        //print($htmlER);
        $this->PDF_Encabezado($FechaCorte,$EmpresaPro, 15, "");
        $this->PDF_Write("<br><br>".$htmlBG);
        $this->PDF_Write($htmlFirmas);
        $this->PDF_Add();
        $this->PDF_Write("<br><br>".$htmlER);
        $this->PDF_Write($htmlFirmas);
        $this->PDF_Output("Estados_Financieros_$FechaCorte");
         
    }
    
    //Genera el pdf de un traslado de un titulo
    //
    public function GenerePDFTrasladoTitulo($idTraslado,$Vector) {
        $DatosTraslado=  $this->obCon->DevuelveValores("titulos_traslados", "ID", $idTraslado);
        $DatosPromocion=$this->obCon->DevuelveValores("titulos_promociones", "ID", $DatosTraslado["Promocion"]);
        $this->PDF_Ini("Traslado Titulo", 9, "");
        $html="<pre>$DatosTraslado[Fecha]
		
		
		
		
		
ASUNTO:    <strong>TRASLADO DE TITULO $DatosTraslado[Mayor1] </strong>
";
        $html.=' El dia '.$DatosTraslado["Fecha"].' se realiza el traslado del titulo '.$DatosTraslado["Mayor1"].' de la promocion '
                . ' '.$DatosPromocion["Nombre"].' que estaba en poder del Sr. '.$DatosTraslado["NombreColaboradorAnterior"].' Identificado '
                . ' Con CC No. '.$DatosTraslado["idColaboradorAnterior"].' y ahora pasa al Sr. '.$DatosTraslado["NombreColaboradorAsignado"].' Identificado '
                . ' Con CC No. '.$DatosTraslado["idColaboradorAsignado"].', Por motivo de '.$DatosTraslado["Observaciones"];
        $html.="</pre><pre>
         
		 
		 
		 
		 
		 
  Entrega: _____________________________          Recibe:_____________________________</pre>";
        $this->PDF_Encabezado($DatosTraslado["Fecha"],1, 22, "");
        $this->PDF_Write("<br><br>".$html);
        $this->PDF_Output("TrasladoTitulo_".$DatosTraslado["Mayor1"].'_'.$DatosTraslado["Fecha"]);
    }
    
    //Crear total de una 
    public function CrearSubtotalCuentaRestaurante($idMesa,$idDepartamento,$idUser,$myPage,$Vector) {
        $this->css=new CssIni("");
        $Titulo="Ver Esta Mesa";
        $Nombre="ImgShowMesa";
        $RutaImage="../images/cuentasxcobrar.png";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialVerMesa";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","left:10px;top:50",$VectorBim);
        $Titulo="Buscar";
        $Nombre="ImgBuscar";
        $RutaImage="../images/buscar.png";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialBuscar";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","right:10px;top:50",$VectorBim);
        
        $this->css->CrearCuadroDeDialogo("DialVerMesa", "Esta Cuenta:");
        $this->css->CrearTabla();
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>Subtotal</strong>", 1);
        $this->css->ColTabla("<strong>IVA</strong>", 1);
        $this->css->ColTabla("<strong>Total</strong>", 2);
        $this->css->CierraFilaTabla();
        
        $sql="SELECT ID FROM restaurante_pedidos WHERE idUsuario='$idUser' AND idMesa='$idMesa' AND Estado='AB'";
        $consulta=  $this->obCon->Query($sql);
        $htmlItems="";
        $Subtotal=0;
        $IVA=0;
        $Total=0;
        if($this->obCon->NumRows($consulta)){
            $DatosPedido=$this->obCon->FetchArray($consulta);
            $idPedido=$DatosPedido["ID"];
            $htmlItems="<tr><td colspan=4><strong>ITEMS EN PEDIDO $idPedido DE LA MESA $idMesa</strong></td></tr>";
            $htmlItems.="<tr><td><strong>Producto</strong></td><td><strong>Cantidad</strong></td>"
                    . "<td><strong>Total</strong></td><td><strong>Borrar</strong></td></tr>";
            
            $consulta2=  $this->obCon->ConsultarTabla("restaurante_pedidos_items", " WHERE idPedido='$idPedido'");
            
            while($DatosItems=$this->obCon->FetchArray($consulta2)){
                $htmlItems.="<tr><td>$DatosItems[NombreProducto]<br>$DatosItems[Observaciones]</td><td>$DatosItems[Cantidad]</td><td>$DatosItems[Total]</td>";
                $htmlItems.="<td><a href='$myPage?idMesa=$idMesa&idDepartamento=$idDepartamento&idDel=$DatosItems[ID]'>X</td>";
                $htmlItems.="</tr>";
                $Subtotal=$Subtotal+$DatosItems["Subtotal"];
                $IVA=$IVA+$DatosItems["IVA"];
                $Total=$Total+$DatosItems["Total"];
            }
        }
        $this->css->FilaTabla(16);
        $this->css->ColTabla(number_format($Subtotal), 1);
        $this->css->ColTabla(number_format($IVA), 1);
        $this->css->ColTabla(number_format($Total), 2);
        $this->css->CierraFilaTabla();
        print($htmlItems);
        $this->css->CerrarTabla();
        $this->css->CerrarCuadroDeDialogo();
        
        $this->css->CrearCuadroDeDialogo("DialBuscar", "Buscar un producto:");
        $this->css->CrearForm2("FrmBuscar", $myPage, "post", "_self");
        $this->css->CrearInputText("idMesa", "hidden", "", $idMesa, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("idDepartamento", "hidden", "", $idDepartamento, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("TxtBusqueda", "text", "", "", "Buscar", "", "", "", 200, 30, 0, 1);
        print("<br>");
        $this->css->CrearBoton("BtnBuscar", "Buscar");
        
        $this->css->CerrarForm();
        $this->css->CerrarCuadroDeDialogo();
    }
    
    //Crear total de una 
    public function CrearSubtotalCuentaDomicilio($idDomicilio,$idDepartamento,$idUser,$myPage,$Vector) {
        $this->css=new CssIni("");
        $Titulo="Ver Este Domicilio";
        $Nombre="ImgShowDomicilio";
        $RutaImage="../images/cuentasxcobrar.png";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialVerDomicilio";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","left:10px;top:50",$VectorBim);
        $Titulo="Buscar";
        $Nombre="ImgBuscarItemDomicilio";
        $RutaImage="../images/buscar.png";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialBuscarItemDomicilio";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","right:10px;top:50",$VectorBim);
        
        $this->css->CrearCuadroDeDialogo("DialVerDomicilio", "Este Domicilio:");
        $this->css->CrearTabla();
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>Subtotal</strong>", 1);
        $this->css->ColTabla("<strong>IVA</strong>", 1);
        $this->css->ColTabla("<strong>Total</strong>", 2);
        $this->css->CierraFilaTabla();
        
        
        $htmlItems="";
        $Subtotal=0;
        $IVA=0;
        $Total=0;
        
            
            $idPedido=$idDomicilio;
            $DatosDomicilio=  $this->obCon->DevuelveValores("restaurante_pedidos", "ID", $idPedido);
            $htmlItems="<tr><td colspan=4><strong>ITEMS EN DOMICILIO $idPedido para el Sr(a)$DatosDomicilio[NombreCliente]</strong></td></tr>";
            $htmlItems.="<tr><td><strong>Producto</strong></td><td><strong>Cantidad</strong></td>"
                    . "</tr>";
            
            $consulta2=  $this->obCon->ConsultarTabla("restaurante_pedidos_items", " WHERE idPedido='$idPedido'");
            
            while($DatosItems=$this->obCon->FetchArray($consulta2)){
                $htmlItems.="<tr><td>$DatosItems[NombreProducto]<br>$DatosItems[Observaciones]</td><td>$DatosItems[Cantidad]</td><td>$DatosItems[Total]</td>";
                //$htmlItems.="<td><a href='$myPage?idDomicilio=$idDomicilio&idDepartamento=$idDepartamento&idDel=$DatosItems[ID]'>X</td>";
                $htmlItems.="</tr>";
                $Subtotal=$Subtotal+$DatosItems["Subtotal"];
                $IVA=$IVA+$DatosItems["IVA"];
                $Total=$Total+$DatosItems["Total"];
            }
        
        $this->css->FilaTabla(16);
        $this->css->ColTabla(number_format($Subtotal), 1);
        $this->css->ColTabla(number_format($IVA), 1);
        $this->css->ColTabla(number_format($Total), 2);
        $this->css->CierraFilaTabla();
        print($htmlItems);
        $this->css->CerrarTabla();
        $this->css->CerrarCuadroDeDialogo();
        
        $this->css->CrearCuadroDeDialogo("DialBuscarItemDomicilio", "Buscar un producto:");
        $this->css->CrearForm2("FrmBuscar", $myPage, "post", "_self");
        $this->css->CrearInputText("idDomicilio", "hidden", "", $idDomicilio, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("idDepartamento", "hidden", "", $idDepartamento, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("TxtBusqueda", "text", "", "", "Buscar", "", "", "", 200, 30, 0, 1);
        print("<br>");
        $this->css->CrearBoton("BtnBuscar", "Buscar");
        
        $this->css->CerrarForm();
        $this->css->CerrarCuadroDeDialogo();
    }
    
    //Dibujo el area de facturacion de un pedido
    //
    public function DibujeAreaFacturacionRestaurante($idPedido,$myPage,$Vector) {
        $this->css=new CssIni("");
        /////////////////////////////////////Se muestra el Cuadro con los valores de la preventa actual
        $Domicilio=0;
        if(isset($Vector["Domicilio"])){
            $Domicilio=1;
        }
    //$obVenta=new ProcesoVenta($idUser);
    $DatosPedido=$this->obCon->DevuelveValores("restaurante_pedidos", "ID", $idPedido);
    $Subtotal=$this->obCon->SumeColumna("restaurante_pedidos_items","Subtotal", "idPedido",$idPedido);
    $IVA=$this->obCon->SumeColumna("restaurante_pedidos_items","IVA", "idPedido",$idPedido);
    $SaldoFavor=0;
    $Total=$Subtotal+$IVA;
    $GranTotal=$Total;
    $this->css->CrearForm2("FrmGuarda",$myPage,"post","_self");
    $this->css->CrearInputText("idPedido","hidden","",$idPedido,"","","","",150,30,0,0);
    $this->css->CrearInputText("TxtAnticipo","hidden","",0,"","","","",150,30,0,0);
    $this->css->CrearInputText("TxtDomicilio","hidden","",$Domicilio,"","","","",150,30,0,0);
    $this->css->ColTablaInputText("TxtTotalH","hidden",$Total,"","","","","",150,30,0,0);
    $this->css->ColTablaInputText("TxtCuentaDestino","hidden",11051001,"","","","","",150,30,0,0);
    $this->css->ColTablaInputText("TxtGranTotalH","hidden",$GranTotal,"","","","","",150,30,0,0);
    $this->css->CrearTabla();
    $this->css->FilaTabla(14);
    $this->css->ColTabla("Esta Venta:",3);
    $this->css->CierraFilaTabla();
    $this->css->FilaTabla(18);
    $this->css->ColTabla("SUBTOTAL:",1);
    $this->css->ColTabla(number_format($Subtotal),2);
    $this->css->CierraFilaTabla();
    $this->css->FilaTabla(18);
    $this->css->ColTabla("IMPUESTOS:",1);
    $this->css->ColTabla(number_format($IVA),2);
    $this->css->CierraFilaTabla();
    
    $this->css->FilaTabla(40);
    $this->css->ColTabla("TOTAL:",1);
    $this->css->ColTabla(number_format($Total),2);
    $this->css->CierraFilaTabla();
    
    $this->css->FilaTabla(18);
    $this->css->ColTabla("PAGA:",1);
    $Visible=0;
    print("<td>");
    //$css->ColTablaInputText("TxtPaga","number","","","Paga","","onkeyup","CalculeDevuelta()",150,30,0,0); 
    $this->css->CrearInputNumber("TxtPaga","number","Efectivo: <br>",round($Total),"Efectivo","","onkeyup","CalculeDevuelta()",150,30,0,1,"","",1);
    print("<strong>+</strong><image name='imgHidde' id='imgHidde' src='../images/hidde.png' onclick=MuestraOculta('DivOtrasOpcionesPago');>");
    $this->css->CrearDiv("DivOtrasOpcionesPago", "", "left", $Visible, 1);
    //print("<br>");
    $this->css->CrearInputNumber("TxtPagaTarjeta","number","Tarjeta: <br>",0,"Tarjeta","","onkeyup","CalculeDevuelta()",150,30,0,0,0,"",1);
    
    $VectorSelect["Nombre"]="CmbIdTarjeta";
    $VectorSelect["Evento"]="";
    $VectorSelect["Funcion"]="";
    $VectorSelect["Required"]=0;
    $this->css->CrearSelect2($VectorSelect);
    
        $sql="SELECT * FROM tarjetas_forma_pago";
        $Consulta=$this->obCon->Query($sql);
        //$css->CrearOptionSelect("", "Seleccione una tarjeta" , 0);
        while($DatosCuenta=$this->obCon->FetchArray($Consulta)){
                        
            $this->css->CrearOptionSelect("$DatosCuenta[ID]", "$DatosCuenta[Tipo] / $DatosCuenta[Nombre]" , 0);
           }
    $this->css->CerrarSelect();
    print("<br>");
    
    $this->css->CrearInputNumber("TxtPagaCheque","number","Cheque: <br>",0,"Cheque","","onkeyup","CalculeDevuelta()",150,30,0,0,0,"",1);
    print("<br>");
    $this->css->CrearInputNumber("TxtPagaOtros","number","Otros: <br>",0,"Otros","","onkeyup","CalculeDevuelta()",150,30,0,0,0,"",1);
    $this->css->CerrarDiv();
    print("</td>");
    print("<td>");
    
    print("<strong>+ Opciones </strong><image name='imgHidde' id='imgHidde' src='../images/hidde.png' onclick=MuestraOculta('DivOtrasOpciones');>");
    $this->css->CrearDiv("DivOtrasOpciones", "", "center", $Visible, 1);
    
    $VarSelect["Ancho"]="200";
    $VarSelect["PlaceHolder"]="Colaborador";
    $VarSelect["Title"]="";
    $this->css->CrearSelectChosen("TxtidColaborador", $VarSelect);
    
        $sql="SELECT Nombre, Identificacion FROM colaboradores";
        $Consulta=$this->obCon->Query($sql);
        $this->css->CrearOptionSelect("", "Colaborador: " , 0);
        while($DatosColaborador=$this->obCon->FetchArray($Consulta)){
            
               $this->css->CrearOptionSelect("$DatosColaborador[Identificacion]", " $DatosColaborador[Nombre] $DatosColaborador[Identificacion]" , 0);
           }
    $this->css->CerrarSelect();
    
    
    $VarSelect["Ancho"]="200";
    $VarSelect["PlaceHolder"]="Busque un Cliente";
    $VarSelect["Title"]="";
    $this->css->CrearSelectChosen("TxtCliente", $VarSelect);
    
        $sql="SELECT * FROM clientes";
        $Consulta=$this->obCon->Query($sql);
        while($DatosCliente=$this->obCon->FetchArray($Consulta)){
            $sel=0;
            if($DatosPedido["idCliente"]==$DatosCliente["idClientes"]){
               $sel=1; 
            }
            $this->css->CrearOptionSelect("$DatosCliente[idClientes]", "$DatosCliente[Num_Identificacion] / $DatosCliente[RazonSocial] / $DatosCliente[Telefono]" , $sel);
           }
           
    $this->css->CerrarSelect();
    
    $VarSelect["Ancho"]="200";
    $VarSelect["PlaceHolder"]="Forma de Pago";
    $VarSelect["Title"]="";
    $this->css->CrearSelectChosen("TxtTipoPago", $VarSelect);
    
        $sql="SELECT * FROM repuestas_forma_pago";
        $Consulta=$this->obCon->Query($sql);
        while($DatosTipoPago=$this->obCon->FetchArray($Consulta)){
            
               $this->css->CrearOptionSelect("$DatosTipoPago[DiasCartera]", " $DatosTipoPago[Etiqueta]" , 0);
           }
    $this->css->CerrarSelect();
    
    print("<br>");
    $this->css->CrearTextArea("TxtObservacionesFactura","","","Observaciones Factura","black","","",200,60,0,0);
    
    $this->css->CerrarDiv();
    print("</td>");
    
    $this->css->CierraFilaTabla();
    $this->css->FilaTabla(18);
    $this->css->ColTabla("DEVOLVER:",1);
    $this->css->ColTablaInputText("TxtDevuelta","text",0,"","Devuelta","","","",150,50,1,0);
    print("<td>");
    
        
    $VectorBoton["Fut"]=0;
    $this->css->CrearBotonEvento("BtnGuardarVenta","Guardar",1,"onclick","EnviaFormVentasRapidas()","naranja",$VectorBoton);
    print("</td>");
    //$css->ColTablaBoton("BtnGuardar","Guardar");
    $this->css->CierraFilaTabla();
    $this->css->CerrarTabla(); 
    $this->css->CerrarForm();
    }
    
    
    //Crear Area de visualizacion de Domicilios
    public function DialVerDomicilios($Vector) {
        $this->css=new CssIni("");
        $Titulo="Ver Domicilios";
        $Nombre="ImgShowDomicilios";
        $RutaImage="../images/domicilio.png";
        $javascript="";
        $VectorBim["f"]=0;
        $target="#DialVerDomicilio";
        $this->css->CrearBotonImagen($Titulo,$Nombre,$target,$RutaImage,"",80,80,"fixed","right:10px;top:50",$VectorBim);
        $this->css->CrearCuadroDeDialogoAmplio("DialVerDomicilio", "Ver Domicilios");
        
        print("<div id='DivDomicilios'>");
        print("</div>");
        //$css->CrearDiv("DivDomicilios", "", "center",1,1);
        //$css->CerrarDiv();//Cerramos contenedor Secundario
        $this->css->CerrarCuadroDeDialogoAmplio();
        
    }
    //Encabezado Cotizacion
    public function PDF_Encabezado_Cotizacion($idCotizacion) {
        $DatosCotizacion=$this->obCon->DevuelveValores("cotizacionesv5", "ID", $idCotizacion);
        $Usuarios_idUsuarios=$DatosCotizacion["Usuarios_idUsuarios"];
        $DatosUsuario=$this->obCon->DevuelveValores("usuarios","idUsuarios",$Usuarios_idUsuarios);
        $nombreUsuario=$DatosUsuario["Nombre"];
        $ApellidoUsuario=$DatosUsuario["Apellido"];
        $DatosEmpresa=$this->obCon->DevuelveValores("empresapro","idEmpresaPro",1);
        $Vendedor=$nombreUsuario." ".$ApellidoUsuario;
        $DatosCliente=$this->obCon->DevuelveValores("clientes","idClientes",$DatosCotizacion["Clientes_idClientes"]);
    
$tbl = <<<EOD
      
<table cellpadding="1" border="1">
    <tr>
        <td><strong>Cliente:</strong></td>
        <td colspan="3">$DatosCliente[RazonSocial]</td>
        
    </tr>
    <tr>
    	<td><strong>NIT:</strong></td>
        <td colspan="3">$DatosCliente[Num_Identificacion] - $DatosCliente[DV]</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Dirección:</strong></td>
        <td><strong>Ciudad:</strong></td>
        <td><strong>Teléfono:</strong></td>
    </tr>
    <tr>
        <td colspan="2">$DatosCliente[Direccion]</td>
        <td>$DatosCliente[Ciudad]</td>
        <td>$DatosCliente[Telefono]</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Fecha: </strong></td>
        <td colspan="2">$DatosCotizacion[Fecha]</td>
    </tr>
    
</table>       
EOD;

$this->PDF->MultiCell(92, 25, $tbl, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');

$tbl = <<<EOD
      
<table cellpadding="1" border="1">
    <tr>
        <td colspan="3"><strong>General:</strong></td>
        
        
    </tr>
    <tr>
    	<td colspan="3" height="36">$DatosEmpresa[DatosBancarios]</td>
        
    </tr>
    <tr>
        <td colspan="3"><strong>Vendedor:</strong> $Vendedor</td>
        
    </tr>
    
    
</table>       
EOD;

$this->PDF->MultiCell(92, 25, $tbl, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');
        
    }
    
    //Armar HTML de los items de la cotizacion
    
    public function ArmeHTMLItemsCotizacion($idCotizacion) {
        
        $html = ' 
        <table cellspacing="1" cellpadding="2" border="0">
            <tr>
                
                <td align="center" style="border-bottom: 2px solid #ddd;"><strong>Referencia</strong></td>
                <td align="center" colspan="5" style="border-bottom: 2px solid #ddd;"><strong>Producto o Servicio</strong></td>
                <td align="center" style="border-bottom: 2px solid #ddd;"><strong>Precio Unitario</strong></td> 
                <td align="center" style="border-bottom: 2px solid #ddd;"><strong>Cantidad</strong></td>
                <td align="center" style="border-bottom: 2px solid #ddd;"><strong>Valor Total</strong></td> 
            </tr>';

        $sql="SELECT * FROM cot_itemscotizaciones WHERE NumCotizacion='$idCotizacion'";
        $Consulta=$this->obCon->Query($sql);
        $h=1;  
        $SubtotalFinal=0;
        $IVAFinal=0;
        $TotalFinal=0;
        $i=0;
        $TotalSistema=0;
        while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
            $i++;
            $SubtotalFinal=$SubtotalFinal+$DatosItemFactura["Subtotal"];
            $IVAFinal=$IVAFinal+$DatosItemFactura["IVA"];
            $ValorUnitario=  number_format($DatosItemFactura["ValorUnitario"]);
            $SubTotalItem=  number_format($DatosItemFactura["Subtotal"]);
            $Multiplicador=$DatosItemFactura["Cantidad"];
            
    if($DatosItemFactura["Multiplicador"]>1){
        $Multiplicador="$DatosItemFactura[Cantidad] X $DatosItemFactura[Multiplicador]";
    }
    if($h==0){
        $Back="#ecfcfc";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    if($DatosItemFactura["TablaOrigen"]<>'sistemas'){
        if($DatosItemFactura["Descripcion"]=="<br>"){
            $html.= <<<EOD
                <hr>
            <tr>

                <td align="center" colspan="7" style="border-bottom: 1px solid #ddd;background-color: $Back;">
                        </td>

            </tr>

EOD;
        }else{
            
        
    $html.= <<<EOD
    
<tr>
    
    <td align="left" style="border-bottom: 1px solid #ddd;background-color: $Back;">$DatosItemFactura[Referencia]</td>
    <td align="left" colspan="5" style="border-bottom: 1px solid #ddd;background-color: $Back;">$DatosItemFactura[Descripcion]</td>
    <td align="right" style="border-bottom: 1px solid #ddd;background-color: $Back;">$ValorUnitario</td>
    <td align="center" style="border-bottom: 1px solid #ddd;background-color: $Back;">$Multiplicador</td>
    <td align="right" style="border-bottom: 1px solid #ddd;background-color: $Back;">$SubTotalItem</td>
</tr>
          
EOD;
    }
    }else{
        $Back="white";
        $h=1;
        $idSistema=$DatosItemFactura["Referencia"];
        $DatosSistema=$this->obCon->DevuelveValores("sistemas", "ID", $idSistema);
        $Cantidad=$DatosItemFactura["Cantidad"];
        $TotalSistema= number_format($this->obCon->Sume("vista_sistemas", "PrecioVenta", "WHERE idSistema='$idSistema'")*$Cantidad);
        $TotalSistema="";  //Se quita cuando quiera ver los precios
        if($DatosItemFactura["Multiplicador"]>1){
            $Cantidad=$DatosItemFactura["Cantidad"]." X ".$DatosItemFactura["Multiplicador"];
        }
        $html.= <<<EOD
    <hr>
<tr>
    
    <td align="center" colspan="7" style="border-bottom: 1px solid #ddd;background-color: $Back;">
        <strong><h2>$Cantidad $DatosItemFactura[Descripcion] $TotalSistema</h2></strong><br><br>
        <div style="font-size:13px">$DatosSistema[Observaciones]</div><br><br>Compuesto por: <br></td>
    
</tr>
          
EOD;
    }
    }

    $html.= "</table>";
    return($html);
    }
    
    //Arme html de los totales de la cotizacion
    
    public function ArmeHTMLTotalesCotizacion($idCotizacion) {
        $DatosCotizacion= $this->obCon->DevuelveValores("cotizacionesv5", "ID", $idCotizacion);
        $Observaciones= $this->obCon->QuitarAcentos($DatosCotizacion["Observaciones"]);
        $sql="SELECT SUM(Subtotal) as Subtotal,SUM(ValorDescuento) as ValorDescuento, SUM(IVA) as IVA, SUM(Total) as Total FROM cot_itemscotizaciones "
                . " WHERE NumCotizacion='$idCotizacion'";
        $Datos=$this->obCon->Query($sql);
        $TotalesCotizacion= $this->obCon->FetchArray($Datos);
        $Subtotal= number_format($TotalesCotizacion["Subtotal"]);
        $IVA= number_format($TotalesCotizacion["IVA"]);
        $Total= number_format($TotalesCotizacion["Total"]);
        if($TotalesCotizacion["ValorDescuento"]>0){
            $ValorDescuento= number_format($TotalesCotizacion["ValorDescuento"]);
            $Observaciones.=" <br>Descuento otorgado: $<strong>$ValorDescuento</strong>";
        }
        $html = <<<EOD
        
<table  cellpadding="2" border="1">
    <tr>
        <td rowspan="5" colspan="3" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>Observaciones:</strong> $Observaciones</td> 
        
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>SUBTOTAL:</h3></td>
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>$ $Subtotal</h3></td>
    </tr>
    <tr>
        
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>IVA:</h3></td>
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>$ $IVA</h3></td>
    </tr>
    <tr>
        
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>TOTAL:</h3></td>
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><h3>$ $Total</h3><br><br><br></td>
    </tr>
    
     
</table>
<table  cellpadding="2" border="1">
  <tr>
        <td height="35" align="left" style="border-bottom: 1px solid #ddd;background-color: white;">REALIZA:</td>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: white;">APRUEBA:</td>
  </tr>              
</table>                
        
EOD;
        return($html);
    }
    
    //Crear un PDF de una cotizacion
    public function PDF_Cotizacion($idCotizacion,$Vector) {
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'ISO 8859-1', false);
        //$pdf->GetY();
        $DatosCotizacion= $this->obCon->DevuelveValores("cotizacionesv5", "ID", $idCotizacion);
        $NumeracionDocumento="COTIZACION No. $idCotizacion";
        $this->PDF_Ini("Cotizacion_$idCotizacion", 8, "");
        
        $this->PDF_Encabezado($DatosCotizacion["Fecha"],1, 1, "",$NumeracionDocumento);
        $this->PDF_Encabezado_Cotizacion($idCotizacion);
        $html= $this->ArmeHTMLItemsCotizacion($idCotizacion);
        //print($html);
        
        $Position=$this->PDF->SetY(67);
        $this->PDF_Write($html);
        
        $Position=$this->PDF->GetY();
        if($Position>253){
          $this->PDF_Add();
        }
        
        $html= $this->ArmeHTMLTotalesCotizacion($idCotizacion);
        $Position=$this->PDF->SetY(254);
        $this->PDF_Write($html);
        
       // $this->PDF->MultiCell(184, 30, $html, 1, 'L', 1, 0, '', '254', true,0, true, true, 10, 'M');
        
        $Datos=$this->obCon->ConsultarTabla("cotizaciones_anexos", " WHERE NumCotizacion='$idCotizacion'");
        $this->PDF->SetMargins(20, 20, 30);
        
        $this->PDF->SetHeaderMargin(20);
        
        while ($DatosAnexos=$this->obCon->FetchArray($Datos)){
            $this->PDF_Add();
            $this->PDF_Write($DatosAnexos["Anexo"]);
        }
        $this->PDF_Output("Cotizacion_$idCotizacion");
    }
    
    //Encabezado de las Facturas
    
    public function PDF_Encabezado_Facturas($idFactura) {
        $DatosFactura=$this->obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
        $DatosCliente=$this->obCon->DevuelveValores("clientes", "idClientes", $DatosFactura["Clientes_idClientes"]);
        $DatosCentroCostos=$this->obCon->DevuelveValores("centrocosto","ID",$DatosFactura["CentroCosto"]);
        $DatosEmpresaPro=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro", $DatosCentroCostos["EmpresaPro"]);
        
        $DatosResolucion=$this->obCon->DevuelveValores("empresapro_resoluciones_facturacion","ID",$DatosFactura["idResolucion"]);
        $DatosUsuario=$this->obCon->DevuelveValores("usuarios", "idUsuarios", $DatosFactura["Usuarios_idUsuarios"]);
        $Vendedor=$DatosUsuario["Nombre"]." ".$DatosUsuario["Apellido"];
        $tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td><strong>Cliente:</strong></td>
        <td colspan="3">$DatosCliente[RazonSocial]</td>
        
    </tr>
    <tr>
    	<td><strong>NIT:</strong></td>
        <td colspan="3">$DatosCliente[Num_Identificacion] - $DatosCliente[DV]</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Dirección:</strong></td>
        <td><strong>Ciudad:</strong></td>
        <td><strong>Teléfono:</strong></td>
    </tr>
    <tr>
        <td colspan="2">$DatosCliente[Direccion]</td>
        <td>$DatosCliente[Ciudad]</td>
        <td>$DatosCliente[Telefono]</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Fecha de Facturación:</strong></td>
        <td colspan="2"><strong>Hora:</strong></td>
    </tr>
    <tr>
        <td colspan="2">$DatosFactura[Fecha]</td>
        <td colspan="2">$DatosFactura[Hora]</td>
        
    </tr>
</table>
        
EOD;


$this->PDF->MultiCell(93, 25, $tbl, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');


////Informacion legal y resolucion DIAN
////
////

$tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td height="53" align="center" >$DatosEmpresaPro[ResolucionDian], RES DIAN: $DatosResolucion[NumResolucion] del $DatosResolucion[Fecha]
             FACTURA AUT. $DatosResolucion[Prefijo]-$DatosResolucion[Desde] A $DatosResolucion[Prefijo]-$DatosResolucion[Hasta] Autoriza impresion en: $DatosResolucion[Factura]</td> 
    </tr>
     
</table>
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="center" ><strong>Vendedor</strong></td>
        <td align="center" ><strong>Forma de Pago</strong></td>
    </tr>
    <tr>
        <td align="center" >$Vendedor</td>
        <td align="center" >$DatosFactura[FormaPago]</td>
    </tr>
     
</table>
<br>  <br><br><br>      
EOD;

$this->PDF->MultiCell(93, 25, $tbl, 0, 'R', 1, 0, '', '', true,0, true, true, 10, 'M');

    return $DatosEmpresaPro;
    }
    
    //Arme HTML de los Items de una Factura
    
    public function HTML_Items_Factura($idFactura) {
        $tbl = <<<EOD
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td align="center" ><strong>Referencia</strong></td>
        <td align="center" colspan="3"><strong>Producto o Servicio</strong></td>
        <td align="center" ><strong>Precio Unitario</strong></td>
        <td align="center" ><strong>Cantidad</strong></td>
        <td align="center" ><strong>Valor Total</strong></td>
    </tr>
    
         
EOD;

$sql="SELECT fi.Dias, fi.Referencia, fi.Nombre, fi.ValorUnitarioItem, fi.Cantidad, fi.SubtotalItem"
        . " FROM facturas_items fi WHERE fi.idFactura='$idFactura'";
$Consulta= $this->obCon->Query($sql);
$h=1;  

while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    $ValorUnitario=  number_format($DatosItemFactura["ValorUnitarioItem"]);
    $SubTotalItem=  number_format($DatosItemFactura["SubtotalItem"]);
    $Multiplicador=$DatosItemFactura["Cantidad"];
    
    if($DatosItemFactura["Dias"]>1){
        $Multiplicador="$DatosItemFactura[Cantidad] X $DatosItemFactura[Dias]";
    }
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= <<<EOD
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: $Back;">$DatosItemFactura[Referencia]</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: $Back;">$DatosItemFactura[Nombre]</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: $Back;">$ValorUnitario</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: $Back;">$Multiplicador</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: $Back;">$SubTotalItem</td>
    </tr>
    
     
    
        
EOD;
    
}

$tbl .= <<<EOD
        </table>
EOD;

        return($tbl);

    }
    
    //HTML Totales Factura
    
    public function HTML_Totales_Factura($idFactura,$ObservacionesFactura,$ObservacionesLegales) {
        $sql="SELECT SUM(ValorOtrosImpuestos) as ValorOtrosImpuestos,SUM(SubtotalItem) as Subtotal, SUM(IVAItem) as IVA, SUM(TotalItem) as Total, PorcentajeIVA FROM facturas_items "
                . " WHERE idFactura='$idFactura' GROUP BY PorcentajeIVA";
        $Consulta=$this->obCon->Query($sql);
        $SubtotalFactura=0;
        $TotalFactura=0;
        $TotalIVAFactura=0;
        $OtrosImpuestos=0;
        while($TotalesFactura= $this->obCon->FetchArray($Consulta)){
            
            $OtrosImpuestos=$OtrosImpuestos+$TotalesFactura["ValorOtrosImpuestos"];
            $SubtotalFactura=$SubtotalFactura+$TotalesFactura["Subtotal"];
            $TotalFactura=$TotalFactura+$TotalesFactura["Total"];
            $TotalIVAFactura=$TotalIVAFactura+$TotalesFactura["IVA"];
            $PorcentajeIVA=$TotalesFactura["PorcentajeIVA"];
            
            $TiposIVA[$PorcentajeIVA]=$TotalesFactura["PorcentajeIVA"];
            $IVA[$PorcentajeIVA]["Valor"]=$TotalesFactura["IVA"];
            $Bases[$PorcentajeIVA]["Valor"]=$TotalesFactura["Subtotal"];
        }
        

    $tbl = '
        <table cellspacing="1" cellpadding="2" border="1">
        <tr>
            <td height="25" width="435" style="border-bottom: 1px solid #ddd;background-color: white;">Observaciones: '.$ObservacionesFactura.'</td> 

            
            <td align="rigth" width="217" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>SUBTOTAL: $ '.number_format($SubtotalFactura).'</strong></td>
        </tr>
        </table> 
        ';
        
        $NumIvas=count($TiposIVA);
        
            $ReferenciaIVA="TOTAL IVA ";
            $tbl.='<table cellspacing="1" cellpadding="2" border="1">'
                . ' <tr>';
                      
            
            foreach($TiposIVA as $PorcentajeIVA){
                
                if(isset($Bases[$PorcentajeIVA]["Valor"])){
                    $tbl.='<td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>Base '.$PorcentajeIVA.': $ '.number_format($Bases[$PorcentajeIVA]["Valor"]).'</strong></td>';

                }
                if(isset($IVA[$PorcentajeIVA]["Valor"])){

                   $tbl.='<td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>IVA '.$PorcentajeIVA.': $ '.number_format($IVA[$PorcentajeIVA]["Valor"]).'</strong></td>';

                }
                
            }
            if($OtrosImpuestos>0){
                $tbl.='<td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>Impoconsumo: $ '.number_format($OtrosImpuestos).'</strong></td>';

            }
        
        $tbl.='</tr></table>';
    
    
    $tbl.= '
        <table cellspacing="1" cellpadding="2" border="1">
        <tr>
            <td height="25" width="435" style="border-bottom: 1px solid #ddd;background-color: white;">'.$ObservacionesLegales.'</td> 
            <td align="rigth" width="217" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>Total Impuestos: $ '.number_format($TotalIVAFactura+$OtrosImpuestos).'</strong></td>
        </tr>
        </table> 
        ';
    $tbl.='<table cellspacing="1" cellpadding="2" border="1"> <tr>
        <td  height="50" align="center" style="border-bottom: 1px solid #ddd;background-color: white;"><br/><br/><br/><br/><br/>Firma Autorizada</td> 
        <td  height="50" align="center" style="border-bottom: 1px solid #ddd;background-color: white;"><br/><br/><br/><br/><br/>Firma Recibido</td> 
        
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTAL: $ '.number_format($TotalFactura+$OtrosImpuestos).'</strong></td>
    </tr>
     
</table>';
    
    return $tbl;
    }
    
    //Crear un PDF de una Factura
    public function PDF_Factura($idFactura,$TipoFactura,$Vector) {
        $VistaFactura=1;
        $DatosFactura=$this->obCon->DevuelveValores("facturas", "idFacturas", $idFactura);
        $CodigoFactura="$DatosFactura[Prefijo] - $DatosFactura[NumeroFactura]";
        $Documento="FACTURA DE VENTA No. $CodigoFactura<BR>$TipoFactura";
        
        $this->PDF_Ini("Factura_$CodigoFactura", 8, "");
        $idFormato=2;
        $this->PDF_Encabezado($DatosFactura["Fecha"],1, $idFormato, "",$Documento);
        $DatosEmpresaPro=$this->PDF_Encabezado_Facturas($idFactura);
        
        $html= $this->HTML_Items_Factura($idFactura);
        $Position=$this->PDF->SetY(80);
        $this->PDF_Write($html);
        
        $Position=$this->PDF->GetY();
        if($Position>246){
          $this->PDF_Add();
        }
        
        $html= $this->HTML_Totales_Factura($idFactura, $DatosFactura["ObservacionesFact"], $DatosEmpresaPro["ObservacionesLegales"]);
        if($VistaFactura==1)
        $this->PDF->SetY(246);
        $this->PDF_Write($html);
        if($VistaFactura==3){
            $this->PDF_Write("<br>");
            $html=$this->FirmaDocumentos();
            $this->PDF_Write($html);
        }
        $this->PDF_Output("Factura_$CodigoFactura");
    }
    
    //dibuje agregar movimiento contable libre
    
    public function DibujeAgregaMovimientoContable($myPage,$Visible,$idComprobante) {
        $this->css=new CssIni("");
        $this->css->CrearForm2("FrmAgregaItemE", $myPage, "post", "_self");
        $MultiTercero="";
        if($myPage=='ComprobantesIngreso.php'){
            $DatosComprobante=$this->obCon->DevuelveValores("comprobantes_ingreso", "ID", $idComprobante);
            $Tercero=$DatosComprobante["Tercero"];
        }else{
            $DatosComprobante=$this->obCon->DevuelveValores("egresos", "idEgresos", $idComprobante);
            $Tercero=$DatosComprobante["NIT"];
            $MultiTercero=1;  //Habilitar multiples terceros en el egreso libre para alturas
        }
        $this->css->CrearDiv("DivAgregaMov", "", "center", $Visible, 1);
        $this->css->CrearTabla();
        $this->css->FilaTabla(16);
        $this->css->ColTabla("<strong>Comprobante:</strong>", 1);
        print("<td>");
           $this->css->CrearInputText("idComprobante", "text", "", $idComprobante, "idComprobante", "black", "", "", 100, 30, 1, 1);
        print("</td>");  
        $this->css->CierraFilaTabla();   
        $this->css->FilaTabla(16);

            $this->css->ColTabla("<strong>Centro de Costo</strong>", 1);
            
            $this->css->ColTabla("<strong>Tercero</strong>", 1);
            $this->css->ColTabla("<strong>Cuenta Destino</strong>", 1);

        $this->css->CierraFilaTabla();    
        $this->css->FilaTabla(16);


            print("<td>");

                $this->css->CrearSelect("CmbCentroCosto"," Centro de Costos:<br>","black","",1);
                //$this->css->CrearOptionSelect("","Seleccionar Centro de Costos",0);

                $Consulta = $this->obCon->ConsultarTabla("centrocosto","");
                while($CentroCosto=$this->obCon->FetchArray($Consulta)){
                                $this->css->CrearOptionSelect($CentroCosto['ID'],$CentroCosto['Nombre'],0);							
                }
                $this->css->CerrarSelect();
                 print("<br>");
                $this->css->CrearSelect("idSucursal"," Sucursal:<br>","black","",1);
                //$this->css->CrearOptionSelect("","Seleccionar Sucursal",0);
               
                $Consulta = $this->obCon->ConsultarTabla("empresa_pro_sucursales","");
                while($CentroCosto=$this->obCon->FetchArray($Consulta)){
                                $this->css->CrearOptionSelect($CentroCosto['ID'],$CentroCosto['Nombre'],0);							
                }
                $this->css->CerrarSelect();
            print("</td>");
            print("<td>");
            //$this->css->CrearInputText("CmbTerceroItem", "text", "", $Tercero, "", "","" , "", 200, 30, 1, 1);
               
            $VarSelect["Ancho"]="200";
                $VarSelect["PlaceHolder"]="Seleccione el tercero";
                $this->css->CrearSelectChosen("CmbTerceroItem", $VarSelect);
                $this->css->CrearOptionSelect("", "Seleccione un tercero" , 0);
                $sql="SELECT * FROM proveedores";
                $Consulta=$this->obCon->Query($sql);

                   while($DatosProveedores=$this->obCon->FetchArray($Consulta)){
                       $Sel=0;

                       $this->css->CrearOptionSelect($DatosProveedores["Num_Identificacion"], "$DatosProveedores[RazonSocial] $DatosProveedores[Num_Identificacion]" , $Sel);
                   }
                   $sql="SELECT * FROM clientes";
                $Consulta=$this->obCon->Query($sql);

                   while($DatosProveedores=$this->obCon->FetchArray($Consulta)){
                       $Sel=0;

                       $this->css->CrearOptionSelect($DatosProveedores["Num_Identificacion"], "$DatosProveedores[RazonSocial] $DatosProveedores[Num_Identificacion]" , $Sel);
                   }
                $this->css->CerrarSelect();
             
            print("</td>");
            print("<td>");
                $VarSelect["Ancho"]="200";
                $VarSelect["PlaceHolder"]="Seleccione la cuenta destino";
                $this->css->CrearSelectChosen("CmbCuentaDestino", $VarSelect);
                $this->css->CrearOptionSelect("", "Seleccione la cuenta destino" , 0);

                
                //En subcuentas se debera cargar todo el PUC
                $sql="SELECT * FROM subcuentas";
                $Consulta=$this->obCon->Query($sql);

                   while($DatosProveedores=$this->obCon->FetchArray($Consulta)){
                       $Sel=0;
                       $NombreCuenta=str_replace(" ","_",$DatosProveedores['Nombre']);
                       $this->css->CrearOptionSelect($DatosProveedores['PUC'].';'.$NombreCuenta, "$DatosProveedores[PUC] $DatosProveedores[Nombre]" , $Sel);
                   }

                $this->css->CerrarSelect();
            print("</td>");
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(16);
            print("<td>");
            $this->css->CrearInputNumber("TxtValorItem", "number", "<strong>Valor:</strong><br>", "", "Valor", "black", "", "", 220, 30, 0, 1, 1, "", 1);
            print("<br>");

            $this->css->CrearSelect("CmbDebitoCredito", "");
                $this->css->CrearOptionSelect("D", "Debito", 1);
                $this->css->CrearOptionSelect("C", "Credito", 0);
            $this->css->CerrarSelect();
            print("</td>");


            print("<td>");
            $this->css->CrearTextArea("TxtConceptoMovimiento","<strong>Concepto:</strong><br>","","Escriba el Concepto","black","","",300,100,0,1);
            print("</td>");
            print("<td>");
            $this->css->CrearInputText("TxtNumFactura","text",'Numero del Documento soporte:<br>',"","Numero del documento","black","","",300,30,0,1);
            echo"<br>";
            $this->css->CrearUpload("foto");
            echo"<br>";
            echo"<br>";

            $this->css->CrearBotonVerde("BtnAgregarItemMov", "Agregar Concepto");
            print("</td>");

        $this->css->CierraFilaTabla();
        $this->css->CerrarTabla();
        
        $this->css->CerrarDiv();
        $this->css->CerrarForm();
    }
    
    //Dibuje espacio para agregar items de una cuenta X Cobrar
    public function DibujePreMovimientoCartera($myPage,$idCartera,$idComprobante,$Vector) {
        $sql="SELECT ci.ID,cii.OrigenMovimiento, cii.idOrigen,ci.Estado FROM comprobantes_ingreso_items cii INNER JOIN comprobantes_ingreso ci "
                . "ON cii.idComprobante=ci.ID WHERE cii.idOrigen='$idCartera' AND cii.OrigenMovimiento='cartera' AND ci.Estado='ABIERTO' ";
        $consulta=$this->obCon->Query($sql);
        $DatosComprobante= $this->obCon->FetchArray($consulta);
        if($DatosComprobante["Estado"]=="ABIERTO"){
            return($DatosComprobante["ID"]);
        }
        $this->css=new CssIni("");
        $DatosCartera= $this->obCon->DevuelveValores("cartera", "idCartera", $idCartera);
        $DatosFactura= $this->obCon->DevuelveValores("facturas", "idFacturas", $DatosCartera["Facturas_idFacturas"]);
        $DatosCliente= $this->obCon->DevuelveValores("clientes", "idClientes", $DatosCartera["idCliente"]);
        
        $this->css->CrearForm2("FrmAgregaMovCXC", $myPage, "post", "_self");
        $this->css->CrearInputText("idComprobante", "hidden", "", $idComprobante, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("idCartera", "hidden", "", $idCartera, "", "", "", "", "", "", "", "");
        $this->css->CrearTabla();
            $this->css->FilaTabla(16);
                $this->css->ColTabla("ID", 1);
                $this->css->ColTabla("Tercero", 1);
                $this->css->ColTabla("Factura", 1);
                $this->css->ColTabla("Total", 1);
                $this->css->ColTabla("Total Abonos", 1);
                $this->css->ColTabla("Saldo", 1);
                $this->css->ColTabla("Abonar", 1);
                $this->css->ColTabla("Agregar", 1);
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(16);
                $this->css->ColTabla($DatosCartera["idCartera"], 1);
                $this->css->ColTabla("$DatosCliente[RazonSocial] $DatosCliente[Num_Identificacion]", 1);
                $this->css->ColTabla("$DatosFactura[Prefijo]-$DatosFactura[NumeroFactura]", 1);
                $this->css->ColTabla(number_format($DatosFactura["Total"]), 1);
                $this->css->ColTabla(number_format($DatosCartera["TotalAbonos"]), 1);
                $this->css->ColTabla(number_format($DatosCartera["Saldo"]), 1);
                print("<td>");
                $this->css->CrearInputNumber("TxtMontoAbono", "number", "", $DatosCartera["Saldo"], "Digite el Abono", "", "", "", 100, 30, 0, 1, 0, $DatosCartera["Saldo"], 1);
                print("</td>");
                print("<td>");
                $this->css->CrearBoton("BtnAgregarMovCXC", "Agregar");
                print("</td>");
            $this->css->CierraFilaTabla();
        $this->css->CerrarTabla();
        $this->css->CerrarForm();
    }
    
    //Dibuje espacio para agregar items de una cuenta X Pagar
    public function DibujePreMovimientoCuentaXPagar($myPage,$idCartera,$idComprobante,$Vector) {
        $sql="SELECT * FROM cuentasxpagar WHERE ID='$idCartera'";
        $consulta=$this->obCon->Query($sql);
        $DatosCuenta= $this->obCon->FetchArray($consulta);
        if($DatosCuenta["Estado"]=="ABIERTO"){
            return($DatosCuenta["ID"]);
        }
        $this->css=new CssIni("");
               
        $this->css->CrearForm2("FrmAgregaMovCXP", $myPage, "post", "_self");
        $this->css->CrearInputText("idComprobante", "hidden", "", $idComprobante, "", "", "", "", "", "", "", "");
        $this->css->CrearInputText("idCartera", "hidden", "", $idCartera, "", "", "", "", "", "", "", "");
        $this->css->CrearTabla();
            $this->css->FilaTabla(16);
                $this->css->ColTabla("ID", 1);
                $this->css->ColTabla("Tercero", 1);
                $this->css->ColTabla("Factura", 1);
                $this->css->ColTabla("Total", 1);
                $this->css->ColTabla("Total Abonos", 1);
                $this->css->ColTabla("Saldo", 1);
                $this->css->ColTabla("Abonar", 1);
                $this->css->ColTabla("Agregar", 1);
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(16);
                $this->css->ColTabla($DatosCuenta["ID"], 1);
                $this->css->ColTabla("$DatosCuenta[RazonSocial] $DatosCuenta[idProveedor]", 1);
                $this->css->ColTabla("$DatosCuenta[DocumentoReferencia]", 1);
                $this->css->ColTabla(number_format($DatosCuenta["Total"]), 1);
                $this->css->ColTabla(number_format($DatosCuenta["Abonos"]), 1);
                $this->css->ColTabla(number_format($DatosCuenta["Saldo"]), 1);
                print("<td>");
                $this->css->CrearInputNumber("TxtMontoAbono", "number", "", $DatosCuenta["Saldo"], "Digite el Abono", "", "", "", 100, 30, 0, 1, 0, $DatosCuenta["Saldo"], 1);
                print("</td>");
                print("<td>");
                $this->css->CrearBoton("BtnAgregarMovCXP", "Agregar");
                print("</td>");
            $this->css->CierraFilaTabla();
        $this->css->CerrarTabla();
        $this->css->CerrarForm();
    }
    
    //PDF Factura de Compra
    
    public function PDF_FacturaCompra($idCompra,$Vector) {
        $DatosFactura=$this->obCon->DevuelveValores("factura_compra", "ID", $idCompra);
        $CodigoFactura="$DatosFactura[ID]";
        $Documento="FACTURA DE COMPRA No. $CodigoFactura";
        
        $this->PDF_Ini("FC_$CodigoFactura", 8, "");
        $idFormato=23;
        $this->PDF_Encabezado($DatosFactura["Fecha"],1, $idFormato, "",$Documento);
        $DatosEmpresaPro=$this->PDF_Encabezado_Factura_Compra($idCompra);
        
        $html= $this->HTML_Items_Factura_Compra($idCompra);
        $Position=$this->PDF->SetY(80);
        if($html<>''){
            
            $this->PDF_Write(utf8_encode($html));
        }
        
        
        
        $html= $this->HTML_Insumos_Factura_Compra($idCompra);
        $this->PDF_Write(utf8_encode("<br><br>".$html));
        $html= $this->HTML_Items_Devueltos_FC($idCompra);
        $this->PDF_Write(utf8_encode($html));
        $html= $this->HTML_Servicios_FC($idCompra);
        $this->PDF_Write(utf8_encode($html));
        
        $html= $this->HTML_Movimiento_Contable_FC($idCompra);
        //print($html);
        $this->PDF_Write($html);
        $this->PDF_Write("<br>");
        $html= $this->FirmaDocumentos();
        $this->PDF_Write($html);
        //$Position=$this->PDF->GetY();
        //if($Position>246){
          //$this->PDF_Add();
        //}
        
        //$html= $this->HTML_Totales_Factura($idFactura, $DatosFactura["ObservacionesFact"], $DatosEmpresaPro["ObservacionesLegales"]);
        //$this->PDF->SetY(246);
        //$this->PDF_Write($html);
        
        $this->PDF_Output("FC_$CodigoFactura");
    }
    
    public function FirmaDocumentos() {
        $html = '<table border="1" cellpadding="2" cellspacing="0" align="left">
            <tr align="left" >
                <td style="height: 100px;" >Preparado:</td>
                <td style="height: 100px;" >Revisado:</td>
                <td style="height: 100px;" >Aprobado:</td>
                <td style="height: 100px;" >Contabilizado:</td>
            </tr>
        </table>';
        return($html);
    }
    //Encabezado de las Facturas
    
    public function PDF_Encabezado_Factura_Compra($idCompra) {
        $DatosFactura=$this->obCon->DevuelveValores("factura_compra", "ID", $idCompra);
        $DatosTercero=$this->obCon->DevuelveValores("proveedores", "Num_Identificacion", $DatosFactura["Tercero"]);
        $DatosCentroCostos=$this->obCon->DevuelveValores("centrocosto","ID",$DatosFactura["idCentroCostos"]);
        $DatosEmpresaPro=$this->obCon->DevuelveValores("empresapro", "idEmpresaPro", $DatosCentroCostos["EmpresaPro"]);
        $RazonSocial=utf8_encode($DatosTercero["RazonSocial"]);
        $Direccion=utf8_encode($DatosTercero["Direccion"]);
        $DatosUsuario=$this->obCon->DevuelveValores("usuarios", "idUsuarios", $DatosFactura["idUsuario"]);
        $Comprador=$DatosUsuario["Nombre"]." ".$DatosUsuario["Apellido"];
        $tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td><strong>Tercero:</strong></td>
        <td colspan="3">$RazonSocial</td>
        
    </tr>
    <tr>
    	<td><strong>NIT:</strong></td>
        <td colspan="3">$DatosTercero[Num_Identificacion] - $DatosTercero[DV]</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Dirección:</strong></td>
        <td><strong>Ciudad:</strong></td>
        <td><strong>Teléfono:</strong></td>
    </tr>
    <tr>
        <td colspan="2">$Direccion</td>
        <td>$DatosTercero[Ciudad]</td>
        <td>$DatosTercero[Telefono]</td>
    </tr>
    <tr>
        <td colspan="4"><strong>Fecha:</strong> $DatosFactura[Fecha]</td>
        
    </tr>
    
</table>
        
EOD;


$this->PDF->MultiCell(93, 25, $tbl, 0, 'L', 1, 0, '', '', true,0, true, true, 10, 'M');


////Concepto
////
////

$tbl = <<<EOD
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td height="42" align="center" >$DatosFactura[Concepto]</td> 
    </tr>
     
</table>
<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="center" ><strong>Realiza: </strong></td>
        <td align="center" ><strong>Documento Referencia:</strong></td>
    </tr>
    <tr>
        <td align="center" >$Comprador</td>
        <td align="center" >$DatosFactura[NumeroFactura]</td>
    </tr>
     
</table>
<br>  <br><br><br>      
EOD;

$this->PDF->MultiCell(93, 25, $tbl, 0, 'R', 1, 0, '', '', true,0, true, true, 10, 'M');

    
    }
    
    //Arme HTML de los Items de una Factura
    
    public function HTML_Movimiento_Contable_FC($idCompra) {
        $tbl = <<<EOD
          <br>      
   <h3 align="CENTER">REGISTROS CONTABLES</H3>
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        
        <td align="center" ><strong>Cuenta PUC</strong></td>
        <td align="center" colspan="3"><strong>Nombre Cuenta</strong></td>
        <td align="center" ><strong>Débitos</strong></td>
        <td align="center" ><strong>Créditos</strong></td>
    </tr>
    
         
EOD;

$sql="SELECT * FROM librodiario WHERE Tipo_Documento_Intero='FacturaCompra' AND Num_Documento_Interno='$idCompra'";
$Consulta= $this->obCon->Query($sql);
$h=1;  

while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    
    $Credito= number_format($DatosItemFactura["Credito"]);
    $Debito=number_format($DatosItemFactura["Debito"]);
    $Cuenta=$DatosItemFactura["CuentaPUC"];
    $NombreCuenta=$DatosItemFactura["NombreCuenta"];
    
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= <<<EOD
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: $Back;">$Cuenta</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: $Back;">$NombreCuenta</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: $Back;">$Debito</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: $Back;">$Credito</td>
        
    </tr>
    
        
EOD;
    
}

$tbl .= <<<EOD
        </table>
EOD;

        return($tbl);

    }
    
    //HTML Totales Factura
    
    public function HTML_Totales_Factura_Compra($idFactura,$ObservacionesFactura,$ObservacionesLegales) {
        $sql="SELECT SUM(SubtotalItem) as Subtotal, SUM(IVAItem) as IVA, SUM(TotalItem) as Total, PorcentajeIVA FROM facturas_items "
                . " WHERE idFactura='$idFactura' GROUP BY PorcentajeIVA";
        $Consulta=$this->obCon->Query($sql);
        $SubtotalFactura=0;
        $TotalFactura=0;
        $TotalIVAFactura=0;
        $OtrosImpuestos=0;
        while($TotalesFactura= $this->obCon->FetchArray($Consulta)){
            $SubtotalFactura=$SubtotalFactura+$TotalesFactura["Subtotal"];
            $TotalFactura=$TotalFactura+$TotalesFactura["Total"];
            $TotalIVAFactura=$TotalIVAFactura+$TotalesFactura["IVA"];
            $PorcentajeIVA=$TotalesFactura["PorcentajeIVA"];
            //$OtrosImpuestos=$OtrosImpuestos+$TotalesFactura["OtrosImpuestos"];
            
            $TiposIVA[$PorcentajeIVA]=$TotalesFactura["PorcentajeIVA"];
            $IVA[$PorcentajeIVA]["Valor"]=$TotalesFactura["IVA"];
        }
        

    $tbl = '
        <table cellspacing="1" cellpadding="2" border="1">
        <tr>
            <td height="25" width="435" style="border-bottom: 1px solid #ddd;background-color: white;">Observaciones: '.$ObservacionesFactura.'</td> 

            
            <td align="rigth" width="217" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>SUBTOTAL: $ '.number_format($SubtotalFactura).'</strong></td>
        </tr>
        </table> 
        ';
        
        $NumIvas=count($TiposIVA);
        
        if($NumIvas>1){
            $ReferenciaIVA="TOTAL IVA ";
            $tbl.='<table cellspacing="1" cellpadding="2" border="1">'
                . ' <tr>';
            
            foreach($TiposIVA as $PorcentajeIVA){
                if($PorcentajeIVA<>'0%'){

                   $tbl.='<td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>IVA '.$PorcentajeIVA.': $ '.number_format($IVA[$PorcentajeIVA]["Valor"]).'</strong></td>';

                }  
            }
        
        $tbl.='</tr></table>';
    }else{
        $ReferenciaIVA="IVA ".$TiposIVA[$PorcentajeIVA];
    }
    
    $tbl.= '
        <table cellspacing="1" cellpadding="2" border="1">
        <tr>
            <td height="25" width="435" style="border-bottom: 1px solid #ddd;background-color: white;">'.$ObservacionesLegales.'</td> 
            <td align="rigth" width="217" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.$ReferenciaIVA.': $ '.number_format($TotalIVAFactura).'</strong></td>
        </tr>
        </table> 
        ';
    $tbl.='<table cellspacing="1" cellpadding="2" border="1"> <tr>
        <td  height="50" align="center" style="border-bottom: 1px solid #ddd;background-color: white;"><br/><br/><br/><br/><br/>Firma Autorizada</td> 
        <td  height="50" align="center" style="border-bottom: 1px solid #ddd;background-color: white;"><br/><br/><br/><br/><br/>Firma Recibido</td> 
        
        <td align="rigth" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTAL: $ '.number_format($TotalFactura).'</strong></td>
    </tr>
     
</table>';
    
    return $tbl;
    }
    //HTML Ventas discriminadas por departamentos
    public function HTML_VentasXDepartamentos($CondicionItems) {
        $html="";
        $sql="SELECT Departamento as idDepartamento, SUM(SubtotalItem) as Subtotal, SUM(IVAItem) as IVA, SUM(TotalItem) as Total, SUM(Cantidad) as Items"
        . " $CondicionItems GROUP BY Departamento";
        $Datos=$this->obCon->Query($sql);
        
        if($this->obCon->NumRows($Datos)){
            $html='<span style="color:RED;font-family:`Bookman Old Style`;font-size:12px;"><strong><em>Total de Ventas Discriminadas por Departamento:
            </em></strong></span><BR><BR>


            <table border="1" cellspacing="2" align="center" >
              <tr> 
                <th><h3>Departamento</h3></th>
                    <th><h3>Nombre</h3></th>
                    <th><h3>Total Items</h3></th>
                    <th><h3>SubTotal</h3></th>
                    <th><h3>IVA</h3></th>
                    <th><h3>Total</h3></th>
              </tr >

            </table>';
            $Subtotal=0;
            $TotalIVA=0;
            $TotalVentas=0;
            $TotalItems=0;
            $flagQuery=0;   //para indicar si hay resultados
            $i=0;
            
            while($DatosVentas= $this->obCon->FetchArray($Datos)){
                $flagQuery=1;	
                $SubtotalUser=number_format($DatosVentas["Subtotal"]);
                $IVA=number_format($DatosVentas["IVA"]);
                $Total=number_format($DatosVentas["Total"]);
                $Items=number_format($DatosVentas["Items"]);
                $DatosDepartamento=$this->obCon->DevuelveValores("prod_departamentos", "idDepartamentos", $DatosVentas["idDepartamento"]);
                $NombreDep=$DatosDepartamento["Nombre"];

                $Subtotal=$Subtotal+$DatosVentas["Subtotal"];
                $TotalIVA=$TotalIVA+$DatosVentas["IVA"];
                $TotalVentas=$TotalVentas+$DatosVentas["Total"];
                $TotalItems=$TotalItems+$DatosVentas["Items"];
                $idDepartamentos=$DatosVentas["idDepartamento"];
                $html.='<table border="1" cellpadding="2"  align="center">
                            <tr>
                                <td>'.$idDepartamentos.'</td>
                                <td>'.$NombreDep.'</td>
                                <td>'.$Items.'</td>
                                <td>'.$SubtotalUser.'</td>
                                <td>'.$IVA.'</td>
                                <td>'.$Total.'</td>
                            </tr>
                            </table>';
            }
            if($flagQuery==1){
            $TotalItems=number_format($TotalItems);
            $Subtotal=number_format($Subtotal);
            $TotalIVA=number_format($TotalIVA);
            $TotalVentas=number_format($TotalVentas);
            $html.= ' 
            <table border="1" cellspacing="2" align="center">
             <tr>
              <td align="RIGHT"><h3>SUMATORIA</h3></td>
              <td><h3>NA</h3></td>
              <td><h3>'.$TotalItems.'</h3></td>
              <td><h3>'.$Subtotal.'</h3></td>
              <td><h3>'.$TotalIVA.'</h3></td>
              <td><h3>'.$TotalVentas.'</h3></td>
             </tr>
             </table>
            ';
            }
        }
        return($html);
    }
    //HTML Ventas X Usuarios Informe admin
    
    public function HTML_VentasXUsuario($CondicionFacturas,$CondicionFecha1,$CondicionFecha3) {
        $html="";
        /*
        $sql="SELECT Usuarios_idUsuarios as IdUsuarios, FormaPago as  TipoVenta, SUM(Subtotal) as Subtotal, SUM(IVA) as IVA, 
        SUM(Total) as Total, SUM(TotalCostos) as TotalCostos"
                . "  FROM $CondicionFacturas GROUP BY Usuarios_idUsuarios, FormaPago";
        
         * 
         */
        $sql="SELECT fi.idUsuarios as IdUsuarios,f.FormaPago as TipoVenta,sum(fi.`TotalItem`) as Total,sum(fi.`IVAItem`) as IVA,sum(fi.`SubtotalItem`) as Subtotal,"
                . "sum(fi.`SubtotalCosto`) as TotalCostos, sum(fi.`ValorOtrosImpuestos`) as Bolsas, "
                . "SUM(fi.`Cantidad`) AS Items, fi.idUsuarios $CondicionFacturas "
                . " GROUP BY fi.idUsuarios,f.FormaPago";
        $Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html='<br><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total de Ventas Discriminadas por Usuarios y Tipo de Venta:
                </em></strong></span><BR>


                <table border="1" cellspacing="2" align="center" >
                  <tr> 
                    <th><h3>Usuario</h3></th>
                        <th><h3>TipoVenta</h3></th>
                        <th><h3>Total Costos</h3></th>
                        <th><h3>SubTotal</h3></th>
                        <th><h3>IVA</h3></th>
                        <th><h3>Bolsas</h3></th>
                        <th><h3>Total</h3></th>
                  </tr >

                </table>';
            $Subtotal=0;
            $TotalIVA=0;
            $TotalVentas=0;
            $TotalCostos=0;
            $TotalBolsas=0;
            $flagQuery=0;
            $i=0;
            while($DatosVentas= $this->obCon->FetchArray($Datos)){
                $flagQuery=1;
                $SubtotalUser=number_format($DatosVentas["Subtotal"]);
                $IVA=number_format($DatosVentas["IVA"]);
                $Bolsas=number_format($DatosVentas["Bolsas"]);
                $Total=number_format($DatosVentas["Total"]+$DatosVentas["Bolsas"]);
                $Costos=number_format($DatosVentas["TotalCostos"]);
                $TipoVenta=$DatosVentas["TipoVenta"];
                $Subtotal=$Subtotal+$DatosVentas["Subtotal"];
                $TotalIVA=$TotalIVA+$DatosVentas["IVA"];
                $TotalBolsas=$TotalBolsas+$DatosVentas["Bolsas"];
                $TotalVentas=$TotalVentas+$DatosVentas["Total"]+$DatosVentas["Bolsas"];
                $TotalCostos=$TotalCostos+$DatosVentas["TotalCostos"];
                $idUser=$DatosVentas["IdUsuarios"];
                $html.=' 
                    <table border="1" cellpadding="2"  align="center">
                        <tr>
                            <td>'.$idUser.'</td>
                            <td>'.$TipoVenta.'</td>
                            <td>'.$Costos.'</td>
                            <td>'.$SubtotalUser.'</td>
                            <td>'.$IVA.'</td>
                            <td>'.$Bolsas.'</td>    
                            <td>'.$Total.'</td>
                        </tr>
                    </table>
                    ';
            }
            if($flagQuery==1){
                $TotalCostos=number_format($TotalCostos);
                $Subtotal=number_format($Subtotal);
                $TotalIVA=number_format($TotalIVA);
                $TotalVentas=number_format($TotalVentas);
                $html.= '
                    <table border="1" cellspacing="2" align="center">
                        <tr>
                            <td align="RIGHT"><h3>SUMATORIA</h3></td>
                            <td><h3>NA</h3></td>
                            <td><h3>'.$TotalCostos.'</h3></td>
                            <td><h3>'.$Subtotal.'</h3></td>
                            <td><h3>'.$TotalIVA.'</h3></td>
                            <td><h3>'.number_format($TotalBolsas).'</h3></td>    
                            <td><h3>'.$TotalVentas.'</h3></td>
                        </tr>
                    </table>
                ';
            }
        }
        //Total de devoluciones
        $sql="SELECT idUsuarios as IdUsuarios, Sum(Cantidad) as Items, 
                SUM(TotalItem) as Total "
        . "  FROM facturas_items WHERE Cantidad < 0 AND $CondicionFecha1 GROUP BY idUsuarios";

        $Datos=$this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html.='<BR><BR><BR><BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total devoluciones:
                </em></strong></span><BR><BR>
                <table border="1" cellspacing="2" align="center" >
                  <tr> 
                    <th><h3>Usuario</h3></th>
                        <th><h3>Total Items</h3></th>
                        <th><h3>Total</h3></th>
                  </tr >

                </table>';
            $TotalVentas=0;
            $TotalItems=0;
            $i=0;
            while($DatosVentas= $this->obCon->FetchArray($Datos)){
                $Total=number_format($DatosVentas["Total"]);
		$Items=number_format($DatosVentas["Items"]);
		$TotalVentas=$TotalVentas+$DatosVentas["Total"];
		$TotalItems=$TotalItems+$DatosVentas["Items"];
		$idUser=$DatosVentas["IdUsuarios"];
                $html.='<table border="1"  cellpadding="2" align="center">
                            <tr>
                                <td>'.$idUser.'</td>
                                <td>'.$Items.'</td>
                                <td>'.$Total.'</td>
                            </tr>
                        </table>';
            }
            $TotalItems=number_format($TotalItems);
            $TotalVentas=number_format($TotalVentas);
            $html.='<table border="1" cellspacing="2" align="center">
                        <tr>
                            <td align="RIGHT"><h3>SUMATORIA</h3></td>
                            <td><h3>'.$TotalItems.'</h3></td>
                            <td><h3>'.$TotalVentas.'</h3></td>
                        </tr>
                    </table>';
        }
        return($html);
    }
    ///Funcion para armar html de los egresos informe admin
    public function HTML_Egresos_Admin($CondicionFecha2) {
        $html="";
        $sql="SELECT Usuario_idUsuario as IdUsuarios, SUM(Subtotal) as Subtotal, SUM(IVA) as IVA, SUM(Valor) as Total FROM egresos
	WHERE $CondicionFecha2 GROUP BY Usuario_idUsuario";	
        $Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html='<BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total Egresos:
                    </em></strong></span><BR>
                    <table border="1" cellspacing="2" align="center" >
                      <tr> 
                        <th><h3>Usuario</h3></th>
                        <th><h3>SubTotal</h3></th>
                        <th><h3>IVA</h3></th>
                        <th><h3>Total</h3></th>
                      </tr >

                    </table>';
            
            $Subtotal=0;
            $TotalIVA=0;
            $TotalVentas=0;
            $TotalItems=0;
            $i=0;
            while($DatosVentas=$this->obCon->FetchArray($Datos)){
                $SubtotalUser=number_format($DatosVentas["Subtotal"]);
                $IVA=number_format($DatosVentas["IVA"]);
                $Total=number_format($DatosVentas["Total"]);
                $Subtotal=$Subtotal+$DatosVentas["Subtotal"];
                $TotalIVA=$TotalIVA+$DatosVentas["IVA"];
                $TotalVentas=$TotalVentas+$DatosVentas["Total"];
                $idUser=$DatosVentas["IdUsuarios"];
                $html.= ' 
                    <table border="1"  cellpadding="2" align="center">
                        <tr>
                            <td>'.$idUser.'</td>
                            <td>'.$SubtotalUser.'</td>
                            <td>'.$IVA.'</td>
                            <td>'.$Total.'</td>
                        </tr>
                    </table>
                    ';
            }
            $TotalItems=number_format($TotalItems);
            $Subtotal=number_format($Subtotal);
            $TotalIVA=number_format($TotalIVA);
            $TotalVentas=number_format($TotalVentas);
	$html.=' 
            <table border="1" cellspacing="2" align="center">
                <tr>
                    <td align="RIGHT"><h3>SUMATORIA</h3></td>
                    <td><h3>'.$Subtotal.'</h3></td>
                    <td><h3>'.$TotalIVA.'</h3></td>
                    <td><h3>'.$TotalVentas.'</h3></td>
                </tr>
            </table>
            ';
        }
        return($html);
    }
    //Funcion para armar el html de los abonos en el informe de administrador
    public function HTML_Abonos_Facturas_Admin($CondicionFecha2) {
        $html="";
        $sql="SELECT FormaPago,Usuarios_idUsuarios, SUM(Valor) as Subtotal FROM facturas_abonos
	WHERE $CondicionFecha2
	GROUP BY Usuarios_idUsuarios,FormaPago";
	$Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html='<BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total Abonos Creditos:
                </em></strong></span><BR>
                <table border="1" cellspacing="2" align="center" >
                    <tr> 
                      <th><h3>Usuario</h3></th>
                      <th><h3>TipoAbono</h3></th>
                      <th><h3>Total</h3></th>
                    </tr >
                </table>';
            $TotalAbonos=0;
            while($DatosAbonos=$this->obCon->FetchArray($Datos)){
                $TotalAbonos=$TotalAbonos+$DatosAbonos["Subtotal"];
                $html.='<table border="1"  cellpadding="2" align="center">
                            <tr>
                                <td>'.$DatosAbonos["Usuarios_idUsuarios"].'</td>
                                <td>'.$DatosAbonos["FormaPago"].'</td>
                                <td>'.number_format($DatosAbonos["Subtotal"]).'</td>

                            </tr>
                        </table>';
            }
            $html.=' 
            <table border="1" cellspacing="2" align="center">
                <tr>
                    <td align="RIGHT"><h3>SUMATORIA</h3></td>
                    <td><h3>NA</h3></td>
                    <td><h3>'.number_format($TotalAbonos).'</h3></td>
                </tr>
            </table>
            ';
        }
        return($html);
    }
    //Funcion para armar el html de los abonos de separados en el informe de administrador
    public function HTML_Abonos_Separados_Admin($CondicionFecha2) {
        $html="";
        $sql="SELECT idUsuarios, SUM(Valor) as Subtotal FROM separados_abonos
	WHERE $CondicionFecha2
	GROUP BY idUsuarios";
	$Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html='<BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total Abonos Separados:
                </em></strong></span><BR>
                <table border="1" cellspacing="2" align="center" >
                    <tr> 
                      <th><h3>Usuario</h3></th>
                      
                      <th><h3>Total</h3></th>
                    </tr >
                </table>';
            $TotalAbonos=0;
            while($DatosAbonos=$this->obCon->FetchArray($Datos)){
                $TotalAbonos=$TotalAbonos+$DatosAbonos["Subtotal"];
                $html.='<table border="1"  cellpadding="2" align="center">
                            <tr>
                                <td>'.$DatosAbonos["idUsuarios"].'</td>
                                
                                <td>'.number_format($DatosAbonos["Subtotal"]).'</td>

                            </tr>
                        </table>';
            }
            $html.=' 
            <table border="1" cellspacing="2" align="center">
                <tr>
                    <td align="RIGHT"><h3>SUMATORIA</h3></td>
                    
                    <td><h3>'.number_format($TotalAbonos).'</h3></td>
                </tr>
            </table>
            ';
        }
        return($html);
    }
    //Funcion para armar el html de los intereses del sistecredito informe de administrador
    public function HTML_Intereses_SisteCredito_Admin($CondicionFecha2) {
        $html="";
        $sql="SELECT idUsuario, SUM(Valor) as Subtotal FROM facturas_intereses_sistecredito
	WHERE $CondicionFecha2
	GROUP BY idUsuario";
	$Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            $html='<BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Total Intereses SisteCredito:
                </em></strong></span><BR>
                <table border="1" cellspacing="2" align="center" >
                    <tr> 
                      <th><h3>Usuario</h3></th>
                      
                      <th><h3>Total</h3></th>
                    </tr >
                </table>';
            $TotalAbonos=0;
            while($DatosAbonos=$this->obCon->FetchArray($Datos)){
                $TotalAbonos=$TotalAbonos+$DatosAbonos["Subtotal"];
                $html.='<table border="1"  cellpadding="2" align="center">
                            <tr>
                                <td>'.$DatosAbonos["idUsuario"].'</td>
                                
                                <td>'.number_format($DatosAbonos["Subtotal"]).'</td>

                            </tr>
                        </table>';
            }
            $html.=' 
            <table border="1" cellspacing="2" align="center">
                <tr>
                    <td align="RIGHT"><h3>SUMATORIA</h3></td>
                    
                    <td><h3>'.number_format($TotalAbonos).'</h3></td>
                </tr>
            </table>
            ';
        }
        return($html);
    }
    //HTML Entregas
    public function HTML_Entregas($CondicionFecha1,$CondicionFecha2) {
        $html="";
        $Entregas[][]="";
        $Usuarios_Entregas[]="";
        $idUsuario="";
        //Venta de contado
        $sql="SELECT `idUsuarios`,SUM(`TotalItem`) AS Total, SUM(`ValorOtrosImpuestos`) AS Bolsas FROM `ori_facturas_items` fi INNER JOIN facturas f ON f.idFacturas=fi.idFactura "
                . "WHERE f.FormaPago='Contado' AND $CondicionFecha2 GROUP BY `idUsuarios`";
        $Datos=$this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            while($DatosEntregas= $this->obCon->FetchArray($Datos)){
                $idUsuario=$DatosEntregas["idUsuarios"];
                $Usuarios_Entregas[$idUsuario]=$idUsuario;
                $Entregas[$idUsuario]["Ventas_Contado"]=$DatosEntregas["Total"];
                $Entregas[$idUsuario]["Bolsas"]=$DatosEntregas["Bolsas"];
            }
        }
        //Abonos a Facturas
        $sql="SELECT FormaPago,Usuarios_idUsuarios as idUsuarios, SUM(Valor) as Total FROM facturas_abonos
	WHERE $CondicionFecha2
	GROUP BY Usuarios_idUsuarios,FormaPago";
        $Datos=$this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            while($DatosEntregas= $this->obCon->FetchArray($Datos)){
                $idUsuario=$DatosEntregas["idUsuarios"];
                $Usuarios_Entregas[$idUsuario]=$idUsuario;
                if($DatosEntregas["FormaPago"]=='SisteCredito'){
                    $Entregas[$idUsuario]["AbonosSisteCredito"]=$DatosEntregas["Total"];
                }else{
                    $Entregas[$idUsuario]["AbonosCredito"]=$DatosEntregas["Total"];
                }
                
                
            }
        }
        
        //Intereses SisteCredito
        $sql="SELECT idUsuario as idUsuarios, SUM(Valor) as Total FROM facturas_intereses_sistecredito
	WHERE $CondicionFecha2
	GROUP BY idUsuario";
        $Datos=$this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            while($DatosEntregas= $this->obCon->FetchArray($Datos)){
                $idUsuario=$DatosEntregas["idUsuarios"];
                $Usuarios_Entregas[$idUsuario]=$idUsuario;
                $Entregas[$idUsuario]["Intereses_SisteCredito"]=$DatosEntregas["Total"];
            }
        }
        
        //Abonos Separados
        $sql="SELECT idUsuarios, SUM(Valor) as Total FROM separados_abonos
	WHERE $CondicionFecha2
	GROUP BY idUsuarios";
	$Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            while($DatosEntregas= $this->obCon->FetchArray($Datos)){
                $idUsuario=$DatosEntregas["idUsuarios"];
                $Usuarios_Entregas[$idUsuario]=$idUsuario;
                $Entregas[$idUsuario]["AbonosSeparados"]=$DatosEntregas["Total"];
            }
        }
        
        //Egresos Ventas Rapidas
        $sql="SELECT Usuario_idUsuario as idUsuarios, SUM(Valor) as Total FROM egresos
	WHERE $CondicionFecha2 AND TipoEgreso='VentasRapidas' GROUP BY Usuario_idUsuario";	
        $Datos= $this->obCon->Query($sql);
        if($this->obCon->NumRows($Datos)){
            while($DatosEntregas= $this->obCon->FetchArray($Datos)){
                $idUsuario=$DatosEntregas["idUsuarios"];
                $Usuarios_Entregas[$idUsuario]=$idUsuario;
                $Entregas[$idUsuario]["Egresos"]=$DatosEntregas["Total"];
            }
        }
        
        if(!empty($idUsuario)){
            
            $html='<BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Entregas:
                    </em></strong></span><BR> <table border="1" CELLPADDING="5" align="center"> ';
            $Salto=0;
            $TotalEntrega=0;
            $Factor="+";
            foreach($Usuarios_Entregas as $idUsuario){
                $Total=0;
                if($idUsuario>0){
                    $html.='<tr><td colspan="2"><strong>Usuario: '.$idUsuario.'</strong></td></tr>';
                    if(isset($Entregas[$idUsuario]["Ventas_Contado"])){
                        $html.='<tr><td><strong>(+) Ventas de Contado: </strong></td><td>'.number_format($Entregas[$idUsuario]["Ventas_Contado"]).'</td></tr>';
                        $Total=$Total+$Entregas[$idUsuario]["Ventas_Contado"];
                        
                    }
                    if(isset($Entregas[$idUsuario]["Bolsas"])){
                        if($Entregas[$idUsuario]["Bolsas"]>0){
                            $html.='<tr><td><strong>(+) Bolsas: </strong></td><td>'.number_format($Entregas[$idUsuario]["Bolsas"]).'</td></tr>';
                            $Total=$Total+$Entregas[$idUsuario]["Bolsas"]; 
                            
                        }
                    }
                    if(isset($Entregas[$idUsuario]["AbonosSisteCredito"])){
                        if($Entregas[$idUsuario]["AbonosSisteCredito"]>0){
                            $html.='<tr><td><strong>(+) Abonos SisteCredito: </strong></td><td>'.number_format($Entregas[$idUsuario]["AbonosSisteCredito"]).'</td></tr>';
                            $Total=$Total+$Entregas[$idUsuario]["AbonosSisteCredito"];  
                                                   
                        }
                    }
                    if(isset($Entregas[$idUsuario]["AbonosCredito"])){
                        if($Entregas[$idUsuario]["AbonosCredito"]>0){
                            $html.='<tr><td><strong>(+) Abonos Creditos: </strong></td><td>'.number_format($Entregas[$idUsuario]["AbonosCredito"]).'</td></tr>';
                            $Total=$Total+$Entregas[$idUsuario]["AbonosCredito"];    
                            
                        }
                    }
                    if(isset($Entregas[$idUsuario]["Intereses_SisteCredito"])){
                        if($Entregas[$idUsuario]["Intereses_SisteCredito"]>0){
                            $html.='<tr><td><strong>(+) Intereses SisteCredito: </strong></td><td>'.number_format($Entregas[$idUsuario]["Intereses_SisteCredito"]).'</td></tr>';
                            $Total=$Total+$Entregas[$idUsuario]["Intereses_SisteCredito"]; 
                            
                        }
                    }
                    if(isset($Entregas[$idUsuario]["AbonosSeparados"])){
                        if($Entregas[$idUsuario]["AbonosSeparados"]>0){
                            $html.='<tr><td><strong>(+) Abonos Separados: </strong></td><td>'.number_format($Entregas[$idUsuario]["AbonosSeparados"]).'</td></tr>';
                            $Total=$Total+$Entregas[$idUsuario]["AbonosSeparados"]; 
                            
                        }
                    }
                    if(isset($Entregas[$idUsuario]["Egresos"])){
                        if($Entregas[$idUsuario]["Egresos"]>0){
                            $html.='<tr><td><strong>(-) Egresos: </strong></td><td>'.number_format($Entregas[$idUsuario]["Egresos"]).'</td></tr>';
                            $Total=$Total-$Entregas[$idUsuario]["Egresos"]; 
                            
                        }
                    }
                    $html.='<tr><td align="RIGTH"><strong>Total Entrega:</strong></td><td>'.number_format($Total).'</td></tr>';
                            
                    $TotalEntrega=$TotalEntrega+$Total;
                    
                }
            }   
            $html.='<tr><td align="RIGTH"><strong>Gran Total:</strong></td><td>'.number_format($TotalEntrega).'</td></tr>';         
            $html.='</table>';
        }
        return ($html);
    }
    //HTML para movimiento librodiario en informe de ventas
    public function HTML_LibroDiario_Informe_Admin($CondicionFecha2,$CentroCostos,$EmpresaPro,$Vector) {
        $html='<BR><BR><span style="color:RED;font-family:Bookman Old Style;font-size:10px;"><strong><em>Movimiento Contable:
                    </em></strong></span><BR><BR> <table border="1" CELLPADDING="5" align="center" > ';
        
        $sql="SELECT `Fecha`,`Tipo_Documento_Intero`,`Num_Documento_Interno`,`Num_Documento_Externo`,"
                . "`Tercero_Identificacion`,`Tercero_Razon_Social`,`Concepto`,`CuentaPUC`,"
                . "`NombreCuenta`,`Debito`,`Credito` "
                . "FROM `librodiario` WHERE $CondicionFecha2 ORDER BY `Tercero_Identificacion` ";
        $consulta=$this->obCon->Query($sql);
        if($this->obCon->NumRows($consulta)){
            $html.='<tr><td><strong>FECHA</strong></td>';
            $html.='<td><strong>DOC</strong></td>';
            $html.='<td><strong>NUM</strong></td>';
            $html.='<td><strong>NUM_EXT</strong></td>';
            $html.='<td><strong>NIT</strong></td>';
            $html.='<td><strong>RAZON SOCIAL</strong></td>';
            $html.='<td><strong>CONCEP</strong></td>';
            $html.='<td><strong>CUENTA</strong></td>';
            $html.='<td><strong>NOMBRE</strong></td>';
            $html.='<td><strong>DEBITO</strong></td>';
            $html.='<td><strong>CREDITO</strong></td>';
            $html.='</tr>';
            while ($DatosLibro=$this->obCon->FetchArray($consulta)){
                $html.='<tr><td>'.$DatosLibro["Fecha"].'</td>';
                $html.='<td>'.$DatosLibro["Tipo_Documento_Intero"].'</td>';
                if($DatosLibro["Tipo_Documento_Intero"]=='FACTURA'){
                    $DatosFactura= $this->obCon->DevuelveValores("facturas", "idFacturas", $DatosLibro["Num_Documento_Interno"]);
                    $NumDoc=$DatosFactura["NumeroFactura"];
                    
                }else{
                    $NumDoc=$DatosLibro["Num_Documento_Interno"];
                }
                $html.='<td>'.$NumDoc.'</td>';
                $html.='<td>'.$DatosLibro["Num_Documento_Externo"].'</td>';
                $html.='<td>'.$DatosLibro["Tercero_Identificacion"].'</td>';
                $html.='<td>'.$DatosLibro["Tercero_Razon_Social"].'</td>';
                $html.='<td>'.$DatosLibro["Concepto"].'</td>';
                $html.='<td>'.$DatosLibro["CuentaPUC"].'</td>';
                $html.='<td>'.$DatosLibro["NombreCuenta"].'</td>';
                $html.='<td>'.number_format($DatosLibro["Debito"]).'</td>';
                $html.='<td>'.number_format($DatosLibro["Credito"]).'</td>';
                $html.='</tr>';
            }
        }
        $html.='</table>';
        return($html);
    }
    //Resolucion de facturacion 
    public function HTML_Uso_Resoluciones($CondicionFecha2,$CentroCostos,$EmpresaPro,$Vector) {
        $html=' 
        <BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Informe de Numeracion Facturas:
        </em></strong></span><BR>


        <table border="1" cellspacing="2" align="center" >
          <tr> 
            <th><h3>Resolucion</h3></th>
            <th><h3>Factura Inicial</h3></th>
            <th><h3>Factura Final</h3></th>
            <th><h3>Total Clientes</h3></th>

          </tr >

        </table>';

        $sql="SELECT idResolucion,MAX(NumeroFactura) as MaxFact, MIN(NumeroFactura) as MinFact FROM facturas
                WHERE $CondicionFecha2 GROUP BY idResolucion";
        $Consulta= $this->obCon->Query($sql);
        while($DatosNumFact=$this->obCon->FetchArray($Consulta)){
                $MinFact=$DatosNumFact["MinFact"];
                $MaxFact=$DatosNumFact["MaxFact"];
                $idResolucion=$DatosNumFact["idResolucion"];
                $TotalFacts=$MaxFact-$MinFact+1;
                $html.='

                <table border="1"  cellpadding="2" align="center">
                 <tr>
                  <td>'.$idResolucion.'</td>
                  <td>'.$MinFact.'</td>
                  <td>'.$MaxFact.'</td>
                  <td>'.$TotalFacts.'</td>

                 </tr>
                 </table>';

        }
        return ($html);
    }
    //Ventas Colaboradores
    public function HTML_Ventas_Colaboradores($CondicionFecha2,$CentroCostos,$EmpresaPro,$Vector) {
        $html=' 
        <BR><span style="color:RED;font-family:Bookman Old Style;font-size:12px;"><strong><em>Ventas X Colaboradores:
        </em></strong></span><BR>


        <table border="1" cellspacing="2" align="center" >
          <tr> 
            
            <th><h3>Colaborador</h3></th>
            <th><h3>Total</h3></th>
            

          </tr >

        </table>';

        $sql="SELECT SUM(Total) as Total, idColaborador FROM colaboradores_ventas
                WHERE $CondicionFecha2 GROUP BY idColaborador";
        $Consulta= $this->obCon->Query($sql);
        while($DatosColaboradores=$this->obCon->FetchArray($Consulta)){
                $DatosCol= $this->obCon->DevuelveValores("colaboradores", "Identificacion", $DatosColaboradores["idColaborador"]);
                $html.='

                <table border="1"  cellpadding="2" align="center">
                 <tr>
                  <td>'.$DatosCol["Nombre"]." ".$DatosCol["Identificacion"].'</td>
                  <td>'.number_format($DatosColaboradores["Total"]).'</td>
                 </tr>
                 </table>';

        }
        return ($html);
    }
    ///Clases para hacer el informe de administrador
    public function PDF_Informe_Ventas_Admin($TipoReporte,$FechaCorte,$FechaIni, $FechaFinal,$CentroCostos,$EmpresaPro,$Vector) {
        
        
        $Condicion=" ori_facturas_items WHERE ";
        $Condicion2=" ori_facturas WHERE ";
        if($TipoReporte=="Corte"){
            $CondicionFecha1=" FechaFactura <= '$FechaCorte' ";
            $CondicionFecha2=" Fecha <= '$FechaCorte' ";
            $CondicionFecha3=" fi.FechaFactura <= '$FechaCorte' ";
            $Rango="Corte a $FechaFinal";
        }else{
            $CondicionFecha1=" FechaFactura >= '$FechaIni' AND FechaFactura <= '$FechaFinal' ";
            $CondicionFecha2=" Fecha >= '$FechaIni' AND Fecha <= '$FechaFinal' ";
            $CondicionFecha3=" fi.FechaFactura >= '$FechaIni' AND fi.FechaFactura <= '$FechaFinal' ";
            $Rango="De $FechaIni a $FechaFinal";
        }

        $CondicionItems=$Condicion.$CondicionFecha1;
        $CondicionFacturas=$Condicion2.$CondicionFecha2;
        
        $CondicionItems=" FROM `ori_facturas_items` fi INNER JOIN facturas f ON fi.`idFactura` = f.idFacturas 
            WHERE $CondicionFecha1
            ";
        
        $idFormato=16;
        $DatosFormatos= $this->obCon->DevuelveValores("formatos_calidad", "ID", $idFormato);
        
        $Documento="$DatosFormatos[Nombre] $Rango";
        
        $this->PDF_Ini("Informe_Ventas", 8, "");
        
        $this->PDF_Encabezado($Rango,1, $idFormato, "",$Documento);
               
        $html= $this->HTML_VentasXDepartamentos($CondicionItems);
        $this->PDF_Write($html);
        
        $html= $this->HTML_VentasXUsuario($CondicionItems,$CondicionFecha1,$CondicionFecha3);
        $this->PDF_Write($html);
        
        $html= $this->HTML_Uso_Resoluciones($CondicionFecha2, $CentroCostos, $EmpresaPro, "");
        $this->PDF_Write($html);
        $html= $this->HTML_Egresos_Admin($CondicionFecha2);
        $this->PDF_Write($html);
        $html= $this->HTML_Abonos_Facturas_Admin($CondicionFecha2);
        $this->PDF_Write($html);
        $html= $this->HTML_Abonos_Separados_Admin($CondicionFecha2);
        $this->PDF_Write($html);
        $html= $this->HTML_Intereses_SisteCredito_Admin($CondicionFecha2);
        $this->PDF_Write($html);
        $html= $this->HTML_Entregas($CondicionFecha1,$CondicionFecha2);
        $this->PDF_Write($html);
        $html= $this->HTML_Ventas_Colaboradores($CondicionFecha2, $CentroCostos, $EmpresaPro, "");
        $this->PDF_Write($html);
         
        /*Solo Juan Car
        $this->PDF_Add();
        //$this->PDF->SetFont('helvetica', '', 6);
        $html= $this->HTML_LibroDiario_Informe_Admin($CondicionFecha2, $CentroCostos, $EmpresaPro, "");
        $this->PDF_Write($html);
         * 
         */
        $this->PDF_Output("Informe_Ventas_");
        
    }
    
    //Arme HTML de los prodctos agregados en una Factura DE COMPRA
    
    public function HTML_Items_Factura_Compra($idFactura) {
        $tbl = "";
        

$sql="SELECT fi.idProducto,fi.Cantidad, fi.CostoUnitarioCompra, fi.SubtotalCompra, fi.ImpuestoCompra, fi.TotalCompra, fi.Tipo_Impuesto, pv.Referencia,pv.Nombre"
        . " FROM factura_compra_items fi INNER JOIN productosventa pv ON fi.idProducto=pv.idProductosVenta WHERE fi.idFacturaCompra='$idFactura'";
$Consulta= $this->obCon->Query($sql);
$h=1;  
if($this->obCon->NumRows($Consulta)){
    $tbl = <<<EOD
                <h3 align="center">PRODUCTOS AGREGADOS</h3>
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td align="center" ><strong>ID</strong></td>
        <td align="center" ><strong>Referencia</strong></td>
        <td align="center" colspan="3"><strong>Producto</strong></td>
        <td align="center" ><strong>Costo Unitario</strong></td>
        <td align="center" ><strong>Cantidad</strong></td>
        <td align="center" ><strong>Subtotal</strong></td>
        <td align="center" ><strong>Impuestos</strong></td>
        <td align="center" ><strong>Total</strong></td>
        <td align="center" ><strong>TipoIVA</strong></td>
    </tr>
    
         
EOD;
$GranSubtotal=0;
$GranIVA=0;
$GranTotal=0;
while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    $GranSubtotal=$GranSubtotal+$DatosItemFactura["SubtotalCompra"];
    $GranIVA=$GranIVA+$DatosItemFactura["ImpuestoCompra"];
    $GranTotal=$GranTotal+$DatosItemFactura["TotalCompra"];
    
    $ValorUnitario=  number_format($DatosItemFactura["CostoUnitarioCompra"]);
    $SubTotalItem=  number_format($DatosItemFactura["SubtotalCompra"]);
    $Cantidad=$DatosItemFactura["Cantidad"];
    
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= '    
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["idProducto"].'</td>    
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Referencia"].'</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Nombre"].'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$ValorUnitario.'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$Cantidad.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$SubTotalItem.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["ImpuestoCompra"]).'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["TotalCompra"]).'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Tipo_Impuesto"].'</td>
    </tr>
        
 ';
    
}
$tbl.= '<tr>'
        . '<td align="right" colspan="7" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTALES</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranSubtotal).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranIVA).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranTotal).'</strong></td>'
        . '<td align="center" style="border-bottom: 1px solid #ddd;background-color: white;"> </td>'
        . '</tr>';
$tbl.= "</table>";
        
}
        return($tbl);

    }
    
    
    //Arme HTML de los prodctos agregados en una Factura DE COMPRA
    
    public function HTML_Insumos_Factura_Compra($idFactura) {
        $tbl = "";
        

$sql="SELECT fi.idProducto,fi.Cantidad, fi.CostoUnitarioCompra, fi.SubtotalCompra, fi.ImpuestoCompra, fi.TotalCompra, fi.Tipo_Impuesto, pv.Referencia,pv.Nombre"
        . " FROM factura_compra_insumos fi INNER JOIN insumos pv ON fi.idProducto=pv.ID WHERE fi.idFacturaCompra='$idFactura'";
$Consulta= $this->obCon->Query($sql);
$h=1;  
if($this->obCon->NumRows($Consulta)){
    $tbl = <<<EOD
                <h3 align="center">INSUMOS AGREGADOS</h3>
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td align="center" ><strong>ID</strong></td>
        <td align="center" ><strong>Referencia</strong></td>
        <td align="center" colspan="3"><strong>Producto</strong></td>
        <td align="center" ><strong>Costo Unitario</strong></td>
        <td align="center" ><strong>Cantidad</strong></td>
        <td align="center" ><strong>Subtotal</strong></td>
        <td align="center" ><strong>Impuestos</strong></td>
        <td align="center" ><strong>Total</strong></td>
        <td align="center" ><strong>TipoIVA</strong></td>
    </tr>
    
         
EOD;
$GranSubtotal=0;
$GranIVA=0;
$GranTotal=0;
while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    $GranSubtotal=$GranSubtotal+$DatosItemFactura["SubtotalCompra"];
    $GranIVA=$GranIVA+$DatosItemFactura["ImpuestoCompra"];
    $GranTotal=$GranTotal+$DatosItemFactura["TotalCompra"];
    
    $ValorUnitario=  number_format($DatosItemFactura["CostoUnitarioCompra"]);
    $SubTotalItem=  number_format($DatosItemFactura["SubtotalCompra"]);
    $Cantidad=$DatosItemFactura["Cantidad"];
    
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= '    
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["idProducto"].'</td>    
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Referencia"].'</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Nombre"].'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$ValorUnitario.'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$Cantidad.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$SubTotalItem.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["ImpuestoCompra"]).'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["TotalCompra"]).'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Tipo_Impuesto"].'</td>
    </tr>
        
 ';
    
}
$tbl.= '<tr>'
        . '<td align="right" colspan="7" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTALES</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranSubtotal).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranIVA).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranTotal).'</strong></td>'
        . '<td align="center" style="border-bottom: 1px solid #ddd;background-color: white;"> </td>'
        . '</tr>';
$tbl.= "</table>";
        
}
        return($tbl);

    }
    
    // Arma el html de los productos devueltos en una compra
    public function HTML_Items_Devueltos_FC($idFactura) {
        $tbl = "";
        

$sql="SELECT fi.idProducto,fi.Cantidad, fi.CostoUnitarioCompra, fi.SubtotalCompra, fi.ImpuestoCompra, fi.TotalCompra, fi.Tipo_Impuesto, pv.Referencia,pv.Nombre"
        . " FROM factura_compra_items_devoluciones fi INNER JOIN productosventa pv ON fi.idProducto=pv.idProductosVenta WHERE fi.idFacturaCompra='$idFactura'";
$Consulta= $this->obCon->Query($sql);
$h=1;  
if($this->obCon->NumRows($Consulta)){
    $tbl = <<<EOD
            <br>
                <h3 align="center">PRODUCTOS DEVUELTOS</h3>
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td align="center" ><strong>ID</strong></td>
        <td align="center" ><strong>Referencia</strong></td>
        <td align="center" colspan="3"><strong>Producto</strong></td>
        <td align="center" ><strong>Costo Unitario</strong></td>
        <td align="center" ><strong>Cantidad</strong></td>
        <td align="center" ><strong>Subtotal</strong></td>
        <td align="center" ><strong>Impuestos</strong></td>
        <td align="center" ><strong>Total</strong></td>
        <td align="center" ><strong>TipoIVA</strong></td>
    </tr>
    
         
EOD;
$GranSubtotal=0;
$GranIVA=0;
$GranTotal=0;
while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    $GranSubtotal=$GranSubtotal+$DatosItemFactura["SubtotalCompra"];
    $GranIVA=$GranIVA+$DatosItemFactura["ImpuestoCompra"];
    $GranTotal=$GranTotal+$DatosItemFactura["TotalCompra"];
    
    $ValorUnitario=  number_format($DatosItemFactura["CostoUnitarioCompra"]);
    $SubTotalItem=  number_format($DatosItemFactura["SubtotalCompra"]);
    $Cantidad=$DatosItemFactura["Cantidad"];
    
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= '    
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["idProducto"].'</td>    
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Referencia"].'</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Nombre"].'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$ValorUnitario.'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$Cantidad.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$SubTotalItem.'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["ImpuestoCompra"]).'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["TotalCompra"]).'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Tipo_Impuesto"].'</td>
    </tr>
        
 ';
    
}
$tbl.= '<tr>'
        . '<td align="right" colspan="7" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTALES</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranSubtotal).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranIVA).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranTotal).'</strong></td>'
        . '<td align="center" style="border-bottom: 1px solid #ddd;background-color: white;"> </td>'
        . '</tr>';
$tbl.= "</table>";
        
}
        return($tbl);

    }
    
    // Arma el html de los servicios agregados en una factura compra
    public function HTML_Servicios_FC($idFactura) {
        $tbl = "";
        

$sql="SELECT fi.CuentaPUC_Servicio,fi.Nombre_Cuenta, fi.Concepto_Servicio, fi.Subtotal_Servicio, fi.Impuesto_Servicio, fi.Total_Servicio, fi.Tipo_Impuesto"
        . " FROM factura_compra_servicios fi WHERE fi.idFacturaCompra='$idFactura'";
$Consulta= $this->obCon->Query($sql);
$h=1;  
if($this->obCon->NumRows($Consulta)){
    $tbl = <<<EOD
            <br>
                <h3 align="center">SERVICIOS AGREGADOS</h3>
<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td align="center" ><strong>Cuenta</strong></td>
        <td align="center" ><strong>Nombre</strong></td>
        <td align="center" colspan="3"><strong>Concepto</strong></td>
        <td align="center" ><strong>Subtotal</strong></td>
        <td align="center" ><strong>Impuestos</strong></td>
        <td align="center" ><strong>Total</strong></td>
        <td align="center" ><strong>TipoIVA</strong></td>
    </tr>
    
         
EOD;
$GranSubtotal=0;
$GranIVA=0;
$GranTotal=0;
while($DatosItemFactura=$this->obCon->FetchArray($Consulta)){
    $GranSubtotal=$GranSubtotal+$DatosItemFactura["Subtotal_Servicio"];
    $GranIVA=$GranIVA+$DatosItemFactura["Impuesto_Servicio"];
    $GranTotal=$GranTotal+$DatosItemFactura["Total_Servicio"];
        
    if($h==0){
        $Back="#f2f2f2";
        $h=1;
    }else{
        $Back="white";
        $h=0;
    }
    
    $tbl .= '    
    
    <tr>
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["CuentaPUC_Servicio"].'</td>    
        <td align="left" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Nombre_Cuenta"].'</td>
        <td align="left" colspan="3" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Concepto_Servicio"].'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["Subtotal_Servicio"]).'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["Impuesto_Servicio"]).'</td>
        <td align="right" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.number_format($DatosItemFactura["Total_Servicio"]).'</td>
        <td align="center" style="border-bottom: 1px solid #ddd;background-color: '.$Back.';">'.$DatosItemFactura["Tipo_Impuesto"].'</td>
    </tr>
        
 ';
    
}
$tbl.= '<tr>'
        . '<td align="right" colspan="5" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>TOTALES</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranSubtotal).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranIVA).'</strong></td>'
        . '<td align="right" style="border-bottom: 1px solid #ddd;background-color: white;"><strong>'.number_format($GranTotal).'</strong></td>'
        . '<td align="center" style="border-bottom: 1px solid #ddd;background-color: white;"> </td>'
        . '</tr>';
$tbl.= "</table>";
        
}
        return($tbl);

    }
    //Se agrega un rango de fechas a un filtro
    public function FiltroRangoFechas($NombreCampo,$statement,$Vector) {
        if(isset($_REQUEST["BtnEnviarRango"])){
            
            $FechaInicial=$this->obCon->normalizar($_REQUEST["TxtFechaInicialRango"]);
            $FechaFinal=$this->obCon->normalizar($_REQUEST["TxtFechaFinalRango"]);
            if (strpos($statement, 'WHERE') !== false) {
                $statement.=" AND $NombreCampo >='$FechaInicial' AND $NombreCampo <='$FechaFinal'";
            }else{
                $statement.=" WHERE $NombreCampo >='$FechaInicial' AND $NombreCampo <='$FechaFinal'";
            }
            
        }
        return($statement);
    }
    
    //Agrega un rango de fechas
    public function FormularioRangoFechas($myPage,$st,$Vector) {
        $FechaIni=date("Y-m-d");
        $FechaFin=date("Y-m-d");
        if(isset($_REQUEST["TxtFechaInicialRango"])){
            $FechaIni=$_REQUEST["TxtFechaInicialRango"];
            
        }
        if(isset($_REQUEST["TxtFechaFinalRango"])){
            $FechaFin=$_REQUEST["TxtFechaFinalRango"];
            
        }
        $this->css=new CssIni("");
        $this->css->CrearForm2("FrmRangos", $myPage, "post", "_self");
        $this->css->CrearInputText("st", "hidden", "", base64_encode($st), "", "", "", "", 150, 30, 0, 1);
                
        $this->css->CrearTabla();
            $this->css->FilaTabla(16);
                $this->css->ColTabla("<strong>FILTRAR POR RANGOS</strong>", 4);
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(16);

                $this->css->ColTabla("<strong>Fecha Inicial</strong>", 1);
                $this->css->ColTabla("<strong>Fecha Final</strong>", 1);
                $this->css->ColTabla("<strong>Enviar</strong>", 1);
            $this->css->CierraFilaTabla();
            $this->css->FilaTabla(16);

                print("<td>");
                    $this->css->CrearInputText("TxtFechaInicialRango", "date", "", $FechaIni, "", "", "", "", 150, 30, 0, 1);
                print("</td>");
                print("<td>");
                    $this->css->CrearInputText("TxtFechaFinalRango", "date", "", $FechaFin, "", "", "", "", 150, 30, 0, 1);
                print("</td>");
                print("<td>");
                    $this->css->CrearBotonNaranja("BtnEnviarRango", "Enviar");
                print("</td>");
            $this->css->CierraFilaTabla();
        $this->css->CerrarTabla();
    $this->css->CerrarForm();
    }
        // FIN Clases	
}
?>