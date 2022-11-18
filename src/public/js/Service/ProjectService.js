var searchProjectTimeout = null;

function searchProjects(searchTerm)
{
    if(searchProjectTimeout !== null) {
        clearTimeout(searchProjectTimeout);
    }

    searchProjectTimeout = setTimeout(
        function() {
            searchProjectsClearResults();

            searchProjectTimeout = null;

            axios.post('/projects/ajaxSearch', {
                searchTerm: searchTerm
            })
            .then(function(response) {
                try {
                    var searchResults = response.data.searchResult;
                    var length = searchResults.length;

                    for(var i = 0; i < length; i++) {
                        var result = searchResults[i];

                        searchProjectsAddResult(result.id, result.name);
                    }

                    initProjectSearchResultActionHandlers();
                    
                } catch(e) {
                    alert('Fehler...');
                    console.log(e);
                }
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        750
    );
}

function searchProjectsClearResults() {
    var results = document.querySelectorAll('#projectSearchResult option');

    if(results.length > 0) {
        for (var i = (results.length - 1); i >= 0; i--) {
            results[i].remove();
        }

        showElement('#projectSearchResult');
    }
}

function searchProjectsAddResult(id, name) {
    var searchResultContainer = document.getElementById('projectSearchResult');

    var opt = document.createElement('option');
    opt.value = id;
    opt.innerHTML = name;

    searchResultContainer.appendChild(opt);
}

function showElement(selector) {
    document.querySelector(selector).style.display = 'block';
}

function hideElement(selector) {
    document.querySelector(selector).style.display = 'none';
}

function initProjectSearchResultActionHandlers() {
    var results = document.querySelectorAll('#projectSearchResult option');

    for (var i = 0; i < results.length; i++) {
        let elem = results[i];

        elem.addEventListener('click', function() {
            document.getElementById('projectSearch').value = elem.text;
            document.getElementById('projectId').value = elem.value;
            hideElement('#projectSearchResult');
        });
    }
}