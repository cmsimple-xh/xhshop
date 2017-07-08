<?php

class XHS_SystemCheckService
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
    private $lang;

    public function __construct()
    {
        global $pth, $plugin_tx;

        $this->pluginsFolder = $pth['folder']['plugins'];
        $this->pluginFolder = "{$this->pluginsFolder}xhshop";
        $this->lang = $plugin_tx['xhshop'];
    }

    /**
     * @return object[]
     */
    public function getChecks()
    {
        return array(
            $this->checkPhpVersion('5.3.0'),
            $this->checkExtension('session'),
            $this->checkExtension('xml'),
            $this->checkXhVersion('1.6.3'),
            $this->checkPlugin('fa'),
            $this->checkWritability("$this->pluginFolder/css/"),
            $this->checkWritability("$this->pluginFolder/config/"),
            $this->checkWritability("$this->pluginFolder/data/"),
            $this->checkWritability("$this->pluginFolder/languages/")
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
        $state = is_dir($this->pluginsFolder) ? 'success' : 'fail';
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
}
