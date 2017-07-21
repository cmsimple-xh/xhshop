<?php

namespace Xhshop;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class CatalogWriterTest extends TestCase
{
    const PRODUCT_A = array(
        'names' => ['de' => 'Kaffee (im Versandbecher)', 'en' => 'Coffee (in shipping cup)'],
        'descriptions' => array(
            'de' => '<p>Coffee. Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne.</p>',
            'en' => '<p>Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne. Hinc possim ex eum, vel electram expetenda ea.</p>'
        ),
        'teasers' => array(
            'de' => '<p>Coffee. Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne.</p>',
            'en' => ''
        ),
        'price' => 2.75,
        'productPages' => array(
            'de' => [],
            'en' => []
        ),
        'previewPicture' => 'preview_coffee.jpg',
        'vat' => 'full',
        'separator' => '/',
        'categories' => array(
            'de' => ['Trinken'],
            'en' => ['Beverages']
        ),
        'stock_on_hand' => 1,
        'weight' => 0.3,
        'variants' => array(
            'de' => [],
            'en' => []
        ),
        'uid' => 'p4bbf07812257f',
        'sortIndex' => 4,
        'image' => 'coffee.jpg',
        'imageFolder' => './userfiles/images/shop/',
        'previewFolder' => './userfiles/images/shop/'
    );

    const PRODUCT_B = array(
        'names' => ['de' => 'Brötchen', 'en' => 'Bread Rolls'],
        'descriptions' => array(
            'de' => '<p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p><p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p>',
            'en' => '<p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p>'
        ),      
        'teasers' => ['de' => '<p>Ganz fantastische Brötchen!</p>'],
        'price' => 0.75,
        'productPages' => array('de' => [], 'en' => []),
        'previewPicture' => 'preview_bread.jpg',
        'vat' => 'reduced',
        'separator' => '/',
        'categories' => array(
            'de' => ['Essen', '– Backwaren'],
            'en' => ['Delicious things', '– fresh from the bakery']
        ),
        'stock_on_hand' => 1,
        'weight' => 0.04,
        'variants' => array(
            'de' => ['Sesam', 'Mohn', 'Käse'],
            'en' => []
        ),
        'uid' => 'p4bbf078122572',
        'sortIndex' => 3,
        'image' => 'bread.jpg',
        'imageFolder' => './userfiles/images/shop/',
        'previewFolder' => './userfiles/images/shop/'
    );

    const CATALOG = <<<'EOT'
<?php 
################### Catalog ###############################;
$version = '1.0';

################### Categories ###############################;
$categories['en'][0] = 'cat1';
$categories['en'][1] = 'cat2';
$categories['de'][0] = 'Kat1';
$categories['de'][1] = 'Kat2';
$default_category['en'] = 'defcat';
$default_category['de'] = 'DefKat';
$category_for_the_left_overs['en'] = 'this and that';
$category_for_the_left_overs['de'] = 'dies und das';


################### Products ######################;
$products['p4bbf07812257f']['names']['de'] = 'Kaffee (im Versandbecher)';
$products['p4bbf07812257f']['names']['en'] = 'Coffee (in shipping cup)';
$products['p4bbf07812257f']['price'] = 2.75;
$products['p4bbf07812257f']['vat'] = 'full';
$products['p4bbf07812257f']['sortIndex'] = 4;
$products['p4bbf07812257f']['previewPicture'] = 'preview_coffee.jpg';
$products['p4bbf07812257f']['image'] = 'coffee.jpg';
$products['p4bbf07812257f']['weight'] = 0.30;
$products['p4bbf07812257f']['stock_on_hand'] = 1;
$products['p4bbf07812257f']['teasers']['de'] = '<p>Coffee. Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne.</p>';
$products['p4bbf07812257f']['teasers']['en'] = '';
$products['p4bbf07812257f']['descriptions']['de'] = '<p>Coffee. Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne.</p>';
$products['p4bbf07812257f']['descriptions']['en'] = '<p>Mediocrem erroribus forensibus ea mel, mea omnes eligendi eu. Sit sint eros ne. Hinc possim ex eum, vel electram expetenda ea.</p>';
$products['p4bbf07812257f']['variants']['de'] = array();
$products['p4bbf07812257f']['variants']['en'] = array();
$products['p4bbf07812257f']['categories']['de'] = array('Trinken', );
$products['p4bbf07812257f']['categories']['en'] = array('Beverages', );
$products['p4bbf07812257f']['productPages']['de'] = array();
$products['p4bbf07812257f']['productPages']['en'] = array();
$products['p4bbf07812257f']['separator'] = '/';
$products['p4bbf07812257f']['uid'] = 'p4bbf07812257f';

#-----------------------------------------------------

$products['p4bbf078122572']['names']['de'] = 'Brötchen';
$products['p4bbf078122572']['names']['en'] = 'Bread Rolls';
$products['p4bbf078122572']['price'] = 0.75;
$products['p4bbf078122572']['vat'] = 'reduced';
$products['p4bbf078122572']['sortIndex'] = 3;
$products['p4bbf078122572']['previewPicture'] = 'preview_bread.jpg';
$products['p4bbf078122572']['image'] = 'bread.jpg';
$products['p4bbf078122572']['weight'] = 0.04;
$products['p4bbf078122572']['stock_on_hand'] = 1;
$products['p4bbf078122572']['teasers']['de'] = '<p>Ganz fantastische Brötchen!</p>';
$products['p4bbf078122572']['descriptions']['de'] = '<p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p><p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p>';
$products['p4bbf078122572']['descriptions']['en'] = '<p>Posse nostrum ne est, amet hinc intellegam et his. Cum tota modus exerci in, verterem aliquyam ei vix, nec id autem reprimique.</p>';
$products['p4bbf078122572']['variants']['de'] = array('Sesam', 'Mohn', 'Käse', );
$products['p4bbf078122572']['variants']['en'] = array();
$products['p4bbf078122572']['categories']['de'] = array('Essen', '– Backwaren', );
$products['p4bbf078122572']['categories']['en'] = array('Delicious things', '– fresh from the bakery', );
$products['p4bbf078122572']['productPages']['de'] = array();
$products['p4bbf078122572']['productPages']['en'] = array();
$products['p4bbf078122572']['separator'] = '/';
$products['p4bbf078122572']['uid'] = 'p4bbf078122572';

#-----------------------------------------------------

?>
EOT;

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
            'products' => array(
                'p4bbf07812257f' => Product::createFromRecord(self::PRODUCT_A, 1, '/'),
                'p4bbf078122572' => Product::createFromRecord(self::PRODUCT_B, 2, '/')
            ),
        );
        $subject = new CatalogWriter($catalog);
        $subject->write();
        $this->assertEquals(self::CATALOG, $this->root->getChild('catalog.php')->getContent());
    }
}
