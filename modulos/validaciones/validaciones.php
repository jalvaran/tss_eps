<?php
/**
 * Pagina para Realizar las validaciones de las facturas
 * 2019-05-28, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="validaciones.php";
$myTitulo="Validacion de Facturas";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$_SESSION["Role"];

$css->PageInit($myTitulo);
    print("<br>");
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        $sql="SELECT * FROM ips i INNER JOIN relacion_usuarios_ips ri ON ri.idIPS=i.NIT WHERE ri.idUsuario='$idUser' ";
    
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "", "");
            $Consulta=$obCon->Query($sql);
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["NIT"], "", "");
                    print($DatosIPS["Nombre"]." ".$DatosIPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        $css->select("CmbEPS", "form-control", "CmbEPS", "EPS", "", "", "");
           
            $css->option("", "", "", 1, "", "");
                print("ASMET");
            $css->Coption();
           
        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        print('<br><div class="input-group">');
        $css->input("text", "TxtBusquedas", "form-control", "TxtBusquedas", "", "", "Buscar Factura o Contrato", "", "", "onchange=BuscarFactura()");


         print('<span class="input-group-addon"><i class="fa fa-fw fa-search"></i></span>
              </div>');
    $css->CerrarDiv();
    print("<br><br><br><br>");
    $css->CrearDiv("", "col-md-12", "center", 1, 1);   
        
        $css->TabInit();
            $css->TabLabel("TabCuentas1", "Facturas sin Relacion IPS", "Tab_1", 1,"onclick=MuestreFacturasNRIPS()");
            $css->TabLabel("TabCuentas2", "Facturas sin Relacion EPS", "Tab_2",0,"onclick=MuestreFacturasNREPS()");
            $css->TabLabel("TabCuentas3", "Cruce de Cartera", "Tab_3",0,"onclick=MuestreCruce()");          
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            /*
             * Contenido de Facturas sin relacion IPS
             * Son las facturas que las tiene la IPS pero no la EPS
             */
            $css->CrearDiv("DivFacturasIPS", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_2");
            
            /*
             * Contenido de Facturas sin relacion EPS
             * Son las facturas que las tiene la EPS pero no la IPS
             */
        
            $css->CrearDiv("DivFacturasEPS", "", "center", 1, 1);

            $css->CerrarDiv();
        
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_3");
            /*
             * Contenido de Cruce de cartera Factura por factura
             * 
             */
             
            $css->CrearDiv("DivCruce", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        
        
    $css->CerrarDiv();
    print("<br><br><br><br><br><br><br><br><br><br>");
$css->PageFin();
print('<script src="jsPages/validaciones.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>