# DSN LeadGen Template Plugin

## 🚀 Overview

The **DSN LeadGen Template** is a powerful, theme-agnostic WordPress plugin designed to create high-conversion, distraction-free landing pages. Unlike traditional theme templates that rely on the current theme's styling and structure, this plugin registers a custom, full-page template that is styled entirely by its own **opinionated Vanilla CSS and JavaScript**, ensuring a consistent brand experience across all WordPress installations.

This plugin is built as a reusable component, handling all the necessary back-end logic (custom fields for data input) and front-end presentation (custom HTML/CSS/JS) independent of the active theme.

---

## ✨ Key Features

* **Theme Independence:** Registers a custom page template (`DSN Lead Generation Page`) that works regardless of the active WordPress theme.
* **Conditional Content Fields:** Adds custom meta boxes (e.g., for **Brand Name**, **Call to Action Label**, **Main Content (WYSIWYG)**, **Logo**, **Video Media URL**, **Media Poster URL** or **Image**, then another **Right Content for the form (WYSIWYG)**), to the Page Edit screen **only when the DSN template is selected**, ensuring a clean and focused editing experience.
* **Opinionated Styling Stack:** Utilizes **Vanilla CSS and JavaScript** (no frameworks like Bootstrap or Tailwind) for maximum performance and complete control over the visual output. The custom CSS is loaded with a high priority (`999`) to ensure it aggressively overrides all theme styles.
* **Form Plugin Integration:** Designed to seamlessly integrate with and heavily override the styles of form plugins (specifically **Gravity Forms**), allowing you to drop a form shortcode into the main page content and have it automatically styled to match the DSN aesthetic.
* **Focused on Conversion:** The template is stripped down, eliminating typical theme elements (sidebars, excessive navigation) to maximize lead capture focus.

---

## ⚙️ Dependencies (Required)

This plugin handles the *layout* and *data presentation*, but it does **not** handle form processing or data storage. A third-party form solution is required.

* **Gravity Forms (Recommended):** The included custom CSS is specifically written to target and override the default Gravity Forms markup, ensuring the form looks exactly as designed within the lead gen page.

---

## 🛠️ Installation & Usage

1.  **Upload:** Upload the `dsn-leadgen-template` folder to your `wp-content/plugins/` directory.
2.  **Activate:** Activate the **DSN LeadGen Template** plugin in your WordPress Dashboard under **Plugins**.
3.  **Create Page:** Create a new WordPress Page (or edit an existing one).
4.  **Select Template:** In the **Page Attributes** or **Template** panel in the page editor sidebar, select the **"DSN Lead Generation Page"** template.
5.  **Content Input (Meta Boxes):** Once the custom template is selected, the **"DSN LeadGen Page Content"** meta box will appear. Fill in your custom content:
    * **Brand Name**
    * **Call to Action Label**
    * **Main Content**
    * **Logo**
    * **Video Media URL**
    * **Media Poster URL**
    * **Image**
    * **Right Content for the form**  Insert your **Gravity Forms shortcode** into the main WordPress content editor.
7.  **Publish:** Save and publish the page. The front end will now display the high-conversion layout with the custom styling and content you defined.

---

## 🎨 Styling & Overrides (Developer Notes)

This plugin enforces a high-specificity styling approach:

* **CSS Enqueuing:** The custom CSS (`assets/css/dsn-form-style.css`) is enqueued with a high priority (`999`) to load last and aggressively overwrite theme styles.
* **Theme Elements Blocked:** The template explicitly uses CSS (`display: none !important;`) to hide common theme distractions like sidebars, main navigation, and default page titles, creating a true landing page experience.
* **Gravity Forms Specificity:** The CSS targets Gravity Forms elements using wrappers and specific classes (e.g., `#dsn-leadgen-wrapper .gform_wrapper`) to guarantee the custom DSN look is applied to all form fields and buttons.
* **CTA Override:** (removed) This plugin does not modify or inject text into form submit buttons. Form button text should be managed within your form plugin (e.g., Gravity Forms) or by custom front-end code if desired.

---

## 🚀 Release & Updates

This plugin supports automated releases and in-dashboard updates via GitHub.

### Creating a New Release

1.  **Commit Changes:** Ensure all your changes are committed to the `main` branch.
2.  **Tag Release:** Create a new git tag starting with `v` (e.g., `v1.0.1`).
    ```bash
    git tag v1.0.1
    git push origin v1.0.1
    ```
3.  **GitHub Action:** Pushing the tag triggers a GitHub Action workflow that:
    *   Zips the plugin files (excluding development files like `.git`).
    *   Creates a new Release on GitHub.
    *   Attaches the zip file to the release.

### Auto-Updates

The plugin includes a built-in updater class (`includes/class-dsn-plugin-updater.php`) that checks the GitHub repository for new releases.

*   **Check Mechanism:** It hooks into WordPress's `pre_set_site_transient_update_plugins` to compare the local version with the latest GitHub release tag.
*   **Update Process:** If a newer version is found, WordPress will show an update notification. Clicking "Update" will download the zip from the latest GitHub release and install it, just like a standard plugin update.
*   **Private Repos:** If the repository is private, you must define a `DSN_GITHUB_TOKEN` constant in your `wp-config.php` with a valid GitHub Personal Access Token (PAT) to allow the plugin to check for updates.
    ```php
    define( 'DSN_GITHUB_TOKEN', 'your_github_pat_here' );
    ```