describe('Forum Index Page', () => {
  beforeEach(() => {
    cy.seedDatabase()
    cy.visitInertia('/forums')
  })

  it('displays the forum title', () => {
    cy.contains('Forum').should('be.visible')
  })

  it('displays categories with forums', () => {
    cy.getByTestId('category').should('exist')
    cy.getByTestId('forum-item').should('exist')
  })

  it('shows forum statistics', () => {
    cy.getByTestId('forum-threads-count').should('be.visible')
    cy.getByTestId('forum-posts-count').should('be.visible')
  })

  it('displays last post information when available', () => {
    cy.get('[data-cy="last-post"]').first().within(() => {
      cy.get('[data-cy="last-post-user"]').should('be.visible')
      cy.get('[data-cy="last-post-time"]').should('be.visible')
    })
  })

  it('allows navigation to forum when clicked', () => {
    cy.getByTestId('forum-item').first().click()
    cy.url().should('include', '/forums/')
  })

  it('shows proper responsive layout on mobile', () => {
    cy.viewport(375, 667)
    cy.getByTestId('category').should('be.visible')
    cy.getByTestId('forum-item').should('be.visible')
  })
})

describe('Forum Show Page', () => {
  beforeEach(() => {
    cy.seedDatabase()
  })

  it('displays forum details and threads', () => {
    cy.visit('/forums/1')
    cy.contains('Threads').should('be.visible')
    cy.getByTestId('thread-item').should('exist')
  })

  it('shows thread information correctly', () => {
    cy.visit('/forums/1')
    cy.getByTestId('thread-item').first().within(() => {
      cy.getByTestId('thread-title').should('be.visible')
      cy.getByTestId('thread-author').should('be.visible')
      cy.getByTestId('thread-replies').should('be.visible')
    })
  })

  it('allows navigation to thread when clicked', () => {
    cy.visit('/forums/1')
    cy.getByTestId('thread-item').first().click()
    cy.url().should('include', '/threads/')
  })

  it('shows breadcrumb navigation', () => {
    cy.visit('/forums/1')
    cy.getByTestId('breadcrumb').should('be.visible')
    cy.getByTestId('breadcrumb').should('contain', 'Forums')
  })
})

describe('Thread Show Page', () => {
  beforeEach(() => {
    cy.seedDatabase()
  })

  it('displays thread title and posts', () => {
    cy.visit('/threads/1')
    cy.getByTestId('thread-title').should('be.visible')
    cy.getByTestId('post-item').should('exist')
  })

  it('shows post content and author information', () => {
    cy.visit('/threads/1')
    cy.getByTestId('post-item').first().within(() => {
      cy.getByTestId('post-content').should('be.visible')
      cy.getByTestId('post-author').should('be.visible')
      cy.getByTestId('post-date').should('be.visible')
    })
  })

  it('displays proper post ordering', () => {
    cy.visit('/threads/1')
    cy.getByTestId('post-item').should('have.length.greaterThan', 0)
  })

  it('shows breadcrumb navigation to forum and category', () => {
    cy.visit('/threads/1')
    cy.getByTestId('breadcrumb').should('be.visible')
    cy.getByTestId('breadcrumb').should('contain', 'Forums')
  })
})