/*
Test GROUPES
*/
--Test de la méthode est_un_sous_groupe(int, int)
insert into droits values('droit_test', false, false, false, false, false, false, false, false);
insert into utilisateur values (5000, 'test1', '', '', '', '', true, '', now(), '', '', now(), 'MAR', 'droit_test', '92230');
	