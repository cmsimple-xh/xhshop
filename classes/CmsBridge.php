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

    /** @return array */
    public function getHeadings()
    {
        return $this->headings;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getHeadingOfUrl($url = '- nope -')
    {
        if (array_search($url, $this->urls) === false) {
            trigger_error('XHS_CMSimple_Bridge::getHeadingOfUrl($url) - ' . $url . ' does not exist.');
        }
        return $this->headings[array_search($url, $this->urls)];
    }

    /** @return array */
    public function getUrls()
    {
        return $this->urls;
    }

    /** @return bool */
    public function pageExists($link = '')
    {
        return in_array($link, $this->urls);
    }

    /**
     * @param string $url
     * @return string
     */
    public function translateUrl($url)
    {
        return '?' . $url;
    }

    /** @return array */
    public function getLevels()
    {
        return $this->levels;
    }

    /** @return void */
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

    /** @return string */
    public function getCurrentPage()
    {
        $url = explode('&', $_SERVER['QUERY_STRING']);
        return $url[0];
    }

    /**
     * @param string $strTitle
     * @return void
     */
    public function setTitle($strTitle)
    {
        global $title;
        $title = strip_tags($strTitle);
    }

    /**
     * @param string $strName
     * @param string $strContent
     * @return void
     */
    public function setMeta($strName, $strContent)
    {
        global $tx;

        $tx['meta'][$strName] = strip_tags($strContent);
    }
}
