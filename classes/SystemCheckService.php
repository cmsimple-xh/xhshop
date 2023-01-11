<?php

namespace Xhshop;

class SystemCheckService
{
    /**
     * @var string
     */
    private $pluginsFolder;

    /**
     * @var string
     */
    private $pluginFolder;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $lang;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx;

        $this->pluginsFolder = $pth['folder']['plugins'];
        $this->pluginFolder = "{$this->pluginsFolder}xhshop";
        $this->config = $plugin_cf['xhshop'];
        $this->lang = $plugin_tx['xhshop'];
    }

    /**
     * @return \stdClass[]
     */
    public function getChecks()
    {
        $checks = array(
            $this->checkPhpVersion('5.5.0'),
            $this->checkExtension('bcmath'),
            $this->checkExtension('json'),
            $this->checkExtension('mbstring'),
            $this->checkExtension('session'),
            $this->checkXhVersion('1.7.0'),
            $this->checkPlugin('fa'),
            $this->checkWritability("$this->pluginFolder/css/"),
            $this->checkWritability("$this->pluginFolder/config/"),
            $this->checkWritability("$this->pluginFolder/languages/"),
            $this->checkWritability(XHS_CATALOG),
            $this->checkWritability(XHS_CONTENT_PATH . 'xhshop/tmp_orders/'),
            $this->checkEmailAddress(),
            $this->checkShippingCountries(),
            $this->checkPageExists($this->lang['config_shop_page']),
            $this->checkPageExists($this->lang['config_gtc_page'], false)
        );
        if ($this->config['shipping_charge_for_shipping']) {
            $checks[] = $this->checkPageExists($this->lang['config_shipping_costs_page'], false);
            $checks[] = $this->checkForwardingExpenses();
        }
        $checks[] = $this->checkDecimal('shop_minimum_order');
        $checks[] = $this->checkDecimal('taxes_vat_full');
        $checks[] = $this->checkDecimal('taxes_vat_reduced');
        $checks[] = $this->checkDecimal('shipping_forwarding_expenses_up_to');
        $checks[] = $this->checkDecimal('cash-in-advance_fee');
        $checks[] = $this->checkDecimal('cash-on-delivery_fee');
        $checks[] = $this->checkDecimal('on-account_fee');
        $checks[] = $this->checkDecimal('paypal_fee');
        $checks[] = $this->checkCatalog();
        return $checks;
    }

    /**
     * @param string $version
     * @return \stdClass
     */
    private function checkPhpVersion($version)
    {
        $state = version_compare(PHP_VERSION, $version, 'ge') ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_phpversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $extension
     * @param bool $isMandatory
     * @return \stdClass
     */
    private function checkExtension($extension, $isMandatory = true)
    {
        $state = extension_loaded($extension) ? 'success' : ($isMandatory ? 'fail' : 'warning');
        $label = sprintf($this->lang['syscheck_extension'], $extension);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $version
     * @return \stdClass
     */
    private function checkXhVersion($version)
    {
        $state = version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH $version", 'ge') ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_xhversion'], $version);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $plugin
     * @return \stdClass
     */
    private function checkPlugin($plugin)
    {
        $state = is_dir("{$this->pluginsFolder}{$plugin}") ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_plugin'], $plugin);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $folder
     * @return \stdClass
     */
    private function checkWritability($folder)
    {
        $state = is_writable($folder) ? 'success' : 'warning';
        $label = sprintf($this->lang['syscheck_writable'], $folder);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /** @return \stdClass */
    private function checkEmailAddress()
    {
        $state = (trim($this->config['contact_order_email']) !== '') ? 'success' : 'fail';
        $label = $this->lang['syscheck_email'];
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $pageUrl
     * @param bool $isMandatory
     * @return \stdClass
     */
    private function checkPageExists($pageUrl, $isMandatory = true)
    {
        global $u;

        $ok = strpos($pageUrl, '?') === 0 && in_array(trim($pageUrl, '?'), $u, true);
        $state = $ok ? 'success' : ($isMandatory ? 'fail' : 'warning');
        $label = sprintf($this->lang['syscheck_page_exists'], $pageUrl);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @param string $key
     * @return \stdClass
     */
    private function checkDecimal($key)
    {
        $state = Decimal::isValid($this->config[$key]) ? 'success' : 'fail';
        $label = sprintf($this->lang['syscheck_decimal'], $key);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @return \stdClass
     */
    private function checkForwardingExpenses()
    {
        $state = $this->areForwardingExpensesValid() ? 'success' : 'fail';
        $label = $this->lang['syscheck_shipping'];
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @return \stdClass
     */
    private function checkShippingCountries()
    {
        $state = $this->areShippingCountriesValid() ? 'success' : 'fail';
        $label = $this->lang['syscheck_shipping_countries'];
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @return bool
     */
    private function areShippingCountriesValid()
    {
        $valid = false;
        foreach (preg_split('/\r\n|\r|\n/', $this->lang['config_shipping_countries']) as $pair) {
            if (count(explode('=', $pair)) !== 2) {
                return false;
            }
            $valid = true;
        }
        return $valid;
    }

    /** @return bool */
    private function areForwardingExpensesValid()
    {
        $lines = preg_split('/\\r\\n|\\r|\\n/', trim($this->config['shipping_forwarding_expenses']));
        $countryGrades = array();
        foreach ($lines as $line) {
            $parts = explode(':', $line);
            if (count($parts) !== 2) {
                return false;
            }
            $countryGrades[trim($parts[0])] = trim($parts[1]);
        }

        foreach ($countryGrades as $grades) {
            if (!$this->isForwardingExpensesLineValid($grades)) {
                return false;
            }
        }

        foreach (explode(';', $this->lang['config_shipping_countries']) as $pair) {
            list($code) = explode('=', $pair);
            if (!isset($countryGrades[trim($code)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $grades
     * @return bool
     */
    private function isForwardingExpensesLineValid($grades)
    {
        $weight = new Decimal('-0.01');
        $cost = new Decimal('-0.01');
        $finished = false;
        foreach (explode(';', $grades) as $expenses) {
            if ($finished) {
                return false;
            }
            $parts = explode('=', trim($expenses));
            switch (count($parts)) {
                case 1:
                    $c = $parts[0];
                    if (!Decimal::isValid($c) || ($c = new Decimal($c)) && !$c->isGreaterThan($cost)) {
                        return false;
                    }
                    $finished = true;
                    break;
                case 2:
                    $w = $parts[0];
                    $c = $parts[1];
                    if (!Decimal::isValid($w) || !Decimal::isValid($c)
                            || ($w = new Decimal($w)) && !$w->isGreaterThan($weight)
                            || ($c = new Decimal($c)) && !$c->isGreaterThan($cost)) {
                        return false;
                    }
                    $weight = $w;
                    $cost = $c;
                    break;
                default:
                    return false;
            }
        }
        return $finished;
    }

    /**
     * @return \stdClass
     */
    private function checkCatalog()
    {
        $state = $this->isCatalogValid() ? 'success' : 'fail';
        $label = $this->lang['syscheck_catalog'];
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @return bool
     */
    private function isCatalogValid()
    {
        include XHS_CATALOG;
        if (!isset($products)) {
            return true; // no products are okay
        }
        foreach ($products as $product) {
            if (!is_float($product['price']) && !Decimal::isValid($product['price'])
                    || !is_float($product['weight']) && !Decimal::isValid($product['weight'])) {
                return false;
            }
        }
        return true;
    }
}
