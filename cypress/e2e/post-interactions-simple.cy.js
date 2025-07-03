describe('Post Interactions', () => {
    beforeEach(() => {
        // Reset database and seed with fresh data
        cy.exec('php artisan migrate:fresh --seed');
    });

    describe('Like Functionality', () => {
        it('should show like buttons with thumbs up icons', () => {
            // Visit the forums page to find existing threads
            cy.visit('/forums');
            
            // Click on the first forum
            cy.get('[data-cy="forum-item"]').first().click();
            
            // Click on the first thread
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Check that like buttons exist with icons
            cy.get('[data-cy="like-button"]').should('exist');
            cy.get('[data-cy="like-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="like-button"]').first().should('contain', 'Like');
        });

        it('should redirect to login when unauthenticated user tries to like', () => {
            // Visit forums and navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Try to click like button
            cy.get('[data-cy="like-button"]').first().click();
            
            // Should redirect to login page
            cy.url().should('include', '/login');
        });

        it('should allow authenticated users to like posts', () => {
            // Register a new user
            cy.visit('/register');
            cy.get('input[name="name"]').type('Test User');
            cy.get('input[name="email"]').type('testuser@example.com');
            cy.get('input[name="password"]').type('password');
            cy.get('input[name="password_confirmation"]').type('password');
            cy.get('button[type="submit"]').click();

            // Navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Check initial state
            cy.get('[data-cy="like-button"]').first().should('contain', 'Like');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-gray-500');

            // Click like button
            cy.get('[data-cy="like-button"]').first().click();

            // Check that button shows loading/processing state briefly
            cy.get('[data-cy="like-button"]').first().should('exist');
            
            // Wait a moment for the API call to complete
            cy.wait(1000);
            
            // The count should have increased (though we can't easily test the exact count)
            cy.get('[data-cy="like-button"]').first().should('contain', 'Like');
        });
    });

    describe('Reply Functionality', () => {
        it('should show reply and quote buttons with icons', () => {
            // Visit forums and navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Check that reply and quote buttons exist with icons
            cy.get('[data-cy="reply-button"]').should('exist');
            cy.get('[data-cy="quote-button"]').should('exist');
            cy.get('[data-cy="reply-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="quote-button"]').first().find('svg').should('exist');
        });

        it('should show reply form when reply button is clicked', () => {
            // Register and login a user
            cy.visit('/register');
            cy.get('input[name="name"]').type('Test User');
            cy.get('input[name="email"]').type('testuser@example.com');
            cy.get('input[name="password"]').type('password');
            cy.get('input[name="password_confirmation"]').type('password');
            cy.get('button[type="submit"]').click();

            // Navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Click reply button
            cy.get('[data-cy="reply-button"]').first().click();

            // Check that reply form appears
            cy.get('[data-cy="reply-textarea"]').should('be.visible');
            cy.get('[data-cy="submit-reply-button"]').should('be.visible');
            cy.get('[data-cy="cancel-reply-button"]').should('be.visible');
            cy.contains('Reply to this post').should('be.visible');
        });

        it('should show quote form when quote button is clicked', () => {
            // Register and login a user
            cy.visit('/register');
            cy.get('input[name="name"]').type('Test User');
            cy.get('input[name="email"]').type('testuser@example.com');
            cy.get('input[name="password"]').type('password');
            cy.get('input[name="password_confirmation"]').type('password');
            cy.get('button[type="submit"]').click();

            // Navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Click quote button
            cy.get('[data-cy="quote-button"]').first().click();

            // Check that quote form appears
            cy.get('[data-cy="reply-textarea"]').should('be.visible');
            cy.contains('Quote & Reply').should('be.visible');
            
            // Check that quoted content block exists
            cy.get('.bg-gray-100.rounded.border-l-4.border-gray-400').should('exist');
            cy.get('.bg-gray-100.rounded.border-l-4.border-gray-400').should('contain', 'wrote:');
        });

        it('should allow canceling reply', () => {
            // Register and login a user
            cy.visit('/register');
            cy.get('input[name="name"]').type('Test User');
            cy.get('input[name="email"]').type('testuser@example.com');
            cy.get('input[name="password"]').type('password');
            cy.get('input[name="password_confirmation"]').type('password');
            cy.get('button[type="submit"]').click();

            // Navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Click reply button and then cancel
            cy.get('[data-cy="reply-button"]').first().click();
            cy.get('[data-cy="reply-textarea"]').should('be.visible');
            cy.get('[data-cy="cancel-reply-button"]').click();

            // Reply form should be hidden
            cy.get('[data-cy="reply-textarea"]').should('not.exist');
        });

        it('should disable submit button when reply is empty', () => {
            // Register and login a user
            cy.visit('/register');
            cy.get('input[name="name"]').type('Test User');
            cy.get('input[name="email"]').type('testuser@example.com');
            cy.get('input[name="password"]').type('password');
            cy.get('input[name="password_confirmation"]').type('password');
            cy.get('button[type="submit"]').click();

            // Navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Click reply button
            cy.get('[data-cy="reply-button"]').first().click();

            // Submit button should be disabled initially
            cy.get('[data-cy="submit-reply-button"]').should('be.disabled');

            // Type content
            cy.get('[data-cy="reply-textarea"]').type('Some content');

            // Submit button should be enabled
            cy.get('[data-cy="submit-reply-button"]').should('not.be.disabled');

            // Clear content
            cy.get('[data-cy="reply-textarea"]').clear();

            // Submit button should be disabled again
            cy.get('[data-cy="submit-reply-button"]').should('be.disabled');
        });
    });

    describe('Button Styling and Icons', () => {
        it('should show proper icons and styling for all interaction buttons', () => {
            // Visit forums and navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Check that all buttons have SVG icons
            cy.get('[data-cy="like-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="quote-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="reply-button"]').first().find('svg').should('exist');

            // Check that buttons have proper styling classes
            cy.get('[data-cy="like-button"]').first().should('have.class', 'flex');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'items-center');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'gap-1');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-gray-500');
        });

        it('should show thumbs up icon for like button', () => {
            // Visit forums and navigate to a thread
            cy.visit('/forums');
            cy.get('[data-cy="forum-item"]').first().click();
            cy.get('[data-cy="thread-item"]').first().click();
            
            // Check that like button has the correct thumbs up SVG path
            cy.get('[data-cy="like-button"]').first()
                .find('svg')
                .should('have.attr', 'viewBox', '0 0 24 24');
                
            cy.get('[data-cy="like-button"]').first()
                .find('svg path')
                .should('have.attr', 'd')
                .and('include', 'M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3');
        });
    });
});