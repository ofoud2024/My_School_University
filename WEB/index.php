<?php
    session_start();

    if (isset($_SESSION['historique']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        array_unshift($_SESSION['historique'], "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $_SESSION['historique'] = array(
            "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
        );
    }
    
    define("CONST_INCLUDE", true);
    require_once __DIR__ . "/php/common/Constants.php";
    require_once __DIR__ . "/php/common/Fonctions.php";
    require_once __DIR__ . "/php/common/File.php";
    require_once __DIR__ . "/php/common/FileUpload.php";

    require_once __DIR__ . "/php/common/errorHandler.php";
    require_once __DIR__ . "/php/common/Token.php";

    require_once __DIR__ . "/php/common/exceptions/ElementIntrouvable.php";
    require_once __DIR__ . "/php/common/exceptions/FichierInexistant.php";
    require_once __DIR__ . "/php/common/exceptions/ParametresInsuffisants.php";
    require_once __DIR__ . "/php/common/exceptions/ParametresIncorrecte.php";
    require_once __DIR__ . "/php/common/exceptions/PasEnseignantException.php";
    require_once __DIR__ . "/php/common/exceptions/NonAutorise.php";
    require_once __DIR__ . "/php/common/exceptions/PasEtudiantException.php";

    require_once __DIR__ . "/php/common/classes/user/utilisateur.php";
    require_once __DIR__ . "/php/common/classes/user/personnel.php";
    require_once __DIR__ . "/php/common/classes/user/enseignant.php";
    require_once __DIR__ . "/php/common/classes/etudiant.php";
?>
<!DOCTYPE html>
<html>
<head>
	
	<title>Etablissement</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="css/Common/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="frameworks/font-awesome/css/all.css" rel="stylesheet" type="text/css">
    <link href="css/Common/toastr.css" rel="stylesheet" type="text/css">
    <link href="frameworks/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="css/Common/jquery.flexdatalist.min.css" rel="stylesheet" type="text/css">
    <link href="frameworks/timepicker/jquery.timepicker.css" rel="stylesheet" type="text/css">

    <link href="css/main.css" rel="stylesheet"  type="text/css">
    <link href="css/mail.css" rel="stylesheet"  type="text/css">
	<link href="css/page-externe.css" rel="stylesheet" type="text/css"> 
	<link href="css/menu-externe.css" rel="stylesheet" type="text/css">
	<link href="css/connexion.css" type="text/css" rel="stylesheet">
	<link href="css/menu-interne.css" rel="stylesheet" type="text/css" >
    <link href="css/administration.css" rel="stylesheet" type="text/css" >
    <link href="css/edt/edt.css" rel="stylesheet" type="text/css">
    <link href="css/moodle/simple-sidebar.css" rel="stylesheet" type="text/css" />
    <link href="css/moodle/styleMoodle.css" rel="stylesheet" type="text/css" /> 

</head>
<body>
    <?php
    

        $module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : '';

        if ($module === "error") {
            
            $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : DEFAULT_ERROR_MESSAGE;
            
            $title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : DEFAULT_ERROR_TITLE;
            
            require_once "php/composants/error.php";
            
            ErrorPanel::showError($title, $message);

        } elseif (Utilisateur::estConnecte()) {

            include_once "php/composants/menu_interne/cont_menu_interne.php" ;
            include_once "php/composants/menu_moodle/cont_menu_moodle.php" ;
            
            Database::initConnexion();

            ob_start();

            if (in_array($module, AVAILABLEMODULES)) {

                include_once "php/common/Database.php";
                
                
                include_once "php/modules/${module}/mod_${module}.php";
                
                $classe = "Mod${module}";
        
                $classe = new $classe();
            
            } else {
                header("Location: index.php?module=edt");
            }

            $affichage = ob_get_clean();
            
            if($module === "moodle"){
                $cont = new ContMenuMoodle();
            }else{
                $cont = new ContMenuInterne();  
            }
            $cont = new ContMenuMoodle();  

    
            $cont->afficherMenu();
            
            echo $affichage;


        } elseif ($module === 'connexion') {
            include_once "php/common/Database.php";
            
            Database::initConnexion();

            include_once "php/modules/connexion/mod_connexion.php";

            $mod = new ModConnexion();
        } else {
            header('Location: Acceuil.php');
        }

      
    ?>

	<script src="scripts/Common/jquery-3.3.1.min.js" ></script>
	<script src="scripts/Common/popper.min.js" ></script>
	<script src="scripts/Common/bootstrap.min.js" ></script>
    <script src="scripts/Common/toastr.js"></script>
    <script src="./frameworks/DataTables/datatables.min.js"></script>
    <script src="./frameworks/DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js"></script>
 
    <script src="frameworks/React/react.development.js"></script>
    <script src="frameworks/React/react-dom.development.js"></script>
    <script src="frameworks/React/babel.min.js"></script>

    <script src="scripts/Common/jquery.flexdatalist.js"></script>
    <script src="frameworks/moment.min.js"></script>
    <script src="frameworks/timepicker/jquery.timepicker.js"></script>

    <script src="scripts/i18n/i18n.js"></script>
    <script src="scripts/vue_generique.js" type="text/babel"></script>
    <script src="scripts/testVars.js"></script>
    <script src="scripts/init.js"></script>   
    <script src="scripts/autocomplete.js"></script>
    <script src="scripts/administration.js"></script>
    <script src="scripts/groupe.js"></script>

    <script src="scripts/edt/edt-init.js" type="text/babel"></script>
    <script src="scripts/edt/edt-view.js" type="text/babel"></script>
    <script src="scripts/edt/edt-action.js" type="text/babel"></script>
    <script src="scripts/edt/edt-executor.js" type="text/babel"></script>

    <script src="scripts/moodle/moodle-view.js" type="text/babel"></script>
    <script src="scripts/moodle/moodle.js" ></script>

    <script src="scripts/responsive.js"></script>

    <script src="scripts/mail/mail-view.js" type="text/babel"></script>
    <script src="scripts/mail/mail-action.js"  type="text/babel"></script>
    
    <script >
    </script>

</body>
</html>