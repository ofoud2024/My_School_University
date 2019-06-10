<?php
    define("EXCEPTIONS_CODE", array(
        '3001'=>"Le droit de visualisation des heures de travail est obligatoire pour pouvoir les modifier",
        '3002'=>"Le nom d'utilisateur n'éxiste pas, merci de vérifier les informations saisies",
        '3003'=>"Le mot de passe saisie est incorrect",
        '3004'=>"Création de sous groupe refusée. Un groupe ne peut pas être un sous groupe de lui même",
        '3005'=>"Création de sous groupe refusée. Un groupe ne peut pas être sous groupe d'un de ses groupes fils"
    ));

    define("AVAILABLEMODULES", array(
        "connexion",
        "administration",
        "edt",  
        "moodle",
        "mail",
        "profile"
    ));

    define("SALT_KEY", '$6$rounds=5000$jljA7uJicFbvUpjjI0tifoMON4E1fDVnvQMxq5y7O7F3V7gmJFTXbybSTEOLz3XHKnN6ddx1m9i9XEo5W51LYif1FBrbt6UhIKON9dGZpRdQqm947QXICOVVhqvkfAPIiNKWpV1DNgyrcddbZZptPYrclSCKVF6LTBoLzr9JJIBIiutk73WFFYtCsJZOfPNFpS1Cvla8sR4r2YZQLhGZ2lriZJEFosg3mF0kVayKjBS031xyiy1VDgaw3UqlpWVFwYGvMv5sQ0CKNZTlRvn0Y5zyTplogB4M7RkaaJjE8tZ0jTT69TZ422Ur7NM1PMXMqr4GOGp8uf7SSX2kPYPmjzLq3woHldZ68lGuRmA6C7ZR2YY4pzpVHBYqSwRhbIre8BmFutgv3ZzjC41O4XQoEN6FYdB6acn9ou2XODnMwHIlHw5RSLGksMe7w92ef2BU4fKSgBrYKZmx72NwiEUIGaboDa94Y8IvgGByzVtmCom8uTPaPp4nDYiPTpF61dfmSZtIi5L250EUgLa7nzJ4imRxUgNDVSVkEvPMYyr6yLRJ3VMjcznqYixJnhHbtEuXZyFTEGjtXr48hgXPz4RbFwrLVF3wiwJ259gBEgKM9Iuc105puuKpqPA7FlfDJAPMOZFowZkbvPb2TmEnVlAw4E2GJgZcYlYP9bIwjlIriVGnhE4mHGgKMizwgz4fHoMVKh3D359JBaHMm26HfOyca3tQQuCT7d0rHPeEodagLTV8aVJDjFO3oDatR6cvUytNQQfOsWbOJWOF70HAfcYNROvzKBQl77yE$');

    define("LONG_CLE_RECUPERATION", 100);

