---
extends: _layouts.master
section: content
title: Installation
---

@include('_partials.work_in_progress')

Our ultimate goal is to have an easy-to-use web installer for MUM that generates a `.env` file based on your input.
Unfortunately, someone has to write the necessary code. If you would like to contribute, we highly appreciate your help.

For now, you will need to perform the following manual steps to install MUM.

@component('_components.info')
If you are worried about installing all of the requirements and MUM yourself, maybe have a look at our
<a class="text-white" href="{{ $page->baseUrl }}/debian-installation-guide">Debian Installation Guide</a>
that explains every step along the way and provides the necessary shell commands for fast copy'n'pasting.
@endcomponent

## Requirements

MUM and it's underlying framework Laravel have a few server requirements that need to be installed beforehand.
Please ensure that your server meets the following requirements:

- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

You will also need a working webserver like Apache2 or NGINX and a MySQL/MariaDB database.

## Quick Start

1. Clone the repository from GitHub and open the resulting directory:
    ```bash
    git clone https://github.com/mum-project/mum.git
    cd mum/
    ```
2. Install the dependencies and compile the frontend Javascript code:
    ```bash
    composer install
    ```
3. Create a `.env` file for your local environment (and configure it) with
    ```bash
    cp .env.example .env
    nano .env
    ```
4. Generate an application key with
    ```bash
    php artisan key:generate
    ```
5. Create your first user with
    ```bash
    php artisan domains:create example.com
    php artisan mailboxes:create admin example.com --super_admin --name='Super Admin'
    ```
