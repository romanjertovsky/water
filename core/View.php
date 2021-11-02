<?php


class View
{


    private $sModContent = '';
    private $sTitle = '';
    private $sModals = [];
    private $sScripts = [];
    private $aAdvises = [];


    public function getRenderLayout(): string
    {
        ob_start();
        include DIR_HTML . 'layout.php';
        return ob_get_clean();
    }


    public function setSTitle(string $sTitle)
    {
        $this->sTitle = $sTitle;
    }


    public function setSModContent($sModContent)
    {
        $this->sModContent = $sModContent;
    }


    public function addAlert($sAlert, $iColor = 0)
    { // $iColor - Уровень: 0 - default, 1 - green, 2 - yellow, 3 - red
        $_SESSION['alerts'][] = [$sAlert, $iColor];
    }


    public function getAlerts(): array
    {
        $aAlerts = array();
        if (!empty($_SESSION['alerts'])) {
            $aAlerts = $_SESSION['alerts'];
            unset($_SESSION['alerts']);
        }
        return $aAlerts;
    }


    public function addAdvice($sAdvice)
    {
        $this->aAdvises[] = $sAdvice;
    }


    public function getAdvices(): array
    {
        return $this->aAdvises;
    }


    public function addModal($sModal)
    {
        $this->sModals[] = $sModal;
    }


    public function getSModals(): array
    {
        return $this->sModals;
    }


    public function addScript($sScript)
    {
        $this->sScripts[] = $sScript;
    }


    public function getSScripts(): array
    {
        return $this->sScripts;
    }


    public function redirect($sUrl)
    {
        header("Location: {$sUrl}");
        exit();
    }


    public function debug($sMessage)
    {
        print "<p style=\"color: red; background-color: #4b492b;\">$sMessage</p>";
    }


}
