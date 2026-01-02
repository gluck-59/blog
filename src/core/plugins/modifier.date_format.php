<?php
/**
 * Smarty plugin for Russian date formatting
 */

if (!function_exists('smarty_modifier_rus_date')) {
    function smarty_modifier_rus_date($string) {
        if (empty($string)) {
            return '';
        }

        $months = [
            1 => 'янв', 2 => 'фев', 3 => 'мар', 4 => 'апр',
            5 => 'мая', 6 => 'июн', 7 => 'июл', 8 => 'авг',
            9 => 'сен', 10 => 'окт', 11 => 'ноя', 12 => 'дек'
        ];

        $timestamp = strtotime($string);
        if ($timestamp === false) {
            return $string;
        }

        $day = date('d', $timestamp);
        $month = $months[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);
        $time = date('H:i', $timestamp);
        
        return "$day $month $year $time";
    }
}
