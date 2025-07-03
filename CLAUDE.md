# FidiFi Forum - Development Guide

This document contains important information about the project setup, conventions, and requirements.

## Project Overview

FidiFi is a modern forum application built with Laravel, featuring:
- Full authentication system (registration, login, logout)
- Hierarchical forum structure: Categories → Forums → Threads → Posts
- Search functionality across threads, posts, and users
- Member directory
- 100% test coverage (PHPUnit and Cypress)

## Tech Stack

### Backend
- **Laravel 11** - PHP framework
- **MySQL** - Database
- **Redis** - Caching and sessions
- **Meilisearch** - Full-text search (ready for implementation)
- **Mailpit** - Local email testing

### Frontend
- **Inertia.js** - Server-side routing with SPA-like experience
- **Vue 3** - JavaScript framework
- **Tailwind CSS 4** - Utility-first CSS framework
- **Vite** - Build tool and dev server
- **Ziggy** - Use Laravel routes in JavaScript

## Docker Setup

The project uses Laravel Sail for Docker containerization.

### Starting the environment
```bash
vendor/bin/sail up -d
```

### Running commands in containers
All PHP/Laravel commands must be run inside the container:
```bash
# Artisan commands
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed

# Composer commands
vendor/bin/sail composer install

# NPM commands
vendor/bin/sail npm install
vendor/bin/sail npm run dev
```

### Available services
- **Laravel app**: http://localhost
- **MySQL**: localhost:3306
- **Redis**: localhost:6379
- **Meilisearch**: http://localhost:7700
- **Mailpit**: http://localhost:8025

## Testing Requirements

### PHPUnit (Backend)
- **Target: 100% code coverage**
- Run tests: `vendor/bin/sail test`
- Run with coverage: `vendor/bin/sail test --coverage --coverage-text --min=100`
- Coverage report: `vendor/bin/sail test --coverage-html tests/coverage/html`

### Cypress (E2E)
- **Target: Full user journey coverage**
- Run tests: `vendor/bin/sail npm run cypress:run`
- Open Cypress UI: `vendor/bin/sail npm run cypress:open`
- Tests located in: `cypress/e2e/`

### Key Testing Principles
1. Write tests first (TDD approach)
2. Test all models, controllers, and features
3. Ensure proper validation testing
4. Test both success and failure scenarios
5. Use factories for test data generation

## Development Workflow

### 1. Always ensure containers are running
```bash
vendor/bin/sail up -d
```

### 2. Keep Vite dev server running for frontend development
```bash
vendor/bin/sail npm run dev
```

### 3. Run tests before committing
```bash
vendor/bin/sail test
vendor/bin/sail npm run cypress:run
```

### 4. Check code coverage
```bash
vendor/bin/sail test --coverage --coverage-text
```

## Database Structure

### Migrations order
1. Laravel default migrations (users, cache, jobs)
2. categories
3. forums  
4. threads
5. posts
6. add_last_post_to_forums
7. add_forum_fields_to_users
8. add_editor_id_to_posts (pending)

### Key Relationships
- Category → hasMany → Forums
- Forum → belongsTo → Category
- Forum → hasMany → Threads
- Thread → belongsTo → Forum
- Thread → belongsTo → User
- Thread → hasMany → Posts
- Post → belongsTo → Thread
- Post → belongsTo → User
- User → hasMany → Threads
- User → hasMany → Posts

## Code Style and Conventions

### PHP/Laravel
- Follow PSR-12 coding standard
- Use type hints for all method parameters and return types
- Keep controllers thin, logic in models/services
- Use form requests for validation
- Always add appropriate indexes to database columns

### Vue/JavaScript
- Use Composition API for Vue 3 components
- Keep components small and focused
- Use TypeScript types where beneficial
- Follow ESLint rules

### Testing
- Test file names match the class they test
- Use descriptive test method names
- Group related tests in describe blocks (Cypress)
- Always reset database state between tests

## Common Commands Reference

### Database
```bash
# Fresh migration with seeding
vendor/bin/sail artisan migrate:fresh --seed

# Create new migration
vendor/bin/sail artisan make:migration create_example_table

# Create model with migration and factory
vendor/bin/sail artisan make:model Example -mf
```

### Testing
```bash
# Run specific test file
vendor/bin/sail test tests/Feature/ExampleTest.php

# Run tests in parallel
vendor/bin/sail test --parallel

# Run specific Cypress test
vendor/bin/sail npm run cypress:run -- --spec "cypress/e2e/authentication.cy.js"
```

### Development
```bash
# Clear all caches
vendor/bin/sail artisan cache:clear
vendor/bin/sail artisan config:clear
vendor/bin/sail artisan route:clear
vendor/bin/sail artisan view:clear

# Generate IDE helper files
vendor/bin/sail artisan ide-helper:generate
vendor/bin/sail artisan ide-helper:models
```

## Security Considerations

1. All user input is validated and sanitized
2. CSRF protection enabled on all forms
3. Authentication required for posting/editing
4. Rate limiting on authentication endpoints
5. Proper authorization checks for all actions

## Performance Optimizations

1. Database queries use eager loading to prevent N+1 issues
2. Implement caching for frequently accessed data
3. Use database indexes on foreign keys and commonly searched fields
4. Paginate all listings (forums, threads, posts, search results)
5. Lazy load images and heavy content

## Future Considerations

- Implement real-time features with Laravel Echo/Pusher
- Add OAuth providers for social login
- Implement full-text search with Meilisearch
- Add moderation tools and user roles
- Implement notification system
- Add API endpoints for mobile apps