describe('Authentication', () => {
  describe('Registration', () => {
    beforeEach(() => {
      cy.visitInertia('/register')
    })

    it('displays the registration form', () => {
      cy.contains('Create your account').should('be.visible')
      cy.get('input[name="name"]').should('be.visible')
      cy.get('input[name="username"]').should('be.visible')
      cy.get('input[name="email"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('input[name="password_confirmation"]').should('be.visible')
    })

    it('allows users to register with valid data', () => {
      cy.get('input[name="name"]').type('John Doe')
      cy.get('input[name="username"]').type('johndoe')
      cy.get('input[name="email"]').type('john@example.com')
      cy.get('input[name="password"]').type('password123')
      cy.get('input[name="password_confirmation"]').type('password123')
      
      cy.get('button[type="submit"]').click()
      
      cy.url().should('include', '/forums')
      cy.contains('John Doe').should('be.visible')
    })

    it('shows validation errors for duplicate email', () => {
      // Create a user first
      cy.exec('php artisan tinker --execute="\\App\\Models\\User::factory()->create([\'email\' => \'existing@example.com\'])"')
      
      cy.get('input[name="name"]').type('John Doe')
      cy.get('input[name="username"]').type('johndoe')
      cy.get('input[name="email"]').type('existing@example.com')
      cy.get('input[name="password"]').type('password123')
      cy.get('input[name="password_confirmation"]').type('password123')
      
      cy.get('button[type="submit"]').click()
      
      cy.contains('The email has already been taken').should('be.visible')
    })

    it('shows validation error when passwords do not match', () => {
      cy.get('input[name="name"]').type('John Doe')
      cy.get('input[name="username"]').type('johndoe')
      cy.get('input[name="email"]').type('john@example.com')
      cy.get('input[name="password"]').type('password123')
      cy.get('input[name="password_confirmation"]').type('different')
      
      cy.get('button[type="submit"]').click()
      
      cy.contains('The password field confirmation does not match').should('be.visible')
    })

    it('has a link to login page', () => {
      cy.contains('sign in to your existing account').click()
      cy.url().should('include', '/login')
    })
  })

  describe('Login', () => {
    beforeEach(() => {
      cy.seedDatabase()
      cy.visitInertia('/login')
    })

    it('displays the login form', () => {
      cy.contains('Sign in to your account').should('be.visible')
      cy.get('input[name="email"]').should('be.visible')
      cy.get('input[name="password"]').should('be.visible')
      cy.get('input[type="checkbox"]').should('be.visible')
    })

    it('allows users to login with email', () => {
      cy.get('input[name="email"]').type('test@example.com')
      cy.get('input[name="password"]').type('password')
      
      cy.get('button[type="submit"]').click()
      
      cy.url().should('include', '/forums')
      cy.contains('Test User').should('be.visible')
    })

    it('allows users to login with username', () => {
      cy.get('input[name="email"]').type('testuser')
      cy.get('input[name="password"]').type('password')
      
      cy.get('button[type="submit"]').click()
      
      cy.url().should('include', '/forums')
      cy.contains('Test User').should('be.visible')
    })

    it('shows error for invalid credentials', () => {
      cy.get('input[name="email"]').type('test@example.com')
      cy.get('input[name="password"]').type('wrongpassword')
      
      cy.get('button[type="submit"]').click()
      
      cy.contains('These credentials do not match our records').should('be.visible')
    })

    it('has a link to registration page', () => {
      cy.contains('create a new account').click()
      cy.url().should('include', '/register')
    })
  })

  describe('Logout', () => {
    beforeEach(() => {
      cy.seedDatabase()
      cy.login('test@example.com')
      cy.visitInertia('/forums')
    })

    it('allows users to logout', () => {
      cy.contains('Test User').click()
      cy.contains('Logout').click()
      
      cy.url().should('equal', Cypress.config().baseUrl + '/')
      cy.contains('Login').should('be.visible')
      cy.contains('Register').should('be.visible')
    })
  })

  describe('Protected Routes', () => {
    it('redirects unauthenticated users to login', () => {
      cy.visit('/dashboard')
      cy.url().should('include', '/login')
    })

    it('allows authenticated users to access protected routes', () => {
      cy.seedDatabase()
      cy.login('test@example.com')
      cy.visitInertia('/dashboard')
      
      cy.contains('Dashboard').should('be.visible')
      cy.contains('Welcome to your dashboard').should('be.visible')
    })
  })
})