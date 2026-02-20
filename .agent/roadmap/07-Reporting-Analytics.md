# Module 7: Reporting & Analytics

**Objective:** Output actionable metrics, capacity planning data, SLA compliance indicators, CSAT scores, and trend analysis to management — without causing primary database stalls.

---

## Step 1: Analytics Queries & Tables (Data Engineer)

- [ ] Add composite indices for historical querying: `(status, created_at)`, `(assignee_id, resolved_at)`, `(category, created_at)`, `(priority, sla_due_at)`.
- [ ] Create `satisfaction_surveys` table (`ticket_id`, `user_id`, `rating` (1-5), `comment` (text nullable), `created_at`).
- [ ] Write complex Eloquent aggregates inside a dedicated `ReportRepository` to keep controllers thin:
  - _Avg First-Response Time_ (from `created_at` to `first_response_at`).
  - _Avg Resolution Time_ (from `created_at` to `resolved_at`).
  - _Volume by Category_ / _Volume by Priority_.
  - _Volume Heatmap_ (tickets grouped by hour-of-day × day-of-week).
  - _SLA Compliance Rate_ (% of tickets resolved before `sla_due_at`).
  - _CSAT Score Average_ (from `satisfaction_surveys`).
  - _Agent Leaderboard_ (tickets closed per agent, avg response time per agent).
  - _Trend Analysis_ (week-over-week, month-over-month volume and resolution changes).
  - _Top Recurring Categories_ (identify repeat issues for problem management).

## Step 2: Backend Export API (Full-Stack Engineer)

- [ ] Expose `GET /api/v1/reports/performance` (Manager/Admin only, date-range filterable).
- [ ] Expose `GET /api/v1/reports/heatmap` (Volume of tickets grouped by hour/day).
- [ ] Expose `GET /api/v1/reports/sla-compliance` (Breach rate by priority, team, time period).
- [ ] Expose `GET /api/v1/reports/csat` (Average CSAT, breakdown by category/agent).
- [ ] Expose `GET /api/v1/reports/trends` (Week-over-week comparison of volume, resolution time, CSAT).
- [ ] Implement `ExportReportJob` (Queueable CSV/Excel generation using `laravel-excel`) to prevent big exports from timing out PHP-FPM.
- [ ] Expose `POST /api/v1/reports/export` to trigger an async export and return a download URL when ready.
- [ ] Setup Laravel Mailables / Scheduled Jobs:
  - `DailyMetricsReport` – emailed to managers daily at 8am.
  - `WeeklySlaReport` – SLA compliance summary every Monday.
- [ ] **CSAT Survey System:**
  - Automatically send a CSAT survey email (1-5 star rating + optional comment) when a ticket moves to "Closed".
  - Expose `POST /api/v1/surveys/{token}` for the user to submit their rating (token-based, no login required).
  - Expose `GET /api/v1/tickets/{id}/survey` to check if a survey exists for a ticket.

## Step 3: Frontend Dashboards (Vue 3)

- [ ] Integrate a lightweight charting library (Chart.js via vue-chartjs or ApexCharts).
- [ ] Build `ManagerDashboard.vue` containing:
  - **KPI Cards:** Total open tickets, avg resolution time, SLA compliance %, avg CSAT score.
  - **Agent Performance Table:** Data grid with sortable columns (name, tickets closed, avg response time, avg CSAT).
  - **Heatmap Widget:** Interactive hour × day grid showing ticket volume intensity.
  - **Category Breakdown:** Pie/Donut chart of ticket categories.
  - **SLA Compliance Gauge:** Visual gauge showing current compliance rate vs. target.
  - **Trend Charts:** Line charts comparing current vs. previous period (volume, resolution time, CSAT).
- [ ] Build `ExportBuilder.vue`:
  - Date range picker (with presets: Today, Last 7 days, Last 30 days, Custom).
  - Report type selector.
  - Format selector (CSV / Excel).
  - "Generate Report" button with progress indicator (polls for completion).
- [ ] Build inline **CSAT display** on ticket detail view (show the star rating if a survey was submitted).

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Validate math calculations on "Avg Resolution Time" using Factories with specific timestamps.
- [ ] **Backend (Pest):** SLA compliance calculation correctly identifies breached vs. on-time tickets.
- [ ] **Backend (Pest):** CSAT survey is sent when ticket is closed; token-based submission works without authentication.
- [ ] **Backend (Pest):** Job assertion: `ExportReportJob` pushes to Queue, generates a file, and returns a download URL.
- [ ] **Backend (Pest):** Scheduled report mailable is dispatched with correct data.
- [ ] **Backend (Pest):** Heatmap aggregation returns 24×7 matrix correctly.
- [ ] **Frontend (Vitest):** Chart components render with mock data.
- [ ] **Frontend (Vitest):** Export builder triggers API call with correct parameters.
- [ ] **E2E (Cypress):** US005 - Admin views the Manager Dashboard and verifies KPI cards display data.
- [ ] **E2E (Cypress):** Admin triggers a CSV export, waits for completion, and downloads the file.
- [ ] **E2E (Cypress):** End-user submits a CSAT survey after ticket closure.
