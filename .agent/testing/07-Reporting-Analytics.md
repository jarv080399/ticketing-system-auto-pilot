# Module 7: Reporting & Analytics Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Reporting & Analytics` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Complex Aggregate Verification (`ReportRepository`)

- [ ] **First-Response Math Accuracy:** Manually seed a ticket with `created_at` matching `2024-03-01 10:00:00` and a `first_response_at` matching `2024-03-01 11:30:00`. Run the query for average first response time. Verify the output correctly computes 90 minutes.
- [ ] **SLA Compliance Validation:** In a given week, close 1 ticket prior to `sla_due_at` and 3 tickets exactly 1 minute _after_ `sla_due_at`. View the Manager Dashboard compliance gauge. Verify it computes exclusively to exactly `25.0%`.

## 2. API Stability and Data Exporting

- [ ] **Large Export Timeout Preventative:** Setup a local factory containing 50,000 closed ticket records. Request an Excel export via `ExportBuilder.vue` for the entire dataset. Verify the browser returns a `202 Accepted` API response dropping the task to a queue instead of freezing, parsing internally, and dispatching a download URL when complete.
- [ ] **Asynchronous Download Link:** Trigger the export manually over the UI. Validate the dynamic Vue progress bar correctly polls the Job id and ultimately surfaces the URL download link perfectly without stalling the UI thread.
- [ ] **Manager Dashboard Chart Loading:** Authenticate as Admin and navigate to the Manager Dashboard. Run network tools to intercept the requests to `/api/v1/reports/heatmap` and others. Ensure the UI renders component skeleton states seamlessly and hydrates data sets correctly via `vue-chartjs`.

## 3. Recurring Mailable Automation

- [ ] **Cron Distribution Email:** Set system time parameter to 08:00 AM immediately pre-cron processing. Execute `php artisan schedule:run`. Capture SMTP delivery stream and evaluate that `DailyMetricsReport` correctly aggregated the previous 24 hours of ticket closures and fired accurately.

## 4. End-User Satisfaction Mechanisms (CSAT)

- [ ] **Closing Protocol Submission:** Close an End-User's Active Ticket in the queue. Validate the backend automatically intercepts the status change and dispatches a standard CSAT request notification directly to the requester email.
- [ ] **Unauthenticated Star Rating:** Check the received email, copy the isolated CSAT submission link (e.g. `/surveys/TOKEN_HASH`), and navigate via Incognito Mode. Submit a "4 Star" JSON payload with "Great service" mapped. Ensure an authenticated session token is completely avoided and a `201 Created` saves the `satisfaction_surveys` array.
- [ ] **Frontend Widget Injection:** Go back to the specific ticket in Agent Cockpit. Refresh the UI. Verify an inline CSAT badge injects onto the user's closing block reading "4 Stars - Great service" clearly.
