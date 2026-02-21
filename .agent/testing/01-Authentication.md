# Module 1: Authentication & User Management Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Authentication & User Management` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Authentication Engine

- [ ] **Standard Login Flow:** Verify valid email/password login securely returns a Sanctum JWT token and redirect user to their dashboard. Verify invalid credentials return `422 Unprocessable Entity` with appropriate error messages.
- [ ] **SSO Integration:** Mock the Socialite provider (e.g., Google or Microsoft). Test that the OAuth callback successfully provisions a new user record or logs in an existing user without requiring a password.
- [ ] **Password Reset Lifecycle:** Validate the forgot-password flow. Request a reset link, capture the token from the mock email server, and submit a new password. Ensure the token is invalidated after use.
- [ ] **Session Replay Protection & Revocation:** Log in to the application. Verify the `user_sessions` table updates. Hit the `POST /api/v1/auth/revoke-all` endpoint and verify the active token is destroyed, returning a `401 Unauthorized` on subsequent API calls.
- [ ] **Idle Timeout Enforcement:** Leave the application idle for the configured timeout duration. Verify the frontend Pinia store triggers an automatic logout and redirects to the login screen.

## 2. Role-Based Access Control (RBAC)

- [ ] **Gate Validation (Admin):** Log in as an `Admin` and attempt to access `/api/v1/admin/users`. Verify a `200 OK` response.
- [ ] **Gate Validation (Agent):** Log in as an `Agent` and attempt to access the same admin endpoint. Verify the middleware blocks access with a `403 Forbidden` Exception.
- [ ] **Gate Validation (End-User):** Log in as an `End-User`. Verify the user can only fetch their own tickets via `GET /api/v1/tickets/my-tickets` and receives `403` if attempting to fetch a ticket assigned to a different user ID.

## 3. User Lifecycle Management

- [ ] **Bulk CSV Import:** Execute the Artisan command `php artisan users:import sample.csv`. Verify the system parses valid rows into the database, assigns default passwords or tokens, and handles malformed rows gracefully (logging errors without crashing the batch).
- [ ] **Admin User Creation:** Use the Admin panel to create a new Agent. Verify the backend validates email uniqueness, provisions the user, and fires a "Welcome" notification.
- [ ] **Soft-Deletion Mechanics:** Delete a user via `DELETE /api/v1/admin/users/{id}`. Verify the record remains in the database with a populated `deleted_at` timestamp. Check that the deleted user cannot log in.

## 4. Profile Management

- [ ] **Avatar and Preference Updates:** From the `ProfilePage.vue`, edit the user's timezone, locale, and upload a new avatar. Verify the payload correctly saves to the `users` table and the UI reactively updates the top-right avatar without a page refresh.
