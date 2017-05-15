<?php

/**
 * Simple Common Javascript Library
 *
 * @author ratno
 */
class js
{
    const jQuery = '$j';

    public static function val($strControlId, $value = "")
    {
        return js::jQuery . "('#{$strControlId}').val({$value})";
    }

    public static function text($strControlId, $input = js_input::text)
    {
        switch ($input) {
            case js_input::option:
                $selected = " option:selected";
                break;
            case js_input::check:
                $selected = ":checked";
                break;
            default:
                $selected = "";
        }

        return js::jQuery . "('#{$strControlId}{$selected}').text()";
    }

    public static function str($str, $plus_pos = js_pos::after)
    {
        switch ($plus_pos) {
            case js_pos::before:
                $out = "+'$str'";
                break;
            case js_pos::after:
                $out = "'$str'+";
                break;
            case js_pos::both:
                $out = "+'$str'+";
                break;
        }

        return $out;
    }
}

class js_input
{
    const text = 1;
    const option = 2;
    const check = 3;
}

class js_pos
{
    const before = 1;
    const after = 2;
    const both = 3;
}