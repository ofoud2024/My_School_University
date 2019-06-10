<?php
    require_once __DIR__ . "/cont_mail.php";
    require_once __DIR__ . "/../../verify.php";

    class ModMail
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContMail();

            switch ($action) {
                case 'liste_mails_recus':
                    $cont->afficher_mails_recus();
                break;

                case 'liste_mails_envoyes':
                    $cont->afficher_mails_envoyes();
                break;
                
                case 'envoyer_mail':
                    $cont->envoyer_mail();
                break;
                
                case 'supprimer_mail':
                    $cont->supprimer_mail();
                break;

                case 'afficher_envoyer_mail':
                    $cont->afficher_envoyer_mail();
                break;

                case 'cacher_mail':
                    $cont->cacher_mail();
                break;

                default:
                    header('Location: index.php?module=mail&action=liste_mails_recus');
                break;
            }
        }
    }
