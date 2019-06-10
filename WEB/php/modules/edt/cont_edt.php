<?php

    require_once __DIR__ . "/../../common/Date.php";
    require_once __DIR__ . "./../../common/cont_generique.php";
    require_once __DIR__ . "/modele_edt.php";
    require_once __DIR__ . "/vue_edt.php";

    class ContEdt extends ContGenerique{
        private $vue;
        private $modele;

        public function __construct(){
            $this->vue = new VueEdt();
            $this->modele = new ModeleEdt($this);
        }

        public function afficherEdt(){
            
            $semaine = isset($_GET['semaine']) ? htmlspecialchars($_GET['semaine']) : date('Y-m-d'); 
                
            if(!Date::validateDate($semaine)){
                $semaine = date('Y-m-d');
                header('Location: index.php?module=edt&semaine='.$semaine);
                exit(0);
            }

            if(!isset($_GET['semestre']) || !isset($_GET['semaine'])){
                $semestre = $this->modele->semestreEtudiant();
                header('Location: index.php?module=edt&semaine='.$semaine."&semestre=".$semestre);
                exit(0);
            }else{
                $this->afficherVueEdt();
            }
            
        }


        public function appliquer_absences(){
            $this->validerToken();
            
            $absences = isset($_POST["absences"]) && is_array($_POST["absences"]) ? $_POST["absences"] : array();
            
            $etudiants_absent = array();

            foreach($absences as $pseudo=>$etat){
                if($etat === "on"){
                    array_push($etudiants_absent, htmlspecialchars($pseudo)); 
                }
            }

            $this->modele->modifier_absences($etudiants_absent);
            
            header('Location: index.php?module=edt');
        }

        private function afficherVueEdt(){
            $semaine = isset($_GET['semaine']) ? htmlspecialchars($_GET['semaine']) : $this->pasAssezDeParametres('semaine'); 
            $semestre= isset($_GET['semestre']) ? htmlspecialchars($_GET['semestre']) : $this->pasAssezDeParametres('semestre');
            $status = $this->modele->getStatus();

            if($status === "etudiant"){
                $absences = $this->modele->absencesEtudiant();
                $this->vue->afficherVueEtudiant($absences["liste_absences"], $absences["somme_absences"]);
            }else if($status === "enseignant"){
                $token = $this->genererToken();
                $this->vue->afficherVueEnseignant($this->modele->etudiantsSeance(), $token);
            }else{
                $this->afficherVueGenerale();
            }            
        }



    }

?>