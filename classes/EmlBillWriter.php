<?php

namespace Xhshop;

use PHPMailer\PHPMailer\PHPMailer;

class EmlBillWriter implements BillWriter
{
    private $template;

    private $subject;

    private $rowTemplate;

    public function loadTemplate($template)
    {
        $template = XH_readFile($template);
        list($this->subject, $this->template) = preg_split('/\r\n|\r|\n/', $template, 2);
        return $template !== false;
    }

    public function replace(array $replacements)
    {
        global $sl;
        
        require_once(XHS_BASE_PATH . 'phpmailer/PHPMailer.php');
        require_once(XHS_BASE_PATH . 'phpmailer/Exception.php');
        foreach ($replacements as $search => $replace) {
            $this->template = str_ireplace($search, $replace, $this->template);
            $this->subject = str_ireplace($search, $replace, $this->subject);
        }
        $mail = new PHPMailer();
        $mail->WordWrap = 60;
        $mail->IsHTML(true);
        $mail->set('CharSet', 'UTF-8');
        $mail->setLanguage($sl, XHS_BASE_PATH . 'phpmailer/language/');
        
        $mail->From = $replacements['%CONTACT_EMAIL%'];
        $mail->FromName = $replacements['%CONTACT_NAME%'];
        $mail->AddAddress($replacements['%EMAIL%'], "{$replacements['%FIRST_NAME%']} {$replacements['%LAST_NAME%']}");
        $mail->Subject = $this->subject;
        $mail->Body = $this->template;
        $mail->preSend();
        return $mail->getSentMIMEMessage();
    }

    public function writeProductRow($name, $amount, $price, $sum, $vatRate)
    {
        if (!isset($this->rowTemplate)) {
            if (preg_match('/<tr\W.*?%PNAME%.*?<\/tr>/is', $this->template, $matches, PREG_OFFSET_CAPTURE)) {
                $this->rowTemplate = $matches[0][0];
                $start = $matches[0][1];
                $end = $matches[0][1] + strlen($this->rowTemplate);
                $this->template = substr($this->template, 0, $start) . '%ROWS%' . substr($this->template, $end);
            }
        }
        return str_ireplace(
            array('%PA%', '%PNAME%', '%PVAT%', '%PPRICE%', '%PSUM%'),
            array($amount, $name, $vatRate, $price, $sum),
            $this->rowTemplate
        );
    }
}
