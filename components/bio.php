<?php

function format_bio_text(?string $text, string $fallback = ''): string {
    $value = $text !== null ? $text : '';
    if (trim($value) === '') {
        $value = $fallback;
    }

    $escaped = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    return preg_replace('/(?:\r\n|\r|\n){2,}/', '<br/><br/>', $escaped);
}
?>
