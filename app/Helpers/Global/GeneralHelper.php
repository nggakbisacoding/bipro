<?php

use Carbon\Carbon;
use Modules\Auth\Entities\User;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Entities\Post;

if (! function_exists('appName')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function appName()
    {
        return config('app.name', 'SI-AJI');
    }
}

if (! function_exists('carbon')) {
    /**
     * Create a new Carbon instance from a time.
     *
     * @return Carbon
     *
     * @throws Exception
     */
    function carbon($time)
    {
        return new Carbon($time);
    }
}

if (! function_exists('homeRoute')) {
    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return string
     */
    function homeRoute()
    {
        if (auth()->check()) {
            /** @var \Modules\Auth\Entities\User $user */
            $user = auth()->user();

            if ($user->isAdmin()) {
                return route('admin.insight.index');
            }

            if ($user->isUser()) {
                return 'frontend.user.insight.index';
            }
        }

        return 'frontend.index';
    }
}

if (! function_exists('acceptDocument')) {
    /**
     * Helper to get document accept type on html input
     *
     * @return string
     */
    function acceptDocument()
    {
        return 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel';
    }
}

if (! function_exists('mapFilepondImages')) {
    /**
     * Helper to get document accept type on html input
     *
     * @return array<string, mixed>
     */
    function mapFilepondImages($image)
    {
        $usingS3 = strtolower(config('filesystems.default')) === 's3';
        $path = url('storage/'.$image->path);
        if ($usingS3) {
            $path = \Storage::temporaryUrl($image->path, \Carbon\Carbon::now()->addMinutes(1));
        }

        return [
            'source' => $image->id,
            'options' => [
                'type' => 'local',
                'file' => [
                    'name' => $image->name,
                    'size' => $image->size,
                    'type' => $image->mime,
                ],
                'metadata' => [
                    'poster' => $path,
                ],
            ],
        ];
    }
}

if (! function_exists('mapFilepondDocument')) {
    /**
     * Helper to get document accept type on html input
     *
     * @return array<string, mixed>
     */
    function mapFilepondDocument($document)
    {
        return [
            'source' => $document->id,
            'options' => [
                'type' => 'local',
                'file' => [
                    'name' => $document->name,
                    'size' => $document->size,
                    'type' => $document->mime,
                ],
            ],
        ];
    }
}

if (! function_exists('sourceOfDataDocument')) {
    /**
     * Helper to get document accept type on html input
     *
     * @return string
     */
    function sourceOfDataDocument($document)
    {
        return collect([
            [
                'source' => $document->id,
                'options' => [
                    'type' => 'local',
                    'file' => [
                        'name' => $document->name,
                        'size' => $document->size,
                        'type' => $document->mime,
                    ],
                ],
            ],
        ]);
    }
}

if (! function_exists('generateYearRange')) {
    /**
     * Get years in 5 years
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws Exception
     */
    function generateYearRange($now, $yearAgo)
    {
        return collect(range($now, $yearAgo))->reverse()->values();
    }
}

if (! function_exists('truncate')) {
    /**
     * Truncate a string to a specified length.
     *
     * @param  string  $string The string to be truncated.
     * @param  int  $limit The maximum length of the truncated string. Default is 10.
     * @return string The truncated string.
     */
    function truncate($string, $limit = 10)
    {
        return \Str::of($string)->limit($limit);
    }
}

if (! function_exists('oldSelected')) {
    /**
     * Checks if the given value matches the value retrieved from the old input for the given name.
     *
     * @param  mixed  $name The name of the input field.
     * @param  mixed  $value The value to compare against the old input value.
     * @param  mixed  $defaultValue The default value to use if the old input value is not set.
     * @return string Returns 'selected' if the value matches the old input value, otherwise returns an empty string.
     */
    function oldSelected($name, $value, $defaultValue = null)
    {
        return old($name, $defaultValue) === $value ? 'selected' : '';
    }
}

if (! function_exists('formatNumber')) {

    /**
     * Formats a number with a specified precision.
     *
     * @param  mixed  $number The number to be formatted.
     * @param  int  $precision The number of decimal places to round to. Default is 2.
     * @return float The formatted number.
     */
    function formatNumber($number, int $precision = 2): float
    {
        return round($number, $precision);
    }
}

