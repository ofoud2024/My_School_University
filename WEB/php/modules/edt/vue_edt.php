<?php
    require_once __DIR__ . "/../../verify.php";

    class VueEdt{
        public function __construct(){
        }

        /*
                            <div class="container-fluid row mx-0 mb-3">
                
                        <div class="container row col-8 ml-2  edt-week-container ">
                        
                            <a class="previous_week">
                            <div class="edt-week-switcher text-center previous_week">&lt;</div>
                            </a>
                            <div class="edt-week-text px-2 semaine_courante"></div>
                            <a class="next_week">
                            <div class="edt-week-switcher text-center ">&gt;</div>
                            </a>
                        </div>
                    
                        <div class="container row col-4 ml-auto mr-0">
                            <select class="form-control" id="ref_semestre">
                            </select>
                        </div>

                
                    </div>

        */

        public function afficherEdt($modal_absences){
            echo '
                <div class="edt container-fluid px-0">
                
                    <div class="mx-4 edt-header-container">
                        <div class="edt-week-container ">
                            <a class="previous_week"><button class=""><i class="fas fa-chevron-left"></i></button></a>
                            <p class="text-center semaine_courante my-0 bg-dark text-white" ></p>
                            <a class="next_week"><button class=""><i class="fas fa-chevron-right"></i></button></a>
                        </div>

                        <div></div>



                        <div class="edt-semestre bg-light">
                            <select class="form-control" id="ref_semestre">
                            </select>
                        </div>

                        <div class="edt-absences ">
                            <button class="btn btn-dark text-white">Absences</button>
                        </div>
                    </div>

                    <div class="container-fluid mx-auto">
                        <h3 class="text-center text-success edt-week-name">
                            Semaine du
                            <span class="semaine_courante"></span> au
                            <span class="semaine_prochaine"></span>
                        </h3>

                    </div>
                
                    <div class="edt-body mx-4 my-5" id="edt-body">
                    </div>

                    <div id="edt-seance-container" class="cursor-pointer">
                
                    </div>
                </div>
                
                <div class="modal fade" id="modal_ajouter_seance" tabindex="-1" role="dialog" aria-labelledby="ajouterSeance"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ajouter/Modifier une Séance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label for="module">Module</label>
                                    <select class="form-control" id="module_seance">
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="nom_salle">Salle</label>
                                    <input type="text" class="form-control" id="nom_salle" />
                                </div>
            
                                <input class="d-none" id="id_seance" />
                                <input class="d-none" id="couleur" />
            
                                <div class="form-group">
                                    <label for="enseignant">Enseignant</label>
                                    <select class="form-control" id="enseignant">
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label for="groupe_seance">Groupe</label>
                                    <select class="form-control" id="groupe_seance">
                                    </select>
                                </div>
            
            
            
                                <div class="row">
                                    <div class="col">
                                        <label for="enseignant">Heure départ</label>
                                        <input type="datetime-local" id="depart_seance" class="form-control" placeholder="Heure départ">
                                    </div>
                                    <div class="col">
                                        <label for="enseignant">Durée (minutes)</label>
                                        <input type="number" step="10" id="duree_seance" class="form-control" placeholder="Durée">
                                    </div>
                                </div>
            
                            </form>
            
                        </div>
                        <div class="modal-footer row justify-content-center">
                            <button type="button" id="appliquer-modification-seance" class="btn btn-outline-success" >Appliquer</button>
                            <button type="button" class="btn btn-outline-danger" id="supprimer_seance" data-dismiss="modal">Supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
            '.$modal_absences.'      
          ';
        }


        public function afficherVueEtudiant($absences_etudiant, $somme_absence){
            $modal = $this->modal_absences_etudiant($absences_etudiant, $somme_absence);
            $this->afficherEdt($modal);

        }

        public function afficherVueEnseignant($liste_etudiants, $token){
            $modal = $this->modal_absence_enseignant($liste_etudiants, $token);
            $this->afficherEdt($modal);
        }


        private function modal_absence_enseignant($liste_etudiants, $token){
            $vue_liste_etudiants = "";
            
            foreach($liste_etudiants as $etudiant){
                $nom_complet = $etudiant["prenom_utilisateur"] . " " . $etudiant["nom_utilisateur"] . " (".$etudiant["pseudo_utilisateur"].")";
                $checked = $etudiant["est_absent"] ? "checked" : "";  
                $vue_liste_etudiants .= '
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" '.$checked.' name="absences['.$etudiant["pseudo_utilisateur"].']" id="'.$etudiant["pseudo_utilisateur"].'">
                    <label class="custom-control-label" for="'.$etudiant["pseudo_utilisateur"].'">'.$nom_complet.'</label>
                </div>
                ';
            }

            return '
            <div class="modal fade" id="modal_absences" tabindex="-1" role="dialog" aria-labelledby="absence"
            aria-hidden="true">
                <form class="modal-dialog" role="document" method="post" action="index.php?module=edt&action=appliquer_absences&token='.$token.'">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Absences pour la séance d\'AP3 </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                    
                        <div class="modal-body">
                            '.$vue_liste_etudiants.'
                        </div>
                        <div class="modal-footer row justify-content-center">
                            <button type="submit" class="btn btn-success" >Confirmer</button>
                        </div>
                </form>
            </div>
        </div>
            ';

        }

        private function absence_administrateur(){

        }

        private function modal_absences_etudiant($absences_etudiant, $somme_absence){
            $table_body = "";


            if(count($absences_etudiant) > 0) {
                foreach($absences_etudiant as $absence){
                    $date_seance = date('d-m-y',strtotime($absence['date_seance'])) . ' ' .$absence['heure_depart_seance'] ;
                    $icon = !$absence['absence_est_justifiee'] ? 
                                    '<td class="text-center text-danger"><i class="far fa-times-circle"></i></td>' :
                                    '<td class="text-center text-success"><i class="fas fa-check"></i></td>';

                    $table_body .= ' 
                        <tr>
                            <td>'.$absence["abreviation_module"].'</td>
                            <td>'. $date_seance . '</td>
                            '.$icon.'
                        </tr>
                    ';
                }
            }else{
                $table_body = '
                    <tr>
                        <td colspan="3">Vous n\'avez aucune absence</td>
                    </tr>
                ';
            }

            return '
            <div class="modal fade" id="modal_absences" tabindex="-1" role="dialog" aria-labelledby="absence"
            aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Absences de <span class="set_full_name" ></span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                    
                        <div class="modal-body">
                            <p class="text-left text-secondary"> Nombre d\'heures d\'absences justifiés: <span class="convert-duration" duration="'.$somme_absence['absences_justifies'].'"></span>  </p>

                            <div class="table-responsive edt-absence-table">
                                <table class="table table-bordered">
                                    
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Module</th>
                                            <th>Date séance</th>
                                            <th>Est justifié</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        '.$table_body.'
                                    </tbody>
                                </table>
                            </div>
                            
                            <p class="text-left text-danger"> Nombre d\'heures non justifiés: <span class="convert-duration" duration="'.$somme_absence['absences_non_justifies'].'"></span>  </p>

                        </div>
                        <div class="modal-footer row justify-content-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                </div>
            </div>
        </div>
            ';
        }
    }
?>