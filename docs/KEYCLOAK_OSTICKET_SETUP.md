# Keycloak + osTicket: login and return to the helpdesk

## Alignment with `forestdx-ui-changes`

That branch sends client **Login** to `login.php`, or **`login.php?do=ext&bk=oauth2.user.p…i…`** when the **Authentication: OAuth2** plugin is installed and active.

**This branch (`mahaagx`) uses the same priority:**

1. If **OAuth2 plugin** is active → header Login and bare `login.php` use **`do=ext`** (plugin handles IdP and return URL).
2. Otherwise → **IUDX Keycloak** auth URL + **`keycloak-callback.php`** / `index.php?code=` handling (see below).

Staff SCP behaviour from `forestdx-ui-changes` is kept where it helps subpath installs: **`scp/staff.inc.php`** `staffLoginPage` dest and **`scp/login.php`** CSRF redirect to **`ROOT_PATH.'scp/login.php'`**.

---

## Current environment (this project)

Base URL: **`http://localhost/osticket/`**

### Keycloak server config (code)

| File | Role |
|------|------|
| `include/client/iudx-keycloak-config.inc.php` | Base URL, realm, client id, **redirect URI**, optional client secret |
| `include/client/mahaagx-keycloak-url.inc.php` | Builds Login / Register links (`response_mode=query`, PKCE, session state) |
| `keycloak-callback.php` | Recommended **Valid redirect URI** target — exchanges `code` and creates osTicket session |
| `include/client/iudx-keycloak-callback.inc.php` | Token exchange + userinfo + `UserAuthenticationBackend` login |
| `index.php` | If redirect URI is exactly `http://localhost/osticket/` (or `.../index.php`), `?code=` is handled here too |

| Setting | Default |
|--------|---------|
| **Server** | `https://mahaagx.maharashtra.gov.in/auth` |
| **Realm** | `mahaagx-prod` |
| **Client ID** | `angular-client` |
| **Redirect URI** | `http://localhost/osticket/keycloak-callback.php` (must match Keycloak exactly) |

**Why you saw Login after “sign-in”:** with `response_mode=fragment`, `#code=...` is **never sent to PHP**, so osTicket could not create a session. Use **`response_mode=query`** and a redirect URI that hits `keycloak-callback.php` or `index.php?code=...`. A small script in `header.inc.php` also forwards old `#code=` URLs to `keycloak-callback.php`.

Add the **redirect URI** under **angular-client → Valid redirect URIs** and **Web origins** (`http://localhost`).

**Session after IUDX login:** tokens are stored as `$_SESSION['iudx_keycloak_access_token']`, not `oauth2_access_token`, so `client.inc.php` does not run the OAuth2 plugin’s userinfo check against a different Keycloak host (which would log the user out immediately).

For the **osTicket OAuth2 plugin** (round-trip to `login.php`), still register e.g.  
`http://localhost/osticket/login.php` if you use that flow.

If you use a non-default Apache port, include it in every URL (e.g. `http://localhost:8080/osticket/`).

---

## Why a separate Keycloak client is needed

The URLs used by the **MahaAgX Angular app** (e.g. `client_id=angular-client` and  
`redirect_uri=https://mahaagx.maharashtra.gov.in/`) send the user **back to the main portal**
after authentication, not to osTicket.

To send users **back to osTicket** after Keycloak login, you need:

1. A **dedicated Keycloak client** for osTicket (do not reuse `angular-client` for this).
2. A **redirect URI** that points to your osTicket **login** endpoint (where the OAuth code is exchanged).
3. The **osTicket “Authentication: OAuth2” plugin** (or equivalent) configured with that client and Keycloak’s OpenID Connect endpoints.

Static links in `include/client/mahaagx-keycloak-url.inc.php` are only suitable for
“send user to the portal”; they do **not** complete a login session inside osTicket.

## 1. Keycloak: create a client for osTicket

In Keycloak Admin → your realm (e.g. `mahaagx-prod`):

