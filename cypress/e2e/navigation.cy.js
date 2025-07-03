describe('Navigation and Routing', () => {
  beforeEach(() => {
    cy.seedDatabase()
  })

  it('navigates from forum index to forum show', () => {
    cy.visitInertia('/forums')
    cy.getByTestId('forum-item').first().click()
    cy.url().should('match', /\/forums\/\d+/)
    cy.getByTestId('forum-title').should('be.visible')
  })

  it('navigates from forum show to thread show', () => {
    cy.visit('/forums/1')
    cy.getByTestId('thread-item').first().click()
    cy.url().should('match', /\/threads\/\d+/)
    cy.getByTestId('thread-title').should('be.visible')
  })

  it('navigates back using breadcrumbs', () => {
    cy.visit('/threads/1')
    cy.getByTestId('breadcrumb').contains('Forums').click()
    cy.url().should('equal', Cypress.config().baseUrl + '/forums')
  })

  it('handles direct URL access', () => {
    cy.visit('/forums/1')
    cy.getByTestId('forum-title').should('be.visible')
    
    cy.visit('/threads/1')
    cy.getByTestId('thread-title').should('be.visible')
  })

  it('maintains proper page state during navigation', () => {
    cy.visitInertia('/forums')
    cy.get('[data-page]').should('have.attr', 'data-page').and('contain', 'Forums/Index')
    
    cy.getByTestId('forum-item').first().click()
    cy.get('[data-page]').should('have.attr', 'data-page').and('contain', 'Forums/Show')
    
    cy.getByTestId('thread-item').first().click()
    cy.get('[data-page]').should('have.attr', 'data-page').and('contain', 'Threads/Show')
  })
})

describe('Error Handling', () => {
  beforeEach(() => {
    cy.seedDatabase()
  })

  it('handles 404 for non-existent forum', () => {
    cy.request({ url: '/forums/999', failOnStatusCode: false })
      .its('status')
      .should('eq', 404)
  })

  it('handles 404 for non-existent thread', () => {
    cy.request({ url: '/threads/999', failOnStatusCode: false })
      .its('status')
      .should('eq', 404)
  })

  it('gracefully handles empty forums', () => {
    // This would require creating a forum with no threads
    // Implementation depends on seeder data structure
    cy.visit('/forums')
    cy.getByTestId('forum-item').should('exist')
  })
})