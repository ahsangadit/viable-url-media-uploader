<?php
/**
 * SVG Sanitizer Class
 * Sanitizes SVG file contents to prevent stored XSS vulnerabilities.
 *
 * @package ahsangadit\viable_url_media_uploader
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if (!defined('ABSPATH')) {
    exit;
}

class VUMU_SVG_Sanitizer {

    /**
     * Allowed SVG element names.
     *
     * @var array
     */
    private static $allowed_tags = array(
        'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline',
        'polygon', 'text', 'tspan', 'textpath', 'defs', 'clippath', 'mask',
        'pattern', 'lineargradient', 'radialgradient', 'stop', 'use', 'image',
        'symbol', 'view', 'marker', 'title', 'desc', 'switch', 'metadata',
        'filter', 'feblend', 'fecolormatrix', 'fecomponenttransfer', 'fecomposite',
        'feconvolvematrix', 'fediffuselighting', 'fedisplacementmap', 'feflood',
        'fefunca', 'fefuncb', 'fefuncg', 'fefuncr', 'fegaussianblur', 'feimage',
        'femerge', 'femergenode', 'femorphology', 'feoffset', 'fespecularlighting',
        'fetile', 'feturbulence',
    );

    /**
     * Allowed SVG attributes.
     *
     * @var array
     */
    private static $allowed_attrs = array(
        'id', 'class', 'style', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry',
        'width', 'height', 'viewbox', 'preserveaspectratio', 'transform', 'd', 'fill', 'stroke',
        'strokewidth', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray',
        'stroke-dashoffset', 'stroke-opacity', 'fill-opacity', 'fill-rule', 'opacity', 'points',
        'offset', 'gradientunits', 'gradienttransform', 'spreadmethod', 'patternunits',
        'patterntransform', 'patterncontentunits', 'clippathunits', 'maskunits', 'maskcontentunits',
        'font-family', 'font-size', 'font-weight', 'font-style', 'text-anchor', 'text-decoration',
        'dominant-baseline', 'alignment-baseline', 'baseline-shift', 'letter-spacing', 'word-spacing',
        'clip-path', 'clip-rule', 'color', 'display', 'visibility', 'overflow', 'xmlns',
        'xmlns:xlink', 'version', 'baseprofile', 'enable-background', 'xml:space', 'xml:lang',
        'xlink:href', 'href', 'role', 'aria-hidden', 'aria-label', 'focusable', 'tabindex',
        'marker-start', 'marker-mid', 'marker-end', 'markerwidth', 'markerheight', 'refx', 'refy',
        'orient', 'markerunits', 'stop-color', 'stop-opacity', 'flood-color', 'flood-opacity',
        'stddeviation', 'in', 'in2', 'result', 'mode', 'type', 'values', 'tablevalues',
        'kernelmatrix', 'divisor', 'bias', 'target', 'edgeMode', 'preserveAlpha',
    );

    /**
     * Dangerous element names that must always be removed.
     *
     * @var array
     */
    private static $blocked_tags = array(
        'script', 'iframe', 'embed', 'object', 'applet', 'link', 'base', 'meta',
        'animate', 'animatemotion', 'animatetransform', 'set', 'handler', 'listener',
    );

    /**
     * Sanitize an SVG file on disk.
     *
     * @param string $file_path Path to the SVG file.
     * @return bool True on success, false on failure.
     */
    public static function sanitize_file($file_path) {
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }

        $content = file_get_contents($file_path);
        if ($content === false) {
            return false;
        }

        $sanitized = self::sanitize_content($content);
        if ($sanitized === false) {
            return false;
        }

        return false !== file_put_contents($file_path, $sanitized);
    }

    /**
     * Sanitize SVG content string.
     *
     * @param string $content Raw SVG content.
     * @return string|false Sanitized content or false on failure.
     */
    public static function sanitize_content($content) {
        if (self::is_gzipped($content)) {
            $decoded = gzdecode($content);
            if ($decoded === false) {
                return false;
            }
            $content = $decoded;
        }

        $content = self::strip_dangerous_content($content);

        if (!self::contains_svg_element($content)) {
            return false;
        }

        if (!class_exists('DOMDocument')) {
            return self::sanitize_with_regex($content);
        }

        return self::sanitize_with_dom($content);
    }

    /**
     * Check whether a file is an SVG based on extension or MIME type.
     *
     * @param string $file_path File path.
     * @param string $mime_type Optional MIME type.
     * @return bool
     */
    public static function is_svg_file($file_path, $mime_type = '') {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if (in_array($extension, array('svg', 'svgz'), true)) {
            return true;
        }

        return 'image/svg+xml' === $mime_type;
    }

    /**
     * Check whether content is gzip-compressed.
     *
     * @param string $content File content.
     * @return bool
     */
    private static function is_gzipped($content) {
        return strlen($content) >= 2 && "\x1f\x8b" === substr($content, 0, 2);
    }

    /**
     * Check whether content contains an SVG root element.
     *
     * @param string $content SVG content.
     * @return bool
     */
    private static function contains_svg_element($content) {
        return (bool) preg_match('/<svg[\s>]/i', $content);
    }

    /**
     * Strip known dangerous patterns before DOM parsing.
     *
     * @param string $content SVG content.
     * @return string
     */
    private static function strip_dangerous_content($content) {
        $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
        $content = preg_replace('/<\?xml-stylesheet[^>]*\?>/i', '', $content);
        $content = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $content);
        $content = preg_replace('/<script\b[^>]*\/>/is', '', $content);

        foreach (self::$blocked_tags as $tag) {
            $content = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/is', '', $content);
            $content = preg_replace('/<' . $tag . '\b[^>]*\/>/is', '', $content);
        }

        $content = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content);
        $content = preg_replace('/\b(href|xlink:href)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $content);
        $content = preg_replace('/\b(href|xlink:href)\s*=\s*["\']?\s*data:text\/html[^"\'>\s]*/i', '', $content);

        return $content;
    }

    /**
     * Sanitize SVG using DOMDocument with an allowlist.
     *
     * @param string $content SVG content.
     * @return string|false
     */
    private static function sanitize_with_dom($content) {
        $previous = libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $loaded = $dom->loadXML($content, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded || null === $dom->documentElement) {
            return false;
        }

        self::sanitize_node($dom->documentElement);

        $sanitized = $dom->saveXML($dom->documentElement);
        if ($sanitized === false || $sanitized === '') {
            return false;
        }

        return $sanitized;
    }

    /**
     * Recursively sanitize a DOM node and its children.
     *
     * @param \DOMNode $node DOM node.
     * @return void
     */
    private static function sanitize_node(\DOMNode $node) {
        if (!$node->hasChildNodes()) {
            return;
        }

        $nodes_to_remove = array();

        foreach ($node->childNodes as $child) {
            if (XML_ELEMENT_NODE !== $child->nodeType) {
                continue;
            }

            $tag_name = strtolower($child->nodeName);

            if (in_array($tag_name, self::$blocked_tags, true)) {
                $nodes_to_remove[] = $child;
                continue;
            }

            if ('foreignobject' === $tag_name) {
                $nodes_to_remove[] = $child;
                continue;
            }

            if (!in_array($tag_name, self::$allowed_tags, true)) {
                $nodes_to_remove[] = $child;
                continue;
            }

            if ($child->hasAttributes()) {
                $attrs_to_remove = array();

                foreach ($child->attributes as $attribute) {
                    $attr_name = strtolower($attribute->nodeName);
                    $attr_value = $attribute->nodeValue;

                    if (0 === strpos($attr_name, 'on')) {
                        $attrs_to_remove[] = $attribute->nodeName;
                        continue;
                    }

                    if (!self::is_allowed_attribute($attr_name)) {
                        $attrs_to_remove[] = $attribute->nodeName;
                        continue;
                    }

                    if (self::is_dangerous_url($attr_value)) {
                        $attrs_to_remove[] = $attribute->nodeName;
                    }
                }

                foreach ($attrs_to_remove as $attr_name) {
                    $child->removeAttribute($attr_name);
                }
            }

            self::sanitize_node($child);
        }

        foreach ($nodes_to_remove as $remove_node) {
            $node->removeChild($remove_node);
        }
    }

    /**
     * Check whether an attribute is allowed.
     *
     * @param string $attr_name Attribute name.
     * @return bool
     */
    private static function is_allowed_attribute($attr_name) {
        if (in_array($attr_name, self::$allowed_attrs, true)) {
            return true;
        }

        if (0 === strpos($attr_name, 'aria-')) {
            return true;
        }

        if (0 === strpos($attr_name, 'data-')) {
            return true;
        }

        return false;
    }

    /**
     * Check whether a URL value is dangerous.
     *
     * @param string $value Attribute value.
     * @return bool
     */
    private static function is_dangerous_url($value) {
        $value = trim($value);

        if ($value === '') {
            return false;
        }

        $lower_value = strtolower($value);

        if (0 === strpos($lower_value, 'javascript:')) {
            return true;
        }

        if (0 === strpos($lower_value, 'data:text/html')) {
            return true;
        }

        if (0 === strpos($lower_value, 'vbscript:')) {
            return true;
        }

        return false;
    }

    /**
     * Fallback regex-based sanitization when DOMDocument is unavailable.
     *
     * @param string $content SVG content.
     * @return string|false
     */
    private static function sanitize_with_regex($content) {
        $content = self::strip_dangerous_content($content);

        if (!self::contains_svg_element($content)) {
            return false;
        }

        return $content;
    }
}
