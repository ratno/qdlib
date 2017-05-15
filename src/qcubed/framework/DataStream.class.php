<?php

/**
 * DataStream for get or post data
 *
 * @author ratno
 */
class DataStream
{
    const MEDIA_FGC = 'file_get_contents';
    const MEDIA_FOPEN = 'fopen';
    const MEDIA_CURL = 'curl';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    protected $url;
    protected $data;
    protected $method;
    protected $media;
    protected $header;
    protected $opts;

    public function __construct($url, $data = null, $method = DataStream::METHOD_GET, $media = DataStream::MEDIA_FGC)
    {
        $this->url = $url;
        $this->setData($data);
        $this->method = $method;
        $this->media = $media;
    }

    public function setData($data, $blnFromFile = false)
    {
        if ($blnFromFile) {
            $this->data = file_get_contents($data);
        } else {
            if (is_array($data) && count($data) > 0) {
                $this->data = http_build_query($data);
            } else {
                $this->data = http_build_query(array("data" => $data));
            }
        }
    }

    public function post()
    {
        switch ($this->media) {
            case DataStream::MEDIA_FGC:
                $this->defaultOpts();
                return file_get_contents($this->url, false, $this->context());

            case DataStream::MEDIA_FOPEN:
                $this->defaultOpts();
                $stream = fopen($this->url, "r", false, $this->context());
                $out['header'] = stream_get_meta_data($stream);
                $out['body'] = stream_get_contents($stream);
                return $out;

            case DataStream::MEDIA_CURL:
                $this->defaultCurlOpts();
                $ch = curl_init();
                curl_setopt_array($ch, $this->opts);
                if (!$result = curl_exec($ch)) {
                    trigger_error(curl_error($ch));
                }
                curl_close($ch);
                return $result;
        }
    }

    protected function defaultOpts()
    {
        $this->setOpt("method", $this->method);
        $this->setOpt("header", 'Content-type: application/x-www-form-urlencoded');
        if ($this->method == DataStream::METHOD_POST) {
            $this->setOpt("content", $this->data);
        } else {
            $this->url .= (strpos($this->url, '?') === FALSE ? '?' : '') . $this->data;
        }
    }

    public function setOpt($key, $val, $context = "http")
    {
        $this->opts[$context][$key] = $val;
    }

    protected function context()
    {
        return stream_context_create($this->opts);
    }

    protected function defaultCurlOpts()
    {
        if ($this->method == DataStream::METHOD_POST) {
            $this->setCurlOpt(CURLOPT_POST, TRUE);
            $this->setCurlOpt(CURLOPT_POSTFIELDS, $this->data);
            $this->setCurlOpt(CURLOPT_FRESH_CONNECT, TRUE);
            $this->setCurlOpt(CURLOPT_FORBID_REUSE, TRUE);
            $this->setCurlOpt(CURLOPT_URL, $this->url);
        } else {
            $this->setCurlOpt(CURLOPT_URL, $this->url . (strpos($this->url, '?') === FALSE ? '?' : '') . $this->data);
        }

        $this->setCurlOpt(CURLOPT_HEADER, FALSE);
        $this->setCurlOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $this->setCurlOpt(CURLOPT_TIMEOUT, 4);
    }

    public function setCurlOpt($key, $val)
    {
        $this->opts[$key] = $val;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setMedia($media)
    {
        $this->method = $media;
    }
}