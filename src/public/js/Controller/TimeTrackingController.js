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

    searchField.addEventListener('focusout', function() {
        if(searchProjectTimeout != null) {
            clearTimeout(searchProjectTimeout);
            searchProjectTimeout = null;
        }

        setTimeout(
            function() { hideElement('#projectSearchResult'); },
            200
        );
    });
}