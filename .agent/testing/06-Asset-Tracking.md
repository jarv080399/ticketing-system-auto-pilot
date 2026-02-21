# Module 6: Asset Tracking Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Asset Tracking` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Asset Registry Validation (`Asset Registry UI`)

- [ ] **Search and Pagination:** Seed exactly 10,050 mock assets into the database. Access the Agent view of `/api/v1/assets`. Verify the server-side pagination correctly returns 50 items < 150ms and prevents memory limit crashes. Use the UI search to query by `serial_number`, confirming instant SQL filtering.
- [ ] **Bulk Import Processing:** Upload a CSV containing 5,000 valid assets and 5 completely malformed rows (missing required `type` or duplicate `serial_number`). Verify the backend Artisan Queue catches the CSV, processes the 5,000 correctly, rejects the 5, and generates a downloadable error log report accurately mapping the misconfigured rows.

## 2. Hardware Lifecycle and Ownership (`AssetDetailView`)

- [ ] **Assign/Unassign Mapping:** Navigate to an unassigned "MacBook Pro". Click "Assign to User" and select User ID 12. Validate that the UI refreshes instantly, the database sets the `owner_user_id`, and a new `asset_history` record is injected stating "Action: Assigned to User 12".
- [ ] **Storefront Synchronization:** Log in to the End-User portal as User 12. View the "My Devices" tile. Verify the newly assigned MacBook Pro renders perfectly with the correct status badge.

## 3. Ticketing Integration Context (`Ticket Cockpit`)

- [ ] **Sidebar Hardware Data:** Create a new ticket as User 12. Log in as an Agent and view the ticket. Verify the Right-Side Cockpit dynamically injects the `Assets` component, fetching User 12's MacBook Pro details directly into the view to eliminate the need for manual agent discovery queries.

## 4. Maintenance & Warranty Automation

- [ ] **Expiry Cron Execution:** Modify an asset's `warranty_expires_at` to accurately reflect a timestamp 25 days from today. Run `php artisan schedule:run`. Validate the scheduled job flags the asset and successfully dispatches an administrative email/notification warning of imminent warranty expiration.
