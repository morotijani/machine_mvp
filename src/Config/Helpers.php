<?php
/**
 * General Helper Functions
 */

if (!function_exists('format_large_number')) {
    /**
     * Formats large numbers into short M/K format with a tooltip for the full number.
     * Example: 1,200,000 -> 1.2M
     */
    function format_large_number($number) {
        $number = (float) $number;
        if ($number >= 1000000) {
            $formatted = number_format($number / 1000000, 2) . 'M';
            $fullNumber = number_format($number, 2);
            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="₵' . $fullNumber . '" style="cursor: pointer; text-decoration: underline dotted rgba(0,0,0,0.3); text-underline-offset: 4px;">' . $formatted . '</span>';
        }
        
        return number_format($number, 2);
    }
}
