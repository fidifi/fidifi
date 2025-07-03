describe('Debug Reply Functionality', () => {
    it('should debug reply button functionality', () => {
        // Reset database with predictable test data
        cy.exec('php artisan migrate:fresh');
        cy.exec('php artisan db:seed --class=CypressTestSeeder');
        
        // Login as the other test user (one that didn't create the post)
        cy.visit('/login');
        cy.get('input[name="email"]').type('other@example.com');
        cy.get('input[name="password"]').type('password');
        cy.get('button[type="submit"]').click();
        
        // Verify we're actually logged in by checking we're not on login page
        cy.url().should('not.include', '/login');
        
        // Navigate to the predictable test thread
        cy.visit('/forums/test-forum/threads/test-thread-for-interactions');
        
        // Verify page loads and we're still authenticated
        cy.url().should('include', '/forums/test-forum/threads/test-thread-for-interactions');
        
        // Check that the page loads and posts are visible
        cy.get('[data-cy="post-item"]').should('exist');
        
        // Check that reply button exists
        cy.get('[data-cy="reply-button"]').first().should('exist');
        
        // Click reply button and check if form appears
        cy.get('[data-cy="reply-button"]').first().click();
        
        // Check if reply form is visible
        cy.get('[data-cy="reply-textarea"]').should('be.visible');
        
        // Type some content
        cy.get('[data-cy="reply-textarea"]').type('Debug test reply content');
        
        // Click submit and check for network activity
        cy.intercept('POST', '/posts/*/reply').as('replyRequest');
        cy.get('[data-cy="submit-reply-button"]').click();
        
        // Wait for request and check if it was made
        cy.wait('@replyRequest', { timeout: 10000 }).then((interception) => {
            // Log response details
            cy.log('Reply request status:', interception.response.statusCode);
            cy.log('Reply request body:', JSON.stringify(interception.response.body));
        });
        
        // Check if we stay on the same page (not redirected to login)
        cy.url().should('include', '/forums/test-forum/threads/test-thread-for-interactions');
        cy.url().should('not.include', '/login');
        
        // Check if reply appears on page
        cy.get('[data-cy="post-content"]').should('contain', 'Debug test reply content');
    });
});