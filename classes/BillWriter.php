<?php

namespace Xhshop;

class BillWriter
{
    private $template;

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

    public function replace($replacements)
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
        $row = '\trowd\trql\trleft-20\trpaddft3\trpaddt0\trpaddfl3\trpaddl10\trpaddfb3\trpaddb0\trpaddfr3\trpaddr10\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clvertalb\cellx724\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\cellx5036\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clvertalb\cellx6481\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clbrdrr\brdrs\brdrw1\brdrcf1\clvertalb\cellx8640
\pard\intbl\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$amount.'}
\cell\pard\plain \intbl\ltrpar\s1\cf0{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$name."\t".$vatRate.'}
\cell\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$price.' }
\cell\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$sum. ' }\cell\row';
        return $row;
    }
}
