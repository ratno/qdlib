<?php
/**
 * Created by PhpStorm.
 * User: ratno
 * Date: 8/17/17
 * Time: 6:51 PM
 */

namespace QD\Lib;


class DbComment
{
    protected $comments = [];

    public function title($title) {
        $this->comments["title"] = $title;
        return $this;
    }

    public function tostring() {
        $this->comments['tostring'] = "true";
        return $this;
    }

    public function filtervis($value = "visible") {
        $this->comments['filtervis'] = $value;
        return $this;
    }

    public function gridvis($value = "none") {
        $this->comments['gridvis'] = $value;
        return $this;
    }
    public function detailvis($value = "none") {
        $this->comments['detailvis'] = $value;
        return $this;
    }
    public function formvis($value = "none") {
        $this->comments['formvis'] = $value;
        return $this;
    }

    const hide_mobile = "mobile";
    const hide_tablet = "tablet";
    const hide_desktop = "desktop";

    public function gridhide($hide=[DbComment::hide_mobile,DbComment::hide_tablet,DbComment::hide_desktop]) {
        $this->comments['gridhide'] = implode(",",$hide);
        return $this;
    }

    public function comment($comment="") {
        $this->comments['comment'] = $comment;
        return $this;
    }

    public function listbox($listbox = "autocomplete") {
        $this->comments['listbox'] = $listbox;
        return $this;
    }

    public function __toString()
    {
        return implode("|",array_map(function($key,$value){
            return "$key:$value";
        },array_keys($this->comments),array_values($this->comments)));
    }
}