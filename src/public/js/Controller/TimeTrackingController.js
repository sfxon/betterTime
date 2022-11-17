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

    searchField.addEventListener('paste', function(event) {
        var searchTerm = event.clipboardData.getData('Text');
        searchProjects(searchTerm);
    });

    searchField.addEventListener('keyup', function() {
        var searchTerm = this.value;
        searchProjects(searchTerm);
    });
}