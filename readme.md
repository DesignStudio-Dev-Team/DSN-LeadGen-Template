# DSN LeadGen Template Plugin

## 🚀 Overview

The **DSN LeadGen Template** is a powerful, theme-agnostic WordPress plugin designed to create high-conversion, distraction-free landing pages. Unlike traditional theme templates that rely on the current theme's styling and structure, this plugin registers a custom, full-page template that is styled entirely by its own **opinionated Vanilla CSS and JavaScript**, ensuring a consistent brand experience across all WordPress installations.

This plugin is built as a reusable component, handling all the necessary back-end logic (custom fields for data input) and front-end presentation (custom HTML/CSS/JS) independent of the active theme.

---

## ✨ Key Features

* **Theme Independence:** Registers a custom page template (`DSN Lead Generation Page`) that works regardless of the active WordPress theme.
* **Conditional Content Fields:** Adds custom meta boxes (e.g., for **Main Content (WYSIWYG)** **Logo** **Video Media URL** **Media Poster URL** Or **Image** then another **Right Content for the form (WYSIWYG)** ), to the Page Edit screen **only when the DSN template is selected**, ensuring a clean and focused editing experience.
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