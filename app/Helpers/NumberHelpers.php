<?php

if (!function_exists('convert_numbers_to_english')) {
    function convert_numbers_to_english(string $number) : string {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumbers = range(0, 9);

        return str_replace(
            $arabicNumbers,
            $englishNumbers,
            str_replace($persianNumbers, $englishNumbers, $number)
        );
    }
}
