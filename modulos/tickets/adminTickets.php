<?php
/**
 * Pagina para Subir los pagos por parte de una EPS
 * 2019-05-24, Julian Alvaran Techno Soluciones SAS
 * 
 * es recomendable No usar los siguientes ID para ningún objeto:
 * FrmModal, ModalAcciones,DivFormularios,BtnModalGuardar,DivOpcionesTablas,
 * DivControlCampos,DivOpciones1,DivOpciones2,DivOpciones3,DivParametrosTablas
 * TxtTabla, TxtCondicion,TxtOrdenNombreColumna,TxtOrdenTabla,TxtLimit,TxtPage,tabla
 * 
 */
$myPage="adminTickets.php";
$myTitulo="Administrador Tickets";
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
    
    $css->CrearDiv("", "col-md-12", "left", 1, 1); 
        $css->h3("", "", "", "");
                print("<strong>Administrador de Tickets</strong>");
        $css->Ch3();
    $css->CerrarDiv(); 
    
    $css->CrearDiv("", "col-md-12", "left", 1, 1);
    $css->CrearTitulo("Generar Informe de Tickets", "azul");
    $css->CerrarDiv(); 
    print("<br><br><br><br><br>");
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        print("<strong>Fecha Inicial:</strong><br>");
        $css->input("date", "FechaInicial", "form-control", "FechaInicial", "", date("Y-m-d"), "Fecha Inicial", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
        
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        print("<strong>Fecha Final:</strong><br>");
        $css->input("date", "FechaFinal", "form-control", "FechaFinal", "", date("Y-m-d"), "Fecha Final", "", "", "style='line-height: 15px;'"."max=".date("Y-m-d"));
        
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        $css->select("CmbEstado", "form-control", "CmbEstado", "Estado:", "", "", "");
            $css->option("", "", "", '', "", "");
                print("Todos los Estados");
            $css->Coption();

            $Consulta=$obCon->ConsultarTabla("tickets_estados", "");
            while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosSelect["ID"], "", "");
                    print(utf8_encode($DatosSelect["Estado"]));
                $css->Coption();
            }

        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        $css->select("CmbProyectosTicketsListado", "form-control", "CmbProyectosTicketsListado", "Proyectos:", "", "onchange=CargarModulosProyectosEnSelect()", "");
            $css->option("", "", "", '', "", "");
                print("Todos los proyectos");
            $css->Coption();

            $Consulta=$obCon->ConsultarTabla("tickets_proyectos", "");
            while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosSelect["ID"], "", "");
                    print(utf8_encode($DatosSelect["Proyecto"]));
                $css->Coption();
            }

        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        $css->select("CmbModulosTicketsListado", "form-control", "CmbModulosTicketsListado", "Módulos:", "", "", "");
            $css->option("", "", "", '', "", "");
                print("Todos los Módulos");
            $css->Coption();

            $Consulta=$obCon->ConsultarTabla("tickets_modulos_proyectos", "");
            while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosSelect["ID"], "", "");
                    print(utf8_encode($DatosSelect["NombreModulo"]));
                $css->Coption();
            }

        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        $css->select("CmbTiposTicketsListado", "form-control", "CmbTiposTicketsListado", "Tipo:", "", "", "");
            $css->option("", "", "", '', "", "");
                print("Todos los Tipos");
            $css->Coption();

            $Consulta=$obCon->ConsultarTabla("tickets_tipo", "");
            while($DatosSelect=$obCon->FetchAssoc($Consulta)){
                $css->option("", "", "", $DatosSelect["ID"], "", "");
                    print(utf8_encode($DatosSelect["TipoTicket"]));
                $css->Coption();
            }

        $css->Cselect();
    $css->CerrarDiv();
    
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        
        print(" ");
        
    $css->CerrarDiv();
    $css->CrearDiv("", "col-md-2", "center", 1, 1);
        
        print(" ");
        
    $css->CerrarDiv();
    $css->CrearDiv("", "col-md-4", "center", 1, 1);
        
        print("<br><strong>Generar Informe:</strong><br>");
        $css->CrearBotonEvento("BtnGenerar", "Generar", 1, "onclick", "GenerarInformeTicketsPDF()", "verde", "");
    $css->CerrarDiv();
    
    
    $css->CrearDiv("DivMensajes", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivProcess", "col-md-12", "center", 1, 1);
    
    $css->CerrarDiv();
    
    $css->CrearDiv("DivPDFReportes", "col-md-12", "center", 0, 1);
        print("<iframe id='FramePDF' name='FramePDF' class='col-md-12' style='height:1000px;border:0px;'></iframe>");
    $css->CerrarDiv();
   
    
    
$css->PageFin();
print('<script src="jsPages/adminTickets.js"></script>');  //script propio de la pagina

$css->Cbody();
$css->Chtml();

?>