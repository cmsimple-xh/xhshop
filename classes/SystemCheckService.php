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
     * @return object[]
     */
    public function getChecks()
    {
        return array(
            $this->checkPhpVersion('5.3.0'),
            $this->checkExtension('json'),
            $this->checkExtension('mbstring'),
            $this->checkExtension('session'),
            $this->checkXhVersion('1.6.3'),
            $this->checkPlugin('fa'),
            $this->checkWritability("$this->pluginFolder/css/"),
            $this->checkWritability("$this->pluginFolder/config/"),
            $this->checkWritability("$this->pluginFolder/languages/"),
            $this->checkWritability(XHS_CATALOG),
            $this->checkWritability(XHS_CONTENT_PATH . 'xhshop/tmp_orders/'),
            $this->checkEmailAddress(),
            $this->checkPageExists($this->lang['config_shop_page']),
            $this->checkPageExists($this->lang['config_cos_page'], false),
            $this->checkForwardingExpenses()
        );
    }

    /**
     * @param string $version
     * @return object
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
     * @return object
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
     * @return object
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
     * @return object
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
     * @return object
     */
    private function checkWritability($folder)
    {
        $state = is_writable($folder) ? 'success' : 'warning';
        $label = sprintf($this->lang['syscheck_writable'], $folder);
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

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
     * @return object
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
     * @return object
     */
    private function checkForwardingExpenses()
    {
        $state = $this->areForwardingExpensesValid() ? 'success' : 'fail';
        $label = $this->lang['syscheck_shipping'];
        $stateLabel = $this->lang["syscheck_$state"];
        return (object) compact('state', 'label', 'stateLabel');
    }

    /**
     * @return bool
     */
    private function areForwardingExpensesValid()
    {
        $weight = 0;
        $cost = 0;
        $finished = false;
        foreach (explode(';', $this->config['shipping_forwarding_expenses']) as $expenses) {
            if ($finished) {
                return false;
            }
            $parts = explode('=', trim($expenses));
            switch (count($parts)) {
                case 1:
                    if ($parts[0] <= $cost) {
                        return false;
                    }
                    $finished = true;
                    break;
                case 2:
                    if ($parts[0] <= $weight || $parts[1] <= $cost) {
                        return false;
                    }
                    $weight = $parts[0];
                    $cost = $parts[1];
                    break;
                default:
                    return false;
            }
        }
        return true;
    }
}
