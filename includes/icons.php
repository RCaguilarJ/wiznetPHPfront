<?php

declare(strict_types=1);

function render_icon(string $name, string $class = ''): string
{
    $icons = [
        'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.6.6 4 .6.6 0 1 .4 1 1V21c0 .6-.4 1-1 1C10.3 22 2 13.7 2 3c0-.6.4-1 1-1h4.5c.6 0 1 .4 1 1 0 1.4.2 2.8.6 4 .1.4 0 .8-.3 1.1l-2.2 2.2Z"/></svg>',
        'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm0 2v.2l9 6.3 9-6.3V7l-8.4 5.9a1 1 0 0 1-1.2 0L3 7Zm18 10V9.7l-8.4 5.9a3 3 0 0 1-3.4 0L3 9.7V17h18Z"/></svg>',
        'wifi' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-4.9-3.8a1 1 0 0 0 1.4 1.4 5 5 0 0 1 7 0 1 1 0 0 0 1.4-1.4 7 7 0 0 0-9.8 0Zm-3.6-3.7a1 1 0 0 0 1.4 1.4 10 10 0 0 1 14.2 0 1 1 0 0 0 1.4-1.4 12 12 0 0 0-17 0Zm17.8-3.9a15 15 0 0 0-18.6 0 1 1 0 0 0 1.2 1.6 13 13 0 0 1 16.2 0 1 1 0 0 0 1.2-1.6Z"/></svg>',
        'globe' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm6.9 9h-3.1a16.9 16.9 0 0 0-1.3-5A8 8 0 0 1 18.9 11Zm-6.9 9c-.9 0-2.3-2-2.9-5h5.8c-.6 3-2 5-2.9 5Zm-3.2-7a20.6 20.6 0 0 1 0-2h6.4a20.6 20.6 0 0 1 0 2H8.8Zm-4.7-2h3.1a16.9 16.9 0 0 1 1.3-5A8 8 0 0 0 4.1 11Zm3.1 2H4.1a8 8 0 0 0 4.4 5 16.9 16.9 0 0 1-1.3-5Zm4.8-9c.9 0 2.3 2 2.9 5H9.1c.6-3 2-5 2.9-5Zm2.5 14a16.9 16.9 0 0 0 1.3-5h3.1a8 8 0 0 1-4.4 5Z"/></svg>',
        'pin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.4 7-12a7 7 0 1 0-14 0c0 5.6 7 12 7 12Zm0-9a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/></svg>',
        'tools' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14.7 6.3 3-3a4 4 0 0 1 4.8 4.8l-3 3-4.8-4.8Zm-1.4 1.4 4.8 4.8-8.2 8.2a2 2 0 0 1-2.8 0l-.6-.6a2 2 0 0 1 0-2.8l8.2-8.2ZM4.6 3.2a1 1 0 0 1 1.4 0l5.8 5.8-2.8 2.8L3.2 6a1 1 0 0 1 0-1.4l1.4-1.4Zm-.8 11 6.8-6.8 2.8 2.8-6.8 6.8H3.8v-2.8Z"/></svg>',
        'bulb' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-4 12.8V17a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2.2A7 7 0 0 0 12 2Zm-2 18h4a2 2 0 0 1-4 0Zm1-14a5 5 0 0 1 3 9v1h-4v-1a5 5 0 0 1 1-9Z"/></svg>',
        'calendar-check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v4H3V6a2 2 0 0 1 2-2h2V2Zm14 10v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8h18Zm-5.8 2.2-4.1 4.1-2.3-2.3 1.4-1.4 0.9.9 2.7-2.7 1.4 1.4Z"/></svg>',
        'calendar-pin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h2v2h6V2h2v2h2a2 2 0 0 1 2 2v4H3V6a2 2 0 0 1 2-2h2V2Zm14 10v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7h18Zm-9 1.5a3 3 0 0 0-3 3c0 2.2 3 5 3 5s3-2.8 3-5a3 3 0 0 0-3-3Zm0 1.8a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z"/></svg>',
        'clipboard' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 2a2 2 0 0 0-2 2H6a3 3 0 0 0-3 3v11a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3h-1a2 2 0 0 0-2-2H9Zm0 2h6v2H9V4Zm1.3 9.7 4-4L16 11.4l-5.7 5.7-2.3-2.3 1.7-1.7 0.6 0.6Z"/></svg>',
        'question' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 17a1.3 1.3 0 1 1 1.3-1.3A1.3 1.3 0 0 1 12 19Zm1.4-5.4v.4h-2.6v-.8a3.3 3.3 0 0 1 1.6-2.8 2.1 2.1 0 0 0 1-1.7 1.9 1.9 0 0 0-3.8.1H7a4.5 4.5 0 0 1 9-.2 4.3 4.3 0 0 1-1.9 3.5 1 1 0 0 0-.7 1.5Z"/></svg>',
        'smile' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm-3 7a1.5 1.5 0 1 1-1.5 1.5A1.5 1.5 0 0 1 9 9Zm6 0a1.5 1.5 0 1 1-1.5 1.5A1.5 1.5 0 0 1 15 9Zm-7.2 5h8.4a4.5 4.5 0 0 1-8.4 0Z"/></svg>',
        'signal-check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h2v-4H4v4Zm5 0h2v-8H9v8Zm5 0h2v-12h-2v12Zm5 0h2V4h-2v16Zm-16.7-4.3 1.4-1.4 1.6 1.6 4.3-4.3 1.4 1.4-5.7 5.7-3-3Z"/></svg>',
        'wifi-heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.5a1 1 0 0 1-.7-.3C9.8 15.8 7 13.4 7 10.9A2.9 2.9 0 0 1 12 9a2.9 2.9 0 0 1 5 1.9c0 2.5-2.8 4.9-4.3 6.3a1 1 0 0 1-.7.3ZM3.5 6.3a1 1 0 0 0 1.4 1.4 10 10 0 0 1 14.2 0 1 1 0 0 0 1.4-1.4 12 12 0 0 0-17 0Z"/></svg>',
        'refresh' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5V2L8 6l4 4V7a5 5 0 1 1-5 5H5a7 7 0 1 0 7-7Z"/></svg>',
        'plug' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 2h2v4H8V2Zm6 0h2v4h-2V2ZM6 8h12v4a6 6 0 0 1-5 5.9V22h-2v-4.1A6 6 0 0 1 6 12V8Z"/></svg>',
        'network' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h4v4H3V4Zm14 0h4v4h-4V4ZM3 16h4v4H3v-4Zm14 0h4v4h-4v-4ZM7 6h4v2H9v8h2v2H7v-2h2V8H7V6Zm10 0h-4v2h2v8h-2v2h4v-2h-2V8h2V6Z"/></svg>',
        'headset' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a8 8 0 0 0-8 8v6a3 3 0 0 0 3 3h1v-7H6v-2a6 6 0 0 1 12 0v2h-2v7h1a3 3 0 0 0 3-3v-6a8 8 0 0 0-8-8Zm-4 14h1v5H8a2 2 0 0 1-2-2v-3Zm8 0h2v3a2 2 0 0 1-2 2h-1v-5Z"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm-1 14-4-4 1.4-1.4 2.6 2.6 4.6-4.6L17 10l-6 6Z"/></svg>',
        'upload' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 16h2V9.8l2.6 2.6L17 11l-5-5-5 5 1.4 1.4 2.6-2.6V16Zm-6 2h14v2H5v-2Z"/></svg>',
        'home' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2 11h3v9h6v-6h2v6h6v-9h3L12 3Z"/></svg>',
        'caret' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5H7Z"/></svg>',
    ];

    $svg = $icons[$name] ?? $icons['globe'];
    $className = trim('icon ' . $class);

    return sprintf('<span class="%s">%s</span>', htmlspecialchars($className, ENT_QUOTES, 'UTF-8'), $svg);
}
