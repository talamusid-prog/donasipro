<?php

use App\Models\AppSetting;

if (!function_exists('app_setting')) {
    /**
     * Get application setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function app_setting($key, $default = null) {
        return AppSetting::getValue($key, $default);
    }
}

// Fallback functions untuk ekstensi yang hilang

// BCMath fallback
if (!extension_loaded('bcmath')) {
    if (!function_exists('bcadd')) {
        function bcadd($num1, $num2, $scale = 0) {
            return (float)($num1 + $num2);
        }
    }
    
    if (!function_exists('bcsub')) {
        function bcsub($num1, $num2, $scale = 0) {
            return (float)($num1 - $num2);
        }
    }
    
    if (!function_exists('bcmul')) {
        function bcmul($num1, $num2, $scale = 0) {
            return (float)($num1 * $num2);
        }
    }
    
    if (!function_exists('bcdiv')) {
        function bcdiv($num1, $num2, $scale = 0) {
            if ($num2 == 0) return false;
            return (float)($num1 / $num2);
        }
    }
    
    if (!function_exists('bcmod')) {
        function bcmod($num1, $num2) {
            return (float)($num1 % $num2);
        }
    }
    
    if (!function_exists('bcpow')) {
        function bcpow($num1, $num2, $scale = 0) {
            return (float)pow($num1, $num2);
        }
    }
    
    if (!function_exists('bcsqrt')) {
        function bcsqrt($num, $scale = 0) {
            return (float)sqrt($num);
        }
    }
    
    if (!function_exists('bccomp')) {
        function bccomp($num1, $num2, $scale = 0) {
            if ($num1 < $num2) return -1;
            if ($num1 > $num2) return 1;
            return 0;
        }
    }
}

// Mbstring fallback
if (!extension_loaded('mbstring')) {
    if (!function_exists('mb_strlen')) {
        function mb_strlen($str, $encoding = null) {
            return strlen($str);
        }
    }
    
    if (!function_exists('mb_substr')) {
        function mb_substr($str, $start, $length = null, $encoding = null) {
            return substr($str, $start, $length);
        }
    }
    
    if (!function_exists('mb_strtolower')) {
        function mb_strtolower($str, $encoding = null) {
            return strtolower($str);
        }
    }
    
    if (!function_exists('mb_strtoupper')) {
        function mb_strtoupper($str, $encoding = null) {
            return strtoupper($str);
        }
    }
    
    if (!function_exists('mb_convert_case')) {
        function mb_convert_case($str, $mode, $encoding = null) {
            switch ($mode) {
                case MB_CASE_UPPER:
                    return strtoupper($str);
                case MB_CASE_LOWER:
                    return strtolower($str);
                case MB_CASE_TITLE:
                    return ucwords(strtolower($str));
                default:
                    return $str;
            }
        }
    }
    
    if (!function_exists('mb_convert_encoding')) {
        function mb_convert_encoding($str, $to_encoding, $from_encoding = null) {
            return $str; // Simple fallback
        }
    }
    
    if (!function_exists('mb_detect_encoding')) {
        function mb_detect_encoding($str, $encoding_list = null, $strict = false) {
            return 'UTF-8'; // Assume UTF-8
        }
    }
    
    if (!function_exists('mb_internal_encoding')) {
        function mb_internal_encoding($encoding = null) {
            return 'UTF-8';
        }
    }
    
    if (!function_exists('mb_http_output')) {
        function mb_http_output($encoding = null) {
            return 'UTF-8';
        }
    }
    
    if (!function_exists('mb_http_input')) {
        function mb_http_input($type = null) {
            return 'UTF-8';
        }
    }
    
    if (!function_exists('mb_language')) {
        function mb_language($language = null) {
            return 'uni';
        }
    }
    
    if (!function_exists('mb_regex_encoding')) {
        function mb_regex_encoding($encoding = null) {
            return 'UTF-8';
        }
    }
    
    if (!function_exists('mb_regex_set_options')) {
        function mb_regex_set_options($options = null) {
            return '';
        }
    }
    
    if (!function_exists('mb_send_mail')) {
        function mb_send_mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null) {
            return mail($to, $subject, $message, $additional_headers, $additional_parameters);
        }
    }
    
    if (!function_exists('mb_strpos')) {
        function mb_strpos($haystack, $needle, $offset = 0, $encoding = null) {
            return strpos($haystack, $needle, $offset);
        }
    }
    
    if (!function_exists('mb_strrpos')) {
        function mb_strrpos($haystack, $needle, $offset = 0, $encoding = null) {
            return strrpos($haystack, $needle, $offset);
        }
    }
    
    if (!function_exists('mb_strstr')) {
        function mb_strstr($haystack, $needle, $before_needle = false, $encoding = null) {
            return strstr($haystack, $needle, $before_needle);
        }
    }
    
    if (!function_exists('mb_strrchr')) {
        function mb_strrchr($haystack, $needle, $before_needle = false, $encoding = null) {
            return strrchr($haystack, $needle);
        }
    }
    
    if (!function_exists('mb_strrichr')) {
        function mb_strrichr($haystack, $needle, $before_needle = false, $encoding = null) {
            return strrchr(strtolower($haystack), strtolower($needle));
        }
    }
    
    if (!function_exists('mb_strripos')) {
        function mb_strripos($haystack, $needle, $offset = 0, $encoding = null) {
            return stripos($haystack, $needle, $offset);
        }
    }
    
    if (!function_exists('mb_stristr')) {
        function mb_stristr($haystack, $needle, $before_needle = false, $encoding = null) {
            return stristr($haystack, $needle, $before_needle);
        }
    }
    
    if (!function_exists('mb_strrchr')) {
        function mb_strrchr($haystack, $needle, $before_needle = false, $encoding = null) {
            return strrchr($haystack, $needle);
        }
    }
    
    if (!function_exists('mb_strwidth')) {
        function mb_strwidth($str, $encoding = null) {
            return strlen($str);
        }
    }
    
    if (!function_exists('mb_strimwidth')) {
        function mb_strimwidth($str, $start, $width, $trimmarker = '', $encoding = null) {
            return substr($str, $start, $width);
        }
    }
    
    if (!function_exists('mb_convert_kana')) {
        function mb_convert_kana($str, $option = 'KV', $encoding = null) {
            return $str;
        }
    }
    
    if (!function_exists('mb_convert_variables')) {
        function mb_convert_variables($to_encoding, $from_encoding, &$vars, ...$rest) {
            return $to_encoding;
        }
    }
    
    if (!function_exists('mb_encode_mimeheader')) {
        function mb_encode_mimeheader($str, $charset = null, $transfer_encoding = null, $linefeed = null, $indent = null) {
            return $str;
        }
    }
    
    if (!function_exists('mb_decode_mimeheader')) {
        function mb_decode_mimeheader($str) {
            return $str;
        }
    }
    
    if (!function_exists('mb_convert_encoding')) {
        function mb_convert_encoding($str, $to_encoding, $from_encoding = null) {
            return $str;
        }
    }
    
    if (!function_exists('mb_detect_order')) {
        function mb_detect_order($encoding_list = null) {
            return ['UTF-8', 'ISO-8859-1'];
        }
    }
    
    if (!function_exists('mb_parse_str')) {
        function mb_parse_str($encoded_string, &$result) {
            return parse_str($encoded_string, $result);
        }
    }
    
    if (!function_exists('mb_output_handler')) {
        function mb_output_handler($contents, $status) {
            return $contents;
        }
    }
    
    if (!function_exists('mb_str_split')) {
        function mb_str_split($string, $split_length = 1, $encoding = null) {
            return str_split($string, $split_length);
        }
    }
    
    if (!function_exists('mb_ord')) {
        function mb_ord($string, $encoding = null) {
            return ord($string);
        }
    }
    
    if (!function_exists('mb_chr')) {
        function mb_chr($codepoint, $encoding = null) {
            return chr($codepoint);
        }
    }
    
    if (!function_exists('mb_scrub')) {
        function mb_scrub($string, $encoding = null) {
            return $string;
        }
    }
}

// Define constants if not defined
if (!defined('MB_CASE_UPPER')) define('MB_CASE_UPPER', 0);
if (!defined('MB_CASE_LOWER')) define('MB_CASE_LOWER', 1);
if (!defined('MB_CASE_TITLE')) define('MB_CASE_TITLE', 2);
if (!defined('MB_CASE_FOLD')) define('MB_CASE_FOLD', 3);
if (!defined('MB_CASE_UPPER_SIMPLE')) define('MB_CASE_UPPER_SIMPLE', 4);
if (!defined('MB_CASE_LOWER_SIMPLE')) define('MB_CASE_LOWER_SIMPLE', 5);
if (!defined('MB_CASE_TITLE_SIMPLE')) define('MB_CASE_TITLE_SIMPLE', 6);
if (!defined('MB_CASE_FOLD_SIMPLE')) define('MB_CASE_FOLD_SIMPLE', 7);

// Original helper functions
if (!function_exists('format_rupiah')) {
    function format_rupiah($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_percentage')) {
    function format_percentage($current, $target) {
        if ($target == 0) return 0;
        return round(($current / $target) * 100, 1);
    }
}

if (!function_exists('get_days_left')) {
    function get_days_left($end_date) {
        $end = \Carbon\Carbon::parse($end_date);
        $now = \Carbon\Carbon::now();
        $diff = $now->diffInDays($end, false);
        return max(0, $diff);
    }
} 