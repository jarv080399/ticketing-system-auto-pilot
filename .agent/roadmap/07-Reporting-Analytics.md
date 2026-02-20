# Module 7: Reporting & Analytics

**Objective:** Output actionable metrics, capacity planning data, and service level achievement (SLA) indicators to management without causing primary database stalls.

## Step 1: Analytics Queries (Data Engineer)

- [ ] Add necessary composite indices for historical querying (e.g., `status + created_at`, `assignee_id + resolved_at`).
- [ ] Write complex Eloquent aggregates inside a dedicated Repository (`ReportRepository`) to keep controllers thin.
  - Calculate _Avg Response Time_
  - Calculate _Avg Resolution Time_
  - Calculate _Volume by Category_ / _Heatmap_.

## Step 2: Backend Export API (Full-Stack Engineer)

- [ ] Expose `GET /api/v1/reports/performance` (Manager/Admin only).
- [ ] Expose `GET /api/v1/reports/heatmap` (Volume of tickets grouped by hour/day).
- [ ] Implement `ExportReportJob` (Queueable CSV generation) to prevent big raw-data exports from timing out PHP-FPM.
- [ ] Setup Laravel Mailables / Scheduled Jobs `DailyMetricsReport` for automated PDF/Email dispatches.

## Step 3: Frontend Dashboards (Vue 3)

- [ ] Integrate a lightweight charting library (e.g., Chart.js or Vue-ECharts).
- [ ] Build `ManagerDashboard.vue` containing:
  - Agent Performance Data Grid.
  - Bar Cart / Heatmap visualisations.
  - SLA breach notification center.
- [ ] Create UI for configuring Custom Exports (date-range pickers, CSV download button triggering background polling).

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Validate math calculations on "Resolution Time" using Factories with specific timestamps.
- [ ] **Backend (Pest):** Job assertion: assure `ExportReportJob` successfully pushes to the Queue and writes to local disk.
- [ ] **Frontend (Vitest):** Mocks charting data injection.
- [ ] **E2E (Cypress):** US005 - Admin triggers a custom CSV report download and views dashboard KPIs.
