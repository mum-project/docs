---
extends: _layouts.master
section: content
title: Integration with Postfix and Dovecot
---

@include('_partials.work_in_progress')

For Postfix and Dovecot to be able to work with MUM, they need to be configured to use MUM's database
as their lookup tables. The following steps will guide you through the process of setting all required options.

## Postfix
Postfix needs to know which domains and mailbox users exist, so that emails to unknown recipient addresses can
be refused. Moreover, since Postfix also handles outgoing emails, it needs to be able to authenticate sending users.
The MySQL user for Postfix can be read-only, since Postfix won't change any values in the database.
The following code snippets represent an excerpt from valid `main.cf` and `master.cf` configuration files that include
all MySQL lookup files.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/main.cf')
smtp_tls_policy_maps = mysql:/etc/postfix/sql/tls-policies.cf
...
smtpd_recipient_restrictions = check_recipient_access mysql:/etc/postfix/sql/recipient-access.cf
...
virtual_alias_maps = mysql:/etc/postfix/sql/aliases.cf
virtual_mailbox_maps = mysql:/etc/postfix/sql/mailboxes.cf
virtual_mailbox_domains = mysql:/etc/postfix/sql/domains.cf
local_recipient_maps = $virtual_mailbox_maps
@endcomponent

@component('_components.code_file')
    @slot('filename', '/etc/postfix/master.cf')
...
submission inet n       -       y       -       -       smtpd
  ...
  -o smtpd_sender_login_maps=mysql:/etc/postfix/sql/sender-login-maps.cf
...
@endcomponent

Each of the following sections represent one configuration file for a MySQL lookup table.
You should replace the following placeholders with the actual value on your machine:

| Placeholder            | Explanation                                                     |
|------------------------|-----------------------------------------------------------------|
| `mum_postfix_user`     | The MySQL user Postfix will use. This user should be read-only. |
| `mum_postfix_password` | A securely generated random password for Postfix's MySQL user.  |
| `mum_database`         | The database that MUM should use.                               |

@component('_components.warning')
Be aware that all SQL statements of the following files <strong>must not</strong> contain any line breaks.
Otherwise, Postfix will not be able to interpret your configuration.
@endcomponent

### TLS Policy Maps
For Postfix to be able to perform an SQL lookup on TLS policies, it needs to
know the credentials and the concrete SQL command. This file provides that
information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/tls-policies.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT policy, params FROM tls_policies WHERE domain = '%s';
@endcomponent

### Recipient Access
For Postfix to be able to perform an SQL lookup on allowed recipients, it needs
to know the credentials and the concrete SQL command. This file provides that
information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/recipient-access.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT IF(send_only = 1, 'REJECT', 'DUNNO') AS access FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%u' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1 LIMIT 1;
@endcomponent

### Sender Login Maps
For Postfix to be able to perform an SQL lookup on allowed senders, it needs
to know the credentials and the concrete SQL command. This file provides that
information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/sender-login-maps.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT CONCAT(mailboxes.local_part, '@', domains.domain) AS owns FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%u' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1 UNION SELECT GROUP_CONCAT(CONCAT(mailboxes.local_part, '@', mailbox_domains.domain) SEPARATOR ',') AS owns FROM aliases INNER JOIN domains alias_domains ON aliases.domain_id = alias_domains.id INNER JOIN alias_senders ON aliases.id = alias_senders.alias_id INNER JOIN mailboxes ON alias_senders.mailbox_id = mailboxes.id INNER JOIN domains mailbox_domains ON mailboxes.domain_id = mailbox_domains.id WHERE aliases.local_part = '%u' AND alias_domains.domain = '%d' AND alias_domains.active = 1 AND mailbox_domains.active = 1 AND aliases.active = 1;
@endcomponent

