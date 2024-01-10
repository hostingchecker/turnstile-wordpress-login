# Cloudflare Turnstile WordPress Login
Easily add Cloudflare Turnstile to your WordPress website login form without any plugins to protect them from spam! 

A user-friendly, privacy-preserving Google reCAPTCHA alternative.

## Supported Forms
We are actively working on this to make it more flexible. Currently you can enable Turnstile on the following forms:

### WordPress
- Login Form
- Registration Form

## Getting Started
It is super quick and easy to get started with Cloudflare Turnstile!

1. Simply, generate a "site key" and "secret key" in your Cloudflare account, and add these to the plugin settings page.
2. Open the `includes/helpers/helper_turnstile_login.php` file add those keys and save the file.
3. Require the `includes/helpers/helper_turnstile_login.php` file in your theme `functions.php` file.
4. A new Cloudflare Turnstile challenge will then be displayed on your selected forms to protect them from spam!

## Usage
Replace the below keys with the newly generated "site key" and "secret key" by Cloudflare.

```php
function cf_turnstile_key() {
    $site_key= 'xxxxxxxxxxxxxxxxxxxxxxxxx';
    $secret_key= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    return [$site_key, $secret_key]; 
}
```

Require the **helper_turnstile_login.php** file in your theme **functions.php** file.

```php
require_once ( 'includes/helpers/helper_captcha_verify.php' )
```

Cloudflare Turnstile provides hassle-free, CAPTCHA-free web experiences for website visitors. Using it in a WordPress login is definitely worth it.
