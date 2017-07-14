<?php

namespace Xhshop;

class BillWriter
{
    private $template;
    private $rowTemplate;

    public function loadTemplate($template)
    {
        $fh = fopen($template, "r");
        if (!$fh) {
            return false;
        }
        $this->template = fread($fh, filesize($template));
        fclose($fh);
        return true;
    }

    public function replace(array $replacements)
    {
        foreach ($replacements as $search => $replace) {
            $cleaned = html_entity_decode($replace, ENT_QUOTES, 'UTF-8');
            $cleaned = $this->convertToRtf($cleaned);
            $this->template = str_replace($search, $cleaned, $this->template);
        }
        return $this->template;
    }

    /**
     * Convert a UTF-8 encoded text to RTF format
     *
     * ASCII characters are not converted, but for compatibility with different
     * code pages *all* non ASCII characters are converted to Unicode escapes
     * without alternative representation. To avoid issues with templates
     * defining a \ucN, all Unicode escapes are grouped in an {\uc0}.
     */
    private function convertToRtf($text)
    {
        ob_start();
        $isAscii = true;
        $utf16 = mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
        $codepoints = unpack('v*', $utf16);
        foreach ($codepoints as $codepoint) {
            if ($codepoint < 0x80) {
                if (!$isAscii) {
                    echo '}';
                    $isAscii = true;
                }
                echo chr($codepoint);
            } else {
                if ($codepoint >= 0x8000) {
                    $codepoint -= 0x10000;
                }
                if ($isAscii) {
                    echo '{\uc0';
                    $isAscii = false;
                }
                echo "\\u$codepoint";
            }
        }
        if (!$isAscii) {
            echo '}';
        }
        return ob_get_clean();
    }

    public function writeProductRow($name, $amount, $price, $sum, $vatRate)
    {
        if (!isset($this->rowTemplate)) {
            if (preg_match_all('/\\\\trowd.*?\\\\row/s', $this->template, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    if (strpos($match[0], '%pname%') !== false) {
                        $this->rowTemplate = $match[0];
                        $this->template = substr($this->template, 0, $match[1]) . '%rows%' . substr($this->template, $match[1] + strlen($this->rowTemplate));
                        break;
                    }
                }
            }
        }
        return str_replace(
            array('%pa%', '%pname%', '%pvat%', '%pprice%', '%psum%'),
            array($amount, $name, $vatRate, $price, $sum),
            $this->rowTemplate
        );
    }
}
