<?php

namespace Xhshop;

class RtfBillWriter implements BillWriter
{
    private $template;
    private $rowTemplate;

    public function loadTemplate($template)
    {
        $this->template = file_get_contents($template);
        return $this->template !== false;
    }

    public function replace(array $replacements)
    {
        foreach ($replacements as $search => $replace) {
            $cleaned = html_entity_decode($replace, ENT_QUOTES, 'UTF-8');
            $cleaned = $this->convertToRtf($cleaned);
            $this->template = str_ireplace($search, $cleaned, $this->template);
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
            preg_match_all('/\{|\}|\\\\trowd|\\\\row|%PNAME%/i', $this->template, $matches, PREG_OFFSET_CAPTURE);
            $structure = $matches[0];
            do {
                $foundAdjacentBracePairs = $this->removeAdjacentBracePairs($structure);
                $foundEmptyRows = $this->removeEmptyRows($structure);
            } while ($foundAdjacentBracePairs || $foundEmptyRows);
            $this->determineTemplates($structure);
        }
        return str_ireplace(
            array('%PA%', '%PNAME%', '%PVAT%', '%PPRICE%', '%PSUM%'),
            array($amount, $name, $vatRate, $price, $sum),
            $this->rowTemplate
        );
    }

    /**
     * @return bool
     */
    private function removeAdjacentBracePairs(array &$structure)
    {
        $found = false;
        for ($i = 0; $i < count($structure) - 1; $i++) {
            if ($structure[$i][0] === '{' && $structure[$i + 1][0] === '}') {
                $found = true;
                unset($structure[$i], $structure[$i + 1]);
                $i++;
            } elseif ($structure[$i][0] === '{' && $structure[$i+1][0] === '%pname%'
                    && $structure[$i+2][0] === '}') {
                $found = true;
                unset($structure[$i], $structure[$i+2]);
                $i += 2;
            }
        }
        $structure = array_values($structure);
        return $found;
    }

    /**
     * @return bool
     */
    private function removeEmptyRows(array &$structure)
    {
        $found = false;
        for ($i = 0; $i < count($structure)-1; $i++) {
            if ($structure[$i][0] === '\trowd' && $structure[$i+1][0] === '\row') {
                $found = true;
                unset($structure[$i], $structure[$i+1]);
                $i++;
            }
        }
        $structure = array_values($structure);
        return $found;
    }

    /**
     * @return void
     */
    private function determineTemplates(array $structure)
    {
        $pname = $this->findPNamePlaceholder($structure);
        if (!isset($pname)) {
            trigger_error('can\'t find the %PNAME% placeholder', E_USER_WARNING);
        }
        if ($structure[$pname-1][0] !== '\trowd') {
            trigger_error('can\'t determine start of row', E_USER_WARNING);
        }
        if (!in_array($structure[$pname+1][0], array('\row', '\trowd'))) {
            trigger_error('can\'t determine end of row', E_USER_WARNING);
        }
        $start = $structure[$pname-1][1];
        $end = $structure[$pname+1][1];
        if ($structure[$pname+1][0] === '\row') {
            $end += strlen('\row');
        }
        $this->rowTemplate = substr($this->template, $start, $end-$start);
        $this->template = substr($this->template, 0, $start) . '%ROWS%' . substr($this->template, $end);
    }

    /**
     * @return ?int
     */
    private function findPNamePlaceholder(array $structure)
    {
        foreach ($structure as $index => $element) {
            if (strcasecmp($element[0], '%pname%') === 0) {
                return $index;
            }
        }
        return null;
    }
}
