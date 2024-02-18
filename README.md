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
   _Extracting the title and body of a web resource is fundamental for understanding and processing the content as the title provides a concise summary of the articles's topic, while the body contains the main textual information._<br>
   <br>
   Extracting structured data from HTML documents involves parsing and manipulating the Document Object Model (DOM). The content title is extracted from HTML meta tags by using PHP's [DOM extension](https://www.php.net/manual/en/book.dom.php), and the body is obtained from relevant JSON-LD metadata; if unavailable, Python's [Goose3 package](https://pypi.org/project/goose3/) package is utilized to extract text from raw HTML.

2) **Extracting Metadata**<br>
   _Metadata provides additional context about the web resource, including information like authorship, publication date, keywords, etc and is crucial for categorizing and indexing web content accurately._<br>
   <br>
   Extracting metadata involves understanding various formats like HTML meta tags, JSON-LD, and other structured data formats. PHP's [DOM extension](https://www.php.net/manual/en/book.dom.php) is used to extract all and any metadata available.

3) **Extracting Images**<br>
   _Images embedded within web content contribute to its visual appeal and often convey essential information and extracting them enables applications to analyze visual content or display it to users._<br>
   <br>
   Image extraction entails retrieving the image source within the article body using PHP's [DOM extension](https://www.php.net/manual/en/book.dom.php), after which the URIs are traversed by a crawler to retrieve, validate, and cache the image data.

4) **Avoiding Duplicate Pages**<br>
   _Preventing the ingestion of duplicate web pages helps in conserving resources and avoiding redundant processing and ensures that only unique content is indexed or stored._<br>
   <br>
   Duplicate detection involves normalizing the requested URL by eliminating tracking parameters from the URL query string, generating a SHA384 hash of the string, and comparing the hash against the indexed column in the database to identify matches.

5) **URL Canonicalization**<br>
   _Canonicalizing URLs involves transforming them into a standardized format to remove redundancy and ensure consistency and helps in identifying equivalent URLs and improving the accuracy of web content retrieval._<br>
   <br>
   URL canonicalization entails inspecting the `Link` HTTP header line to identify any potential canonical URL of the fetched resource; if absent, PHP's [DOM extension](https://www.php.net/manual/en/book.dom.php) is employed to locate the `<link rel=canonical>` meta tag, and if it's missing, the URL remains unchanged.

6) **Orchestrating Requests**<br>
   _Coordinating HTTP requests to remote servers involves managing concurrency, handling timeouts, and optimizing resource utilization to ensure efficient retrieval of web content without overwhelming servers or causing performance bottlenecks. Additionally, retaining session cookies, and adhering to rate limits are essential for accessing protected or rate-limited web resources securely and efficiently._<br>
   <br>
   Concurrency is controlled using Laravel's queue worker, dynamically adjusting the number of processes, and scaling back when encountering rate limits, while PHP's [Guzzle package](https://packagist.org/packages/guzzlehttp/guzzle) serves as a wrapper for [cURL](https://curl.se/), facilitating internet access. Cookies from various websites are retained for subsequent requests using a cookie jar. _However, this version of the application does not bypass authentication barriers._

7) **Saving and Indexing Content**<br>
   _Saving extracted content in a structured format and indexing it enables efficient retrieval and search functionalities. It facilitates organizing and managing large volumes of web data effectively._<br>
   <br>
   Presently, the system exclusively saves content into structured data, although indexing capability can be enabled by integrating a search engine such as [Elasticsearch](https://www.elastic.co/elasticsearch), [Meilisearch](https://www.meilisearch.com/), or [Typesense](https://typesense.org/).

8) **Avoid Scraping the Same Resource Again and Again**<br>
   _Preventing redundant scraping of the same resource is crucial for conserving computational resources, reducing server load, and avoiding unnecessary network traffic. It ensures efficient utilization of resources and prevents unnecessary duplication of data._<br>
   <br>
   Prevention is implemented by tracking previously visited URLs, indexing the hash of each URL in the database to avoid redundant requests and data duplication.

9) **Explicitly Scrape the Same Resource Again to Update It**<br>
   _Explicitly scraping a resource again to update it allows systems to retrieve the latest version of the content, incorporating any changes or updates since the last scrape. It ensures that the indexed or stored data remains current and accurate._<br>
   <br>
   Presently, a basic checkbox is utilized to trigger the system to scrape a previously scraped URL and reindex it. However, future enhancements may involve implementing mechanisms to automatically detect changes, such as checking for timestamps from relevant HTTP header lines or HTML meta tags, or employing efficient content diffing tools to automate the process of rescraping and caching content.

10) **Handling Responses of Remote Servers**<br>
    _Effectively handling responses from remote servers involves interpreting HTTP status codes, following redirects, and managing network errors to ensure reliable scraping._<br>
    <br>
    In the event of any network, client, or server errors, the system implements a backoff mechanism, temporarily delaying the processing of the URI to allow for retries.

11) **Cache Responses from the Server**<br>
    _Caching responses from the server helps in reducing latency and bandwidth usage by storing and reusing previously retrieved content. It improves the overall performance and scalability of web content ingestion systems._<br>
    <br>
    At present, the system stores the entire HTTP response body into S3 as a raw data file, but to mitigate high disk usage, a mechanism can be introduced to periodically remove outdated content files from storage.

12) **Ingesting Resources Other Than HTML Pages**<br>
    _Ingesting various types of content like PDFs, videos, images, and HTML data tables expands the scope of web content ingestion systems, enabling them to process diverse multimedia and structured data._<br>
    <br>
    At present, the system exclusively handles PDF documents for text extraction and relevant metadata retrieval, leveraging Python's [PyPDF2 package](https://pypi.org/project/PyPDF2/).
