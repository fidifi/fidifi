describe('Post Interactions', () => {
    beforeEach(() => {
        // Reset database and seed with fresh data
        cy.exec('php artisan migrate:fresh --seed');
        
        // Create test users using artisan command
        cy.exec('php artisan make:command CreateTestUser', { failOnNonZeroExit: false });
        
        // Use a simpler approach - create users via registration
        cy.visit('/register');
        cy.get('input[name="name"]').type('Test User');
        cy.get('input[name="email"]').type('testuser@example.com');
        cy.get('input[name="password"]').type('password');
        cy.get('input[name="password_confirmation"]').type('password');
        cy.get('button[type="submit"]').click();
        cy.get('form[method="POST"]').submit(); // logout
        
        // Register second user
        cy.visit('/register');
        cy.get('input[name="name"]').type('Other User');
        cy.get('input[name="email"]').type('otheruser@example.com');
        cy.get('input[name="password"]').type('password');
        cy.get('input[name="password_confirmation"]').type('password');
        cy.get('button[type="submit"]').click();
        cy.get('form[method="POST"]').submit(); // logout
    });

    describe('Like Functionality', () => {
        it('should allow authenticated users to like posts', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Check initial state - should not be liked
            cy.get('[data-cy="like-button"]').first().should('contain', 'Like (0)');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-gray-500');

            // Click like button
            cy.get('[data-cy="like-button"]').first().click();

            // Check updated state - should be liked
            cy.get('[data-cy="like-button"]').first().should('contain', 'Liked (1)');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-blue-600');

            // Click unlike button
            cy.get('[data-cy="like-button"]').first().click();

            // Check state reverted - should not be liked
            cy.get('[data-cy="like-button"]').first().should('contain', 'Like (0)');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-gray-500');
        });

        it('should show thumbs up icon in like button', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Check that like button contains an SVG (thumbs up icon)
            cy.get('[data-cy="like-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="like-button"]').first().find('svg').should('have.attr', 'viewBox', '0 0 24 24');
        });

        it('should prevent double-clicking like button', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click like button multiple times quickly
            cy.get('[data-cy="like-button"]').first().click();
            cy.get('[data-cy="like-button"]').first().click();
            cy.get('[data-cy="like-button"]').first().click();

            // Should only be liked once
            cy.get('[data-cy="like-button"]').first().should('contain', 'Liked (1)');
        });
    });

    describe('Reply Functionality', () => {
        it('should allow authenticated users to reply to posts', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click reply button
            cy.get('[data-cy="reply-button"]').first().click();

            // Check reply form appears
            cy.get('[data-cy="reply-textarea"]').should('be.visible');
            cy.get('[data-cy="submit-reply-button"]').should('be.visible');
            cy.get('[data-cy="cancel-reply-button"]').should('be.visible');

            // Type reply content
            const replyText = 'This is my reply to the post.';
            cy.get('[data-cy="reply-textarea"]').type(replyText);

            // Submit reply
            cy.get('[data-cy="submit-reply-button"]').click();

            // Check that page reloads and reply appears
            cy.get('[data-cy="post-content"]').should('contain', replyText);
        });

        it('should show reply form with correct heading', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click reply button
            cy.get('[data-cy="reply-button"]').first().click();

            // Check reply form heading
            cy.contains('Reply to this post').should('be.visible');
        });

        it('should allow canceling reply', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click reply button
            cy.get('[data-cy="reply-button"]').first().click();

            // Type some content
            cy.get('[data-cy="reply-textarea"]').type('This reply will be cancelled');

            // Click cancel
            cy.get('[data-cy="cancel-reply-button"]').click();

            // Check reply form is hidden
            cy.get('[data-cy="reply-textarea"]').should('not.exist');
        });

        it('should disable submit button when reply is empty', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
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

    describe('Quote Functionality', () => {
        it('should allow authenticated users to quote posts', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click quote button
            cy.get('[data-cy="quote-button"]').first().click();

            // Check quote form appears with correct heading
            cy.contains('Quote & Reply').should('be.visible');

            // Check that quoted content is shown
            cy.contains('This is a test post for interactions.').should('be.visible');

            // Check that textarea has quote markup
            cy.get('[data-cy="reply-textarea"]').should('contain.value', '[quote=');
            cy.get('[data-cy="reply-textarea"]').should('contain.value', 'This is a test post for interactions.');
            cy.get('[data-cy="reply-textarea"]').should('contain.value', '[/quote]');
        });

        it('should show quoted content in a styled block', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click quote button
            cy.get('[data-cy="quote-button"]').first().click();

            // Check that quoted content block exists and has proper styling
            cy.get('.bg-gray-100.rounded.border-l-4.border-gray-400').should('exist');
            cy.get('.bg-gray-100.rounded.border-l-4.border-gray-400').should('contain', 'wrote:');
            cy.get('.bg-gray-100.rounded.border-l-4.border-gray-400').should('contain', 'This is a test post for interactions.');
        });

        it('should allow submitting quote with additional content', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Click quote button
            cy.get('[data-cy="quote-button"]').first().click();

            // Add additional content after the quote
            cy.get('[data-cy="reply-textarea"]').type('\\n\\nI agree with this statement!');

            // Submit quote
            cy.get('[data-cy="submit-reply-button"]').click();

            // Check that page reloads and quoted reply appears
            cy.get('[data-cy="post-content"]').should('contain', 'I agree with this statement!');
        });
    });

    describe('Button Icons and Styling', () => {
        it('should show proper icons for all interaction buttons', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Check that all buttons have SVG icons
            cy.get('[data-cy="like-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="quote-button"]').first().find('svg').should('exist');
            cy.get('[data-cy="reply-button"]').first().find('svg').should('exist');

            // Check that buttons have proper styling
            cy.get('[data-cy="like-button"]').first().should('have.class', 'flex');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'items-center');
            cy.get('[data-cy="like-button"]').first().should('have.class', 'gap-1');
        });

        it('should show different visual states for liked and unliked posts', () => {
            // Login as otherUser
            cy.visit('/login');
            cy.get('input[name="email"]').type('other@test.com');
            cy.get('input[name="password"]').type('password');
            cy.get('button[type="submit"]').click();

            // Visit thread
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Initial state - gray color
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-gray-500');

            // Click like button
            cy.get('[data-cy="like-button"]').first().click();

            // Liked state - blue color
            cy.get('[data-cy="like-button"]').first().should('have.class', 'text-blue-600');
        });
    });

    describe('Authentication Requirements', () => {
        it('should redirect unauthenticated users when trying to interact', () => {
            // Visit thread without logging in
            cy.visit(`/forums/${forum.slug}/threads/${thread.slug}`);
            
            // Try to click like button - should redirect to login
            cy.get('[data-cy="like-button"]').first().click();
            cy.url().should('include', '/login');
        });
    });
});