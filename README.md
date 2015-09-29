# code-snippets-saver

An experimental multi-user PHP application to save code snippets with automatic syntax-highlighting based on predefined categories. The code snippets can be shared publicly or saved privately for personal use.

**Live URL:** http://wayi.me/app/snippets

## Limitation

**Version 1.0 (Experimental)**
This application is built from scratch, so no any PHP framework is utilized here. The code syntax highlighting is powered by [highlight.js](https://highlightjs.org/) which automatically detects the programming language and applies the highlighting. The categories are limited, preset only to _Plain Text_, _HTML_, _CSS_, _JavaScript_, _PHP_, _MySQL_, _DOS_ and _C#_. No administration system.

## Installation

1. Upload all files and folders to your web server, e.g. `/public/www/code-snippets-saver`.
2. Modify `config.php` file for MySQL settings and application information.
3. Go to your **phpMyAdmin**, import these two SQL files; `snippets.sql` and `HNAuthDB.sql` into the same database that you have created.
4. Go to your URL, e.g. `http://www.example.com/code-snippets-saver` and you should see the application is working.
5. Create/register an account and start exploring it.

> P/S: Right now, I am not supporting any issue related to this application.

# License

Released under MIT license.
