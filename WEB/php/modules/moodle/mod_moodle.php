<?php
    require_once __DIR__ . "/cont_moodle.php";
    require_once __DIR__ . "/../../verify.php";

    class ModMoodle
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
            $cont = new ContMoodle();

            switch($action){
                
                case 'depot_cours':
                    $cont->afficher_depot_cours();
                break;

                case 'ouvrir_depot':
                    $cont->afficher_ouvrir_depot();
                break;

                case 'acces_depot':
                    $cont->afficher_acces_depot(); 
                break;

                case 'effectuer_depot_cours':
                    $cont->effectuer_depot_cours();
                break;

                case 'effectuer_ouvrir_depot':
                    $cont->effectuer_ouvrir_depot();
                break;

                case 'liste_modules':
                    $cont->afficherListeModules();
                break;

                case 'liste_cours':
                    $cont->afficherListeSupports();
                break;

                case 'details_depot':
                    $cont->detailsDepotEtudiant();
                break;

                case 'deposer_exercice':
                    $cont->deposer_exercice();
                break;

                case 'ajouter_controle':
                    $cont->afficher_ajouter_controle();
                break;

                case 'notes_etudiant':
                    $cont->notes_etudiant();
                break;


                case 'charger_notes':
                    $cont->charger_notes();
                break;

                case 'details_notes_etudiants':
                    $cont->details_notes_etudiant();
                break;

                case 'changer_notes_etudiants':
                    $cont->changer_notes_etudiant();
                break;

                case 'changer_etat_support':
                    $cont->changer_etat_support();
                break;

                case 'supprimer_support':
                    $cont->supprimer_support();
                break;

                case 'supprimer_depot':
                    $cont->supprimer_depot();
                break;

                case 'notes_etudiant':
                    $cont->notes_etudiant();
                break;

                default:
                    header('Location: index.php?module=moodle&action=liste_modules');
                break;
            }    
        }


    }
    