### Virtual Alias Maps
For Postfix to be able to perform an SQL lookup on allowed alias recipients, it
needs to know the credentials and the concrete SQL command. This file provides
that information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/aliases.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT GROUP_CONCAT(DISTINCT alias_recipients.recipient_address SEPARATOR ',') FROM aliases INNER JOIN alias_recipients ON aliases.id = alias_recipients.alias_id INNER JOIN domains ON aliases.domain_id = domains.id WHERE aliases.local_part = '%u' AND domains.domain = '%d' AND aliases.active = 1 AND domains.active = 1;
@endcomponent

### Virtual Mailbox Maps
For Postfix to be able to perform an SQL lookup on available mailboxes, it needs
to know the credentials and the concrete SQL command. This file provides that
information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/mailboxes.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT 1 AS found FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%u' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1 LIMIT 1;
@endcomponent

### Virtual Mailbox Domains
For Postfix to be able to perform an SQL lookup on available mailbox domains, it
needs to know the credentials and the concrete SQL command. This file provides
that information to your Postfix server.

@component('_components.code_file')
    @slot('filename', '/etc/postfix/sql/domains.cf')
user = mum_postfix_user
password = mum_postfix_password
hosts = 127.0.0.1
dbname = mum_database
query = SELECT domain FROM domains WHERE domain = '%s' AND active = 1;
@endcomponent

## Dovecot
Just like Postfix, Dovecot needs access to the MUM database. 
The MySQL user for Dovecot can be read-only, since Dovecot won't change any values in the database.
The following code snippet represents an excerpt from a valid `dovecot.conf` configuration file 
that specifies where Dovecot will find it's SQL queries to manage and authenticate users.


@component('_components.code_file')
    @slot('filename', '/etc/')
...
passdb {
    driver = sql
    args = /etc/dovecot/dovecot-sql.conf
}
userdb {
    driver = sql
    args = /etc/dovecot/dovecot-sql.conf
}
...
@endcomponent

Each of the following sections represent one configuration file for a MySQL lookup table.
You should replace the following placeholders with the actual value on your machine:

| Placeholder            | Explanation                                                     |
|------------------------|-----------------------------------------------------------------|
| `mum_dovecot_user`     | The MySQL user Dovecot will use. This user should be read-only. |
| `mum_dovecot_password` | A securely generated random password for Dovecot's MySQL user.  |
| `mum_database`         | The database that MUM should use.                               |

@component('_components.warning')
Be aware that all SQL statements of the following files <strong>must not</strong> contain any line breaks.
Otherwise, Dovecot will not be able to interpret your configuration.
@endcomponent

### Dovecot SQL File
This file contains the MySQL credentials and all of Dovecot's SQL queries. 
The `default_pass_scheme` needs to match MUM's password hashing algorithm. Otherwise, Dovecot will not be able
to authenticate your users. 
We strongly recommend that you use either Argon or Bcrypt for password hashing. That said, most Linux
distributions (like Debian) currently provide Dovecot packages that are too old to support Argon or Bcrypt.
Have a look at the [Dovecot documentation on password schemes](https://wiki.dovecot.org/Authentication/PasswordSchemes)
for details on which version of Dovecot supports the hashing algorithm of your choice.
If your Linux distribution does not support Argon and Bcrypt, you should probably use SHA512 or SHA256.

@component('_components.code_file')
    @slot('filename', '/etc/dovecot/dovecot-sql.conf')
driver = mysql
connect = "host=127.0.0.1 dbname=mum_database user=mum_dovecot_user password=mum_dovecot_password"
default_pass_scheme = BLF-CRYPT

password_query = SELECT mailboxes.local_part AS username, domains.domain, mailboxes.password FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%n' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1;
user_query = SELECT mailboxes.homedir AS home, mailboxes.maildir AS mail, CONCAT('*:storage=', COALESCE(mailboxes.quota, domains.quota, 0), 'G') AS quota_rule FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%n' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1 AND mailboxes.send_only = 0;
iterate_query = SELECT mailboxes.local_part AS username, domains.domain FROM mailboxes INNER JOIN domains ON mailboxes.domain_id = domains.id WHERE mailboxes.local_part = '%n' AND domains.domain = '%d' AND domains.active = 1 AND mailboxes.active = 1 AND mailboxes.send_only = 0;
@endcomponent
