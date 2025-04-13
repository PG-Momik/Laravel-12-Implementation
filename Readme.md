# Setup Guide

1. **Copy the repository**
    - Clone or download the repository to your local machine.
      ```bash
        git clone git@github.com:PG-Momik/Laravel-12-Implementation.git
      ```

2. **Ensure Docker is installed**
    - Make sure you have Docker installed on your machine.

3. **Copy the environment file**
    - Copy `.env.example` to `.env`.
      ```bash
        cp .env.example .env
      ```

4. **Add token in `.env`**
    - In the `.env` file, add your Webz.io API token under the relevant variable. Example:
      ```env
      WEBZ_API_TOKEN=your-api-token-here
      ```

5. **Build and start the Docker containers**
    - Run the following command to build and start the containers:
      ```bash
      docker compose up --build
      ```
    - If you’ve already built the containers previously, you can simply use:
      ```bash
      docker compose up
      ```
    - This will run all migrations and everything you need to access the app's API.

6. **Access the APIs**
    - After the containers are running, you will have access to the following APIs:
    1. **`localhost:8000/`**: Returns basic app information.
    2. **`localhost:8000/api/posts`**: Fetches posts that are stored in the database, with pagination and the ability to filter by different fields. This api accepts query params similar to the ones that of Webz,io's News Lite API uses:
        - `q` (required): Keyword to search for in post titles.
        - `author`: Filter by post author.
        - `language`: Filter by language.
        - `sentiment`: Filter by sentiment of the post.
        - `ai_allow`: Boolean flag to filter AI-allowed posts.
        - `webz_reporter`: Boolean flag to filter posts reported by Webz.
        - `published`: Filter by published date (exact match).
        - `crawled`: Filter by crawled date (exact match).
        - Additionally, there is a `per_page` query param that changes how many results are returned. (default=20)
        ```
         http://localhost:8000/api/posts?author=Medin
        ```
    3. **`localhost:8000/api/fetch-webz-posts`**: Triggers a call to the Webz.io API. For now, these are the only query params that can be passed to Web.io's News Lite APi:
        - `q`
        - `sort`
        - `order`
        - `sentiment`
        - `highlight`
        - `size`
        ```
         http://localhost:8000/api/fetch-webz-posts?q=AI in military&sentiment=negative
        ```
---

# Same stuff as Node implementation
- See node implementation here: https://github.com/PG-Momik/Node-Implementation-Human/Readme.mmd
- From design considerations to TODO's everything carried over.


---

# Things to Note

## 1. Custom Artisan command
- I created a custom Artisan command: app:fetch-webz-posts. This allows users to fetch data from the News Lite API via the command line.
- I built this command to make testing my code easier.
- You’re welcome to use it too! Just note a small caveat: it needs to be run inside the container environment.
- Option 1: SSH into the container first
  ```bash
    docker compose exec app bash
  ```

  ```bash
    php artisan app:fetch-webz-posts --q="AI" --params="sentiment=positive&sort_by=relevance"
  ```

- Option 2: Run directly using docker (no need to SSH)
  ```bash
    docker compose exec app php artisan app:fetch-webz-posts --q="AI" --params="sentiment=positive&sort_by=relevance"
  ```
-You can pass additional query parameters (besides q) using the --params flag.

## 2. Can run test
- Unit tests have been written for the `WebzApiService`. You can run them using:
  ```
  php artisan test
  ```

  Or, if you're outside the container:

  ```
  docker compose exec app php artisan test
  ```

## 3. Did Not Normalize Categories and Topics Fields
- Although categories and topics are predefined in the API response, they haven't been normalized in the current schema.
- If normalization is needed in the future, consider the following structure:
    - `categories` (id, category_name)
    - `topics` (id, topic_name, category_id)
- Depending on how the API returns data:

  |If categories and topics are optional or can appear independently |        If both are always present|
    |----|-----|
  | post_categories` (post_id, category_id) `post_topics` (post_id, topic_id)|A single linking table such as post_topics might suffice.|

---

# Remaining TODOs
- [ ] Add support for more query parameters as documented by the Webz.io API.
- [ ] Investigate why the 'from' parameter doesn't affect results, possibly being ignored by the API.
- [ ] Confirm whether 'moreResultsAvailable' decrements on subsequent requests or if it remains static.
- [ ] Use 'moreResultsAvailable' in a while loop to continue fetching results until no more remain.
- [ ] Ensure UUIDs are truly unique across multiple pages of results.
- [ ] Debug why `sleep(1000)` causes the app to crash and find a more stable delay method.
- [ ] Format UUIDs to the standard hyphenated 36-character format.
- [ ] Normalize categories and topics fields — both are predefined, so a lookup or separate table could be used for consistency.

---

# Project Structure
- Standard laravel project structure.

---

# If things don't work out
- I know you can figure it out. Good luck!! :D
  