window.addEventListener('DOMContentLoaded', (event) => {
    const dlhProjectComboBox = new DlhComboBoxControl('#projectSearchContainer');
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
});