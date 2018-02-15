<?php

function set_active($path, $active = 'active')
{
    if (is_array($path)) {
        foreach ($path as $p) {
            if (Request::is($p)) {
                return $active;
            }
        }
        return '';
    }

    return Request::is($path) ? $active : '';
}

function upload_path($folder = null, $file = null)
{
    $path = 'upload';
    if ($folder) {
        $path .= '/' . $folder;
        if ($file) {
            $path .= '/' . $file;
        }
    }
    return $path;
}

function session_success($text)
{
    Session::flash('success_message', $text);
}

function session_error($text)
{
    Session::flash('error_message', $text);
}

function session_info($text)
{
    Session::flash('info_message', $text);
}

function BaseActions(Illuminate\Database\Schema\Blueprint $table)
{
    $table->integer('created_by')->unsigned()->nullable();
    $table->integer('updated_by')->unsigned()->nullable();
    $table->integer('deleted_by')->unsigned()->nullable();

    $table->foreign('created_by')->references('id')->on('users');
    $table->foreign('updated_by')->references('id')->on('users');
    $table->foreign('deleted_by')->references('id')->on('users');
}

function remove_turkish($string)
{
    $charsArray = [
    'c' => ['ç', 'Ç'],
    'g' => ['ğ', 'Ğ'],
    'I' => ['İ'],
    'i' => ['ı'],
    'o' => ['Ö', 'ö'],
    's' => ['Ş', 'ş'],
    'u' => ['ü', 'Ü'],
  ];
    foreach ($charsArray as $key => $val) {
        $string = str_replace($val, $key, $string);
    }
    return $string;
}

function title_case_turkish($string)
{
    return mb_convert_case(str_replace('i', 'İ', str_replace('I', 'ı', $string)), MB_CASE_TITLE, 'UTF-8');
}

function upper_case_turkish($string)
{
    return mb_convert_case(str_replace('i', 'İ', str_replace('ı', 'I', $string)), MB_CASE_UPPER, 'UTF-8');
}

function lower_case_turkish($string)
{
    return mb_convert_case(str_replace('İ', 'i', str_replace('I', 'ı', $string)), MB_CASE_LOWER, 'UTF-8');
}

function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = [])
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }
        $url .= ' />';
    }
    return $url;
}
