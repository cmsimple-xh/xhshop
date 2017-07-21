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
        $this->root = vfsStream::setup('root');
        uopz_redefine('XHS_LANGUAGE', 'en');
        uopz_redefine('XHS_CATALOG', vfsStream::url('root/catalog.php'));
    }

    public function testWrite()
    {
        $catalog = (object) array(
            'version' => '1.0',
            'categories' => array(
                'en' => ['cat1', 'cat2'],
                'de' => ['Kat1', 'Kat2']
            ),
            'default_category' => ['en' => 'defcat', 'de' => 'DefKat'],
            'category_for_the_left_overs' => ['en' => 'this and that', 'de' => 'dies und das'],
            'products' => []
        );
        $subject = new CatalogWriter($catalog);
        $subject->write();
    }
}
