<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {date_format} modifier plugin
 *
 * Type:     modifier
 * Name:     date_format
 * Purpose:  format date according strftime()
 * Input:    string - date to format
 *           string - date format (optional)
 *           string - default date (optional)
 *           string - formatter ('rus' для русского)
 * Example:  {$timestamp|date_format:"%D"}
 */
function smarty_modifier_date_format($string, $format = SMARTY_RESOURCE_DATE_FORMAT, $default_date = '', $formatter = 'auto')
{
    // Include the {@link shared.make_timestamp.php} plugin
    require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');

    if ($string != '') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    }

    if ($formatter == 'rus') {
        // Оставляем совместимость: при formatter='rus' используем 
        // тот же формат, что и в отдельном модификаторе rus_date().
        return smarty_modifier_rus_date($string);
    }

    if ($formatter == 'strftime' || ($formatter == 'auto' && strpos($format, '%') !== false)) {
        if (DIRECTORY_SEPARATOR == '\\') {
            $_win_from = array('%D', '%h', '%n', '%r', '%R', '%t', '%T');
            $_win_to   = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
            if (strpos($format, '%e') !== false) {
                $_win_from[] = '%e';
                $_win_to[]   = sprintf('% 2d', date('j', $timestamp));
            }
            if (strpos($format, '%l') !== false) {
                $_win_from[] = '%l';
                $_win_to[]   = sprintf('% 2d', date('h', $timestamp));
            }
            $format = str_replace($_win_from, $_win_to, $format);
        }

        return strftime($format, $timestamp);
    }

    return date($format, $timestamp);
}

/**
 * Отдельный модификатор для русской даты, чтобы не конфликтовать с
 * встроенным date_format в Smarty. Использование в шаблоне:
 * {$post.published_at|rus_date}
 */
function smarty_modifier_rus_date($string)
{
    if ($string == '') {
        return '';
    }

    $timestamp = smarty_make_timestamp($string);

    $months = array(
        1 => 'янв.',
        2 => 'фев.',
        3 => 'марта',
        4 => 'апр.',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'авг.',
        9 => 'сент.',
        10 => 'окт.',
        11 => 'ноя.',
        12 => 'дек.',
    );

    $day   = (int) date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)] ?? '';
    $year  = date('Y', $timestamp);
    $time  = date('H:i', $timestamp);

    return sprintf('%d %s %s %s', $day, $month, $year, $time);
}
