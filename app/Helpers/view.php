<?php

/**
 * Get the category's color.
 *
 * @param $name
 * @return string|void
 */
function categoryColor($name)
{
    switch ($name) {
        case 'content':
            return 'success';
        case 'website':
            return 'danger';
        case 'marketing':
            return 'info';
        case 'social':
            return 'warning';
        case 'custom':
            return 'dark';
    }
}

/**
 * Encode a string for Quill display.
 *
 * @return string
 */
function encodeQuill($input)
{
    return "<p>" . str_replace("\n\n", "<p><br></p>", $input) . "</p>";
}

/**
 * Return the total words count.
 *
 * @param $text
 * @return float
 */
function wordsCount($text)
{
    $words = array_filter(explode(' ', preg_replace('/[^\w]/ui', ' ', mb_strtolower(trim($text)))));

    $wordsCount = 0;
    foreach ($words as $word) {
        $wordsCount += wordCount($word);
    }
    return round($wordsCount);
}

/**
 * Parse a word and return its word count based on a symbol to word ratio.
 *
 * @param $word
 * @return float|int
 */
function wordCount($word)
{
    foreach (config('completions.ratios') as $ratio) {
        if (preg_match('/\p{' . implode('}|\p{', $ratio['scripts']) . '}/ui', $word)) {
            return mb_strlen($word) * $ratio['value'];
        }
    }

    return 1;
}

/*
    categoryColor($name): This function takes a category name as input and returns a corresponding color string based on predefined categories. For example, 'content' returns 'success', 'website' returns 'danger', etc. If the category name does not match any of the predefined cases, the function returns void.

    encodeQuill($input): This function is designed to encode input for display in a Quill text editor. It wraps the input text in <p> tags and replaces double line breaks (\n\n) with a paragraph break (<p><br></p>).

    wordsCount($text): This function calculates the total word count for a given text input. It splits the text into words, disregards any non-word characters, and then counts the words using the wordCount function. It returns the rounded word count.

    wordCount($word): This function takes a single word as input and returns its word count based on a symbol-to-word ratio defined in the configuration under completions.ratios. It uses regular expressions to match the word against defined scripts and multiplies the length of the word by the corresponding ratio value. If no match is found, it returns 1.

    Overall, these functions could be used in various contexts, such as categorizing content, formatting text for a rich text editor, and analyzing text for word count based on specific rules.

*/