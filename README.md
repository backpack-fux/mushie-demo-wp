## This are gpt generated and they aren't 100% accurate, I made some corrections, this should get you to where you need to be though

# Local Server Setup and Environments for WordPress Project

## Prerequisites

- Local by Flywheel: Download and install Local.
- Node.js: Download and install Node.js.
- Cursor: Download and install [VS Code](https://cursor.sh/).

## Steps

### 1. Clone the Repository

Clone your project repository to your local machine:

```
git clone <repository-url>
cd <repository-directory>
```

### 2. Setup Local Environment

1. Open Local and create a new site:

- **Site Name**: Your project name
- **Environment**: Preferred environment (e.g., PHP 7.4, MySQL 5.7)
- **WordPress Version**: Latest

  2. Configure Site Paths:

- **Local Site Path**: Point to the cloned repository directory.

### 3. Configure wp-config.php

Ensure wp-config.php is correctly set up. If it doesn't exist, create it from wp-config-sample.php.

### 4. Install Dependencies

<!-- you probably have this already, if not how did you get here? -->

Install PHP and Node.js dependencies:

```
composer install
npm install
```

### 5. Composer

Composer is the package manager for PHP

```
composer install
composer update
```

### 6. Import Database

If you have a database dump, import it using Local's database management tool (Adminer or phpMyAdmin).

### 7. Update Database Configuration

Ensure database credentials in wp-config.php match those provided by Local:

```
// wp-config.php
define('DB_NAME', 'local_db_name');
define('DB_USER', 'local_db_user');
define('DB_PASSWORD', 'local_db_password');
define('DB_HOST', 'localhost');
```

### 8. Start Local Site

Start the site from Local and access it via the provided local URL.

### 9. Additional Configuration

If your project requires additional configuration, such as setting up environment variables, ensure these are configured in Local's environment settings or .env file.

### 10. Verify Setup

Access the local site and verify everything is working correctly.

## Troubleshooting

- Permissions: you need to add a user and password or try username: rick_sanchez password: mortysucks
- Dependencies: composer should be setup to do this correctly for you, if not then we may need to talk it through,, composer is very handsy
- Configuration: this was mostly composer effort

## Useful Commands

- Start Local Server: Local has a GUI with a start button, use that
- Build Shit: composer install || composer update
- Install Dependencies: composer require
