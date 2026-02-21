# Module 5: Knowledge Base Complete Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Knowledge Base` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Content Visibility Engine (`KbArticle` Visibility Filter)

- [ ] **Role Filtering:** Create Article A with `visibility: public` and Article B with `visibility: internal`. Log in as an End-User and execute `GET /api/v1/kb/articles`. Verify Article A renders, but Article B is completely blocked from the JSON response array.
- [ ] **Draft Constraint:** Set Article A status to `draft`. As an End-User, hit `GET /api/v1/kb/articles/slug-a`. Verify the payload returns `404 Not Found` or `403 Forbidden` effectively enforcing the draft parameter.
- [ ] **Agent View All:** Switch to an Agent token. Query the same index endpoints. Verify both Drafts, Internals, and Public articles are cleanly rendered and categorized.

## 2. Editor & Version Tracking (`KbArticleVersion`)

- [ ] **Revision Logging:** As an Agent, create a "VPN Troubleshooting" KB article. Edit the content body two separate times via `PUT /api/v1/kb/articles/{slug}`. Check the `kb_article_versions` table to verify three distinct version snapshots exist containing the diff data timestamped sequentially.
- [ ] **WYSIWYG Saving:** From the frontend `ArticleEditor.vue`, upload an embedded image via TipTap or Trix. Verify the markup stringizes and saves directly into the `LONGTEXT` database field without malforming the payload characters.
- [ ] **Stale Article Cron:** Modify an article's `updated_at` to accurately reflect a datetime > 6 months ago. Run `php artisan schedule:run`. Verify the system sweeps the database and dispatches a review flagged email notification to the `author_id`'s corresponding address mapping.

## 3. Feedback Processing (`helpful_yes/helpful_no`)

- [ ] **Helpfulness Capture:** Render an active KB article page. As an End-User, click "Yes" under "Was this helpful?". Verify the frontend emits a Vue Toast completion popup, intercepts double-clicks, and the backend increments `helpful_yes` by exactly 1 in the API response `view_count` incrementation block.

## 4. Universal Search & Suggestions Node (`ArticleSuggestions.vue`)

- [ ] **Index Search:** Input "WiFi" into the End-User portal header search. Validate that the MySQL Full-Text indexing fires instantly returning all KB articles containing the wildcard string in either the title or body `LIKE` operator natively.
- [ ] **Creation Suggestions Array:** Navigate to "Create New Ticket". Type "Monitor won't turn on" into the description field. Validate an asynchronous background query `GET /api/v1/kb/suggest?q=monitor+wont+turn+on` executes via the component, returning top 3 closely related articles as visual badges without blocking form progression.