| Setting | Typical value |
|--------|----------------|
| Client ID | e.g. `osticket-client` or `mahaagx-osticket` |
| Client type | **OpenID Connect** |
| Access type | **Confidential** (with client secret) or **Public** with PKCE — match what the osTicket OAuth2 plugin supports |
| Valid redirect URIs | Exact URLs of your osTicket `login.php` (see below) |
| Web origins | Your osTicket origin (e.g. `https://support.example.gov.in` or `http://localhost`) |

### Redirect URI examples

Use the **full URL** to `login.php` (including path prefix if the site is in a subdirectory):

- **This install (XAMPP):** `http://localhost/osticket/login.php`
- Production: `https://YOUR-DOMAIN/osticket/login.php`

The osTicket OAuth2 plugin may also document an alternate callback path; if so, add **that** exact URL in Keycloak.

**Important:** Keycloak requires an **exact** match (scheme, host, port, path). Trailing slashes matter if your server uses them.

## 2. Keycloak: OpenID Connect endpoints

For realm `mahaagx-prod` on `https://mahaagx.maharashtra.gov.in` (adjust if your auth host differs):

| Purpose | URL |
|--------|-----|
| Well-known | `https://mahaagx.maharashtra.gov.in/auth/realms/mahaagx-prod/.well-known/openid-configuration` |
| Authorization | `https://mahaagx.maharashtra.gov.in/auth/realms/mahaagx-prod/protocol/openid-connect/auth` |
| Token | `https://mahaagx.maharashtra.gov.in/auth/realms/mahaagx-prod/protocol/openid-connect/token` |
| Userinfo | `https://mahaagx.maharashtra.gov.in/auth/realms/mahaagx-prod/protocol/openid-connect/userinfo` |

Use the plugin’s fields to paste **Authorization URL**, **Token URL**, and **Userinfo URL** (or issuer, if the plugin supports discovery).

## 3. osTicket: install and configure the OAuth2 plugin

1. Download **Authentication: OAuth2** from the [osTicket plugin list](https://osticket.com/download) (or your vendor package).
2. Upload/install it in osTicket **Admin Panel → Manage → Plugins**.
3. Add an **instance** for **end users** (clients), not only staff, if you want portal users on the client interface.
4. Fill in:
   - **Client ID** / **Client secret** (from Keycloak)
   - **Scopes**: usually `openid email profile` (add others if your IdP requires them)
   - **Redirect URI**: must match what you entered in Keycloak (same as your public `login.php` URL)

**Current MahaAgX UI behaviour:** the header **Login** link and a plain GET to  
`login.php` redirect to **`MAHAAGX_KEYCLOAK_AUTH_URL`** (Maharashtra Keycloak).  
Use **`login.php?local=1`** if you need the local email/password form (e.g. support).  
OAuth2 plugin flows still use **`login.php?do=ext&bk=…`** (not redirected).

## 4. Registration (“Register” button)

The **Register** link still points to Keycloak’s registration UI  
(`MAHAAGX_KEYCLOAK_REGISTER_URL` in `include/client/mahaagx-keycloak-url.inc.php`).

After registration, where the user lands is controlled by **Keycloak** (client “Home URL”, required actions, etc.), not osTicket. If you need users to land on osTicket after signup, configure that flow in Keycloak or switch registration to an osTicket page that then triggers SSO.

## 5. Optional: remove the dead redirect in `login.php`

`login.php` contains:

```php
if (class_exists('oAuth2')) {
    Http::redirect(oAuth2::getAuthUrl('keycloak'));
}
```

There is no global `oAuth2` class in core; this is effectively dead unless you add such a class. Prefer the **OAuth2 plugin** + `do=ext` flow above.

## Summary

| Goal | Action |
|------|--------|
| User logs in and returns to **osTicket** | New Keycloak client + redirect to `…/login.php` + OAuth2 plugin configured |
| User stays on **MahaAgX portal** | Keep using `angular-client` / portal URLs (current static auth URL) |
