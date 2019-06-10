<h2
  class="text-center text-dark underline mb-4 pt-2 "
  style="text-decoration:underline"
>
  Ajouter un utilisateur
</h2>

<form
  class="pb-2"
  method="post"
  action="index.php?module=administration&type=utilisateur&action=inscription_utilisateur"
>
  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="nom">Nom</label>
      <input
        type="text"
        class="form-control"
        id="nom"
        name="nom"
        placeholder="Toto"
        required
      />
    </div>
    <div class="form-group col-md-4">
      <label for="prenom">Prenom</label>
      <input
        type="text"
        class="form-control"
        id="prenom"
        name="prenom"
        placeholder="Titi"
        required
      />
    </div>
    <div class="form-group col-md-4 ">
      <label for="date_naissance">Date de naissance</label>
      <input
        type="date"
        class="form-control"
        id="date_naissance"
        name="date_naissance"
        placeholder="31/01/2018"
        required
      />
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="email">Email</label>
      <input
        type="email"
        class="form-control"
        id="email"
        name="email"
        placeholder="email@domain.com"
        required
      />
    </div>
    <div class="form-group col-md-6">
      <label for="mot_de_passe">Mot de Passe</label>
      <input
        type="password"
        class="form-control"
        id="mot_de_passe"
        name="mot_de_passe"
        placeholder="*********"
        required
      />
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="civilite">civilite</label>
      <select id="civilite" name="est_homme" class="form-control" required>
        <option value="1" selected>Monsieur</option>
        <option value="0">Madame</option>
      </select>
    </div>
    <div class="form-group col-md-8">
      <label for="addresse">Address</label>
      <input
        type="text"
        class="form-control"
        id="addresse"
        name="addresse"
        placeholder="Votre adresse"
        required
      />
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="ville">Ville</label>
      <input
        type="text"
        placeholder="Votre ville"
        class="form-control"
        id="ville"
      />
    </div>
    <div class="form-group col-md-4">
      <label for="code_postal">Code postal</label>
      <input
        placeholder="Code postal"
        type="text"
        class="form-control"
        id="code_postal"
        name="code_postal"
        required
      />
    </div>
    <div class="form-group col-md-4">
      <label for="pays_naissance">Pays naissance</label>
      <input
        id="pays_naissance"
        list="pays"
        placeholder="Pays de naissance"
        type="text"
        name="pays_naissance"
        class="form-control"
        required
      />
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="tel">Numéro de telephone</label>
      <input
        type="tel"
        maxlength="10"
        class="form-control"
        id="tel"
        name="tel"
        placeholder="0610203040"
        required
      />
    </div>

    <div class="form-group col-md-6"><label for="droits">Droits</label>