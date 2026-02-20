# Module 5: Knowledge Base (KB)

**Objective:** Create a searchable, versioned repository of articles that empowers end-users to resolve common issues independently and provides agents with robust internal documentation, reducing overall ticket volume.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `kb_articles` table (`title`, `slug` (unique), `content` (LONGTEXT), `excerpt` (VARCHAR 255), `visibility` (`public`, `internal`), `status` (`draft`, `published`, `archived`), `author_id`, `category_id`, `view_count` (INT default 0), `helpful_yes` (INT default 0), `helpful_no` (INT default 0), `deleted_at`).
- [ ] Create `kb_categories` table (`name`, `slug`, `icon`, `parent_id` (nullable, self-referencing for hierarchy), `sort_order`).
- [ ] Create `kb_article_versions` table (`article_id`, `version_number`, `title`, `content`, `changed_by`, `change_summary`, `created_at`).
- [ ] Create `kb_article_tags` (pivot: `article_id`, `tag_id`) and `tags` table (`name`, `slug`).
- [ ] Apply MySQL Full-Text indexing to `kb_articles.title` and `kb_articles.content`.
- [ ] Create `KbArticle`, `KbCategory`, `KbArticleVersion`, `Tag` models with relationships.
- [ ] Implement Factory and Seeder (seed a mix of internal IT procedures and public FAQs across multiple categories).

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Expose `GET /api/v1/kb/articles` (Search API with visibility filtering: End-Users see `public` + `published`, Agents see all).
- [ ] Expose `GET /api/v1/kb/articles/{slug}` (single article view, increment `view_count`).
- [ ] Expose standard CRUD: `POST, PUT, DELETE` for `kb_articles` (Agent/Admin only).
- [ ] **Versioning:** On every update, create a `kb_article_versions` record with the previous content. Expose `GET /api/v1/kb/articles/{slug}/versions` to list version history.
- [ ] Expose CRUD for `kb_categories` (Admin only): `GET, POST, PUT, DELETE /api/v1/kb/categories`.
- [ ] Expose `POST /api/v1/kb/articles/{slug}/feedback` for end-user helpfulness rating (thumbs up/down, with optional comment).
- [ ] Connect the Universal Search implementation (from Module 2) to query the full-text `kb_articles` index.
- [ ] Implement **Article Suggestions API**: `GET /api/v1/kb/suggest?q=` returns top 3 relevant articles for display during ticket creation.
- [ ] Setup scheduled review flagging: Laravel scheduled job emails authors whose articles haven't been updated in 6+ months.

## Step 3: Frontend KB Portal (Vue 3)

- [ ] Build `ArticleEditor.vue`: A WYSIWYG or robust Markdown editor component (TipTap recommended) for Agents to write articles.
  - Include public/internal toggle switch.
  - Draft / Publish status toggle.
  - Category selector dropdown.
  - Tag multi-select input.
- [ ] Build `KbDirectory.vue` for exploring categories in a tree-based or card-grid layout.
- [ ] Build `KbArticleViewer.vue` to display content:
  - Table of contents sidebar (auto-generated from headings).
  - "Was this helpful?" Yes/No buttons with optional comment.
  - Related articles sidebar (same category or tags).
  - "Last updated" and version info.
- [ ] Build `ArticleVersionHistory.vue` to show diffs between versions (simple side-by-side or inline diff).
- [ ] Integrate into Ticket Creation view: `ArticleSuggestions.vue` component displaying top 3 suggestions before the user clicks Submit (inline, not blocking).

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Visibility Test - ensure non-agent requests for "internal" or "draft" articles return `403/404`.
- [ ] **Backend (Pest):** Full-text search returns relevant results and respects visibility.
- [ ] **Backend (Pest):** Versioning - update an article, verify a version record was created with old content.
- [ ] **Backend (Pest):** Feedback endpoint correctly increments `helpful_yes` / `helpful_no`.
- [ ] **Backend (Pest):** Category hierarchy (parent-child) nests correctly in API response.
- [ ] **Frontend (Vitest):** Markdown content correctly renders to HTML.
- [ ] **Frontend (Vitest):** Article suggestion component fetches and displays results on query change.
- [ ] **E2E (Cypress):** US002 - An end-user searches the KB and opens an article suggestion.
- [ ] **E2E (Cypress):** Agent creates a draft article, publishes it, and verifies it appears in the public KB.
