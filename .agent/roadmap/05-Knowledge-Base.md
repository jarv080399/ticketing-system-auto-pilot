# Module 5: Knowledge Base (KB)

**Objective:** Create a searchable repository of articles that empowers end-users to resolve common issues and provides agents with robust internal documentation.

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `kb_articles` table (`title`, `content` (LONGTEXT), `visibility` (`public`, `internal`), `author_id`, `deleted_at`).
- [ ] Apply MySQL Full-Text indexing to `title` and `content`.
- [ ] Create `KbArticle` model, Factory, and Seeder (seed a mix of internal IT procedures and public FAQs).

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Expose `GET /api/v1/kb` (Search API with visibility filtering: End-Users only see `public`, Agents see both).
- [ ] Expose standard CRUD: `POST, PUT, DELETE` for `kb_articles` (Agent only).
- [ ] Connect the Universal Search implementation (from Module 2) to query the full-text `kb_articles` index.
- [ ] Setup scheduled review flagging (e.g., email author if `updated_at` > 6 months).

## Step 3: Frontend KB Portal (Vue 3)

- [ ] Build `ArticleEditor.vue`: A WYSIWYG or robust Markdown editor component for Agents to write articles. Include public/internal toggle switch.
- [ ] Build `KbDirectory.vue` for exploring topics/categories.
- [ ] Build `KbArticleViewer.vue` to display content. Add end-user feedback buttons (Thumbs-up, usefulness rating).
- [ ] Integrate into Ticket Creation view: "Did you mean this article?" component displaying top 3 suggestions before the user clicks Submit.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Visibility Test - ensure non-agent requests for "internal" articles return `403/404`.
- [ ] **Backend (Pest):** Full-text search performance/latency check.
- [ ] **Frontend (Vitest):** Content markdown correctly renders to HTML.
- [ ] **E2E (Cypress):** US002 - An end-user searches the KB and opens an article suggestion.
