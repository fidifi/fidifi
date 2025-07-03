// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

// Laravel/Inertia specific commands
Cypress.Commands.add('visitInertia', (url) => {
  cy.visit(url)
  cy.get('[data-page]').should('exist')
})

// Forum specific commands
Cypress.Commands.add('seedDatabase', () => {
  cy.exec('php artisan migrate:fresh --seed', { timeout: 30000 })
})

Cypress.Commands.add('getByTestId', (testId) => {
  return cy.get(`[data-cy="${testId}"]`)
})