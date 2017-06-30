<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class XHS_BillWriter{
    var $template;
    var $currency;

    function __construct(){

    }
    function loadTemplate($template){
        $fh = fopen($template, "r");
        if(!$fh){return false;}
        $this->template = fread($fh, filesize($template));
        fclose($fh);
        return true;
    }

    function replace($replacements){
        $html_table = get_html_translation_table(HTML_ENTITIES);
        $change_back_table = array();
        foreach($html_table as $char => $html){
            $change_back_table[$html] = utf8_encode($char);
        }
        foreach($replacements as $search => $replace){
            $cleaned = strtr($replace, $change_back_table);

            $cleaned = utf8_decode($cleaned);
            $this->template = str_replace($search, $cleaned, $this->template);
        }
        return $this->template;
    }

    function saveBill(){
        $fh = fopen(XHS_BILLS_PATH.'bill.rtf','w+');
        if ($fh)
        {
            fwrite ($fh,$this->template);
            fclose($fh);
        }
    }

    function setCurrency($currency){
        if($currency == '&euro;' || '€'){$currency = "\'80";}
        if($currency == '&pound;'){$currency = "{\'a3}";}
        if($currency == '&yen;'){$currency = "\'a5";}
        $this->currency = $currency;
    }

    function getCurrency(){
        return $this->currency;
    }

    function writeProductRow($name, $amount, $price, $sum, $vatRate) {
        $row = '\trowd\trql\trleft-20\trpaddft3\trpaddt0\trpaddfl3\trpaddl10\trpaddfb3\trpaddb0\trpaddfr3\trpaddr10\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clvertalb\cellx724\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\cellx5036\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clvertalb\cellx6481\clbrdrl\brdrs\brdrw1\brdrcf1\clbrdrb\brdrs\brdrw1\brdrcf1\clbrdrr\brdrs\brdrw1\brdrcf1\clvertalb\cellx8640
\pard\intbl\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$amount.'}
\cell\pard\plain \intbl\ltrpar\s1\cf0{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$name."\t".$vatRate.'}
\cell\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$price.' }
\cell\pard\plain \intbl\ltrpar\s1\cf0\qr{\*\hyphen2\hyphlead2\hyphtrail2\hyphmax0}\rtlch\af1\afs24\lang255\ltrch\dbch\af1\langfe255\hich\f1\fs24\lang1031\loch\f1\fs24\lang1031 {\rtlch \ltrch\loch\f1\fs24\lang1031\i0\b0 '.$sum. ' '.$this->currency.'}\cell\row';
        return $row;
    }
}
?>