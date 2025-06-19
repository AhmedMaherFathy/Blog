<?php

if (! function_exists('translate_success_message')) {

    function translate_success_message(string $key1, string $key2): string
    {
        return __('messages.'.$key1).' '.__('messages.'.$key2);
    }
}

if (! function_exists('translate_word')) {
    function translate_word(string $word): string
    {
        return __('messages.'.$word);
    }
}
