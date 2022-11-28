class DlhComboBoxControl {
    constructor(selector, arg) {
        this.selector = selector;
        this.containerElem = null;
        this.searchInputElem = null;
        this.searchResultElem = null;
        this.comboboxValueElem = null;
        this.searchProjectTimeout = null;

        this._onSearchInputPaste = this.onSearchInputPaste.bind(this);
        this._onSearchInputKeyup = this.onSearchInputKeyup.bind(this);
        this._onSearchInputFocusout = this.onSearchInputFocusout.bind(this);

        if(typeof arg !== 'undefined') {
            this.onChange = arg.onChange ? arg.onChange : null;
        }

        if(this.initSelectors()) {
            this.initActions();
        }
    }

    initSelectors() {
        this.containerElem = document.querySelector(this.selector);

        if(this.containerElem !== null) {
            this.searchInputElem = this.containerElem.querySelector('.dlh-combobox-search');
            this.searchResultElem = this.containerElem.querySelector('.dlh-combobox-result');
            this.comboboxValueElem = this.containerElem.querySelector('.dlh-combobox-value');
            return true;
        }

        return false;
    }

    initActions() {
        this.initSearchInputPasteEvent();
        this.initSearchInputKeyupEvent();
        this.initSearchInputFocusoutEvent();
    }

    initSearchInputPasteEvent() {
        if(this.searchInputElem === null) {
            return;
        }

        this.searchInputElem.addEventListener('paste', this._onSearchInputPaste);
    }

    onSearchInputPaste(event) {
        var searchTerm = event.clipboardData.getData('Text');
        this.searchProjects(searchTerm);
    }

    initSearchInputKeyupEvent() {
        if(this.searchInputElem === null) {
            return;
        }

        this.searchInputElem.addEventListener('keyup', this._onSearchInputKeyup);
    }

    onSearchInputKeyup(event) {
        var searchTerm = event.target.value;
        this.searchProjects(searchTerm);
    }

    initSearchInputFocusoutEvent() {
        if(this.searchInputElem === null) {
            return;
        }

        this.searchInputElem.addEventListener('focusout', this._onSearchInputFocusout);
    }

    onSearchInputFocusout() {
        if(this.searchProjectTimeout != null) {
            clearTimeout(this.searchProjectTimeout);
            this.searchProjectTimeout = null;
        }

        setTimeout(
            () => { this.hideElement(this.searchResultElem); },
            200
        );
    }

    resetTimeout() {
        if(this.searchProjectTimeout !== null) {
            clearTimeout(this.searchProjectTimeout);
            this.searchProjectTimeout = null;
        }
    }

    searchProjects(searchTerm) {
        this.resetTimeout();

        this.searchProjectTimeout = setTimeout(
            () => {
                this.searchProjectsAjax(searchTerm);
            },
            750
        );
    }

    searchProjectsAjax(searchTerm) {
        this.searchProjectsClearResults();
        this.searchProjectTimeout = null;

        axios.post('/projects/ajaxSearch', {
            searchTerm: searchTerm
        })
        .then(function(response) {
            try {
                var searchResults = response.data.searchResult;
                var length = searchResults.length;

                if(length == 0) {
                    this.searchProjectsAddResult("", "Keine Ergebnisse");
                    this.makeInputReadonly(this.searchResultElem);
                } else {
                    this.makeInputWritable(this.searchResultElem);

                    for(var i = 0; i < length; i++) {
                        let result = searchResults[i];
                        this.searchProjectsAddResult(result.id, result.name);
                    }

                    this.initProjectSearchResultActionHandlers();
                }

                this.markContainerAsOpened(this.containerElem);
                this.setSearchResultSize(length);
                this.showElement(this.searchResultElem);
            } catch(e) {
                alert('An error occured, please try again. Check console for error details.');
                console.log(e);
            }
        }.bind(this))
        .catch(function(error) {
            alert('(2)An error occured, please try again. Check console for error details.');
            console.log(error);
        });
    }

    searchProjectsClearResults() {
        var results = this.searchResultElem.querySelectorAll('option');
    
        if(results.length > 0) {
            for (var i = (results.length - 1); i >= 0; i--) {
                results[i].remove();
            }
    
            this.hideElement(this.searchResultElem);
        }
    }
    
    searchProjectsAddResult(id, title) {
        var opt = document.createElement('option');
        opt.value = id;
        opt.innerHTML = title;
        this.searchResultElem.appendChild(opt);
    }
    
    showElement(elem) {
        elem.style.display = 'block';
    }
    
    hideElement(elem) {
        elem.style.display = 'none';
    }
    
    initProjectSearchResultActionHandlers() {
        var results = this.searchResultElem.querySelectorAll('option');
    
        for (var i = 0, j = results.length; i < j; i++) {
            let elem = results[i];

            this._onSearchResultOptionClick = this.onSearchResultOptionClick.bind(this);
            elem.addEventListener('click', this._onSearchResultOptionClick);
        }
    }

    onSearchResultOptionClick(event) {
        this.searchInputElem.value = event.target.text;
        this.comboboxValueElem.value = event.target.value;
        this.hideElement(this.searchResultElem);
        this.markContainerAsClosed(this.containerElem);
        
        if(typeof this.onChange === 'function') {
            this.onChange(event.target.value, event.target.text);
        }
    }
    
    markContainerAsOpened(elem) {
        elem.classList.add('opened');
    }
    
    markContainerAsClosed(elem) {
        elem.classList.remove('opened');
    }
    
    setSearchResultSize(size) {
        if(!Number.isInteger(size)) {
            size = 2;
        }
        
        // Height must be 2 at least, to show an opened dropdown.
        if(size < 2) {
            size = 2;
        } else if(size > 10) {
            size = 10;
        }
    
        this.searchResultElem.size = size;
    }
    
    makeInputReadonly(elem) {
        elem.setAttribute("disabled", "disabled");
    }
    
    makeInputWritable(elem) {
        elem.removeAttribute("disabled");
    }
}