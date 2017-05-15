<?php

/*
 * api ini dapat diakses dari http://localhost/folder/api/sample/coba
 * buat class yang extends QApi
 * buat sebuah function Process dengan output disimpan ke dalam $this->arrOutput
 * dengan demikian api sudah tercipta, 
 * pastikan untuk sebuah keluaran selalu mencantumkan status=ok atau status=error
 */

class SampleCoba extends QApi
{
    protected function Process()
    {
        parent::Process();
        $this->arrOutput = array(
            "status" => "ok",
            "data" => array("satu", "dua", "tiga")
        );
    }
}