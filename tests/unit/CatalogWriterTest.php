<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class CatalogWriterTest extends TestCase
{
    private $root;

    protected function setUp()
    {
        uopz_redefine('XHS_LANGUAGE', 'en');
        $this->root = vfsStream::setup('root');
        uopz_redefine('XHS_CATALOG', vfsStream::url('root/catalog.php'));
    }

    public function testWrite()
    {
        $catalog = $this->createMock(Catalogue::class);
        $catalog->method('getAllCategories')->willReturn(array(
            'en' => ['cat1', 'cat2'],
            'de' => ['Kat1', 'Kat2']
        ));
        $catalog->method('getAllDefaultCategories')->willReturn(['en' => 'defcat', 'de' => 'DefKat']);
        $catalog->method('getAllLeftOverCategories')->willReturn(['en' => 'this and that', 'de' => 'dies und das']);
        $catalog->method('getProducts')->willReturn([]);
        $subject = new CatalogWriter($catalog);
        $subject->write();
    }
}
