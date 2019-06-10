<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/cont_edt.php";

    class ModEdt{
        public function __construct(){
            $cont = new ContEdt();
            $action =  isset($_GET['action']) ? htmlspecialchars($_GET['action']) : "";

            switch($action){
                case "appliquer_absences":
                    $cont->appliquer_absences();
                break;
                default:
                    $cont->afficherEdt();
                break;
            }
        }
    }
?>