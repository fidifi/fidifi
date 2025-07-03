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

// Authentication command
Cypress.Commands.add('login', (email, password = 'password') => {
  cy.visit('/login')
  cy.get('input[name="email"]').type(email)
  cy.get('input[name="password"]').type(password)
  cy.get('button[type="submit"]').click()
})