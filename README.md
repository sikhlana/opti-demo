# Demo Project for Optimizely

A simple application stack that tries to solve the process of extracting content from the web, in the form of HTML pages and PDF documents.

## Tools / Technologies Used

- **PHP 8.3**<br>
  Built the main application to handle most of the tasks as this is the language I'm most comfortable with.
- **P**ython 3.12**<br>
  Built an internal API to extract texts from unstructured HTML pages and PDF documents as it has the best packages compared to other language stacks.
- **Bun 1.0**<br>
  Used as a package manager and development environment for the frontend panel for its fast execution.
- **NodeJS 20.11**<br>
  Used to compile sources of the frontend panel during the deployment process instead of _Bun_ because of the stability.

### Frameworks / Packages Used

- **Laravel**<br>
  The most matured framework for PHP. Used as the main backend application to handle HTTP requests and scrape web resources.
- **FastAPI**<br>
  Facilitates rapid development of web APIs. Used as an internal API server to extract text from content-heavy HTML pages and PDF documents.
- **Nuxt**<br>
  The best-in-class meta framework for Vue. Used as the main web page users are going to visit to use this application stack.

### Third-party Applications Used

- **Nginx**<br>
  To serve the statically-generated site for the frontend panel.
- **MySQL**<br>
  Relational database for the main application to store models.
- **Redis**<br>
  In-memory database used as a cache and job queueing store for the main application.
- **Minio**<br>
  OSS alternative of S3 for persistent storage of the scraped contents and images.

## Running the Project

### Pre-requisites

- **Docker** with _compose_ plugin enabled<br>
  The project has been containerized to ease the deployment process since the project uses multiple environment and application stacks.

### Instructions

A `Makefile` is provided in the root directory. Just:

```bash
# 1) Clone the project...
git clone https://github.com/sikhlana/opti-demo.git
# 2) Enter the directory...
cd opti-demo
# 3) Build the images and run the stack.
make build run
# 4) And voila!
```

### Accessing the Website

Please visit `http://localhost:3000` to access the panel.

## The Problems (and Their Solutions)

1) **Extracting Title and Body**<br>
   _Extracting the title and body of a web resource is fundamental for understanding and processing the content as the title provides a concise summary of the articles's topic, while the body contains the main textual information._<br><br>
   The content's title is extracted from HTML meta tags, and the body is obtained from relevant JSON-LD metadata; if unavailable, Python's [Goose3](https://pypi.org/project/goose3/) package is utilized to extract text from raw HTML.
