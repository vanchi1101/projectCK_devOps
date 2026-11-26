# projectCK_devOps

## Laptop storefront categories

Run `database/migrations/20261126_seed_laptop_categories.sql` against the configured
`shopvip` database to create the eight laptop categories. The migration is safe to
run again and hides the old `TV` demo category from the public storefront.

Run `database/migrations/20261126_seed_tgdd_laptop_products.sql` to add the 149
currently listed laptop models. The source currently has 17 MacBook and 12
Gigabyte models in these category pages. Imported products start with stock 1 as
a display placeholder; update inventory before accepting real orders.

The public storefront is available at `/`; the admin dashboard is available at
`/Admin` after signing in with an admin account. Use **Quản lý cửa hàng** in the
admin area to add product records with their authorized image URLs, prices, and
specifications.

## Google sign-in

Run `database/migrations/20261126_add_google_login.sql` against `shopvip` before
enabling Google sign-in. In Google Cloud Console, create an OAuth 2.0 Web
application client and add this authorized redirect URI:

`http://localhost/Auth/Google/Callback`

Set `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in the Apache environment
(for example, in the local XAMPP Apache configuration), then restart Apache.
Optionally set `GOOGLE_REDIRECT_URI` if the callback URL differs. Do not commit
the client secret or share it in chat. First-time Google sign-ins create regular
customer accounts; they are not automatically linked to existing accounts by
email. Existing Google-linked accounts continue to sign in using their Google
subject identifier.

## Password recovery

Run `database/migrations/20261126_add_password_reset_expiry.sql` against
`shopvip`. New registrations require a valid email address. Password reset
codes are sent using the existing SMTP settings in the `options` table, stored
as hashes, and expire after 10 minutes.
Existing accounts without an email must have one added by an admin in
**Thành viên → Chỉnh sửa** before password recovery can be used.

## Order email

After an order is created, the customer and the store email configured in
`options.email_admin` receive its items, totals, payment method, and delivery details.
Checkout requires a valid customer email (pre-filled from the account when
available). Delivery uses the SMTP account configured in `options.email` and
`options.pass_email`; configure the recipient in **Admin → Cấu hình**. An email
delivery issue does not cancel the order.

## GitHub Actions CI/CD to cPanel

The workflow in `.github/workflows/deploy.yml` runs six separate jobs in order:
Checkout → Validate PHP → Test application logic → Build deployment package →
Deploy to cPanel → Verify live website. It runs on pushes to `main` and can also
be started with **Actions → PHP CI/CD to cPanel → Run workflow**. Manual runs
deploy only when the selected branch is `main`.

### GitHub Secrets

In the repository, open **Settings → Secrets and variables → Actions → New
repository secret** and add:

| Secret | Value |
| --- | --- |
| `CPANEL_SSH_HOST` | cPanel SSH hostname (or IPv4 address) |
| `CPANEL_SSH_USER` | cPanel account username |
| `CPANEL_SSH_PORT` | SSH port; optional, defaults to `22` |
| `CPANEL_SSH_PRIVATE_KEY` | Private key for an SSH key authorized by cPanel |
| `CPANEL_SSH_KEY_PASSPHRASE` | Passphrase set when generating the cPanel SSH key |
| `CPANEL_SSH_KNOWN_HOSTS` | Verified SSH host-key line(s) for this server and port |
| `CPANEL_DEPLOY_PATH` | Absolute document-root path, commonly `/home/CPANEL_USER/public_html` |
| `SITE_URL` | Public HTTPS URL to check after deployment, e.g. `https://example.com/` |

Enable SSH access in cPanel, create/import an SSH key pair, and authorize its
public key. Add the private key only as the `CPANEL_SSH_PRIVATE_KEY` secret.
Store the key's passphrase as `CPANEL_SSH_KEY_PASSPHRASE`; the workflow loads
the encrypted key into a temporary SSH agent without printing the passphrase.
Obtain the server host key with `ssh-keyscan -p PORT HOST`, verify its
fingerprint with the hosting provider, and store the verified line as
`CPANEL_SSH_KNOWN_HOSTS`. Do not disable SSH host-key checking. Confirm the
document root in cPanel's **Domains** page or ask the hosting provider; do not
assume every domain uses `public_html`.

### What deployment preserves

The package installs Composer production dependencies, then uploads files over
SSH and extracts them in the configured document root. It does not delete
remote files. The package excludes `config/config.php`, `assets/storage/`,
`cron/LogBank.txt`, cron lock files, SQL files, and error logs. Therefore the
hosting database credentials/configuration, uploaded storage, and MySQL data
remain untouched; the workflow never imports dumps or runs database migrations.
Keep the production database settings in the existing cPanel copy of
`config/config.php`. Before the first deployment, create/configure that file in
cPanel with the cPanel MySQL database name, user, password, and the site's
production base URL; do not upload the local XAMPP configuration. The PHP
application reads its database settings from this file, not from GitHub Actions
environment variables. Apply any required database migration manually after
backing up the database.

For cPanel setups that support Apache environment variables, `config/config.php`
also accepts `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and
`DB_PASSWORD`. For example, Apache can set them with `SetEnv DB_NAME shopvip`
inside the relevant virtual-host or `.htaccess` configuration, subject to the
host's policy. Existing local defaults remain unchanged when these variables
are absent. Never add real database passwords to `.env.example`, source control,
workflow files, or GitHub Actions logs.

### Checks and troubleshooting

Open **Actions**, select the run, then open a job or step to inspect its log.
Deploy is gated on successful validation, tests, and build; Verify runs only
after deployment. If a job fails:

1. **Validate**: check the PHP parse error and Composer manifest/lock output;
   verify that the cPanel PHP version is compatible with PHP 8.2.
2. **Test**: review the failing application assertion in `tests/run.php`.
3. **Build**: check Composer dependency resolution and the deployment-package
   protection check.
4. **Deploy**: confirm SSH access, authorized key, verified host key, port, and
   document-root permissions. If the log says shell access is not enabled,
   ask the hosting provider to enable shell access for the cPanel account;
   this workflow needs remote SSH commands to extract the package. Missing
   secrets or unexecuted remote commands are reported as explicit errors.
5. **Verify**: check that `SITE_URL` is correct, publicly reachable over HTTPS,
   and returns HTTP 2xx with application content rather than a directory
   listing or a PHP/database error. This is still a basic health check, not a
   full database integration test.

If a deployment causes a problem, first restore the affected files from the
hosting backup if the extraction was incomplete. Then revert the faulty commit
(`git revert <commit>`) and push the revert to `main`; the same tested workflow
will deploy the previous code. Do not restore or overwrite `config/config.php`
or database files as part of a code rollback. Keep regular cPanel file and
MySQL backups before the first production deployment.

### Demo flow

1. Configure and verify the secrets, then run **Run workflow** once on `main`
   to confirm the connection.
2. Make a small, safe PHP or CSS change locally.
3. Run `php tests/run.php` and commit/push the change to `main`.
4. In **Actions**, show the six sequential jobs and their step logs; wait for
   Validate, Test, Build, Deploy, and Verify to pass.
5. Refresh the public site and show the updated change. The workflow run and
   job logs provide the demo evidence.
