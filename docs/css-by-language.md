# wpLingua – CSS by Language

Multilingual websites often require different design adjustments depending on the active language or the text direction (LTR or RTL). With **wpLingua**, developers can easily define language-specific or direction-specific CSS rules to ensure the best reading experience for every user.

This documentation explains how to:

- [Write CSS rules that only apply to a given language (`lang` attribute)](#css-by-language)
- [Write CSS rules that only apply to a given text direction (`dir` attribute)](#css-by-language-direction)
- [Register and enqueue separate CSS files based on the current language](#register-a-css-file)
- [Inject inline CSS directly into the `<head>` of the page](#embed-css-in-head)

By combining these methods, you can create a fully responsive, multilingual design that adapts seamlessly to each user’s language context.

---

## CSS rules by language

### CSS by language

You can use the `lang` attribute automatically set on the `<html>` element.
This allows you to write CSS rules that only apply to specific languages.

```css

html[lang=fr] h1 {
    /* Only apply on french language */
}


html[lang=en] h1 {
    /* Only apply on english language */
}


body.wplingua-translated h1 {
    /* For translated pages */
}
```

### CSS by language direction

In addition to the language code, wpLingua also sets the `dir` attribute (`ltr` or `rtl`) on the `<body>` element.
This makes it possible to target CSS rules based on text direction.

```css

body[dir=ltr] h1 {
    /* Only apply on LTR dir */
}


body[dir=rtl] h1 {
    /* Only apply on RTL dir */
}
```

---

## Register and embed CSS by language

Sometimes you may want to load entire CSS files depending on the current language.
wpLingua provides hooks to conditionally enqueue or embed styles.

### Register a CSS file

You can register and enqueue styles based on the current language code returned by wpLingua.
This method is recommended when you want to keep language-specific rules in separate files.

```php
<?php

// Hook into 'wp_enqueue_scripts' to enqueue styles at the right moment
add_action( 'wp_enqueue_scripts', function() {

    // Get the current language code using wpLingua
    // Example return values: 'en', 'fr', 'es', 'ar'
    switch ( wplng_get_language_current_id() ) {

        case 'en':
            // Enqueue a CSS file only when the current language is English
            wp_enqueue_style(
                'my-english-style', // Unique handle name
                get_stylesheet_directory_uri() . '/css/style-en.css', // File path
                array(), // Dependencies (none in this case)
                '1.0' // Version number
            );
            break;

        case 'fr':
            // Enqueue a CSS file only when the current language is French
            wp_enqueue_style(
                'my-french-style', // Unique handle name
                get_stylesheet_directory_uri() . '/css/style-fr.css', // File path
                array(), // Dependencies (none in this case)
                '1.0' // Version number
            );
            break;
    }
});


```

### Embed CSS in head

Alternatively, you can directly output inline styles in the `<head>` section using WordPress hooks.
This method is useful for small tweaks without having to create extra CSS files.

```php
<?php

// Hook into 'wp_head' to inject inline CSS directly in the <head> section
add_action( 'wp_head', function() {

    // Get the current language code using wpLingua
    // Example return values: 'en', 'fr', 'es', 'ar'
    switch ( wplng_get_language_current_id() ) {

        case 'en':
            // Output CSS rules only when the current language is English
            echo '<style>h1 { color: red; }</style>';
            break;

        case 'fr':
            // Output CSS rules only when the current language is French
            echo '<style>h1 { color: blue; }</style>';
            break;
    }
});

```
