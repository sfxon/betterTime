function initActions() {
    initSearchProjectAction();
}

initActions();

/* Function Definitions */
function initSearchProjectAction() {
    var searchField = document.getElementById('projectSearch');

    if(null == searchField) {
        return;
    }

    searchField.addEventListener('change', function() {
        var searchTerm = this.value;
        searchProjects(searchTerm);
    });

    searchField.addEventListener('keyup', function() {
        var searchTerm = this.value;
        searchProjects(searchTerm);
    });
}