window.addEventListener('DOMContentLoaded', (event) => {
    // Project selection javascript.
    const onProjectComboBoxChange = (value, text) => {
        document.querySelector('#accordionProject .accordion-project-title').textContent = text;
    }

    const dlhProjectComboBox = new DlhComboBoxControl(
        '#projectSearchContainer',
        {
            onChange: onProjectComboBoxChange
        }
    );

    // Init project selector quick-selection-items.
    initProjectQuickSelectActions();
    initProjectQuickResetAction();

    // Time-Selection Javascript.
    const accordionTime = document.getElementById('accordionTime');

    const dateTimeStartInput = document.getElementById('starttime');
    const dateTimeEndInput = document.getElementById('endtime');

    const dateTimeStart = new tempusDominus.TempusDominus(
        dateTimeStartInput,
        {
            display: {
                sideBySide: true
            },
        }
    );

    const dateTimeEnd = new tempusDominus.TempusDominus(
        document.getElementById('endtime'),
        {
            display: {
                sideBySide: true
            },

        }
    );

    dateTimeStartInput.addEventListener('change', (e) => {
        betterTimeEndDialog__updateTimeHeading();
        
        dateTimeEnd.updateOptions({
            restrictions: {
                minDate: e.detail.date,
            },
        });
    });

    dateTimeEndInput.addEventListener('change', (e) => {
        betterTimeEndDialog__updateTimeHeading();

        dateTimeStart.updateOptions({
            restrictions: {
                maxDate: e.detail.date,
            },
        });
    });

    function betterTimeEndDialog__updateTimeHeading() {
        var formattedDateStart = dateTimeStart.viewDate.toLocaleDateString([], { day: "2-digit", month: "2-digit", year: "numeric" });
        var formattedDateEnd = dateTimeEnd.viewDate.toLocaleDateString([], { day: "2-digit", month: "2-digit", year: "numeric" });
        var formattedTimeStart = dateTimeStart.viewDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit"});
        var formattedTimeEnd = dateTimeEnd.viewDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit"});

        if(formattedDateStart == formattedDateEnd) {
            formattedDateEnd = "";
        } else {
            formattedDateEnd = "&nbsp;" + formattedDateEnd + ', ';
        }

        accordionTime.querySelector('.bt-heading-date-start').innerHTML = formattedDateStart;
        accordionTime.querySelector('.bt-heading-time-start').innerHTML = formattedTimeStart;
        accordionTime.querySelector('.bt-heading-date-end').innerHTML = formattedDateEnd;
        accordionTime.querySelector('.bt-heading-time-end').innerHTML = formattedTimeEnd;
    }

    function initProjectQuickSelectActions() {
        var quickSelectors = document.querySelectorAll('#accordionProject .bt-quick-select');

        for(let i = 0, length = quickSelectors.length; i < length; i++) {
            let sel = quickSelectors[i];

            sel.addEventListener('click', function() {
                let text = this.innerText;
                let value = this.getAttribute('data-attr-id');

                document.querySelector('#projectSearch').value = text;
                document.querySelector('#projectId').value = value;
                document.querySelector('#accordionProject .accordion-project-title').textContent = text;
            });
        }
    }

    function initProjectQuickResetAction() {
        let sel = document.querySelector('#accordionProject .bt-quick-reset');

        sel.addEventListener('click', function() {
            let text = this.getAttribute('data-attr-name');
            let value = this.getAttribute('data-attr-id');

            document.querySelector('#projectSearch').value = text;
            document.querySelector('#projectId').value = value;
            document.querySelector('#accordionProject .accordion-project-title').textContent = text;
        });
    }
});