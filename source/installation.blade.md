---
extends: _layouts.master
section: content
title: Installation
---

@include('_partials.work_in_progress')

Our ultimate goal is to have an easy-to-use web installer for MUM that generates a `.env` file based on your input.
Unfortunately, someone has to write the necessary code. If you would like to contribute, we highly appreciate your help.

For now, you will need to perform the following manual steps to install MUM:

1. Clone the repository from GitHub and open the resulting directory:
    ```bash
    git clone https://github.com/mum-project/mum.git
    cd mum/
    ```
2. Create a `.env` file for your local environment (and configure it) with
    ```bash
    cp .env.example .env
    nano .env
    ```
3. Generate an application key with
    ```bash
    php artisan key:generate
    ```
4. Ensure the `.env` file read-only for the web server user (e.g. `www-data`) since our configuration file specifies
    the shell commands that are executed by MUM. Sample configuration:
    ```bash
    chown myuser:mygroup .env
    chmod 660 .env
    setfacl -m u:www-data:r .env
    ```