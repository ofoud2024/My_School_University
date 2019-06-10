class MailView extends React.Component {
    constructor(props) {
        super(props);
        this.componentDidUpdate = this.componentDidUpdate.bind(this);
        this.componentDidMount = this.componentDidMount.bind(this);
        this.chargerReponses = this.chargerReponses.bind(this);
        this.envoyerReponse = this.envoyerReponse.bind(this);
        this.state = {
            reponses: []
        }
        this.reponse_area = React.createRef();
    }

    componentDidMount() {
        this.chargerReponses();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (prevProps !== this.props) {
            this.chargerReponses();
        }
    }


    envoyerReponse() {
        let that = this;
        let message = this.reponse_area.current.value;
        if (message.length > 0) {
            $.post("php/api/index.php?type=mail&action=ajouter_reponse", {
                id_mail: this.props.mail.id_mail,
                message_reponse: that.reponse_area.current.value
            }).done(function () {
                that.reponse_area.current.value = "";
                that.chargerReponses()
            });
        } else {
            toastr.warning("Votre message est vide");
        }
    }

    chargerReponses() {
        let that = this;
        $.get(
            "php/api/index.php",
            {
                type: "mail",
                action: "reponses_mail",
                id_mail: this.props.mail.id_mail
            }
        ).done(function (data) {
            let json_data = JSON.parse(data);
            that.setState({
                reponses: json_data
            });
        });
    }

    render() {
        let mail = this.props.mail;

        let sender = mail.prenom_utilisateur + " " + mail.nom_utilisateur + " (" + mail.pseudo_utilisateur + ") ";

        let destination = this.props.utilisateurs.map(
            function (utilisateur) {
                return utilisateur.pseudo_utilisateur;
            }
        ).join(", ");

        if (destination.length > 0 && this.props.groupes.length > 0)
            destination += ", ";

        destination += this.props.groupes.map(
            function (groupe) {
                return groupe.nom_groupe;
            }
        ).join(", ");

        let reponses = this.state.reponses.map(
            function (reponse) {
                let nom_complet = reponse.prenom_utilisateur + " " + reponse.nom_utilisateur + " (" + reponse.pseudo_utilisateur + ")";
                return (
                    < div className="reponse-mail" >
                        <h3 className="reponse-nom">{nom_complet}</h3>
                        <p className="reponse-text">{reponse.contenu_reponse}</p>
                    </div >
                );
            }
        );

        let piece_jointe = mail.nom_piece_jointe && mail.nom_piece_jointe.length ? (
            <a target="_blank" href={"php/api/index.php?type=mail&action=telecharger_piece_jointe&id_mail=" + mail.id_mail}>
                <i className="fas fa-paperclip"></i>{mail.nom_piece_jointe}
            </a>
        ) : "";

        return (
            <div >
                <h3 className="message-full-title">{mail.sujet_mail}</h3>
                <div className="message-user-details">
                    <h4><span className="text-secondary">From: </span>{sender}</h4>
                    <h4><span className="text-secondary">To: </span>{destination}</h4>
                    <h4><span className="text-secondary">Date: </span>{mail.date_envoi_mail.split('-').reverse().join('/')}</h4>
                </div>
                <div className="message-full-content">
                    {mail.message_mail}
                    {reponses}
                </div>

                <div className="piece-jointe-mail">
                    {piece_jointe}
                </div>


                <div className="message-reply">
                    <textarea ref={this.reponse_area} className="form-control" placeholder="Ecrivez une réponse"></textarea>
                    <div className="container-fluid row justify-content-start mt-2">
                        <button className="btn btn-outline-primary  btn-sm" onClick={this.envoyerReponse}>
                            Répondre <i className="fas fa-reply"></i>
                        </button>
                    </div>
                </div>
            </div>

        );
    }
}