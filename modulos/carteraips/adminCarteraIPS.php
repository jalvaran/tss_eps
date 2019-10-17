<?php
/**
 * Pagina para crear o editar las diferentes opciones del software
 * 2019-05-20, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="adminCarteraIPS.php";
$myTitulo="Cartera IPS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];
$Role=$DatosUsuario["Role"];
$css->PageInit($myTitulo);
    $css->Modal("ModalAcciones", "TS5", "", 1);
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();        
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    
print("<br>");
   $css->section("", "content", "", "");
   $css->CrearDiv("", "col-md-4", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT * FROM ips";
        }else{
            $sql="SELECT r.idIPS as NIT,i.Nombre,i.DataBase FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "onchange=CargarAdminCarteraIPS()", "");
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["DataBase"], "", "");
                    print($DatosIPS["Nombre"].$DatosIPS["NIT"]);
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
    $css->CrearDiv("DivProgress", "col-md-12", "center", 1, 1);
        print("<br><br><br>");
        $css->ProgressBar("PgProgresoUp", "LyProgresoUP", "", 0, 0, 100, 0, "0%", "", "");
    $css->CerrarDiv();
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
   $css->TabInit();
            $css->TabLabel("TabCuentas1", "<strong >Cartera cargada IPS</strong>", "Tab_1", 1,"onclick=CargarAdminCarteraIPS()");
            $css->TabLabel("TabCuentas2", "<strong >Historial de Actualizacion a la cartera IPS</strong>", "Tab_2",0,"onclick=CargarAdminHistorialActualizacionesCarteraIPS()");
            $css->TabLabel("TabCuentas3", "<strong >Facturas sin relacionar en ASMET</strong>", "Tab_3",0,"onclick=MuestreFacturasNRIPS()");  
            $css->TabLabel("TabCuentas4", "<strong >Control de Cargas</strong>", "Tab_4",0,"onclick=CargarAdminControlCargueIPS()"); 
        $css->TabInitEnd();
        $css->TabContentInit();
        
        
        $css->TabPaneInit("Tab_1", 1);
            
            $css->CrearDiv("DivOpcionesCatCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivCatCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_2");
            
            
        
            $css->CrearDiv("DivOpcionesCatHisCartera", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivCatHisCartera", "", "center", 1, 1);

            $css->CerrarDiv();
        
        $css->TabPaneEnd();
        $css->TabPaneInit("Tab_3");
            
             
            $css->CrearDiv("DivOpcionesSRAsmet", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivSRAsmet", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
        
        $css->TabPaneInit("Tab_4");
                         
            $css->CrearDiv("DivOpcionesControlCargue", "", "center", 1, 1);

            $css->CerrarDiv();
            $css->CrearDiv("DivControlCargue", "", "center", 1, 1);

            $css->CerrarDiv();
            
        $css->TabPaneEnd();
    $css->Csection();
$css->PageFin();

print('<script src="jsPages/adminCarteraIPS.js"></script>');  //script propio de la pagina
print('<script src="jsPages/validaciones.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>