<?php

namespace Xhshop;

class CmsBridge
{
    var $headings = array();
    var $levels = array();
    var $urls = array();

    function __construct()
    {
        global $h, $l, $u;
        $this->headings = $h;
        $this->levels = $l;

        $this->urls = $u;
    }

    function getHeadings($level = 0)
    {
        if ($level > 0)
        {
            $array = array();
            foreach ($this->headings as $key => $heading)
            {
                if ($this->levels[$key] <= $level)
                {
                    $array[$key] = $heading;
                }
            }
            return $array;
        }
        return $this->headings;
    }

    function getHeadingOfUrl($url = '- nope -')
    {
        if (array_search($url, $this->urls) === false)
        {
            trigger_error('XHS_CMSimple_Bridge::getHeadingOfUrl($url) - ' . $url . ' does not exist.');
        }
        return $this->headings[array_search($url, $this->urls)];
    }

    function getUrls($level = 0)
    {
        if ($level > 0)
        {
            $array = array();
            foreach ($this->urls as $key => $link)
            {
                if ($this->levels[$key] <= $level)
                {
                    $array[$key] = $link;
                }
            }
            return $array;
        }
        return $this->urls;
    }

    function getLevelOfUrl($url)
    {
        if (array_search($url, $this->urls) === false)
        {
            trigger_error('XHS_CMSimple_Bridge::getLevelOfUrl($url) - ' . $url . ' does not exist.');
        }
        return $this->levels[array_search($url, $this->urls)];
    }

    function pageExists($link = '')
    {
        return in_array($link, $this->urls);
    }

    function translateStringToUrl($string = '')
    {
        return '?' . uenc($string);
    }

    function translateUrl($url)
    {
        return '?' . $url;
    }

    function getLevels($level = 0)
    {
        if ($level > 0)
        {
            $array = array();
            foreach ($this->levels as $mylevel)
            {
                if ($myLevel <= $level)
                {
                    $array[$key] = $myLevel;
                }
            }
            return $array;
        }
        return $this->levels;
    }

    function addToHeader($string)
    {
        global $hjs;
        $hjs .= $string;
    }

    function shopSubpages($level = 6)
    {
        $pages = array();
        $index     = array_search(XHS_URL, $this->urls);
        $shopLevel = $this->levels[$index];
        $index++;
        $i         = 0;
        while ($index < count($this->urls) && $this->levels[$index] > $shopLevel)
        {
            if ($this->levels[$index] > $level)
            {
                $index++;
                continue;
            }
            $pages[$i]['url']     = '?' . $this->urls[$index];
            $pages[$i]['heading'] = $this->headings[$index];
            $pages[$i]['level']   = $this->levels[$index];
            $index++;
            $i++;
        }
        return $pages;
    }

    function initProductDescriptionEditor()
    {
        global $bjs;
        // init_editor(); [cmb]
		include_editor();

        $bjs .= '<script>'
                . editor_replace('xhsTeaser', 'minimal')
                . editor_replace('xhsDescription', 'medium')
                . '</script>';
    }

    function getCurrentPage()
    {
        $url = explode('&', $_SERVER['QUERY_STRING']);
        return $url[0];
    }

    function setTitle($strTitle){
        global $title;
        $title = strip_tags($strTitle);
    }

    function setMeta($strName, $strContent){
        global $cf;
        $cf['meta'][$strName] = strip_tags($strContent);
    }
}
?>