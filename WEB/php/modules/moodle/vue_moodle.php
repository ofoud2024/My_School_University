<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/vue_generique.php";

    class VueMoodle extends VueGenerique
    {
        public function __construct()
        {
        }

        /*
            Moodle Enseignant
        */
        public function afficher_depot_cours($liste_supports, $token){
            try{
                Enseignant::idEnseignantCourant();
                
                $button = "<button class=''></button>";
                $liste_vue_supports = array_map(
                    function($support) use($token) {
                        $id_support = $support['id_support'];
                        $bouton = $support['support_est_cachee'] ? '<a href="index.php?module=moodle&action=changer_etat_support&id_support='.$id_support.'&token='.$token.'"><button class="btn btn-sm btn-outline-warning px-2 py-0">Afficher<i class="far fa-eye"></i></button></a>' : 
                        '<a href="index.php?module=moodle&action=changer_etat_support&id_support='.$id_support.'&token='.$token.'"><button class="btn btn-sm btn-outline-primary px-2 py-0">cacher<i class="far fa-eye-slash"></i></button></a>';
                        
                        return array(
                            'id_support'=>$support['id_support'],
                            'ref_module'=>$support['ref_module'],
                            'nom_support'=>$support['nom_support'],
                            'date_depot_support'=>$support['date_ouverture_support'],
                            'date_ouverture_support'=>$support['date_ouverture_support'],
                            'support_est_cachee'=> $bouton,
                            'nb_consultation_support'=>$support['nb_consultation_support']
                        );

                    },
                    $liste_supports
                );

                echo '
                    <div class="moodle ">
                        <div id="page-content-wrapper">
                            <form class="container-fluid row justify-content-center" method="post" action="index.php?module=moodle&action=effectuer_depot_cours"
                                enctype="multipart/form-data">
                                <h1 class="my-3 col-12 text-center">Déposer un cours</h1>
                                <div class="container-fluid">
                                    <div class="row mx-auto container-fluid">
                                        <div class="col-lg-6 col-12 border border-primary rounded">
                                            <h4 class="mt-2  text-center">Déposez ici !</h4>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="support_cours" name="support_cours">
                                                <label class="custom-file-label" for="support_cours">Choisir un fichier</label>
                                            </div>
                                            <div class="form-group row my-2 justify-content-end">
                                                <div class="container justify-content-start col-lg-6 col-12 ">
                                                    <label for="date_ouverture_depot" class="text-left pt-lg-1">Date d\'ouverture</label>
                                                </div>
                                                <div class="container justify-content-end col-12 col-lg-6">
                                                    <input class="form-control " type="date" value="2018-12-13" id="date_ouverture_depot"
                                                        name="date_ouverture_depot">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="switch mr-2">
                                                    <input type="checkbox" checked id="cours_est_visible" name="cours_est_visible">
                                                    <span class="slider round"></span>
                                                </label>Rendre visible ?
                                            </div>

                                        </div>

                                        <div class=" col-lg-6">
                                            <div class="form-group">
                                                <label for="titre_cours">Titre du support</label>
                                                <input type="text" class="form-control" id="titre_cours" name="titre_cours" placeholder="Saisir le titre">
                                            </div>
                                            <div class="form-group">
                                                <label for="module_cours">Module</label>
                                                <select class="form-control" id="module_cours" name="module_cours">
                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row  container col-12 mt-3">
                                        <div id="boutonDynamic" class="container col-12 justify-content-center row">
                                            <button type="submit" class="btn btn-success">Enregister
                                            </button>

                                        </div>
                                    </div>
                                </div>

                                <input type="text" class="d-none sr-only" id="token" name="token" value="'.$token.'">

                            </form>
                ';
                
                echo "<h3 class='text-center text-primary underline mt-4  col-11'>Vos cours</h3>";
                echo '<div class="my-3 mx-2 table-bg-white">';
                
                $this->afficherTableauSuppression(
                    $liste_vue_supports,
                    array('ref_module', 'nom_support', 'date_depot_support', 'date_ouverture_support', 'support_est_cachee', 'nb_consultation_support' ),
                    'id_support',
                    'index.php?module=moodle&action=supprimer_support&&token='.$token.'&id_support=',
                    array('Module', 'nom', 'date_depot', 'date ouverture', 'status', 'consultations')
                );

                echo "</div></div></div>";
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur(new Exception("Pas un enseignant"), NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function afficherDetailsSupport(){
            try{
                Enseignant::idEnseignantCourant();
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
            echo '
                <div class="panel-body">
          
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="2"><u>Informations du dépôt</u></th>
                            </tr>
                        </thead>
    
                        <tbody>
    
                            <tr>
                                <td><b>Module</b></td>
                                <td id="validationBranch">M1203</td>
                            </tr>
        
                            <tr>
                                <td><b>Nom</b></td>
                                <td id="validationService">Dépôt test</td>
                            </tr>
        
                            <tr>
                                <td><b>Ouverture</b></td>
                                <td id="validationDate">05/01/2019</td>
                            </tr>
        
                            <tr>
                                <td><b>est Cachée</b></td>
                                <td id="validationTime">Non</td>    
                            </tr>
        
                        </tbody>
    
                    </table>
    
    
                </div>
          
                <div class="row justify-content-around container-fluid">
                
                    <button class="btn  btn-outline-danger">Cacher</button>

                    <button class="btn  btn-outline-warning">Supprimer</button>
                              
                </div>
            ';
        }

        public function afficher_ouvrir_depot($liste_depots, $token){

            try{
                echo '
                <div id="page-content-wrapper" class=" px-0 ">
                    <form class="container-fluid  px-0 " action="index.php?module=moodle&action=effectuer_ouvrir_depot" method="post"
                        enctype="multipart/form-data">
                
                        <h1 class="my-3 text-center">Ouvrir un dépôt</h1>
                
                        <div class="container-fluid row py-2 justify-content-center  mx-0">
                
                            <div class="col-11 col-lg-10 border border-primary rounded">
                
                                <h4 class="mt-2  text-center">Détails du dépôt </h4>
                
                                <div class="form-group">
                                    <label for="nom_depot">Nom dépôt</label>
                                    <input type="text" id="nom_depot" name="nom_depot" class="form-control" />
                                </div>
                
                                <div class="form-group">
                                    <label for="module_depot">Module</label>
                                    <select class="form-control" name="module_depot" id="module_depot" class="module_depot">
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label for="groupe_depot">Groupe</label>
                                    <select class="form-control" name="groupe_depot" id="groupe_depot" module="module_depot" class="select_group_depot">
                                    </select>
                                </div>
                
                
                            </div>
                
                
                
                            <div class="col-11 col-lg-10 mt-4 border border-primary rounded">
                                <h4 class="mt-2  text-center">Déposez ici !</h4>
                                <div class="custom-file my-3">
                                    <input type="file" class="custom-file-input" id="support_depot" name="support_depot">
                                    <label class="custom-file-label" for="support_depot">Choisir un fichier</label>
                                </div>
                            </div>
                
                
                
                            <div class="col-11 col-lg-10 my-3 row container-fluid">
                
                                <div class="form-group row col-lg-6">
                                    <label for="date_debut_depot">Date début dépôt</label>
                                    <input class="form-control col-12 " type="datetime-local" id="date_debut_depot" name="date_debut_depot">
                                </div>
                
                                <div class="form-group row col-lg-5 offset-lg-1 ">
                                    <label for="date_fermeture_depot">Date fermeture dépôt</label>
                                    <input class="form-control col-12 " type="datetime-local" id="date_fermeture_depot" name="date_fermeture_depot">
                                </div>
                
                                <div class="form-group row col-lg-6">
                                    <label for="date_ouverture_depot">Date d\'ouverture</label>
                                    <input class="form-control col-12 " type="date" id="date_ouverture_depot" name="date_ouverture_depot">
                                </div>
                
                
                                <div class="form-group row col-lg-5 offset-lg-1 ">
                                    <label for="coefficient_depot">Coefficient</label>
                                    <input type="number" step="0.25" class="form-control col-12 " id="coefficient_depot" name="coefficient_depot" />
                                </div>
                
                            </div>
                
                
                
                            <div class="row col-3 col-sm-4 col-md-6 col-lg-8">
                                <button type="submit" class="btn btn-success mx-auto">Ouvrir</button>
                            </div>

                            <input type="text" class="d-none sr-only" id="token" name="token" value="'.$token.'">

                        </div>
                
                    </form>
                ';
                
                echo "<h3 class='text-center text-primary underline mt-4  col-11'>Dépôts ouverts </h3>";
                echo '<div class="my-3 mx-2 table-bg-white">';
                
                $this->afficherTableau(
                    $liste_depots,
                    array('ref_module', 'nom_support', 'date_debut_depot_exercice', 'date_fermeture_depot_exercice', 'nb_consultation_support' ),
                    '',
                    '',
                    array('Module', 'nom', 'Ouverture', 'Fermeture', 'consultations')
                );

                echo "</div>";
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur(new Exception("Pas un enseignant"), NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function afficher_acces_depot($token){
            $input_token = $this->inputToken($token);
            try{
                Enseignant::idEnseignantCourant();
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }

            echo '
                <div id="page-content-wrapper">
                    <div class="container-fluid">
                        <h1 class="my-3 text-center">Accéder aux dépôts des étudiants</h1>
                        <div class="container-fluid justify-content-center row">
                            <div class="row mx-auto container-fluid">
                                <form class=" border border-primary rounded col-12">
                                    <h4 class="mt-2  text-center">Choisir le dépôt à ouvrir !</h4>
                                    <div class="form-group">
                                        <label for="mail">Dépôts</label>
                                        <input type="text" class="form-control" placeholder="Ecrivez le nom/groupe du dépôt" required
                                            id="depot_a_lire" />
                                    </div>
                                    '.$input_token.'
                                </form>
                            </div>
                
                            <div class="container-fluid mt-4" id="tableau-depot">
                            </div>
                        </div>
                    </div>
                </div>
            ';
            
        }

        /*FIN ENSEIGNANT */

        /*
            Moodle Etudiant
        */

        public function afficherListeModules($liste_semestres){
            
            echo '<div class="container-fluid mx-auto col-lg-10 mt-3">';

            foreach($liste_semestres as $ref_semestre=>$semestre){
                echo '
                    <table class="table bg-white cursor-pointer">
                        <thead class="thead-dark">
                        <tr >
                            <th scope="col" class="collapse_th " collapse-target="'.$ref_semestre.'">'. $semestre['nom_semestre'].'</th>
                        </tr>
                        </thead>
                    <tbody class="collapse_target" style="cursor:pointer;display:none; width:100%" id="'.$ref_semestre.'" is_collapsed="true">
                ';
                foreach($semestre['modules'] as $module){
                    $nom_module = $module['ref_module'] . '-' . $module['nom_module']; 
                    echo '
                        
                        <tr class="module-moodle" onclick="window.location=\'index.php?module=moodle&action=liste_cours&ref_module='.$module['ref_module'].'\'">
                            <td> '.$nom_module.'</td>
                        </tr>
                    ';
                }
                echo '</tbody></table>';
            }

            echo '</div>';

        }


        public function afficherListeSupports($liste_cours, $liste_depots){
            $liste_cours_html = "";
            $liste_depots_html = "";

            if(count($liste_cours) > 0){
                $liste_cours_html .= '<ol class="fa-ul">';
                foreach($liste_cours as $cours){
                    $date_depot = date('d M', strtotime($cours['date_depot_support']));

                    $liste_cours_html .= '
                    <li class="mb-1">

                        <span class="fa-li">
                            <i class="far fa-file-alt"></i>
                        </span>

                        <div class="d-flex">
                            <a target="_blank" href="php/api/index.php?type=moodle&action=telecharger_cours&id_cours='.$cours["id_support"].'" class="lien_vers_cours">
                                <span>'.$cours["nom_support"].'</span>
                            </a>
                            <span class="flex-grow-1 in-between-dot"></span>
                            <span class="d-flex align-items-center text-center">'.$date_depot.'</span>
                        </div>

                    </li>
                    ';
                }
                $liste_cours_html .= '</ol>';
                
            }else{
                $liste_cours_html = "<p class='text-center text-danger'>Aucun cours n'a été trouvé </p>";
            }

            if(count($liste_depots)){
                $liste_depots_html = '<ol class="fa-ul text-primary">';
                
                foreach($liste_depots as $depot) {

                    if($depot['depot_est_fermer']){
                        $liste_depots_html .= '
                        <li class="mb-1">
                        
                            <span class="fa-li">
                                <i class="fas text-success fa-check-double"></i>
                            </span>
                            <a href="index.php?module=moodle&action=details_depot&id_depot='.$depot["id_depot_exercice"].'">
                                '.$depot["nom_support"].'
                            </a>
                        </li>
                        ';
                    
                    }else if($depot['depot_a_commencer']){
                        $liste_depots_html .= '
                            <li class="mb-1">
                                <span class="fa-li">
                                    <i class="far text-info fa-clock"></i>
                                </span>
                                <a href="index.php?module=moodle&action=details_depot&id_depot='.$depot["id_depot_exercice"].'">
                                    '.$depot["nom_support"].'
                                </a>
                            </li>
                        </a>
                        ';
                    }else{
                        $liste_depots_html .= '

                            <li class=" text-secondary mb-1">

                                    <span class="fa-li">
                                        <i class="far fa-window-close"></i>
                                    </span>
                                    '.$depot["nom_support"].'
                                </a>
                            </li>
                        
                        ';
                    }
                }

                $liste_depots_html .= "</ol>";
            }else{
                $liste_depots_html = "<p class='text-center text-danger'>Aucun dépôt à rendre </p>";
            }


            echo '
            <div class="container-fluid mx-auto col-lg-9 mt-3  cursor-pointer">
                <div class="moodle_list_depot  p-2 mb-4 ">
                    <h3 class="text-secondary underline text-center">Liste des cours</h3>
                    '.$liste_cours_html.'
                </div>
                <div class="moodle_list_depot p-2 mt-2 mt-4 mb-3">
                    <h3 class="text-secondary text-center underline">Liste des dépots</h3>
                    '.$liste_depots_html.'
                    </div>
            </div>
            
            ';
            
        }

        public function detailsDepotEtudiant($details_depot, $token){
            // print_r($details_depot);
            $input_token = $this->inputToken($token);

            $coefficient = $details_depot['coefficient_depot'] > 0 ? $details_depot['coefficient_depot'] : "Pas noté";
            
            $nom_groupe  = $details_depot['nom_groupe'];

            setlocale(LC_TIME, "fr_FR.utf8");
            
            $date_fermeture = ucfirst(strftime('%A %d %B %Y, %k:%M', strtotime($details_depot['date_fermeture_depot_exercice'])));

            $derniere_version_remise = $details_depot['nom_depot_etudiant'] ? $details_depot['nom_depot_etudiant'] : 'Vous n\'avez remis aucun travail pour ce dépôt ';

            $commentaire_etudiant = $details_depot['commentaire_depot_etudiant'] ? $details_depot['commentaire_depot_etudiant'] : "Vous n'avez pas encore déposé";
           
            if(!$details_depot['est_fermee']){
                $status_travail = $details_depot['date_depot_etudiant'] ? "Remis pour évaluation" : "Pas encore remis";
                echo '
                    <form class="container-fluid mx-auto col-lg-9 mt-3 mb-3" 
                        action="index.php?module=moodle&action=deposer_exercice&id_depot='.$details_depot["id_depot_exercice"].'"
                        method="post"
                        enctype="multipart/form-data"
                    >
                        '.$input_token.'
                        <div class=" mx-auto mt-3 mb-3 ">
                            <h3 class="text-center mb-3 underline">Dépôt : '.$details_depot["nom_support"].'</h3>
                    
                            <div class="border border-primary bg-white rounded mb-3">
                                <table class="table moodle-details-depot-table">
                                    <tbody>
                                        <tr>
                                            <td class="text-info">Support</td>
                                            <td>
                                                <i class="fas fa-file"></i>
                                                <a target="_blank" href="php/api/index.php?type=moodle&action=telecharger_cours&id_cours='.$details_depot["id_support"].'" class="lien_vers_cours">
                                                '.$details_depot["nom_support"].'
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-info">Statut du travail</td>
                                            <td>'.$status_travail.'</td>
                                        </tr>
                                        <tr>
                                            <td class="text-info">Coefficient dépôt</td>
                                            <td>'.$coefficient.'</td>
                                        </tr>
                    
                                        <tr>
                                            <td class="text-info">Groupe</td>
                                            <td>'.$nom_groupe.'</td>
                                        </tr>
                    
                                        <tr>
                                            <td class="text-info">Date finale de remise</td>
                                            <td>'.$date_fermeture.'</td>
                                        </tr>
                    
                                        <tr>
                                            <td class="text-info">Dernière version remise</td>
                                            <td>'.$derniere_version_remise.'</td>
                                        </tr>

                                        <tr>
                                            <td class="text-info">Votre Commentaire</td>
                                            <td>'.$commentaire_etudiant.'</td>
                                       </tr>

                    
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 border border-primary rounded">
                                <h4 class="mt-2  text-center">Déposez ici !</h4>
                                <div class="custom-file my-3">
                                    <input type="file" class="custom-file-input" id="fichier_depot_etudiant" name="fichier_depot_etudiant">
                                    <label class="custom-file-label" for="fichier_depot_etudiant">Choisir un fichier</label>
                                </div>
                            </div>
                        </div>
                        <div class="px-3 border border-primary rounded">
                            <div class="form-group">
                                <label for="commentaire" class="font-weight-bold">Commentaire</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row ml-1 mt-3 ">
                            <button type="submit" class="mx-auto btn btn-success">Valider</button>
                        </div>
                    </form>
                ';
            }else{
                $status_travail = $details_depot['note_depot'] ? "Corrigé et noté" : "En attente de correction";
                $note = "Votre rendu n'a pas encore été corrigé";
                $classe_note =  "";
                $commentaire_enseignant = $details_depot['commentaire_depot'];

                if($details_depot['note_depot']){
                    $note = $details_depot['note_depot'];

                    if($note > 10) {
                        $classe_note = 'success';
                    }else if($note == 10){
                        $classe_note = 'warning';
                    }else{
                        $classe_note = 'danger';
                    }
                }

                echo '
                <div class="container-fluid mx-auto col-lg-9 mt-3 mb-3">
                    <div class=" mx-auto mt-3 mb-3 ">
                        <h3 class="text-center mb-3">Dépôt : TP-01 Serveur DNS</h3>
                
                        <div class="border border-primary bg-white rounded mb-3">
                            <table class="table moodle-details-depot-table">
                                <tbody>
                                    <tr>
                                        <td class="text-info">Support</td>
                                        <td>
                                            <a target="_blank" href="php/api/index.php?type=moodle&action=telecharger_cours&id_cours='.$details_depot["id_support"].'" class="lien_vers_cours">
                                            <i class="fas fa-file"></i>
                                            '.$details_depot["nom_support"].'
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-info">Statut du travail</td>
                                        <td>'.$status_travail.'</td>
                                    </tr>
                                    <tr>
                                        <td class="text-info">Coefficient dépôt</td>
                                        <td>'.$coefficient.'</td>
                                    </tr>
                
                                    <tr>
                                        <td class="text-info">Groupe</td>
                                        <td>'.$nom_groupe.'</td>
                                    </tr>
                
                                    <tr>
                                        <td class="text-info">Date finale de remise</td>
                                        <td>'.$date_fermeture.'</td>
                                    </tr>
                
                                    <tr>
                                        <td class="text-info">Dernière version remise</td>
                                        <td>'.$derniere_version_remise.'</td>
                                    </tr>
                
                                    <tr>
                                        <td class="text-info">Votre Note</td>
                                        <td class="'.$classe_note.'">'.$note.'/20</td>
                                    </tr>
                
                                    <tr>
                                        <td class="text-info">Commentaire enseignant</td>
                                        <td class="text-primary">'.$commentaire_enseignant.'</td>
                                    </tr>
                
                
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                
                </div>
                ';
            }
        }

        public function ajouterControle($liste_controles, $token){
            $input_token = $this->inputToken($token);

            echo '
            <div class="container-fluid row px-0 mx-0 justify-content-center mb-3">
                <div class="mr-md-4 mx-1 col-md-5  pt-3 mt-2">
                    <h3 class="text-center text-primary">Ajouter un contrôle</h3>
                    <form action="index.php?module=moodle&action=charger_notes" method="post" enctype="multipart/form-data">
                        '.$input_token.'
                        <div class="form-group">
                            <label for="nom_controle">Nom</label>
                            <input class="form-control" id="nom_controle" name="nom_controle" required />
                        </div>
            
                        <div class="form-group">
                            <label for="date_controle">Date du contrôle</label>
                            <input type="date" class="form-control" id="date_controle" name="date_controle" required />
                        </div>
            
                        <div class="form-group">
                            <label for="module_controle">Module</label>
                            <select class="form-control select-module-enseignant" id="module_controle" name="module_controle"
                                required>
                            </select>
                        </div>
            
                        <div class="form-group">
                            <label for="coefficient_controle">Coefficient</label>
                            <input type="number" min="0" step="0.025" max="20" class="form-control" id="coefficient_controle" name="coefficient_controle"
                                required />
                        </div>
            
                        <div class="custom-file my-3">
                            <input type="file" class="custom-file-input" id="fichier_notes" name="fichier_notes" required>
                            <label class="custom-file-label" for="fichier_notes">Fichier Notes</label>
                        </div>
            
                        <div class="form-group">
                            <label for="module_controle">Séparateur</label>
                            <select class="form-control" id="separateur_fichier" name="separateur_fichier" required>
                                <option value=";">;</option>
                                <option value=",">,</option>
                                <option value="|">|</option>
                            </select>
                        </div>
            
                        <div class="row justify-content-center p-2 mx-0" style="border:2px solid beige">
                            <h5 class="text-center col-12 text-underline">Colonnes du fichier</h5>
                            <div class="form-group col-sm-4 col-xs-8">
                                <label for="col_pseudo">Pseudo</label>
                                <input type="number" min="0" class="form-control" name="col_pseudo" id="col_pseudo" required />
                            </div>
            
                            <div class="form-group col-sm-4 col-xs-8">
                                <label for="col_note">Note</label>
                                <input type="number" min="0" class="form-control" id="col_note" name="col_note" required />
                            </div>
            
                            <div class="form-group col-sm-4 col-xs-8">
                                <label for="col_commentaire">Commentaire</label>
                                <input type="number" min="0" class="form-control" id="col_commentaire" name="col_commentaire"
                                    required>
                            </div>
                        </div>
            
            
                        <div class="container-fluid row justify-content-center mt-3">
                            <button class="btn btn-success" type="submit">Valider</button>
                        </div>
            
            
                    </form>
            
                </div>
            
                <div class="col-md-6 mx-1  pt-3 mt-2 ">
                    <h3 class="text-center text-primary mb-4">Liste des contrôles papiers</h3>
                    ';

            $this->afficherTableau(
                $liste_controles,
                array('ref_module', 'nom_controle', 'coefficient_controle', 'date_controle'),
                'index.php?module=moodle&action=details_notes_etudiants&id_controle=',
                'id_controle',
                array('module', 'nom', 'coefficient', 'date')
            );
            
            echo    '</div></div>';
        }

        public function details_notes_etudiant($details_controle, $notes_controle, $token){
            $tableau = "";
            $input_token = $this->inputToken($token);

            foreach($notes_controle as $note){

                $tableau .= "<tr>";

                $tableau .= '<td class="align-middle">'.$note['pseudo_utilisateur'].'</td>';
                $tableau .= '<td class="align-middle">'.$note['nom_utilisateur'].'</td>';
                $tableau .= '<td class="align-middle">'.$note['prenom_utilisateur'].'</td>';
                $tableau .= '<td class="align-middle"><input name="notes['.$note["pseudo_utilisateur"].'][note]" class="mark-input form-control" type="number" min="0" max="20" step="0.025" value="'.$note['note_controle'].'" required></td>';
                $tableau .= '<td class="commentaire-td">
                                <textarea name="notes['.$note["pseudo_utilisateur"].'][commentaire]">'.$note['commentaire_controle'].'</textarea>
                            </td>
                            ';
                
                $tableau .= "</tr>";
            }


            echo '
            <form action="index.php?module=moodle&action=changer_notes_etudiants&id_controle='.$details_controle["id_controle"].'" method="POST">
                '.$input_token.'
                <h2 class="text-secondary text-center underline mt-2">Contrôle : '.$details_controle['nom_controle'].'</h2>
                <div class=" table-responsive pl-sm-4 pl-2 pr-2 pr-sm-4 mt-4">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <th>Pseudo</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Note</th>
                            <th class="commentaire-td">Commentaire</th>
                        </thead>
                        <tbody class="bg-white">

                            '.$tableau.'

                        </tbody>
                    </table>

                </div>

                <div class="container-fluid row justify-content-around">
                    <button name="modifier" type="submit" class="btn btn-primary mt-2 ">Modifier</button>
                    <button name="supprimer" type="submit" class="btn btn-danger mt-2">Supprimer</button>
                </div>


            </form>
            ';
        }


        public function notes_etudiant($liste_notes){
            echo '
            <div class="container-fluid row px-0 mx-0 pt-3" style="min-height:100vh; background-color: #f0f8ff9e">
                <div class="note-module col-12">
                    <table class="table-notes col-12">
            ';

            setlocale(LC_TIME, "fr_FR.utf8");

            foreach($liste_notes['modules'] as $notes_module){
                echo '
                    <tr class="text-info row-module">
                        <td class="" colspan="2">'.$notes_module["nom_module"].' ('.$notes_module["ref_module"].')</td>
                        <td class="">coef '.$notes_module["coefficient_module"].'</td>
                        <td class="">'.($notes_module["moyenne"] ? round($notes_module["moyenne"],2) : " - " ).'</td>
                    </tr>
                ';

                foreach($notes_module["liste_controles"] as $controle){
                    $date_controle = ucfirst(strftime("%A  %d  %G", strtotime($controle["date_controle"])));
                    echo '
                    <tr >
                        <td class="tab-indent">'.$date_controle.'</td>
                        <td class="text-left">'.$controle["nom_controle"].'</td>
                        <td class="">coef '.$controle["coefficient"].'</td>
                        <td class="">'.($this->validerNote($controle["note"]) ? round($controle["note"],2) : "-").'</td>
                    </tr>
                    ';
                }

                foreach($notes_module["liste_depots"] as $depot){
                    $date_depot = ucfirst(strftime("%A  %d  %G", strtotime($depot["date_depot"])));
                    echo '
                    <tr >
                        <td class="tab-indent">'.$date_depot.'</td>
                        <td class="text-left">'.$depot["nom_depot"].'</td>
                        <td class="">coef '.$depot["coefficient"].'</td>
                        <td class="">'.($this->validerNote($depot["note"]) ? round($depot["note"],2) : "-").'</td>
                    </tr>
                    ';
                }

                echo '  <tr style="height:20px;">
                            <td colspan="4"></td>
                        </tr>
                    ';
            }

            echo '
                <tr class="display-mark-row">
                    <td class="tab-indent display-mark-name" colspan="2">Moyenne calculée </td>
                    <td class="text-left display-mark" colspan="2">'.($this->validerNote($liste_notes["moyenne_calculee"]) ? round($liste_notes["moyenne_calculee"],2) : "-").' </td>
                </tr>';

            echo "</table></div></div>";
        }

        public function validerNote($note){
            return $note !== null || $note !== false;
        }
    }