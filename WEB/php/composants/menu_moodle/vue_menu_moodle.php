<?php

    require_once __DIR__ . "/../../verify.php";

    class VueMenuMoodle
    {
        private $action;
        private $module;
        private $type;
        
        public function __construct($module, $action, $type){
            $this->action = $action;
            $this->module = $module;
            $this->type   = $type;
        }


        public function afficherMenu($action, $utilisateur_courant, $droits)
        {
            $menu_administration    = $this->menuAdministration($droits);

            $menu_moodle            = $this->menuMoodle($utilisateur_courant);
            
            $menu_mail              = $this->menuMail();

            $edt_active             = $this->module === "edt" ? "activeMenu" : "";

            echo '
            <div id="wrapper" >
                <div class="moodle">

                    <div id="sidebar-wrapper">
                        <ul class="sidebar-nav">
                        
                            <li class="sidebar-brand">
                                <a href="index.php?module=edt" style="color : #00ff71" class="font-weight-bold"><img class="mb-1 mr-2" src="images/logo_etablissement.png"
                                width="30" height="30" alt="logo" />MSU</a>
                            </li>

                            '.$menu_administration. '
                            '.$menu_moodle. '
                            '.$menu_mail. '

                            <li class="'.$edt_active.'">
                                <a href="index.php?module=edt"> Emploi de temps</a>
                            </li>

                        </ul>
                    </div>



                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <a href="#menu-toggle" class="navbar-brand" id="menu-toggle">
                            <img src="images/bars-solid.svg" width="30" height="30" alt="" />
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navHeader" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navHeader">
                            <ul class="navbar-nav  my-2 my-lg-0 ml-auto ">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    '.strtoupper($utilisateur_courant['prenom'] . ' ' . $utilisateur_courant['nom'] ). '
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="index.php?module=profile">Profil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="index.php?module=connexion&action=seDeconnecter">Deconnexion</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            '; 
        }

        public function menuMail(){
            $liens_menu_mail = array(
                "Mails reçus"=>"index.php?module=mail&action=liste_mails_recus",
                "Mails envoyés"=>"index.php?module=mail&action=liste_mails_envoyes",
                "Envoyer un mail"=>"index.php?module=mail&action=afficher_envoyer_mail"
            );

            $actions_menu = array(
                "Mails reçus"=>"liste_mails_recus",
                "Mails envoyés"=>"liste_mails_envoyes",
                "Envoyer un mail"=>"afficher_envoyer_mail"
            );

            $active = strcasecmp($this->module, "mail") == 0 ? "activeToggle" : "";
            $is_shown = strcasecmp($this->module, "mail") == 0 ? "show" : "";

            $menu = '
                <li class="mb-1">
                    <a href="#mail" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle sub-menu '.$active.'">Mails</a>
                    <ul class="collapse list-unstyled '.$is_shown.'" id="mail">
            ';

            foreach($liens_menu_mail as $nom=>$lien){
                $active = "";
                
                if(strcasecmp($actions_menu[$nom], $this->action) == 0){
                    $active = "activeMenu";
                }

                $menu .= "
                            <li class='pl-3 ${active}'>
                                <a href='${lien}'>${nom}</a>
                            </li>
                        ";
            }

            return $menu . "</ul></li>";

        }


        public function menuMoodle($details){


            $sous_menu_cours = array(
                "Accueil"               => "prenom",//Je met une valeur indiquant que l'utilisateur est connecté
                "Déposer un cours"      => "est_enseignant",
                "Ouvrir un dépôt"       => "est_enseignant",
                "accès dépôts"          => "est_enseignant",
                "Contrôles papier"      => "est_enseignant",
                "Ma moyenne"            => "est_etudiant"
            );

            $liens_menu_cours = array(
                "Accueil"               => "index.php?module=moodle&action=liste_modules",
                "Déposer un cours"      => "index.php?module=moodle&action=depot_cours",
                "Ouvrir un dépôt"       => "index.php?module=moodle&action=ouvrir_depot",
                "accès dépôts"          => "index.php?module=moodle&action=acces_depot",
                "Contrôles papier"      => "index.php?module=moodle&action=ajouter_controle",
                "Ma moyenne"            => "index.php?module=moodle&action=notes_etudiant"
            );

            $actions_menu = array(
                "Accueil"               => array("liste_modules", "liste_cours", "details_depot"),
                "Déposer un cours"      => array("depot_cours"),
                "Ouvrir un dépôt"       => array("ouvrir_depot"),
                "accès dépôts"          => array("acces_depot"),
                "Contrôles papier"      => array("ajouter_controle"),
                "Ma moyenne"            => array("notes_etudiant")

            );

            $active = strcasecmp($this->module, "moodle") == 0 ? "activeToggle" : "";
            $is_shown = strcasecmp($this->module, "moodle") == 0 ? "show" : "";
            $menu = '
                    <li class="mb-1">
                        <a href="#cours" data-toggle="collapse" aria-expanded="true" class="dropdown-toggle sub-menu '.$active.'">Cours</a>
                        <ul class="collapse list-unstyled collapse '.$is_shown.'"  id="cours">
            ';

            $list_items = "";
            
            foreach($sous_menu_cours as $nom=>$indice_requis){
                if($details[$indice_requis]){
                    $active = "";
                    if(in_array(strtolower($this->action), $actions_menu[$nom]))
                        $active = "activeMenu";

                        $list_items .= "
                        <li class='ml-3 ${active}'>
                            <a href='${liens_menu_cours[$nom]}'>${nom}</a>
                        </li>
                    ";
                }
            }


            if($list_items === "")
                $menu = "";
            else
                $menu .= $list_items . "</ul></li>";
            
            return $menu;

        }

        public function menuAdministration($droits){

            $sous_menu_administration = array(
                "Utilisateurs"  => "droit_creation_utilisateurs",
                "Personnels"    => "droit_creation_utilisateurs",
                "Etudiants"     => "droit_creation_utilisateurs",
                "Groupes"       => "droit_creation_groupes",
                "Droits"        => "droit_modification_droits",
                "Modules"       => "droits_creation_modules",
                "Salles"        => "droit_creation_cours",
                "Semestres"     => "droit_creation_cours",
                "Absences"      => "droit_modification_absences"
            );

            $liens_menu_administration = array(
                "Utilisateurs"  => "index.php?module=administration&type=utilisateur&action=liste_utilisateurs",
                "Personnels"    => "index.php?module=administration&type=personnel&action=liste_personnels",
                "Etudiants"     => "index.php?module=administration&type=etudiant&action=liste_etudiants",
                "Groupes"       => "index.php?module=administration&type=groupe&action=liste_groupes",
                "Droits"        => "index.php?module=administration&type=droits&action=liste_droits",
                "Modules"       => "index.php?module=administration&type=module&action=liste_modules",
                "Semestres"     => "index.php?module=administration&type=semestre&action=liste_semestre",
                "Salles"        => "index.php?module=administration&type=salle&action=liste_salles",
                "Absences"      => "index.php?module=administration&type=salle&action=liste_salles"
            );

            $types_menu_administration = array(
                "Utilisateurs"  => "utilisateur",
                "Personnels"    => "personnel",
                "Etudiants"     => "etudiant",
                "Groupes"       => "groupe",
                "Droits"        => "droits",
                "Modules"       => "module",
                "Salles"        => "salle",
                "Semestres"     => "semestre",
                "Absences"      => "absence"
            );

            $active = strcasecmp($this->module, "administration") == 0 ? "activeToggle" : "";
            $is_shown = strcasecmp($this->module, "administration") == 0 ? "show" : "";

            $menu = '
                    <li class="mb-1">
                        <a href="#administration"  data-toggle="collapse" aria-expanded="false" class="dropdown-toggle sub-menu '.$active.'">Administration</a>
                        <ul class="collapse list-unstyled '.$is_shown.'" id="administration">
            ';

            $list_items = "";
            

            foreach($sous_menu_administration as $nom=>$droit_requis){
                if($droits[$droit_requis]){
                    $active = "";
                    if(strcasecmp($types_menu_administration[$nom], $this->type) == 0){
                        $active = "activeMenu";
                    }
                        

                    $list_items .= "
                        <li class='pl-3'>
                            <a href='${liens_menu_administration[$nom]}' class='${active}'>${nom}</a>
                        </li>
                    ";
                }
            }



            if($list_items === "")
                $menu = "";
            else
                $menu .= $list_items . "</ul></li>";
            
            return $menu;
        }

    }
