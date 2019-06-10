CREATE OR REPLACE FUNCTION valider_new_seance() RETURNS TRIGGER AS $$

DECLARE
    duration_minute integer;
    day_index integer;
    time_hour integer;
BEGIN
    duration_minute = extract(minute from new.duree_seance);
    --Vérification de la durée
    IF(duration_minute % 10 != 0) THEN
        RAISE EXCEPTION 'Durée invalide %', new.duree_seance  USING ERRCODE = '22000';
    END IF;

    --Vérification de la date
    day_index = extract(dow from new.date_seance); 
    IF(day_index in (0, 6)) THEN
        RAISE EXCEPTION 'Date invalide %', new.date_seance  USING ERRCODE = '22000';
    END IF;

    --Vérification de l'heure de départ
    time_hour = extract(hour from new.heure_depart_seance);
    IF(time_hour < 8 or time_hour > 19) THEN
        RAISE EXCEPTION 'Heure de départ invalide %', new.heure_depart_seance  USING ERRCODE = '22000';
    END IF;

    --Vérification du groupe de la séance
    perform * from seance 
        where  
            (
                (date_seance + heure_depart_seance::time - Interval '00:00:01', duree_seance::time::interval) overlaps 
                (new.date_seance + new.heure_depart_seance::time::interval, new.duree_seance ::time + Interval '00:00:01')
            )
            and id_groupe = new.id_groupe
            and id_seance != new.id_seance;

    IF (FOUND) THEN 
        RAISE EXCEPTION 'ce groupe est déjà dans une autre séance %', new.id_groupe  USING ERRCODE = '22000';
    END IF;

    --Vérification si un enseignant est déjà occupé avec un module différent
    perform * from seance 
        where (
                (date_seance + heure_depart_seance::time - Interval '00:00:01', duree_seance::time::interval) overlaps 
                (new.date_seance + new.heure_depart_seance::time::interval, new.duree_seance ::time + Interval '00:00:01')
            )
            and id_seance != new.id_seance
            and id_enseignant = new.id_enseignant
            and ref_module != new.ref_module;

    IF (FOUND) THEN 
        RAISE EXCEPTION 'Cet enseignant est occupé à cette date %', new.id_enseignant  USING ERRCODE = '22000';
    END IF;

    --Vérification si un cours n'est pas déjà en cours dans la même salle
    perform * from seance 
        where (
                (date_seance + heure_depart_seance::time - Interval '00:00:01', duree_seance::time::interval) overlaps 
                (new.date_seance + new.heure_depart_seance::time::interval, new.duree_seance ::time + Interval '00:00:01')
            )
            and id_seance != new.id_seance
            and nom_salle = new.nom_salle
            and ref_module != new.ref_module;

    IF (FOUND) THEN 
        RAISE EXCEPTION 'Cet salle est reservée pour un autre module %', new.ref_module USING ERRCODE = '22000';
    END IF;

    --Séance validée
    RETURN NEW;
END;
$$ LANGUAGE PLPGSQL;


CREATE TRIGGER trig_modif_seance 
    BEFORE UPDATE OR INSERT ON seance 
    FOR EACH ROW
    EXECUTE PROCEDURE valider_new_seance();
