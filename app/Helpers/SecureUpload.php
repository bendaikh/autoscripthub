<?php

namespace Fickrr\Helpers;

use Illuminate\Http\UploadedFile;

class SecureUpload
{
    /**
     * Extensions that must never be stored under the web root.
     */
    public const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar',
        'cgi', 'pl', 'py', 'asp', 'aspx', 'jsp', 'shtml', 'htaccess',
        'sh', 'bash', 'exe', 'bat', 'cmd', 'com', 'dll', 'so',
        'js', 'html', 'htm', 'svg', 'xhtml',
    ];

    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function extension(UploadedFile $file)
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());
        // Strip anything after first extension segment (file.php.jpg handled by allowlists)
        if (strpos($ext, '.') !== false) {
            $parts = explode('.', $ext);
            $ext = end($parts);
        }
        return $ext;
    }

    public static function isDangerousExtension($extension)
    {
        $extension = strtolower(ltrim((string) $extension, '.'));
        return in_array($extension, self::DANGEROUS_EXTENSIONS, true);
    }

    public static function filterAllowedExtensions(array $extensions)
    {
        $clean = [];
        foreach ($extensions as $ext) {
            $ext = strtolower(trim(ltrim((string) $ext, '.')));
            if ($ext === '' || self::isDangerousExtension($ext)) {
                continue;
            }
            $clean[] = $ext;
        }
        return array_values(array_unique($clean));
    }

    /**
     * Validate an image-only upload (editor / avatar style).
     * Returns null on success, or an error message string.
     */
    public static function validateImage(UploadedFile $file = null)
    {
        if (!$file) {
            return 'No file uploaded';
        }

        $extension = self::extension($file);
        if (self::isDangerousExtension($extension) || !in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            return 'Invalid file type';
        }

        $mime = (string) $file->getMimeType();
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];
        if (!in_array($mime, $allowedMimes, true)) {
            return 'Invalid file type';
        }

        // Reject polyglot / fake images that contain PHP markers
        $path = $file->getRealPath();
        if ($path && is_readable($path)) {
            $head = @file_get_contents($path, false, null, 0, 4096);
            if ($head !== false && preg_match('/<\?php|<\?=|<script[\s>]/i', $head)) {
                return 'Invalid file contents';
            }
        }

        return null;
    }

    /**
     * Validate a marketplace item file against configured allowlist minus dangerous types.
     */
    public static function validateItemFile(UploadedFile $file = null, array $configuredExtensions)
    {
        if (!$file) {
            return 'No file uploaded';
        }

        $extension = self::extension($file);
        if (self::isDangerousExtension($extension)) {
            return 'Executable uploads are not allowed';
        }

        $allowed = self::filterAllowedExtensions($configuredExtensions);
        if (!in_array($extension, $allowed, true)) {
            return 'Invalid file type';
        }

        return null;
    }

    public static function safeFilename($extension)
    {
        $extension = strtolower(ltrim((string) $extension, '.'));
        return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }
}
