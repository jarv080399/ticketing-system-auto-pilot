# Module 9: Settings & System Administration Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Settings & Admin` module to verify the implementation matches the defined roadmap architecture.

---

## 1. System Settings Editor (`GeneralSettingsPage.vue`)

- [ ] **Data Retrieval:** Verify navigating to `/admin/settings` securely populates the `SystemSetting` model key-value pairs from the backend API into the forms without data leaks.
- [ ] **Update Process:** Change the specific parameters (e.g. `auto_close_hours` or `nudge_hours`). Click "Apply Changes". Verify a `vue-toastification` notification drops "System parameters successfully applied", and verify checking `DB::table('system_settings')` reflects the new integer constraint.
- [ ] **Cache Flush Check:** Update the `app_name` string from "IT Helpdesk" to "Autopilot Support". Assuming Redis singleton caching is active, verify that navigating back to an agent dashboard immediately renders the new title, confirming the `SettingsService` cache was cleanly invalidated.

## 2. Business Hours Configurator (`BusinessHoursPage.vue`)

- [ ] **Standard Schedule Check:** Load the page and verify seed data (Monday-Friday, 9:00 AM - 5:00 PM) is successfully populated natively upon `onMounted`.
- [ ] **Day Toggle Mechanic:** Toggle "Saturday" from "Non-Working" to "Working" using the custom CSS toggle pill. Verify the background class dynamic switches from `bg-gray-700` to `bg-emerald-500`.
- [ ] **SLA Calculation Integration:** Submit a new configuration adding Sunday as a working day. Programmatically trigger a ticket via Tinker. Check if the backend `SlaService` accurately decrements timers over that Sunday schedule rather than bypassing it.

## 3. Global Holiday Engine (`HolidaysPage.vue`)

- [ ] **Rendering Seeded Holidays:** Verify the 18 default Philippine and Global holidays seamlessly populate the datatable. Check that `is_recurring` Boolean values visually render correctly inside the data structure.
- [ ] **Create New Holiday:** Open the modal via "Add Custom Holiday". Create a non-recurring "Company Retreat" record. Execute the "Create Holiday" payload. Verify the datatable pushes the new record dynamically and the database successfully generated the 19th row.
- [ ] **SLA Bypass Check:** If "Company Retreat" is set to "July 1", generate a High Priority SLA ticket on "July 1". Validate the background timer logic freezes incrementation for 24 hours because it maps to the active `Holiday::where('date')` array.

## 4. Custom Ticket Fields Router (`CustomFieldsPage.vue`)

- [ ] **Create Select Field:** Use the GUI to create a new Custom Field for entity `ticket`. Name it "Department", select type `select`, and generate JSON options (`IT`, `HR`, `Facilities`).
- [ ] **Frontend Propagation:** Boot into the Self-Service Portal "Create Ticket" form. Verify the system dynamically pulled the `ticket` scoped custom fields and is actively parsing and rendering a functional dropdown menu titled "Department".
- [ ] **Constraint Enforcement:** Edit the "Department" custom field from the Admin Panel to mark `is_required = true`. Attempt to POST a new ticket from the self-service portal WITHOUT supplying a department. Verify `422 Unprocessable Entity` is thrown to the browser validation chain.

## 5. Automation Architecture Matrix (`AdminAutomationPage.vue`)

- [ ] **Rule Hydration Checks:** Verify clicking the edit pencil on "Route Hardware Issues to L2" safely pre-fills the configuration modal parameters stringifying the `condition_json` logic cleanly.
- [ ] **Priority Matrix Visualizer:** Validate the SLA grid colors render properly mapping to `priority` constraints (Critical=Red, High=Amber).
- [ ] **Toggle Disable Test:** Set a `ticket_created` auto-tagging rule `is_active = false`. Fire an explicit API request creating a Ticket matching the trigger parameters. Validate the `AutomationService` bypassed the logic node completely.

## 6. Real-Time System Health Metrics (`SystemHealthPage.vue`)

- [ ] **Ping Check Resolution:** Load the page, check if the system successfully hits `DB::connection()->getPdo()` and `Redis::connection()->ping()` and paints the "Running" pulse pill accurately.
- [ ] **Interactive Worker Refresh:** Click the "Restart Workers" maintenance button. Verify the UI generates the internal CSS spinning wheel, awaits the Axios promise to resolve, locks the button parameter natively `disabled`, and finally shoots the positive success notification popup once the Docker CLI clears `php artisan queue:restart`.
- [ ] **Log Access Restrictions:** Attempt to route to `/admin/system-health` and `/admin/activity-log` using standard `User` JWT bearer tokens. Verify the API throws a rigid `403 Forbidden` Exception preventing access outside `role:admin`.
