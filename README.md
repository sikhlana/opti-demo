# Demo Project for Optimizely

A simple application stack that tries to solve the process of extracting content from the web, in the form of HTML pages and PDF documents.

## Tools / Technologies Used

- PHP 8.3
- Python 3.12
- Bun 1.0
- NodeJS 20.11

### Frameworks / Packages Used

- **Laravel**<br>
  As the main backend application to handle requests and scraping.
- **FastAPI**<br>
  As an extractor to extract texts from HTML pages and PDF documents.
- **Nuxt**<br>
  As the front-facing web page for the visitors.

### Third-party Applications Used

- **Nginx**<br>
  To serve the statically-generated site for the visitors.
- **MySQL**<br>
  RDB for the main application.
- **Redis**<br>
  In-memory database used as a cache and job queueing store.
- **Minio**<br>
  OSS alternative of S3 for persistent storage of the scraped contents and images.

## Running the Project

### Pre-requisites

- **Docker** with Compose plugin enabled

### Instructions

A `Makefile` is provided for ease of deployment. Just run:

```bash
make build run
```

And voila!
