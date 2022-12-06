it('Is Tracking started and ended from Dashboard', () => {
    cy.visit('http://dlh.localhost/')
  
    // Resize window to 1920 x 919
    cy.viewport(1920, 919)
  
    // Click on <a> "Starten"
    cy.get('[href="http://dlh.localhost/timetracking/start?project_id=1ed593c0-cc84-6872-bf64-a1f297239c2f"]').click()
  
    // Click on <a> "Beenden"
    cy.get('button.btn-end-tracking').click()
  
    // Click on "Datetime Accordion Heading"
    cy.get('#flush-headingOne > .accordion-button').click()
  
    // Click on <input> #starttime and select 2030-Okt-31
    cy.get('#starttime').click()
    cy.get('[title="Select Month"]').click()    // Click on <div> "Dezember 22"
    cy.get('[title="Select Year"]').click()     // Click on <div> "2022"
    cy.get('[title="Select Decade"]').click()   // Click on <div> "2021-2032"
    cy.get('.decade:nth-child(5)').click()      // Click on <div> "2030"
    cy.get('.year:nth-child(2)').click()        // Click on <div> "2030"
    cy.get('.month:nth-child(10)').click()      // Click on <div> "Okt"
    cy.get('.day:nth-child(40)').click()        // Click on <div> "31"
  
    // Click on <div> hour selector and select the 12:00
    cy.get('[title="Pick Hour"]').click()
    cy.get('.hour:nth-child(13)').click()
    cy.get('[title="Pick Minute"]').click()
    cy.get('.minute:nth-child(1)').click()      // click on 00


    // Click on <input> #endtime and select 2030-10-31
    cy.get('#endtime').click()
    cy.get('.show > .td-row [title="Select Month"]').click()    // Click on <div> "Dezember 22"
    cy.get('[title="Select Year"]').click()     // Click on <div> "2022"
    cy.get('[title="Select Decade"]').click()   // Click on <div> "2021-2032"
    cy.get('.show > .td-row .decade:nth-child(5)').click()      // Click on <div> "2030"
    cy.get('.show > .td-row .year:nth-child(2)').click()        // Click on <div> "2030"
    cy.get('.show > .td-row .month:nth-child(10)').click()      // Click on <div> "Nov"
    cy.get('.show > .td-row .day:nth-child(41)').click()        // Click on <div> "1."
  
    // Click on <div> hour selector and select the 02:00
    cy.get('.tempus-dominus-widget.show [title="Pick Hour"]').click()
    cy.get('.tempus-dominus-widget.show .time-container-hour .hour:nth-child(2)').click()
    cy.get('.tempus-dominus-widget.show [title="Pick Minute"]').click()
    cy.get('.tempus-dominus-widget.show .minute:nth-child(3)').click()      // click on 10
  
    // Beschreibung eingeben.
    cy.get('.mb-3:nth-child(6) > .form-control').click()
    cy.get('[name="comment"]').type('Dies ist ein Test.')
  
    // Click on <label> " Abrechnen"
    cy.get('.mb-3:nth-child(5) > .form-label').click()
  
    // Click on <button> "Speichern"
    cy.get('.btn-primary').click()
  
    // Click on <a> "Trackings auflisten"
    cy.get('[href="/timetracking/listProjectTimes?project_id=1ed593c0-cc84-6872-bf64-a1f297239c2f"]').click()
  
    // Validate data
    // Click on <td> "Dies ist ein Test."
    cy.get('tr:nth-child(1) > td:nth-child(6)').contains('Dies ist ein Test');
  
    // Click on <td> "1 Stunden; 0 Minuten; 0 S..."
    cy.get('tr:nth-child(1) > td:nth-child(5)').contains('13 Stunden; 10 Minuten; 0 Sekunden');
  
    // Click on <td> "Ja"
    cy.get('tr:nth-child(1) > td:nth-child(3)').contains('Ja')
  })