---
extends: _layouts.master
section: content
title: What is MUM?
---

@include('_partials.work_in_progress')

MUM is a user management web interface for email servers. It is designed to be easy to use but
still have powerful features.

The software works with the popular open source packages Postfix and Dovecot that can 
use MUM's MySQL database for their lookups. We provide all of the configuration that is necessary.
Additionally, you may integrate _any_ other software with MUM's workflow using our integrations.
This makes MUM a super flexible tool that is compatible to most environments.

## Screenshots
<a href="{{ $page->baseUrl }}/img/screenshot_dashboard.png">
![Screenshot of MUM's Dashboard]({{ $page->baseUrl }}/img/screenshot_dashboard.png)
</a>
<a href="{{ $page->baseUrl }}/img/screenshot_domains_index.png">
![Screenshot of a List of Domains]({{ $page->baseUrl }}/img/screenshot_domains_index.png)
</a>
<a href="{{ $page->baseUrl }}/img/screenshot_mailboxes_create.png">
![Screenshot of a Mailbox Creation Form]({{ $page->baseUrl }}/img/screenshot_mailboxes_create.png)
</a>
<a href="{{ $page->baseUrl }}/img/screenshot_aliases_edit.png">
![Screenshot of an Alias Edit Form]({{ $page->baseUrl }}/img/screenshot_aliases_edit.png)
</a>

