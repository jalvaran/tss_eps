<?php
/**
 * Pagina para crear o editar los contratos
 * 2019-05-20, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="contratos.php";
$myTitulo="Plataforma TSS";
include_once("../../sesiones/php_control_usuarios.php");
include_once("../../constructores/paginas_constructor.php");

$css =  new PageConstruct($myTitulo, ""); //objeto con las funciones del html

$obCon = new conexion($idUser); //Conexion a la base de datos
$NombreUser=$_SESSION['nombre'];

$sql="SELECT TipoUser,Role FROM usuarios WHERE idUsuarios='$idUser'";
$DatosUsuario=$obCon->Query($sql);
$DatosUsuario=$obCon->FetchAssoc($DatosUsuario);
$TipoUser=$DatosUsuario["TipoUser"];

$css->PageInit($myTitulo);

    $css->Modal("ModalAcciones", "TS5", "", 1);
    
        
        $css->div("DivFrmModalAcciones", "", "", "", "", "", "");
        $css->Cdiv();    
            
      
    $css->CModal("BntModalAcciones", "onclick=SeleccioneAccionFormularios()", "button", "Guardar");
    
    print("<br>");
     $css->CrearDiv("", "col-md-4", "center", 1, 1);
        if($TipoUser=="administrador"){
            $sql="SELECT NIT as idIPS, Nombre FROM ips";
        }else{
            $sql="SELECT r.idIPS,i.Nombre FROM relacion_usuarios_ips r INNER JOIN ips i ON i.NIT=r.idIPS WHERE idUsuario='$idUser' ";
        }
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbIPS", "form-control", "CmbIPS", "IPS", "", "", "");
            while($DatosIPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosIPS["idIPS"], "", "");
                    print($DatosIPS["Nombre"]." ".$DatosIPS["idIPS"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();    
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        $sql="SELECT * FROM eps ORDER BY ID";
        $Consulta=$obCon->Query($sql);
        
        $css->select("CmbEPS", "form-control", "CmbEPS", "EPS", "", "", "");
            while($DatosEPS=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosEPS["NIT"], "", "");
                    print($DatosEPS["Nombre"]." ".$DatosEPS["NIT"]);
                $css->Coption();
            }
        $css->Cselect();
    $css->CerrarDiv();
    $css->CrearDiv("", "col-md-4", "left", 1, 1);
        $css->CrearBotonEvento("btnCrearContrato", "Crear Contrato", 1, "onclick", "AbreFormularioCrearContrato(``)", "azul", "style='width:150px;'");
    $css->CerrarDiv();
    print("<br><br><br><br><br><br>");
    $css->CrearDiv("DivOpcionesContratos", "col-md-12", "left", 1, 1);
    $css->CerrarDiv();    
    
    $css->CrearDiv("DivContratos", "col-md-12", "left", 1, 1);
    $css->CerrarDiv();
    
    
    
    $css->CerrarDiv();
    
$css->PageFin();
print('<script src="jsPages/contratos.js"></script>');  //script propio de la pagina

print('<script src="../../general/js/CreacionContratos.js"></script>');  //script para la creacion de contratos
$css->Cbody();
$css->Chtml();

?>