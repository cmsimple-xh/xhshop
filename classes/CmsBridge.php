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

    public function getHeadings($level = 0)
    {
        if ($level > 0) {
            $array = array();
            foreach ($this->headings as $key => $heading) {
                if ($this->levels[$key] <= $level) {
                    $array[$key] = $heading;
                }
            }
            return $array;
        }
        return $this->headings;
    }

    public function getHeadingOfUrl($url = '- nope -')
    {
        if (array_search($url, $this->urls) === false) {
            trigger_error('XHS_CMSimple_Bridge::getHeadingOfUrl($url) - ' . $url . ' does not exist.');
        }
        return $this->headings[array_search($url, $this->urls)];
    }

    public function getUrls($level = 0)
    {
        if ($level > 0) {
            $array = array();
            foreach ($this->urls as $key => $link) {
                if ($this->levels[$key] <= $level) {
                    $array[$key] = $link;
                }
            }
            return $array;
        }
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

    public function getLevels($level = 0)
    {
        if ($level > 0) {
            $array = array();
            foreach ($this->levels as $mylevel) {
                if ($myLevel <= $level) {
                    $array[$key] = $myLevel;
                }
            }
            return $array;
        }
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
        global $cf;
        $cf['meta'][$strName] = strip_tags($strContent);
    }
}
