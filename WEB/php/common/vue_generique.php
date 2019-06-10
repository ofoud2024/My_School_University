<?php

    require_once __DIR__ . "/../verify.php";

    class VueGenerique
    {
        public function __construct()
        {
        }

        /*
            - @Deprecated
            - Il est déconseillé d'encore utiliser cette fonction
            - Cette fonction ne sera pas supprimée car elle est encore utilisé par d'autres classes
        */
        public function afficherListeDroits($droits, $value = '')
        {
            echo '<select id="droits" value="'.$value.'" name="droits" class="form-control" required>';

            foreach ($droits as $droit) {
                $selected = '';
                if($droit === $value)
                    $selected  = 'selected';
                echo "<option value='${droit}' ${selected}>${droit}</option>";
            }

            echo '</select>';
        }



        /*
            -Permet de transformer un tableau d'élements en liste
            -@param JSON items: Le tableau d'objets à transformer en liste
            -@param String key : La clé de l'attribut à afficher.
            -@param String class: La classe à appliquer aux éléments affichés
            -@param String empty_message : Le message à afficher si le tableau est vide ou n'est pas valide
        */
        public function toListItems($items, $key, $class = '', $empty_message = '')
        {
            $html = '';

            if (is_array($items) && count($items) > 0) {
                foreach ($items as $item) {
                    $html .= '<li class="'.$class.'">'.$item[$key].'</li>';
                }
            } else {
                $html = '<li class="text-secondary text-center '.$class.'">'.$empty_message.'</li>';
            }

            return $html;
        }

        /*
            -Donne une représentation graphique aux condtions en utilisant des icons
            -@param boolean cond : La condition a évaluée.
            -@param String additionalClass: La classe css à ajouter à l'icon
        */
        public function showCond($cond, $additionalClass = '')
        {
            if ($cond) {
                return "<i class=' text-center fas fa-check ${additionalClass}'></i>";
            } else {
                return "<i class='far fa-times-circle ${additionalClass}'></i>";
            }
        }

        /*
            -Génère un champs du formulaire qui contient le token
            -@param String token : Le token qui sert à la validation du formulaire
        */
        public function inputToken($token){
            $input = "<input type='text' class='sr-only d-none' value='".$token."' name='token' id='token' />";
            return $input;
        }

                /*
            - Transforme un tableau d'objets en un tableau de suppression
            - Un tableau de suppression comporte un bouton supprimer.
            
            - @param Json tableau : Le tableau des objets à afficher.
            - @param String[] cles: Les clés dans l'ordre de l'objet à afficher.
            - @param String cle_suppression : La clé utilisé dans le lien de suppression
            - @param String debut_lien_suppression: Le lien qui permet de supprimer la ligne séléctionnée. A ce lien, il est ajouté la valeur de la clé de suppression
            - @param Fonction condition_suppression: La condition a vérifiée pour afficher une lien. Si aucune condition n'est fourni alors toutes les lignes sont affichées
            
            - @return String le tableau en HTML.  
        */
        public function afficherTableauSuppression($tableau, $cles, $cle_suppression, $debut_lien_suppression, $enTete = null, $condition_suppression = null, $classe_enTete = 'thead-dark')
        {
            if ($enTete == null) {
                $enTete = $cles;
            }

            $headerHTML = "<tr>";
            
            foreach ($enTete as $cleEntete) {
                $headerHTML .= "<th scope='col'>${cleEntete}</th>";
            }
            $headerHTML .= "<th scope='col'>supprimer</th>";

            $headerHTML .= '</tr>';


            $htmlBody = $this->transformerEnTableauSuppression($tableau, $cles, $cle_suppression, $debut_lien_suppression, $condition_suppression);

            echo '
            <div class="table-responsive small-table">
                <table class="data-table  text-center table table-striped table-hover table-bordered">
                    <thead class="'.$classe_enTete.'">'.$headerHTML.'</thead>
                    <tbody>'.$htmlBody.'</tbody>
                </table>
            </div>';
        }


        /*
            - Renvoie le corps du tableau de suppression, regardez la méthode ci-dessus
            - Un tableau de suppression comporte un bouton supprimer.
            
            - @param Json tableau : Le tableau des objets à afficher.
            - @param String[] cles: Les clés dans l'ordre de l'objet à afficher.
            - @param String cle_suppression : La clé utilisé dans le lien de suppression
            - @param String debut_lien_suppression: Le lien qui permet de supprimer la ligne séléctionnée. A ce lien, il est ajouté la valeur de la clé de suppression
            - @param Fonction condition_suppression: La condition a vérifiée pour activer ou pas le bouton de suppression. 
                Si aucune condition n'est fourni alors tous les boutons de suppression sont activés.
            
            - @return String le tableau en HTML.  
        */
        public function transformerEnTableauSuppression($tableau, $cles, $cle_suppression, $debut_lien_suppression, $condition_suppression = null)
        {
            $html = "";

            foreach ($tableau as $ligne) {
                $html .= "<tr>";
                
                foreach ($cles as $cle) {
                    if(is_bool($ligne[$cle])){
                        $html .= "<td>" . $this->showCond($ligne[$cle]) . "</td>";
                    }
                    else if ($ligne[$cle] !== null) {
                        $html .= "<td>${ligne[$cle]}</td>";
                    } else {
                        $html .= "<td>-</td>";
                    }
                }

                if (!$condition_suppression || $condition_suppression($ligne)) {
                    $html .= "<td><a href='${debut_lien_suppression}${ligne[$cle_suppression]}'>
                                <button class='btn btn-sm btn-outline-danger px-2 py-0'>Supprimer</button>
                            </a></td>";
                } else {
                    $html .= "<td>
                                <button disabled class='btn btn-sm btn-outline-danger px-2 py-0'>Supprimer</button>
                              </td>";
                }

                $html .= "</tr>";
            }

            return $html;
        }



        /*

            - Cette fonction permet d'afficher un tableau,

            - @param JSON $data : Un tableau des objets à afficher dans le tableau
            - @param String[] $keys : Les clés des éléments qui seront afficher dans le tableau
            - @param String link_first_part: Le début du lien qui sera associé au clique sur la ligne du tableau
            - @param String link_key : La clé utlisée pour indiquée quelle partie du tableau 'Data' est utilisé dans le lien
            - @param String header : La liste des noms de colonnes
            - @param String header_class : La classe qui sera appliqué à l'entête
            
            - @return String : Le tableau en HTML
        */


        public function afficherTableau($data, $keys, $link_first_part = '', $link_key = null, $header = null, $header_class = 'thead-dark', $table_id = '')
        {
            if ($header == null) {
                $header = $keys;
            }

            $headerHTML = "<tr>";
            
            foreach ($header as $headerData) {
                $headerHTML .= "<th scope='col'>${headerData}</th>";
            }

            $headerHTML .= '</tr>';


            $htmlBody = "";


            foreach ($data as $row) {
                if ($link_key != null) {
                    $htmlBody .= "<tr onclick=\"document.location = '${link_first_part}${row[$link_key]}'\">";
                } else {
                    $htmlBody .= "<tr>";
                }
                
                foreach ($keys as $key) {
                    if(is_bool($row[$key])){
                        $htmlBody .= "<td>" . $this->showCond($row[$key]) . "</td>";
                    }
                    else if ($row[$key] !== null) {
                        $htmlBody .= "<td>${row[$key]}</td>";
                    } else {
                        $htmlBody .= "<td>-</td>";
                    }
                }

                $htmlBody .= "</tr>";
            }

            echo '
            <div class="table-responsive small-table">
                <table id="'.$table_id.'" class="data-table  text-center table table-striped table-hover table-bordered">
                    <thead class="'.$header_class.'">'.$headerHTML.'</thead>
                    <tbody class="bg-white">'.$htmlBody.'</tbody>
                </table>
            </div>';
        }
    }
