=== wpLingua ===
Contributors: wpr0ck, lyly13
Donate link: https://wplingua.com/
Tags: translation, translate, autotranslate, autotranslation, multilingual, multilingual website, localization
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make your websites multilingual and translates them automatically: no word limits, free first language, SEO-friendly, no coding knowledge needed and more!

== Description ==

An all-in-one solution that makes your websites multilingual and translates them automatically, without word or page limits. 

The highlights: 

* a free first language
* an on-page visual editor for editing translations
* a customizable language switcher
* search engine optimization (SEO)
* no coding knowledge needed
* self-hosted data
* open source, find us on ([Github](https://github.com/julien-jacob/wplingua))
* and more!

= One free target language =
With our complimentary offer, wpLingua empowers you to introduce your website to a broader audience in a new language. **While the free version supports translation into a single target language**, we set no boundaries on the number of words you can translate. Whether your site has a hundred pages or just a few, you can seamlessly make it accessible in the language you desire. Dive into the world of **multilingual web experiences** and delight your global visitors. _Enjoy the journey with wpLingua!_

= Streamlined Setup Process =
Dive into a hassle-free multilingual experience with wpLingua. Forget about complex configurations and the intimidating world of coding. All you need to get started is to request your complimentary API key. Once you do, we'll promptly send it straight to your inbox. It's as straightforward as that! Our commitment is **to make multilingual capabilities accessible and effortless for every WordPress user**. Let us take care of the technicalities while you focus on creating captivating content for your global audience.

= Optimized for international search engines (SEO) =
In the digital age, visibility is everything. When expanding your website's linguistic reach with wpLingua, it's paramount that **each translated page** shines brightly in the vast universe of search results. That's why our plugin goes beyond mere translation. wpLingua ensures that every piece of content you craft in different languages is primed for **search engine discovery**. By **optimizing indexing**, we make sure that your **translated pages** aren't just understood by your audience, but are also easily found by **search engines**. Dive into a multilingual experience that doesn’t compromise on visibility.

= Tailor-made translations at your fingertips with our On-Page Editor =
Although wpLingua gives you the convenience of **machine translations**, we understand the nuances of each language. wpLingua offers a canvas in which you can **refine each translation** so that it resonates with your unique voice and your brand message adapted to the cultural and contextual expectations of your audience. Experience the fusion of automation and the personal touch with our **visual on-page editor**.

= Fully customizable language switcher =
With dozens of **pre-designed themes** at your fingertips, you have the liberty to select an appearance for your language switcher that seamlessly blends with your site's aesthetics. Not content with our array? _No problem_. Elevate the switcher's look by importing your own unique icons. But that's not all — whether you prefer a fixed position for the switcher or want the flexibility to showcase it anywhere using a **shortcode**, wpLingua ensures your language toggle is as distinctive as your brand itself. 

= Intuitive possibilities to exclude certain translations =
Navigate the complexities of website translation with wpLingua's intelligent exclusion feature. Want to preserve specific sections of a page in its original language? Our system seamlessly allows you to pinpoint and **exclude translations using CSS selectors**. But it doesn't stop there. If entire pages need to remain untouched, effortlessly exclude them by their URL. All of this can be managed with ease through our user-friendly exclusions manager. With wpLingua, you're always in command of what gets translated and what doesn't, ensuring your site's essence remains intact.

= The wpLingua API =
The wpLingua plugin relies on our own wpLingua API, an integrated third-party service, to provide its machine translation functionality. The call to this Third Party Service (wpLingua API) is made when creating an API key from the plugin, during API key verification and when your site requests a new automatic translation (new texts discovered on a page web or request automatic translations from the translation edition). We invite you to consult our [Terms & Conditions page](https://wplingua.com/terms-and-conditions/) for more information.


== Installation ==

1. Download the plugin zip file and install it via the WordPress interface, or upload it directly to your plugins directory.
2. Activate wpLingua from your plugins page.
3. Navigate to the wpLingua settings to get your free API key and to set up your language preferences.
4. Start translating your content!

== Frequently Asked Questions ==

= What languages are available for translation?=
For the moment the available languages are: 

* English
* French
* German
* Italian
* Japanese
* Portuguese
* Simplified Chinese
* Spanish

If you need a language, let us know using the [contact form available on our website](https://wplingua.com/contact/), we add languages as we go.

= What languages are available in the plugin administration? =
At the moment, the plugin is available in English, French, German, Italian, Spanish and Portuguese. If you want to help us translate it into more languages, don't hesitate!

= Is it compatible with page builders? =
Yes, wpLingua is designed to work seamlessly with most popular page builders.

= How it works? =
wpLingua intercepts page content and analyzes HTML and JS code. It discovers all texts, translates them and makes the pages multilingual. This approach ensures compatibility with most themes, plugins and page builders such as Gutenberg, Elementor, Divi…

= Is it possible to edit translations manually? =
Of course ! Simply activate the **translation editor** on the page to make your changes.

= What is the translated word limit? =
wpLingua does **not limit the translation of the number of words**.

= Is wpLingua compatible with WordPress Multisite? =
No, wpLingua is not compatible with WordPress Multisite

= Is wpLingua compatible with caching plugins? =
Quite ! And it's even recommended to cache your pages and translations to improve site loading and performance.

= Is there a Gutenberg block or Divi/Elementor widget to easily display the language switcher? =
No, not at the moment but in the meantime, you can easily display the language switcher wherever you want using our shortcode provided for this purpose. Simply copy and paste this shortcode where you want: **[wplng_switcher]**

== Screenshots ==

1. Once the plugin is downloaded, installed and activated, register your API key.
2. As soon as your API key has been registered, your site is multilingual. This option screen allows you to configure your languages and their flags, as well as activate the functionalities.
3. Customizing the language switcher, enable or disable auto-insertion + choose position or use the shortcode.
4. You have the possibility to exclude pages or parts of pages.
5. If you need, you can edit a translation directly from your pages using our visual editor.
6. You can access the list of translations present on a page, including those which are not directly visible (Meta SEO, texts, alternative images, title attributes, etc.)
7. You can edit all translations of your website.
8. All translations are stored on your WordPress site.

== Changelog ==

= 1.0.3 = 

* Prepare SVN folders

= 1.0.2 = 

* Fix error on API key register

= 1.0.1 =

* Review code
* Review shortcode
* Review some texts and HTML
* Update plugin translations
* Add default excluded element for translation : code
* Add default excluded element for editor links : option
* CSS: Better switcher margin
* CSS: Hover effect for link’s editor
* Better flags in admin

= 1.0.0 =
* **Options pages** 
 * Register
     * Set API key
     * Request API key
 * Settings
     * Original language
     * Target languages
     * Features
     * API key
 * Switcher
     * Theme (20)
     * Style (Dropdown / List / Block)
     * Language name (Untranslated name / ID / None)
     * Custom CSS
     * Auto insert
 * Exclusions
     * Exclude HTML elements by selector
     * Exclude URLs by regex
    
* **Translation post type**
 * Edit translation
 * Status: Generated or Edited
 * Re-generate translation
    
* **Translate webpage**
 * Text node (p, span, …)
 * Attribute (alt, title, …)
 * Dir attribute (ltr or rtl)
 * Body class (dir and language ID)
 * JS / JSON (Text / HTML / URL / Language ID)
 * AJAX (HTML / JSON)
 * Add links alternate hreflang
    
* **On page : Visual editor**
* **On page : Translations list**
* **On page : Admin bar**
 * Access visual editor
 * Access all translation on current page
 * Access exclusion option if current page is excluded

* **Feature : Search from translated page**
* **Shortcode**
 * wplingua-switcher
 * wplingua-notranslate

* **Plugin translation**
 * French
 * Portuguese
 * Spanish


== Upgrade Notice ==

= 1.0.0 =
First release. Be sure to set up your preferences after installation.