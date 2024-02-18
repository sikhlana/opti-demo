# Demo Project for Optimizely

A simple application stack that tries to solve the process of extracting content from the web, in the form of HTML pages and PDF documents.

## Tools / Technologies Used

- **PHP 8.3**<br>
  Built the main application to handle most of the tasks as this is the language I'm most comfortable with.
- **P**ython 3.12**<br>
  Built an internal API to extract texts from unstructured HTML pages and PDF documents.
- **Bun 1.0**<br>
  Used as a package manager for the frontend panel.
- **NodeJS 20.11**<br>
  Used to compile sources for the frontend panel.

### Frameworks / Packages Used

- **Laravel**<br>
  As the main backend application to handle requests and scrape web resources.
- **FastAPI**<br>
  As an extractor to extract texts from HTML pages and PDF documents.
- **Nuxt**<br>
  As the front-facing web page for the visitors.

### Third-party Applications Used

- **Nginx**<br>
  To serve the statically-generated site for the frontend panel.
- **MySQL**<br>
  Relational database for the main application.
- **Redis**<br>
  In-memory database used as a cache and job queueing store for the main application.
- **Minio**<br>
  OSS alternative of S3 for persistent storage of the scraped contents and images.

## Running the Project

### Pre-requisites

- **Docker** with Compose plugin enabled

### Instructions

A `Makefile` is provided in the root directory for ease of deployment. Just run:

```bash
git clone https://github.com/sikhlana/opti-demo.git # Clone the project...
cd opti-demo # Enter the directory...
make build run # Build the images and run the stack.
```

And voila!