//----------------------------------ERROR MESSAGES----------------------------------//
    define("DEFAULT_ERROR_MESSAGE", "Une erreur inconnue est survenue");
    define("DEFAULT_ERROR_TITLE", "Erreur");

    define("DEFAULT_API_ERROR_MESSAGE", "Le site est en maintenance actuellement, merci de revenir plus tard");
    define("DEFAULT_API_ERROR_TITLE", "Erreur API");

    define("NOT_CONNECTED_EXCEPTION_TITLE", "ConNOT_ENOUGH_ROLES_TITLEnexion requise");
    define("NOT_CONNECTED_EXCEPTION_MESSAGE", "Vous devez être connecter pour accéder à cette page");

    define("INVALID_ACTION_ERROR_TITLE", "Action invalide");
    define("INVALID_ACTION_ERROR_MESSAGE", "Cette action est invalide pour ce module");

    define("INVALID_TYPE_ERROR_MESSAGE", "Le type fourni dans la requête est inconnu");

    define("NOT_ENOUGH_PARAM_TITLE", "Paramètres insuffisants");
    define("NOT_ENOUGH_PARAM_MESSAGE", "Vous devez fournir le paramètre {{parametre}}");

    define("DATABASE_ERROR_MESSAGE", "La base de données est en maintenance, Merci de réessayer plus tard");
    define("DATABASE_ERROR_TITLE", "Erreur BD");

    define("UNDEFINED_USERNAME_ERROR", "Merci de bien indiquer le nom d'utilisateur");
    define("UNDEFINED_PASSWORD_ERROR", "Merci de bien indiquer le mot de passe");

    define("PASSWORD_LENGTH_ERROR_TITLE", "Mot de passe invalid");
    define("PASSWORD_LENGTH_ERROR_MESSAGE", "Le mot de passe doit être composé d'au moins 8 caractères");

    define("FILE_COPY_ERROR", "Erreur lors de la copie du fichier");

    define("NOT_ENOUGH_ROLES_TITLE", "Droits insuffisants");
    define("NOT_ENOUGH_ROLES_MESSAGE", "Vous n'avez pas assez de droit pour éffectuer l'action de {{action}}");
   
    define("NOT_FOUND_ERROR_TITLE", "Element introuvable");
    define("NOT_FOUND_ERROR_MESSAGE", "Nous n'avons pas pû trouver {{element}} correspondant à cet identifiant : {{id}}");

    define("UPDATE_ERROR_TITLE", "Modification échouée");
    define("UPDATE_ERROR_MESSAGE", "Mise à jour échouée pour {{type}} identifié par : {{id}}");
    define("INSERT_ERROR_TITLE", "Insertion échouée");
    define("INSERT_ERROR_MESSAGE", "Nous n'avons pas pû insérer {{type}}");
    define("DELETE_ERROR_MESSAGE", "Nous n'avons pas pû supprimer {{type}} identifié par {{id}}");
    define("DELETE_ERROR_TITLE", "Suppression échouée");
    
    define("TOKEN_CREATION_ERROR_TITLE", "Erreur interne");
    define("TOKEN_CREATION_ERROR_MESSAGE", "Erreur lors de la génération du Token");


    define("INVALID_COUNTRY_TITLE", "Pays invalide");
    define("INVALID_COUNTRY_MESSAGE", "Le pays que vous avez saisi n'a pas été reconnu");

    
    //Etudiants
    define("NO_STUDENT_NUM_ERROR_MESSAGE",  "Vous devez obligatoirement fournir un numéro d'étudiant");
    define("STUDENT_LIST_ERROR_MESSAGE",    "Une erreur est survenue lors de la récupération de la liste des étudiants");
    define("STUDENT_INSERT_ERROR_MESSAGE",  "L'insertion de cet utilisateur a échouée");
    define("STUDENT_DELETE_ERROR_MESSAGE",  "Nous n'avons pas pû supprimer l'étudiant");
    define("STUDENT_NO_EXIST_ERROR_TITLE", "Étudiant inéxistant");
    define("STUDENT_NO_EXIST_ERROR_MESSAGE", "Il n'existe aucun étudiant avec le numéro fourni");
    define("STUDENT_DETAILS_ERROR_MESSAGE", "Nous n'avons pas pû récupérer les détails de cet utilisateur");
    define("NO_USER_ID_ERROR_MESSAGE","Vous devez obligatoirement fournir un identifiant d'utilisateur");
    define("INVALID_MARK_ERROR_TITLE", "Note invalide");
    define("INVALID_MARK_ERROR_MESSAGE", "Cette note {{note}} est invalide, elle doit être entre 0 et 20");
    define("NOT_STUDENT_ERROR_TITLE", "Etudiant requis");
    define("NOT_STUDENT_ERROR_MESSAGE", "Vous devez être un étudiant pour accéder à cette page");

    //Moodle
    define('NOT_TEACHER_ERROR_TITLE', "Enseignant requis");
    define('NOT_TEACHER_ERROR_MESSAGE', "Vous ne pouvez pas accéder à ce module si vous n'êtes pas enseignant");
    
    define('ADD_LESSON_ERROR_TITLE', "Échec ajout de cours");
    define('ADD_LESSON_ERROR_MESSAGE', "L'ajout du support {{support}} a échouée");

    define('OPEN_DEPOSIT_ERROR_TITLE', "Échec ouverture dépôt");
    define('OPEN_DEPOSIT_ERROR_MESSAGE', "L'ouverture du dépôt {{nom_depot}} a échouée");

    define('NOT_YOUR_DEPOSIT_ERROR_TITLE', "Dépôt invalide");
    define('NOT_YOUR_DEPOSIT_ERROR_MESSAGE', "Ce dépôt n'existe pas ou n'est pas le votre");

    define("STUDENT_FILE_UPLOAD_FAILED_TITLE", "Dépôt échouée");
    define("STUDENT_FILE_UPLOAD_FAILED_MESSAGE", "Attention, votre dépôt n'a pas été pris en compte.{{newLine}}Veuillez réessayer plus tard ou demandez de l'aide à votre professeur");

    define("INVALID_MARKS_FILE_TITLE", "Fichier de notes invalide");
    define("INVALID_MARKS_FILE_MESSAGE", "Le fichier \"{{name}}\" est invalid.{{newLine}}Veuillez vérifier que vous avez bien indiqué le bon emplacement des colonnes du fichier");

    define("MARKS_UPDATE_ERROR_TITLE", "Mise à jour de notes échouée");


    //MODULE
    define("UNKNOWN_MODULE_EXCEPTION_TITLE", "Module inconnu");
    define("UNKNOWN_MODULE_EXCEPTION_MESSAGE" , "Aucun module ne porte la référence {{ref}}");



    //FICHIERS
    define('DOWNLOAD_ERROR_TITLE', 'Télechargement échoué');
    define('DOWNLOAD_ERROR_MESSAGE', 'Nous n\'avons pas pû envoyer ce fichier, merci de réessayer plus tard');
    

    //Semestre
    define("INVALID_SEMESTER_PERIOD_TTILE", "Période invalide");
    define("INVALID_SEMESTER_PERIOD_MESSAGE", "La période de semestre doit être soit 1, soit 2");


    /*
    MAILS
    */
    define("MAIL_SEND_ERROR_TITLE", "Échec d'envoi du mail");
    define("MAIL_SEND_ERROR_MESSAGE", "Nous n'avons pas pû envoyer ce mail aux destinataires");
    
    define("MAIL_DELETE_ERROR_TITLE", "Échec de suppression du mail");
    define("MAIL_DELETE_ERROR_MESSAGE", "Nous n'avons pas pû supprimer ce mail. Veuillez réessayer plus tard");

    /*
        EDT
    */ 

    define("EDT_INSERT_ERROR_MESSAGE", "Nous n'avons pas pû ajouter cette séance. Vérifiez que c'est bien une séance valide");
    define("EDT_INSERT_ERROR_TITLE", "Ajout de séance échouée");
    
    define("EDT_UPDATE_ERROR_MESSAGE", "La modification de la séance a été refusée");
    define("EDT_DELETE_ERROR_MESSAGE", "La suppression de la séance a échouée");

    /*
    PROFILE
    */
    define("PASSOWRD_CONFIRMATION_ERROR_TITLE", "Confirmation du mot de passe incorrecte");
    define("PASSOWRD_CONFIRMATION_ERROR_MESSAGE", "La confirmation du mot de passe doit être la même");