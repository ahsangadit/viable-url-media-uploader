=== Viable URL Media Uploader ===
**Contributors:** viablecube, ahsangadit  
**Tags:** media, upload, url, media library, files, import, images, videos, pdfs, svg, media import, url upload  
**Requires at least:** 5.0  
**Tested up to:** 6.8  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

Import media files directly from URLs into your WordPress media library — no download needed. Simply paste the URL and upload images, videos, PDFs, SVG, and more.

== Description ==

**Viable URL Media Uploader** allows you to quickly import media files directly from URLs into your WordPress media library without downloading them to your computer first.

This plugin is ideal for content creators, developers, and site administrators who need a quick and efficient way to add media files from external sources like CDNs, cloud storage, or stock photo sites.

**Key Features:**

- Upload media files directly from URLs via a dedicated upload page  
- Supports multiple file types: images (JPG, PNG, GIF, WebP, SVG), videos (MP4, WebM), PDFs, and audio files  
- Automatic thumbnail generation for uploaded images  
- Built-in SVG support with proper preview in the media library  
- Saves original source URL as post meta for tracking and reference  
- Standard form submission with comprehensive error handling  
- Automatic file type detection from URLs and content  
- User-friendly interface with clear status messages  
- Developer-friendly OOP architecture with hooks and filters  
- Fully translatable and ready for internationalization  
- Lightweight and optimized for performance  

== Installation ==

1. Upload the plugin to `/wp-content/plugins/` or install it from the WordPress plugin repository.  
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

= Can I customize the upload process? =  
Yes. Use the `vumu_remote_get_args` filter to modify download parameters like timeout, user agent, headers, and more. You can also use `vumu_before_upload` and `vumu_after_upload` action hooks to execute custom code during the upload process.

= Does it work with SVG files? =  
Yes. The plugin includes built-in SVG support, allowing you to upload SVG files and view them properly in the WordPress media library.

= Will thumbnails be generated for all images? =  
Yes. Thumbnails are automatically generated for all image types except SVG files, following WordPress's standard thumbnail generation process.

= Can I track the original source URL? =  
Yes. The original URL is saved as post meta for each uploaded file, allowing you to reference the source later.

= Is the plugin compatible with multisite? =  
Yes. The plugin works in both single-site and multisite WordPress installations.

== Screenshots ==

1. Dedicated upload page accessible from the Media menu  
2. URL input field and upload button with clean interface  
3. Success message displayed after a file is uploaded from URL  
4. Uploaded file appearing in the media library with original URL metadata  
5. SVG files displayed correctly in the media library with proper preview  
6. Multiple file types (images, videos, PDFs) successfully imported from URLs

== Changelog ==

= 1.0.0 =  
* Initial release with URL-based media upload functionality, dedicated upload page, SVG support, automatic thumbnail generation, and developer hooks

== Upgrade Notice ==

= 1.0.0 =  
First stable release of the Viable URL Media Uploader plugin.

== License ==

This plugin is licensed under the GPLv2 or later.  
You can find the full license text here: https://www.gnu.org/licenses/gpl-2.0.html
