CREATE OR REPLACE FUNCTION modifier_seance(
    id integer,
    date_depart date,
    heure_depart time without time zone,
    duree time without time zone,
    groupe integer,
    enseignant integer,
    module varchar,
    salle varchar
) returns INTEGER as $$

declare
    new_id_seance integer;
begin
    if(id < 0) then --alors on ajoute
        insert into seance values(
            default,
            heure_depart,
            duree,
            module,
            groupe,
            salle,
            enseignant,
            date_depart
        ) returning id_seance into new_id_seance;

    else
        update seance set 
            heure_depart_seance = heure_depart,
            duree_seance = duree,
            ref_module = module,
            id_groupe = groupe, 
            nom_salle = salle,
            id_enseignant = enseignant,
            date_seance = date_depart
        where id_seance = id
        returning id_seance into new_id_seance;

    end if;
    
    IF(new_id_seance is null) THEN 
        RAISE EXCEPTION 'Modification ou ajout refusée' using ERRCODE = '22000' ;
    END IF;

    RETURN new_id_seance;
end;
$$ language plpgsql;