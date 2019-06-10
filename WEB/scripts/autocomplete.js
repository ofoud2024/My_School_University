if (
  initVars.url.module === "administration" &&
  initVars.url.type === "utilisateur" &&
  (initVars.url.action === "modification" ||
    initVars.url.action === "afficherCreationUtilisateur")
) {
  var villes = [];

  $("input#ville")
    .flexdatalist({
      selectionRequired: true,
      minLength: 0,
      visibleProperties: ["nom_ville", "code_postal_ville"],
      searchIn: ["nom_ville", "code_postal_ville"],
      valueProperty: "nom_ville",
      data: "php/api/index.php?type=utilisateur&action=ville"
    })
    .on("select:flexdatalist", function (instance, value, options) {
      $("input#code_postal").flexdatalist("value", value.code_postal_ville);
    });

  $("input#code_postal")
    .flexdatalist({
      selectionRequired: true,
      minLength: 0,
      visibleProperties: ["code_postal_ville", "nom_ville"],
      searchIn: ["code_postal_ville", "nom_ville"],
      valueProperty: "code_postal_ville",
      data: "php/api/index.php?type=utilisateur&action=ville"
    })
    .on("select:flexdatalist", function (instance, value, options) {
      $("input#ville").flexdatalist("value", value.nom_ville);
    });

  $("input#pays_naissance").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: ["nom_pays", "code_pays"],
    searchIn: ["nom_pays", "code_pays"],
    valueProperty: "nom_pays",
    data: "php/api/index.php?type=utilisateur&action=pays"
  });
}

if (
  initVars.url.module === "administration" &&
  initVars.url.type === "personnel" &&
  initVars.url.action === "liste_personnels"
) {
  $("input#pseudo").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "pseudo",
    searchIn: "pseudo",
    valueProperty: "pseudo",
    cache: false,
    data: "php/api/index.php?type=utilisateur&action=pseudo_personnel"
  });
}

if (
  initVars.url.module === "administration" &&
  initVars.url.type === "groupe" &&
  initVars.url.action === "afficher_modification"
) {
  var id_groupe = new URL(window.location.href).searchParams.get("id");

  $("input#pseudo_utilisateur").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "pseudo_utilisateur",
    searchIn: "pseudo_utilisateur",
    valueProperty: "id_utilisateur",
    cache: false,
    data: "php/api/index.php?type=groupe&action=utilisateurs&id=" + id_groupe
  });

  $("input#groupe_fils").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "nom_groupe",
    searchIn: "nom_groupe",
    valueProperty: "id_groupe",
    cache: false,
    data: "php/api/index.php?type=groupe&action=sous_groupes&id=" + id_groupe
  });
}

if (
  initVars.url.module === "administration" &&
  initVars.url.type === "module" &&
  initVars.url.action === "afficher_module"
) {
  var reference = new URL(window.location.href).searchParams.get("id");

  $("input#pseudo_enseignant").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "pseudo_utilisateur",
    searchIn: ["pseudo_utilisateur", "nom_utilisateur", "prenom_utilisateur"],
    valueProperty: "id_enseignant",
    cache: false,
    data:
      "php/api/index.php?type=utilisateur&action=enseignants_possibles&module=" +
      reference
  });

  $("input#groupe_fils").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "nom_groupe",
    searchIn: "nom_groupe",
    valueProperty: "id_groupe",
    cache: false,
    data: "php/api/index.php?type=groupe&action=sous_groupes&id=" + id_groupe
  });
}

if (
  initVars.url.module === "administration" &&
  initVars.url.type === "etudiant" &&
  initVars.url.action === "liste_etudiant"
) {
  $("input#pseudo_etudiant").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "pseudo_utilisateur",
    searchIn: "pseudo_utilisateur",
    valueProperty: "id_utilisateur",
    cache: false,
    data: "php/api/index.php?type=utilisateur&action=pseudo_etudiant"
  });
}



$.get("php/api/index.php?type=semestre&action=liste_semestres", function (data) {
  result = JSON.parse(data);
  $(".semestre_select").each(function () {
    $(this).html("");
  });

  result.forEach(function (semestre) {
    $(".semestre_select").each(function () {
      let innerHTML =
        $(this).html() +
        "<option value='" +
        semestre.ref +
        "'>" +
        semestre.nom +
        "</option>";

      $(this).html(innerHTML);
    });
  });
});


if (
  initVars.url.module === "moodle" &&
  initVars.url.action === "acces_depot"
) {
  $("input#depot_a_lire").flexdatalist({
    selectionRequired: true,
    minLength: 1,
    visibleProperties: ["nom", "groupe"],
    searchIn: ["nom", "groupe"],
    valueProperty: "id",
    cache: false,
    data: "php/api/index.php?type=moodle&action=depots_enseignant"
  });
}




if (
  initVars.url.module === "edt"
) {
  $("input#nom_salle").flexdatalist({
    selectionRequired: true,
    minLength: 0,
    visibleProperties: "nom_salle",
    searchIn: "nom_salle",
    valueProperty: "nom_salle",
    cache: false,
    data: "php/api/index.php?type=salle&action=liste_salles"
  });
}


if (
  initVars.url.module === "mail" &&
  initVars.url.action === "afficher_envoyer_mail"
) {
  $("input#utilisateurs_destinataire").flexdatalist({
    minLength: 0,
    visibleProperties: ["nom", "prenom"],
    searchIn: ["pseudo", "nom", "prenom"],
    valueProperty: "id",
    cache: true,
    multiple: "multiple",
    data: "php/api/index.php?type=utilisateur&action=liste_utilisateurs"
  });

  $("input#groupes_destinataire").flexdatalist({
    minLength: 0,
    visibleProperties: "nom_groupe",
    searchIn: "nom_groupe",
    valueProperty: "id_groupe",
    cache: true,
    multiple: "multiple",
    data: "php/api/index.php?type=groupe&action=liste_groupes"
  });
}

if (
  initVars.url.module === "moodle"
) {
  $.get("php/api/index.php?type=moodle&action=modules_enseignant").done(function (data) {

    let json_data = JSON.parse(data);
    let item = $(".select-module-enseignant");
    let html = "<option value='' >Vous n'êtes responsable d'aucun module</option>";

    if (json_data.length > 0)
      html = "<option value=''>----Choisissez un module---- </option>";
    json_data.forEach(function (module) {
      html += "<option value='" + module.ref_module + "'> " + module.nom_module + "</option>";
    })

    item.html(html);

  });

}
