<?php
    define('CONST_INCLUDE', true);
    session_start();
    require_once __DIR__ . "/../common/Constants.php";
    require_once __DIR__ . "/../common/Response.php";
    require_once __DIR__ . "/../common/Fonctions.php";
    require_once __DIR__ . "/../common/File.php";
    require_once __DIR__ . "/../common/Database.php";

    require_once __DIR__ . "/../common/errorHandlerAPI.php";
    require_once __DIR__ . "/../common/Token.php";

    require_once __DIR__ . "/../common/exceptions/ElementIntrouvable.php";
    require_once __DIR__ . "/../common/exceptions/FichierInexistant.php";
    require_once __DIR__ . "/../common/exceptions/ParametresInsuffisants.php";
    require_once __DIR__ . "/../common/exceptions/PasEnseignantException.php";
    require_once __DIR__ . "/../common/exceptions/NonAutorise.php";

    require_once __DIR__ . "/../common/classes/user/utilisateur.php";
    require_once __DIR__ . "/../common/classes/user/personnel.php";
    require_once __DIR__ . "/../common/classes/user/enseignant.php";
    require_once __DIR__ . "/../common/classes/etudiant.php";


    require_once __DIR__ . "/utilisateur/mod_utilisateur_api.php";
    require_once __DIR__ . "/groupe/mod_groupe_api.php";
    require_once __DIR__ . "/semestre/mod_semestre_api.php";
    require_once __DIR__ . "/moodle/mod_moodle_api.php";
    require_once __DIR__ . "/module/mod_module_api.php";
    require_once __DIR__ . "/salle/mod_salle_api.php";
    require_once __DIR__ . "/edt/mod_edt_api.php";
    require_once __DIR__ . "/mail/mod_mail_api.php";

    Database::initConnexion();

    $type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';

    $mod = null;


    switch ($type) {
        case 'utilisateur':
            $mod = new ModUtilisateurAPI();
        break;

        case 'groupe':
            $mod = new ModGroupeAPI();
        break;

        case 'semestre':
            $mod = new ModSemestreAPI();
        break;

        case 'moodle':
            $mod = new ModMoodleAPI();
        break;

        case 'module':
            $mod = new ModModuleAPI();
        break;

        case 'salle':
            $mod = new ModSalleAPI();
        break;
        
        case 'edt':
            $mod = new ModEdtAPI();
        break;
        
        case 'mail':
            $mod = new ModMailAPI();
        break;
        
        default:
            Response::send_error(HTTP_BAD_REQUEST, " pas de module " );
        break;

    }

