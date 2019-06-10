<?php
    require_once __DIR__ . "/cont_module.php";
    require_once __DIR__ . "/../../../verify.php";

    class ModModule
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : null;
            $cont = new ContModule();

            switch ($action) {
                case 'liste_modules':
                    $cont->liste_modules();
                break;

                case 'ajouter_module':
                    $cont->ajouter_module();
                break;
                
                case 'afficher_module':
                    $cont->afficher_module();
                break;

                case 'modifier_module':
                    $cont->modifier_module();
                break;

                case 'retirer_enseignant':
                    $cont->retirer_enseignant();
                break;

                case 'ajouter_enseignant':
                    $cont->ajouter_enseignant();
                break;

                case 'supprimer_module':
                    $cont->supprimer_module();
                break;

                default:
                    header('Location: index.php?module=administration&type=module&action=liste_modules');
                break;

            }
        }
    }
