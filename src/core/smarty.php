<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/plugins/modifier.date_format.php';

/**
 *  Мультибайтовое усечение строки
 *
 * @param $string
 * @param $length
 * @param $placeholder
 * @return string
 */
function smarty_modifier_mb_truncate(string $string, int $length = 80, string $placeholder = '...') {
    if ($length <= mb_strlen($string, 'UTF-8')) {
        return $string;
    }

    // учтем пробелы между слованим ытобы не рвать их
    $cut = mb_substr($string, 0, $length, 'UTF-8');
    $lastSpace = mb_strrpos($cut, ' ', 0, 'UTF-8');
    if ($lastSpace !== false) {
        $cut = mb_substr($cut, 0, $lastSpace, 'UTF-8');
    }

    if ($cut === '') {
        $cut = mb_substr($string, 0, $length, 'UTF-8');
    }

    return rtrim($cut) . $placeholder;
}



/**
 * print_r
 *
 * @param $value
 * @return string
 */
function smarty_modifier_print_r($value): string
{
    return '<pre>' . print_r($value, true) . '</pre>';
}


/**
 * @return \Smarty\Smarty
 * @throws \Smarty\Exception
 */
function get_smarty(): Smarty\Smarty
{
    static $smarty = null;

    if ($smarty === null) {
        $smarty = new Smarty\Smarty();
        $smarty->setTemplateDir(SMARTY_TEMPLATES_DIR);
        $smarty->setCompileDir(SMARTY_COMPILE_DIR);
        $smarty->setCacheDir(SMARTY_CACHE_DIR);

        $smarty->setCaching(true);
        if (IS_LOCAL) {
            $smarty->clearAllCache();
            $smarty->clearCompiledTemplate();
        }

        $smarty->registerPlugin('modifier', 'mb_truncate', 'smarty_modifier_mb_truncate');
        $smarty->registerPlugin('modifier', 'rus_date', 'smarty_modifier_rus_date');
        $smarty->registerPlugin('modifier', 'print_r', 'smarty_modifier_print_r');
    }

    return $smarty;
}
