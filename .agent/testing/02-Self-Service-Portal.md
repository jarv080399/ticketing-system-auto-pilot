# Module 2: End-User Self-Service Portal Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `End-User Self-Service Portal` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Ticket Submission Matrix (`TicketSubmissionForm.vue`)

- [ ] **Validation Layer Check:** Attempt to submit the form empty. Ensure the browser prevents submission with dynamic `required` field highlight and throws the proper localization error (e.g. "Title is required").
- [ ] **Category Dynamic Routing:** Select the "Hardware Issue" category tile. Confirm the generic title/descriptions inputs morph into specific drop-downs mapping to the backend payload (e.g. "Asset Make", "Model", etc).
- [ ] **Attachment Integrity Protocol:** Drag and drop 3 total valid image items (PNG, JPEG) and 1 invalid 50MB ZIP file. Verify the front-end file-size constraint intercepts the 50MB file, tossing a Vue toast verification warning without locking the rest of the queue. Verify the valid images hit `/api/v1/tickets` natively when the form submits.
- [ ] **Duplicate Filtering Engine:** Submit a new ticket titled "Printer broken". Verify the backend processes the array, recognizes a duplicated string within the requester’s ID the last 24hrs, and throws a UI-level soft warning before hard-saving.
- [ ] **Auto-Acknowledgement Execution:** Submit a valid ticket successfully. Navigate to Mailtrap/the mock-SMTP router and confirm the `TicketCreatedNotification` was instantly fired with the precise `TKT-XXXXX` identifier inside the template payload.

## 2. Universal Search Architecture

- [ ] **Real-Time Typo Indexing:** Enter a partial term (e.g., "vpn pass"). Ensure the search bar component throttles with a debounce wrapper (preventing API spam), executes `GET /api/v1/search?q=vpn_pass`, and natively renders a skeleton loader before injecting matched KB Articles or related public-facing tickets in a dropdown.
- [ ] **Strict Access Filtering:** Verify searching for a ticket title you do not own as an end-user strictly filters out other end-users’ request responses to guarantee zero BOLA exposure.

## 3. End-User Interface Tracking (`PortalLayout.vue` & `TicketDetailView`)

- [ ] **Live Status Tracking:** Open `My Tickets`. Confirm the ticket badge count matches your total non-closed array count. Navigate directly to a specific ticket via `/tickets/TKT-XXXXX`.
- [ ] **Domino Steps Generation:** Verify the `TicketStatusTracker.vue` paints the "In Progress" graphical dot green and gray out future stages natively mapping to the Enum value.
- [ ] **Conversation Sync:** As an end-user, post a text-only reply. Confirm the ticket `updated_at` parameter bumps dynamically in the cache, the API hits a 201 Created block, and the UI pushes your new comment into the timeline seamlessly.
