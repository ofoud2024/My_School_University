<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueEtudiant extends VueGenerique
    {
        public function __construct()
        {
        }

        public function afficher_etudiants($liste_etudiants, $token)
        {
            try{
                Utilisateur::possedeDroit('droit_creation_utilisateurs');
                
                echo '<h2 class="text-center text-dark underline mb-4 pt-2 underline">
                        Gestion des étudiants
                    </h2>';
                $input_token = $this->inputToken($token);
                $this->afficherTableau(
                    $liste_etudiants,
                    array('num_etudiant', 'nom_utilisateur', 'prenom_utilisateur', 'points_ets'),
                    'index.php?module=administration&type=etudiant&action=afficher_etudiant&num=',
                    'num_etudiant',
                    array('No étudiant', 'Nom', 'Prénom', 'Points ets')
                );

                echo '
                <form autocomplete="off" method="post" action="index.php?module=administration&type=etudiant&action=ajouter_etudiant">
                            <h4 class=" text-center mt-3 mb-1 underline">Ajout d\'un étudiant</h4>
                    <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="num_etudiant">Numéro étudiant</label>
                        <input type="text" class="form-control" id="num_etudiant" name="num_etudiant" placeholder="10NJ45OMLK" required />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pseudo_etudiant">Pseudo utilisateur</label>
                        <input type="text" class="form-control" id="pseudo_etudiant" name="pseudo_etudiant" placeholder="pseudo de l\'utilisateur"
                        required />
                    </div>
                    '.$input_token.'
                    </div>
                
                    <div class="container-fluid row justify-content-center">
                    <button type="submit" class="btn btn-success mb-2">Ajouter</button>
                    </div>
                </form>
                ';
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des étudiants")
                );            
            }
        }

        public function afficher_etudiant($detailsEtudiant, $num, $liste_semestres, $periode_courante, $token)
        {
            try{                
                Utilisateur::possedeDroit('droit_creation_utilisateurs');

                $select_semestre = "";

                $semestre_courant = null;

                $input_token = $this->inputToken($token);
                
                

                foreach($detailsEtudiant as $resultat_semestre){
                    if($resultat_semestre['date_debut'] === $periode_courante[0] && $resultat_semestre['date_fin'] === $periode_courante[1] ){
                        $semestre_courant = $resultat_semestre;
                        break;
                    }
                }

                $semestre_validee = ($semestre_courant && $semestre_courant['est_valide']) ? "checked" : "";
                $moyenne          = ($semestre_courant && $semestre_courant['moyenne'] != null) ? $semestre_courant['moyenne'] : "";

                foreach($liste_semestres as $semestre){
                    $selected = "";
                    if($semestre_courant['ref_semestre'] === $semestre['ref_semestre']){
                        $selected = "selected";
                    }
                    $select_semestre .= "<option ${selected} value='${semestre['ref_semestre']}'>${semestre['nom_semestre']}</value>";
                }
                

                echo "<h2 class='text-center underline'> Détails de l'étudiant $num</h2>";
    
                echo "<h3 class='text-center underline'> Semestres passés </h3>";

                $this->afficherTableau(
                    $detailsEtudiant,
                    array('moyenne', 'ref_semestre', 'date_debut', 'date_fin')
                );
    
                echo '
                <form class=" pb-2 " method="post" action="index.php?module=administration&type=etudiant&action=modifier_semestre_etudiant&num_etudiant='.$num.'">
                    <fieldset class="pb-2">
                        '.$input_token.'
                        <legend align="center" class="col-auto px-0">Semestre courant</legend>
                
                        <div class="form-row justify-content-center align-items-center ">
                            <div class="col-sm-4 col-11  text-md-center my-1">
                                <label for="ref_semestre_etudiant">Semestre actuel</label>
                            </div>
                            <div class="col-sm-5 col-11  my-1">
                                <select class="form-control" id="ref_semestre_etudiant" name="ref_semestre_etudiant">
                                    <option value="">------Choisir semestre------</option>
                                    '.$select_semestre.'
                                </select>
                            </div>
                            <div class="col-sm-3 col-11 justify-content-center row  my-1">
                                <button type="submit" class="btn btn-success">Modifier</button>
                            </div>
                        </div>

                    </fieldset>
                </form>

                <form class=" pb-2" method="post" action="index.php?module=administration&type=etudiant&action=modifier_moyenne_semestre&num_etudiant='.$num.'">
                    <fieldset class="pb-2">
                    '.$input_token.'

                        <legend align="center" class="col-auto px-0">Modification du semestre</legend>
                
                        <div class="form-row justify-content-center align-items-center">
                            <div class="col-sm-4 col-11 my-1  d-none d-md-block">
                                <div class="form-check text-center">
                                    <input '.$semestre_validee.' class="form-check-input" type="checkbox" id="semestre_est_valide" name="semestre_est_valide">
                                    <label class="form-check-label" for="semestre_est_valide">
                                        est validé
                                    </label>
                                </div>
                            </div>
            
                            <div class="col-sm-5  col-11 text-center my-1 ">
                                <input value="'.$moyenne.'" type="number" min="0" max="20" class="ml-md-3 form-control " placeholder="moyenne du semestre" id="moyenne_semestre" name="moyenne_semestre" />
                            </div>
                        
                            <div class="col-sm-4 my-1 col-11 pl-md-5 d-block d-md-none">
                                <div class="form-check">
                                <input '.$semestre_validee.' class="form-check-input" type="checkbox" id="semestre_est_valide" name="semestre_est_valide">
                                <label class="form-check-label" for="semestre_est_valide">
                                    est validé
                                </label>
                                </div>
                            </div>

        
                            <div class="col-sm-3 col-11 justify-content-center  ml-md-auto my-1 mx-0 row">
                                <button type="submit" class="btn btn-primary">Soumettre</button>
                            </div>
                        </div>
    


                </fieldset>
            </form>

                ';
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder aux détails de l'étudiant")
                );            
            }
        }


    }