if (! function_exists('uploadFilename')) {
    /**
     * Generates a filename for uploading based on the username, path, and extension.
     *
     * @param  string  $filePath The username of the user.
     * @return string The generated filePath.
     */
    function uploadFilename(string $filePath): string
    {
        $filePaths = explode('/', $filePath);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $filename = sha1($filePath);
        $filePath = "{$filePaths[0]}/{$filePaths[1]}/{$filename}.{$extension}";

        return $filePath;
    }
}

if (! function_exists('getOriginalUrl')) {
    /**
     * Get original post url
     *
     * @return string
     *
     * @throws Exception
     */
    function getOriginalUrl(string $source, string $username, string $postId = null)
    {
        if (is_null($postId)) {
            return '';
        }

        if ($source === Keyword::SOURCE_TWITTER) {
            $postId = str_replace('t-', '', $postId);

            return "https://twitter.com/{$username}/status/{$postId}";
        }

        if ($source === Keyword::SOURCE_TIKTOK) {
            $postId = str_replace('tt-', '', $postId);

            return "https://www.tiktok.com/@{$username}/video/{$postId}";
        }

        if ($source === Keyword::SOURCE_INSTAGRAM) {
            $postId = str_replace('ig-', '', $postId);

            // https://www.instagram.com/p/B4tp1hrDDSR/
            return 'https://www.instagram.com';
        }

        return '';
    }
}

if (! function_exists('getProfileLink')) {
    /**
     * Get profile link
     *
     * @return string
     *
     * @throws Exception
     */
    function getProfileLink(string $source, string $username)
    {
        if ($source === Keyword::SOURCE_TWITTER) {

            return "https://twitter.com/{$username}";
        }

        if ($source === Keyword::SOURCE_TIKTOK) {
            // https://www.tiktok.com/@jordandrew6/video/7239866400557321499
            return 'https://www.tiktok.com/@'.$username;
        }

        return '';
    }
}

if (! function_exists('removeQueryParameters')) {
    function removeQueryParameters(string $url): string
    {
        $parts = parse_url($url);
        $query = isset($parts['query']) ? $parts['query'] : '';
        parse_str($query, $params);

        $parts['query'] = http_build_query([]);

        $path = isset($parts['path']) ? $parts['path'] : '';
        $newUrl = $parts['scheme'].'://'.$parts['host'].$path;
        if (isset($parts['fragment'])) {
            $newUrl .= '#'.$parts['fragment'];
        }

        return $newUrl;
    }
}

if (! function_exists('getMentionUrl')) {
    /**
     * Get original post url
     *
     * @return string
     *
     * @throws Exception
     */
    function getMentionUrl(string $source, string $message)
    {
        $patternUrl = '/\b((?:https?:\/\/|www\.)[^\s]+(?:\?[^\s]+)?|(?:www\.)?[^\s]+\.[^\s]+)/';
        $patternMention = '/@(\w+)/';
        $patternHashtag = '/#(\w+)/';

        $message = preg_replace_callback($patternUrl, function ($matches) {
            $url = $matches[0];
            $isClock = preg_match('/^([01]\d|2[0-3])\.([0-5]\d)$/', $url);

            if (filter_var($url, FILTER_VALIDATE_URL) !== false && ! $isClock) {
                return '<a href="'.$url.'" target="_blank">'.removeQueryParameters($url).'</a>';
            } else {
                return $url;
            }
        }, $message);

        if (Str::contains($message, ['@', '#'])) {
            if ($source === Keyword::SOURCE_TWITTER) {
                $routeUser = route('admin.post.show.user', '');
                $routeTag = route('admin.post.show.tag', '');

                /** @var User $user */
                $user = auth()->user();

                if ($user->isType(User::TYPE_USER)) {
                    $routeUser = route('frontend.user.post.show.user', '');
                    $routeTag = route('frontend.user.post.show.tag', '');
                }
                $message = preg_replace($patternMention, '<a href="'.$routeUser.'/$1">@$1</a>', $message);
                $message = preg_replace($patternHashtag, '<a href="'.$routeTag.'/$1">#$1</a>', $message);
            }
        }

        return $message;
    }
}

if (! function_exists('getActiveProjectId')) {
    /**
     * Get original post url
     *
     * @return string
     *
     * @throws Exception
     */
    function getActiveProjectId($type = 'string')
    {
        if ($type != 'string') {
            return session()->get('activeProjectIdInt');
        }

        return session()->get('activeProjectId');
    }
}
