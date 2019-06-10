
function chargerDepots(id_depot) {
    $.get(
        "php/api/index.php",
        {
            type: "moodle",
            action: "depots_etudiants",
            id: id_depot
        }
    ).done(function (data) {
        let liste_depots = JSON.parse(data);
        ReactDOM.render(
            <TableauDepots liste_depots={liste_depots} id_depot={id_depot} />,
            document.getElementById("tableau-depot")
        );
    });
}


class TableauDepots extends React.Component {
    constructor(props) {
        super(props);
        this.cols = ["etudiant", "commentaire etudiant", "note", "télécharger", "commentaire", "modifier"];
    }

    render() {

        let liste_depots = this.props.liste_depots.length > 0 ? this.props.liste_depots.map(function (depot) {
            return <ColDepotEtudiant depot={depot} />
        }) : <td colSpan="6">Aucun étudiant n'a encore déposé pour ce dépôt</td>;

        return (
            <div className="container-fluid">
                <div className="table-responsive small-table">
                    <table className=" text-center bg-light table table-striped table-hover table-bordered">
                        <thead className="thead-dark">
                            <th>etudiant</th>
                            <th>commentaire etudiant</th>
                            <th style={{ minWidth: "80px" }}>note</th>
                            <th >télécharger</th>
                            <th>commentaire</th>
                            <th>modifier</th>
                        </thead>
                        <tbody>
                            {
                                liste_depots
                            }
                        </tbody>
                    </table>
                </div>

                <div className="container-fluid row justify-content-center">
                    <a href={"index.php?module=moodle&action=supprimer_depot&id_depot=" + this.props.id_depot + "&token=" + initVars.token}>
                        <button type="button" className="btn btn-danger">Supprimer</button>
                    </a>
                </div>
            </div>

        )
    }
}

class ColDepotEtudiant extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            modify_disabled: true,
            note: this.props.depot.note_depot,
            commentaire: this.props.depot.commentaire_enseignant
        };
        this.note_input = React.createRef();
        this.commentaire_input = React.createRef();
        this.toggleModify = this.toggleModify.bind(this);
        this.modifierNote = this.modifierNote.bind(this);
    }

    toggleModify() {
        this.state.modify_disabled = false;
        this.setState(this.state);
    }

    modifierNote() {

        let note = this.note_input.current.value;
        let commentaire = this.commentaire_input.current.value;
        let that = this;

        $.get(
            "php/api/index.php",
            {
                type: "moodle",
                action: "modifier_note",
                etudiant: this.props.depot.num_etudiant,
                depot: this.props.depot.id_depot_exercice,
                note: note,
                commentaire: commentaire
            }
        ).done(function () {

            that.state = {
                note: note,
                commentaire: "Ok",
                modify_disabled: true
            }

            that.setState(that.state);
        });

    }

    render() {
        let depot = this.props.depot;
        return (
            <tr >
                <td className="align-middle">{depot.pseudo_utilisateur}</td>
                <td className="align-middle" style={{ maxWidth: "150px", overflowY: "scroll" }}>{formatValueToView(depot.commentaire_etudiant)}</td>
                <td className="align-middle" onClick={this.toggleModify}>{
                    this.state.modify_disabled ? formatValueToView(this.state.note) :
                        <input ref={this.note_input} type='text' className='form-control' defaultValue={this.state.note} />
                }</td>
                <td className="align-middle">{downloadButton("php/api/index.php?type=moodle&action=telecharger_depot&etudiant=" + depot.num_etudiant + "&depot=" + depot.id_depot_exercice)}</td>
                <td className="align-middle">
                    <textarea defaultValue={this.state.commentaire} ref={this.commentaire_input}></textarea>
                </td>
                <td className="align-middle">
                    <button type="button" onClick={this.modifierNote} className="btn btn-outline-primary btn-sm">
                        Modifier
                    </button>
                </td>
            </tr >
        );
    }
}