function formatValueToView(value, additionalClass = "") {
    if (value === undefined || value === null || value === "") {
        return "-";
    }

    if (typeof value === "boolean") {
        if (value) {
            return <i className={"text-center fas fa-check " + additionalClass}></i>;
        } else {
            return <i className={"far fa-times-circle " + additionalClass}></i>;
        }
    } else {
        return value;
    }
}

function downloadButton(link) {
    return (
        <a target="_blank" href={link}>
            <button class="text-center btn btn-outline-success btn-sm" >
                Télecharger
          <i class="fas fa-download"></i>
            </button>
        </a>
    );
}
