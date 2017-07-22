<?php

namespace Xhshop;

class CsvBillWriter implements BillWriter
{
    const BOM = "\xEF\xBB\xBF";

    private $records = array();

    private $rowRecordNum;

    private $hasBom;

    public function loadTemplate($template)
    {
        if (!($stream = fopen($template, 'r'))) {
            return false;
        }
        $this->hasBom = fread($stream, 3) === self::BOM;
        if (!$this->hasBom) {
            rewind($stream);
        }
        while (($record = fgetcsv($stream, 0, ';')) !== false) {
            $this->records[] = $record;
        }
        fclose($stream);
        return true;
    }

    public function replace(array $replacements)
    {
        unset($this->records[$this->rowRecordNum]);
        foreach ($replacements as $search => $replace) {
            $cleaned = html_entity_decode($replace, ENT_QUOTES, 'UTF-8');
            foreach ($this->records as &$record) {
                foreach ($record as &$field) {
                    $field = str_ireplace($search, $cleaned, $field);
                }
                unset($field);
            }
            unset($record);
        }
        $stream = fopen('php://memory', 'w+');
        if ($this->hasBom) {
            fwrite($stream, self::BOM);
        }
        foreach ($this->records as $record) {
            fputcsv($stream, $record, ';');
        }
        rewind($stream);
        $result = stream_get_contents($stream);
        fclose($stream);
        return $result;
    }

    public function writeProductRow($name, $amount, $price, $sum, $vatRate)
    {
        if (!isset($this->rowRecordNum)) {
            foreach ($this->records as $i => $record) {
                foreach ($record as $fields) {
                    if (stripos($fields, '%PNAME%') !== false) {
                        $this->rowRecordNum = $i;
                        break 2;
                    }
                }
            }
        }
        $record = $this->records[$this->rowRecordNum];
        foreach ($record as &$field) {
            $field = str_ireplace(
                array('%PA%', '%PNAME%', '%PVAT%', '%PPRICE%', '%PSUM%'),
                array($amount, $name, $vatRate, $price, $sum),
                $field
            );
        }
        unset($field);
        array_splice($this->records, $this->rowRecordNum++, 0, array($record));
        return '';
    }
}
