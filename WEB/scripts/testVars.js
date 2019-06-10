var liste_seances = [
    { id_seance: 1, couleur: "#c8b4fa", heure_depart_seance: "13:00:00", duree_seance: "02:00:00", jour_seance: "2018-12-21", groupe_seance: "TPA2", module_seance: "méthodologie", enseignant: "AR", salle: "B0-06" },
    { id_seance: 2, couleur: "#90ec28", heure_depart_seance: "17:00:00", duree_seance: "01:00:00", jour_seance: "2018-12-18", groupe_seance: "S2", module_seance: "Probabilité", enseignant: "PB", salle: "B1-11" },
    { id_seance: 3, couleur: "#c8b4fa", heure_depart_seance: "08:00:00", duree_seance: "01:00:00", jour_seance: "2018-12-18", groupe_seance: "TD2", module_seance: "Mathématique", enseignant: "AB", salle: "B0-01" },
    { id_seance: 4, couleur: "#f37a14", heure_depart_seance: "16:00:00", duree_seance: "00:30:00", jour_seance: "2018-12-19", groupe_seance: "TPA1", module_seance: "BD1", enseignant: "Myl", salle: "D1-12" },
    { id_seance: 5, couleur: "grey", heure_depart_seance: "09:00:00", duree_seance: "09:00:00", jour_seance: "2018-12-17", groupe_seance: "S2", module_seance: "modélisation", enseignant: "AR", salle: "Amphi1" }
]

var types_seance = {
    "AMPHI": 1,
    "TD": 3,
    "TP": 6
}

var groupes_seance = {
    "TD": ["TD1", "TD2", "TD5"],
    "TP": ["TPA1", "TPA2", "TPB1", "TPB2", "TPC1", "TPC2"]
}

var sous_groupes = {
    "S2": ["TD1", "TD2", "TD5", "TPA1", "TPA2", "TPB1", "TPB2", "TPC1", "TPC2"],
    "TD1": ["TPA1", "TPA2"]
}