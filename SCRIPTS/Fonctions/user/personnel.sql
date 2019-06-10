CREATE OR REPLACE FUNCTION setHeuresTravail(id_personnel_param integer, heures_travail_param integer, debut date, fin date) returns void as $$
BEGIN
    IF(heures_travail_param >= 0) THEN
        
        perform * from heures_travail where id_personnel = id_personnel_param and date_debut = debut;
    
        IF(NOT FOUND) THEN
            INSERT INTO heures_travail values (heures_travail_param, debut, fin, id_personnel_param);
        ELSE
            UPDATE heures_travail set heures_travail = heures_travail_param where date_debut = debut and id_personnel = id_personnel_param;
        END IF;

    ELSE
        RAISE EXCEPTION 'Les heures de travail ne peuvent pas être négatives';
    END IF;

END;
$$LANGUAGE PLPGSQL;