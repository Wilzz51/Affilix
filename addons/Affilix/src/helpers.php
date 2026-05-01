<?php

if (!function_exists('affiliation_setting')) {
    /**
     * Obtenir un paramètre d'affiliation depuis les settings ClientXCMS
     */
    function affiliation_setting(string $key, $default = null)
    {
        try {
            return setting($key, $default);
        } catch (\Exception $e) {
            return $default;
        }
    }
}
