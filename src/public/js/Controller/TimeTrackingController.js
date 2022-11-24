window.addEventListener('DOMContentLoaded', (event) => {
    const dlhProjectComboBox = new DlhComboBoxControl('#projectSearchContainer');

    const dateTimeStartInput = document.getElementById('starttime');

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
        dateTimeEnd.updateOptions({
            restrictions: {
                minDate: e.detail.date,
            },
        });
    });
      
      //using subscribe method
    const subscription = dateTimeEnd.subscribe('change', (e) => {
        dateTimeStart.updateOptions({
            restrictions: {
                maxDate: e.date,
            },
        });
    });
});