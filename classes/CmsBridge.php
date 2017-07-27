<?php

namespace Xhshop;

class CmsBridge
{
    private $headings = array();
    private $levels = array();
    private $urls = array();

    public function __construct()
    {
        global $h, $l, $u;
        $this->headings = $h;
        $this->levels = $l;

        $this->urls = $u;
    }

    public function getHeadings()
    {
        return $this->headings;
    }

    public function getHeadingOfUrl($url = '- nope -')
    {
        if (array_search($url, $this->urls) === false) {
            trigger_error('XHS_CMSimple_Bridge::getHeadingOfUrl($url) - ' . $url . ' does not exist.');
        }
        return $this->headings[array_search($url, $this->urls)];
    }

    public function getUrls()
    {
        return $this->urls;
    }

    public function pageExists($link = '')
    {
        return in_array($link, $this->urls);
    }

    public function translateUrl($url)
    {
        return '?' . $url;
    }

    public function getLevels()
    {
        return $this->levels;
    }

    public function initProductDescriptionEditor()
    {
        global $bjs;
        // init_editor(); [cmb]
        include_editor();

        $bjs .= '<script>'
                . editor_replace('xhsTeaser', 'minimal')
                . editor_replace('xhsDescription', 'medium')
                . '</script>';
    }

    public function getCurrentPage()
    {
        $url = explode('&', $_SERVER['QUERY_STRING']);
        return $url[0];
    }

    public function setTitle($strTitle)
    {
        global $title;
        $title = strip_tags($strTitle);
    }

    public function setMeta($strName, $strContent)
    {
        global $tx;

        $tx['meta'][$strName] = strip_tags($strContent);
    }
}
