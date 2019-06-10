<?php

    require_once __DIR__ . "/../../verify.php";


    class VueMenuInterne
    {
        public function afficherMenu()
        {
            $estAdmin = $this->estAdmin();

            echo '
                <div class="container-fluid mt-2 row justify-content-around menu-interne">
                    <div class=" col-lg-6 ">
                        <div class="titre-menu mx-0 px-0 container-fluid">
                            <h2 class="text-center py-4 px-2">Espace numérique de travail</h2>
                        </div>
                    </div>

                    <div class="col-lg-5 row justify-content-end">
                        <div class="col-auto mb-2">
                            <div class="btn-group btn-menu" role="group" aria-label="Menu de navigation">
                                ';
            if($estAdmin)
                echo '<a href="index.php?module=administration">
                        <button type="button" class="btn btn-dark">Administration</button>
                      </a>';
            echo '
                                <a href="index.php?module=edt">
                                    <button type="button" class="btn btn-dark">Edt</button>
                                </a>
                                <a href="index.php?module=mails">
                                    <button type="button" class="btn btn-dark">Mails</button>
                                </a>
                                <a href="index.php?module=moodle">
                                    <button type="button" class="btn btn-dark">Cours</button>
                                </a>
                            </div>
                        </div>

                        <div class="container-fluid formation-container">
                            <h3 class="text-center bg-light py-2">DUT INFO S3 APP</h3>
                        </div>
                    </div>
                </div>
                
                ';
        }

        public function estAdmin(){
            try{
                Utilisateur::possedeDroit("droit_creation_utilisateurs");
                return true;
            }catch(NonAutoriseException $e){}

            try{
                Utilisateur::possedeDroit("droits_creation_modules");
                return true;
            }catch(NonAutoriseException $e){}

            try{
                Utilisateur::possedeDroit("droit_creation_cours");
                return true;
            }catch(NonAutoriseException $e){}

            try{
                Utilisateur::possedeDroit("droit_creation_groupes");
                return true;
            }catch(NonAutoriseException $e){}

            try{
                Utilisateur::possedeDroit("droit_modification_absences");
                return true;
            }catch(NonAutoriseException $e){}
            
            try{
                Utilisateur::possedeDroit("droit_modification_droits");
                return true;
            }catch(NonAutoriseException $e){}
            
            return false;
        }

    }
