<?php

namespace Xhshop;

/**
 * Interface for so called "bill" writers
 *
 * The "bills" actually don't have to be bills, but could also be delivery
 * notes or data exchange files. We stick with the name "bill" for historic
 * purposes. Anyhow, the bill is attached to the order notification email
 * sent to the shop provider.
 *
 * To implement a `BillWriter` for a new format, a class `FooBillWriter` has
 * to be placed in the same folder as `BillWriter` which is supposed to
 * implement the `BillWriter` interface. The prefix of the class (`Foo` in this
 * example) is the file extension of the mail attachment with the first letter
 * written in upper case.
 *
 * To be able to select the new bill format in the plugin configuration, the
 * respective entry in `metaconfig.php` has to be extended.
 */
interface BillWriter
{
    /**
     * Loads the template
     *
     * The `$template` argument can be ignored, if the template is located in a
     * non standard location, or if there isn't even a template at all.
     *
     * @param string $template
     * @return bool
     */
    public function loadTemplate($template);

    /**
     * Replaces the general placeholders in the template
     *
     * @param array $replacements A map from placeholders to their replacements
     * @return string The final document
     */
    public function replace(array $replacements);

    /**
     * "Writes" a single product row
     *
     * This method is called for each product, and can be used to replace the
     * order line placeholders in the template.
     * 
     * @param string $name The product name
     * @param string $amount The ordered amount
     * @param string $price The price of a single product
     * @param string $sum The order line total
     * @param string $vatRate The vate rate of the product
     * @return string
     */
    public function writeProductRow($name, $amount, $price, $sum, $vatRate);
}
