=== Viable URL Media Uploader ===

Contributors: viablecube, ahsangadit

Donate link: https://viablecube.com/docs/viable-url-media-uploader/?utm_source=vumu&utm_medium=donate-link

Tags: media, upload, media uploader, attachment, url

Requires at least: 5.6.0

Tested up to: 6.9

Stable tag: 1.0.0

Requires PHP: 7.0

License: GPLv2 or later

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Media uploader that adds attachments from URLs to your WordPress media library. Upload images, videos, PDFs, and more with just a URL.

== Description ==

**Viable URL Media Uploader** allows you to quickly import media files directly from URLs into your WordPress media library without downloading them to your computer first.

https://youtu.be/ChxShlJ3YNw

With just a **URL**, you can:

* Import images (JPG, PNG, GIF, WebP, SVG) from any publicly accessible source.

* Upload videos (MP4, WebM), PDFs, audio files, and more.

* Automatically generate thumbnails for images.

* Save the original URL as post meta for reference.

* Upload via a clean, dedicated upload page.

Built by **ViableCube**, this plugin is perfect for content creators, developers, and site administrators who need a quick and efficient way to add media files from external sources like CDNs, cloud storage, or stock photo sites.

== Features ==

* **Dedicated Upload Page** – Standalone upload page accessible from the Media menu.

* **Multiple File Type Support** – Supports images (JPG, PNG, GIF, WebP, SVG), videos (MP4, WebM), PDFs, and audio files.

* **SVG Support** – Full support for SVG files with proper preview in the media library.

* **Automatic Thumbnail Generation** – Generates thumbnails automatically for uploaded images.

* **Original URL Storage** – Saves the original source URL as post meta for tracking and reference.

* **Standard Form Submission** – Reliable uploads with comprehensive error handling.

* **Automatic File Type Detection** – Intelligently detects file types from URLs and content.

* **Error Handling** – Comprehensive error handling with user-friendly messages.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/` or install it directly from the WordPress Plugin Directory.  

2. Activate the plugin through the **Plugins** menu in WordPress.  

3. Navigate to **Media → Upload from URL** in the admin menu.  

4. Paste the URL of the file you want to upload and click **Upload File**.  

5. The file will be automatically added to your media library and ready to use.

== Frequently Asked Questions ==

= What file types are supported? =  

The plugin supports all file types that WordPress can handle, including images (JPG, PNG, GIF, WebP, SVG), videos (MP4, WebM), PDFs, and audio files.

= Can I upload from any URL? =  

Yes, as long as the URL is publicly accessible and the file can be downloaded by WordPress. The source server must allow remote access to the file.

= Does the plugin check for duplicate uploads? =  

The plugin will upload the file even if it already exists in your media library. You can add deduplication logic using the provided filters.

= How does the plugin handle servers that block downloads? =

The plugin uses a fallback mechanism to handle servers that block standard browser requests. If a server returns a 403 (Forbidden) error, the plugin will automatically retry the download with alternative user agents, including a generic browser user agent and, as a last resort, a Googlebot user agent string. This helps bypass some server restrictions that block non-browser requests. **Important:** Impersonating search engine crawlers may violate some websites' terms of service. Use this feature responsibly and only download files from sources you have permission to access. You can customize or disable this behavior using the `vumu_remote_get_args` filter.

= Does it work with SVG files? =

Yes. The plugin includes built-in SVG support, allowing you to upload SVG files and view them properly in the WordPress media library.

= Will thumbnails be generated for all images? =  

Yes. Thumbnails are automatically generated for all image types except SVG files, following WordPress's standard thumbnail generation process.

= Can I track the original source URL? =  

Yes. The original URL is saved as post meta for each uploaded file, allowing you to reference the source later.

= Is the plugin compatible with multisite? =  

Yes. The plugin works in both single-site and multisite WordPress installations.

== Changelog ==

= 1.0.0 – Initial Release =  

* Added URL-based media upload functionality  

* Added dedicated upload page in Media menu  

* Added SVG file support with proper preview  

* Added automatic thumbnail generation for images  

* Added original URL storage as post meta  

* Added standard form submission with error handling  

* Added comprehensive error handling  

* Added developer hooks and filters  

* Initial stable release  

== Upgrade Notice ==

= 1.0.0 =  

First stable release of **Viable URL Media Uploader** — import media files directly from URLs into your WordPress media library without downloading them first.

== License ==

This plugin is licensed under the **GPLv2 or later**.  

You can view the full license text here: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

== Support & Feedback ==

We'd love to hear your feedback and suggestions!  

📩 Email: [ahsan@viablecube.com](mailto:ahsan@viablecube.com)  

🌐 Website: [https://viablecube.com](https://viablecube.com)
