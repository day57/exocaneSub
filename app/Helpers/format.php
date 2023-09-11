<?php

/**
 * Format the page titles.
 *
 * @param null $value
 * @return string|null
 */
function formatTitle($value = null)
{
    if (is_array($value)) {
        return implode(" - ", $value);
    }

    return $value;
}

/**
 * Format money.
 *
 * @param $amount
 * @param $currency
 * @param bool $separator
 * @param bool $translate
 * @return string
 */
function formatMoney($amount, $currency, $separator = true, $translate = true)
{
    if (in_array(strtoupper($currency), config('currencies.zero_decimals'))) {
        return number_format($amount, 0, $translate ? __('.') : '.', $separator ? ($translate ? __(',') : ',') : false);
    } else {
        return number_format($amount, 2, $translate ? __('.') : '.', $separator ? ($translate ? __(',') : ',') : false);
    }
}

/**
 * Get and format the Gravatar URL.
 *
 * @param $email
 * @param int $size
 * @param string $default
 * @param string $rating
 * @return string
 */
function gravatar($email, $size = 80, $default = 'identicon', $rating = 'g')
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= '?s='.$size.'&d='.$default.'&r='.$rating;
    return $url;
}

/**
 * Convert a number into a readable one.
 *
 * @param   int   $number  The number to be transformed
 * @return  string
 */
function shortenNumber($number)
{
    $suffix = ["", "K", "M", "B"];
    $precision = 1;
    for($i = 0; $i < count($suffix); $i++) {
        $divide = $number / pow(1000, $i);
        if($divide < 1000) {
            return round($divide, $precision).$suffix[$i];
        }
    }

    return $number;
}



/*
  
    defines four standalone PHP functions that are likely to be used for formatting and displaying data within an application. Here's a summary of each function:

    formatTitle($value = null):

    Input: A string or an array.
    Output: If the input is an array, it concatenates the elements with a " - " separator. If it's a string or null, it returns the value as-is.
    Purpose: Formats a page title, especially useful if the title consists of multiple parts.
    formatMoney($amount, $currency, $separator = true, $translate = true):

    Input: Amount of money, currency code, a boolean to include separators, and a boolean to translate separators.
    Output: A formatted money string.
    Purpose: Formats a money amount based on the given currency. If the currency is in a zero-decimals list (defined in a config file), it uses zero decimal places; otherwise, it uses two. Translation and separation are handled based on the boolean flags.
    gravatar($email, $size = 80, $default = 'identicon', $rating = 'g'):

    Input: Email address, size of the image, default image type, and rating.
    Output: A URL to the Gravatar image.
    Purpose: Generates a Gravatar URL for the given email. The size, default image, and rating can be customized.
    shortenNumber($number):

    Input: A numerical value.
    Output: A shortened, human-readable string representation of the number.
    Purpose: Converts large numbers into a more readable format using suffixes like "K" for thousands, "M" for millions, and "B" for billions. It's useful for displaying large numbers in a compact way.
    These functions provide utility in various parts of an application, assisting with the presentation of data in a user-friendly manner.
   
*/