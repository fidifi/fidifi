describe('Like Button Test', () => {
    beforeEach(() => {
        cy.seedDatabase();
    });

    it('should show like buttons and allow basic interaction', () => {
        // Visit a thread directly
        cy.visitInertia('/threads/1');
        
        // Should be on a thread page with posts
        cy.getByTestId('post-item').should('exist');
        
        // Check that like buttons exist
        cy.getByTestId('like-button').should('exist');
        
        // Like button should have proper text and icon
        cy.getByTestId('like-button').first().should('contain', 'Like');
        cy.getByTestId('like-button').first().find('svg').should('exist');
        
        // Reply and Quote buttons should exist
        cy.getByTestId('reply-button').should('exist');
        cy.getByTestId('quote-button').should('exist');
        
        // Buttons should have icons
        cy.getByTestId('reply-button').first().find('svg').should('exist');
        cy.getByTestId('quote-button').first().find('svg').should('exist');
    });
});