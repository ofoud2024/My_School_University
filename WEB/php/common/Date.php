<?php
    class Date
    {
        public function __constructor()
        {
        }

        /*
            - Renvoie le mois courant
        */
        public static function getMonth()
        {
            return date('n');
        }

        /*
            - Renvoie l'année courante
        */
        public static function getYear()
        {
            return date('Y');
        }

        /*
            -Renvoie la date correspondante à la période courante:
            -Les deux périodes possibles sont : 
                -Premier semestre : YYYY-09-01 => (YYYY + 1)-02-01
                -Deuxième semestre: YYYY-02-01 => YYYY-07-01
            -return Json : {
                debut: date,
                fin: date
            }
        */
        public static function getPeriodeCourante()
        {
            $date_debut = "";
            $date_fin = "";
            
            $mois = self::getMonth();
            $annee = self::getYear();

            if($mois >= 8){
                $date_debut = $annee . "-09-01";
                $date_fin   = ($annee + 1) ."-02-01";
            }else if($mois >= 2){
                $date_debut = $annee ."-02-01" ;
                $date_fin   = $annee. "-07-01" ;
            }else{
                $date_debut = ($annee - 1) . "-09-01";
                $date_fin   =  $annee . "-02-01";
            }

            return array("debut"=>$date_debut, "fin"=>$date_fin);
        }

        public static function validateDate($date, $format = 'Y-m-d')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        }
        
    }
