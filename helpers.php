<?php
// Source - https://stackoverflow.com/a/77591472
// Posted by Denis Sinyukov, modified by community. See post 'Timeline' for change history
// Retrieved 2026-06-10, License - CC BY-SA 4.0

if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     */
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="'.csrf_token().'">';
    }
}
