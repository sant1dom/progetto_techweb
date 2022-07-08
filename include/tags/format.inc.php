<?php

Class Format extends taglibrary {
    function injectStyle() {}

    public function format_date($name, $data, $pars) {
        return date("d/m/Y", strtotime($data));
    }

    public function format_price($name, $data, $pars) {
        return number_format($data, 2, ",", ".");
    }

    public function format_datetime($name, $data, $pars) {
        return date("d/m/Y H:i", strtotime($data));
    }

    public function upper($name, $data, $pars) {
        return strtoupper($data);
    }

    public function lower($name, $data, $pars) {
        return strtolower($data);
    }

    public function capitalize($name, $data, $pars) {
        return ucwords($data);
    }

    public function truncate($name, $data, $pars) {
        return substr($data, 0, $pars["length"]) . "...";
    }

    public function truncate_words($name, $data, $pars) {
        $words = explode(" ", $data);
        $result = "";
        $i = 0;
        foreach ($words as $word) {
            if ($i >= $pars["length"]) {
                break;
            }
            $result .= $word . " ";
            $i++;
        }
        return $result;
    }

    public function format_text($name, $data, $pars) {
        return nl2br($data);
    }

}