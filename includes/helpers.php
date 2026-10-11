<?php
/**
 * helpers.php
 * Small helper functions shared by the pages.
 */

/** Escape output for safe display in HTML (prevents XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Returns the previously submitted value of a field (for a "sticky" form). */
function old(string $field): string
{
    return e($_SESSION['old'][$field] ?? '');
}

/** Returns the error message for a field, wrapped in markup, or an empty string. */
function error_for(string $field): string
{
    $msg = $_SESSION['errors'][$field] ?? null;
    return $msg ? '<p class="error-text">' . e($msg) . '</p>' : '';
}

/** Adds the "invalid" CSS class to an input that has an error. */
function invalid_class(string $field): string
{
    return isset($_SESSION['errors'][$field]) ? ' invalid' : '';
}
