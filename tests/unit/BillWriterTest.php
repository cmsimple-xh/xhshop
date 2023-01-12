<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;

class BillWriterTest extends TestCase
{
    private $replacements = [
        "%SUM%" => "21.25 €",
        "%SHIPPING%" => "48.00 €",
        "%FEE_LABEL%" => "fee",
        "%FEE%" => "9.95 €",
        "%TOTAL%" => "79.20 €",
        "%SALUTATION%" => "Mister",
        "%FIRST_NAME%" => "Clause",
        "%LAST_NAME%" => "Santa",
        "%STREET%" => "Iceberg 1",
        "%EXTRA_ADDRESS_LINE%" => "The large one",
        "%ZIP%" => "7777",
        "%CITY%" => "North Pole",
        "%COUNTRY_CODE%" => "FR",
        "%COUNTRY%" => "France",
        "%PHONE%" => "12345",
        "%EMAIL%" => "claus@santa.com",
        "%MAY_FORWARD_EMAIL%" => "no",
        "%PAYMENT_METHOD%" => "Cash on Delivery",
        "%ANNOTATION%" => "Yada yada",
        "%CONTACT_EMAIL%" => "a@b.de",
        "%CONTACT_NAME%" => "XH-Shop Team",
        "%COMPANY_NAME%" => "XH-Shop DEMO",
        "%COMPANY_STREET%" => "XH Str. 1.7",
        "%COMPANY_ZIP%" => "12345",
        "%COMPANY_CITY%" => "Simpleburg/XH",
        "%DATE%" => "January 12th, 2023",
        "%VAT_HINT%" => "VAT included in total: 5.18 € (7 %: 5.18 € - 19 %: 0.00 €)",
    ];

    public function testWritesCorrectCsvBill()
    {
        $sut = new CsvBillWriter();
        $sut->loadTemplate(__DIR__ . "/bills/bill.tpl.csv");
        $this->writeProductRows($sut);
        $actual = $sut->replace($this->replacements);
        $this->assertStringEqualsFile(__DIR__ . "/bills/bill.csv", $actual);
    }

    public function testWritesCorrectEmlBill()
    {
        global $sl;

        $sl = "en";
        $sut = new EmlBillWriter();
        $sut->loadTemplate(__DIR__ . "/bills/bill.tpl.eml");
        $rows = $this->writeProductRows($sut);
        $actual = $sut->replace($this->replacements +  ["%ROWS%" => $rows]);
        $this->assertStringMatchesFormatFile(__DIR__ . "/bills/bill.eml", $actual);
    }

    public function testWritesCorrectRtfBill()
    {
        $sut = new RtfBillWriter();
        $sut->loadTemplate(__DIR__ . "/bills/bill.tpl.rtf");
        $rows = $this->writeProductRows($sut);
        $actual = $sut->replace($this->replacements +  ["%ROWS%" => $rows]);
        $this->assertStringEqualsFile(__DIR__ . "/bills/bill.rtf", $actual);
    }

    private function writeProductRows(BillWriter $writer): string
    {
        return $writer->writeProductRow("Peas (1000g package) ", "8 ", "2.25 €", "18.00 €", "7 %")
            . $writer->writeProductRow("Bread Rolls ", "1 ", "0.75 €", "0.75 €", "7 %")
            . $writer->writeProductRow("Muffins ", "2 ", "1.25 €", "2.50 €", "7 %");
    }
}
