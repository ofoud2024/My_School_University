--Cette fonction renvoie vrai si un utilisateur fait partie du groupe
--Un utilisateur fait partie d'un groupe du moment où il fait partie de l'un dessous_goup
CREATE OR REPLACE FUNCTION public.utilisateur_appartient_a_groupe(id_utilisateur_courant int, num_groupe int) RETURNS BOOLEAN AS $$
DECLARE
	sous_groupe integer;
BEGIN

	perform * from membres_de_groupe where id_groupe = num_groupe and id_utilisateur = id_utilisateur_courant;

	IF(FOUND) THEN
		RETURN TRUE;
	END IF;

	for sous_groupe in (select id_groupe_fils from sous_groupe where id_groupe_parent = num_groupe) LOOP
		
		perform * from membres_de_groupe where id_groupe = sous_groupe and id_utilisateur = id_utilisateur_courant;
		
		IF(FOUND) THEN
			return TRUE;
		ELSIF(utilisateur_appartient_a_groupe(id_utilisateur_courant, sous_groupe)) THEN
			return TRUE;
		END IF;

	END LOOP;

	RETURN FALSE;
END;
$$ LANGUAGE PLPGSQL;



CREATE OR REPLACE FUNCTION public.est_un_sous_groupe(id_groupe_enfant_param int, id_groupe_parent_param int) RETURNS BOOLEAN AS $$
DECLARE
	sous_groupe integer;
BEGIN
	perform * from sous_groupe where id_groupe_parent = id_groupe_parent_param and id_groupe_fils = id_groupe_enfant_param;

	IF(FOUND) THEN
		RETURN TRUE;
	END IF;

	for sous_groupe in (select id_groupe_fils from sous_groupe where id_groupe_parent = id_groupe_parent_param) LOOP
		
		IF(est_un_sous_groupe(id_groupe_enfant_param, sous_groupe)) THEN
			return TRUE;
		END IF;

	END LOOP;

	RETURN FALSE;
END;
$$ LANGUAGE PLPGSQL;