<?php
    /*
        Vérifie si le tableau associative contient toute les clés
        -@param keys: Les clés qu'on veut vérifier
        -@param array: Le tableau où l'on veut vérifier l'éxistance des clés
        -@return boolean: Vrai si le tableau $array contient toute les clés, Faux sinon
    */
    function checkArrayForKeys($keys, $array)
    {
        foreach ($keys as $key) {
            if (!isset($array[$key]) || $array[$key] === false) {
                return false;
            }
        }

        return true;
    }


    /*
        Transforme un tableau associative en un tableau normale
        -@param keysOrder: L'ordre des clés dans le tableau
        -@param array : Le tableau associative que l'on veut transformer
        -@Warning: On ne vérifie pas l'éxistance de toutes les clés dans cette fonction
        -@return: Le tableau normal
    */
    function associativeToNumArray($keysOrder, $array)
    {
        $value_array = array();

        foreach ($keysOrder as $key) {
            array_push($value_array, $array[$key]);
        }
        
        return $value_array;
    }



    /*
        Génère une chaîne de caractère aléatoire
    */
    function randomString($length)
    {
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
        $strlength = strlen($characters);
        
        $random = '';
        
        for ($i = 0; $i < $length; $i++) {
            $random .= $characters[rand(0, $strlength - 1)];
        }
    
        return $random;
    }


    //Retourne la valeur se trouvant dans l'indice de recherche
    //Sinon retourne la valeur notFoundValue
    //Le tableau doit être multiDimensionnel

    function includesAt($multiDimArray, $searchIndex, $resultIndex, $search, $notFoundValue = false)
    {
        if (is_array($multiDimArray) && count($multiDimArray) > 0 &&  is_array($multiDimArray[0])) {
            foreach ($multiDimArray as $arr) {
                if ($arr[$searchIndex] == $search) {
                    return $arr[$resultIndex];
                }
            }
        }

        return $notFoundValue;
    }


    function ExceptionHandler($e){
        ErrorHandler::afficherErreur($e);
    }
    //On définit le gestionnaire d'éxception par défaut pour toutes les exceptions non gérées
    set_exception_handler('ExceptionHandler');