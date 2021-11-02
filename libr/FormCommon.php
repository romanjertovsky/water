<?php


class FormCommon
{


    /* VARIABLES */
    protected $iTabs = 0;               // Кол-во табуляций перед формой
    protected $sClasses = [];
    protected $sConfirmMsg = '';        // Сообщение вызываемое при клике на кнопку и отправке формы


    /* SETTERS */
    public function setITabs(int $iTabs)
    {
        $this->iTabs = $iTabs;
    }
    public function addClass($sClass)
    {
        // Это костыль, нельзя допускать множественного вызова addClass
        if(!in_array($sClass, $this->sClasses))
            //хотя такая проверка здесь нужна.
            $this->sClasses[] = $sClass;
    }
    public function setSConfirmMsg(string $sConfirmMsg)
    {
        $this->sConfirmMsg = $sConfirmMsg;
    }


    /* GETTERS */
    /**
     * Подготавливает и возвращает содержимое для class="" в html
     * @return string
     */
    protected function getClasses()
    {
        if(!empty($this->sClasses))
            return implode(' ', $this->sClasses);
        return '';
    }


    /* METHODS */
    /**
     * Вставляет табуляцию (четыре пробела) столько раз, сколько указано в $this->iTabs (Сеттер setITabs(int $iTabs)) + $i
     * @param int $iSuperTabs дополнительные табуляции кроме $this->iTabs
     * @return string
     */
    protected function tab($iSuperTabs = 0)
    {
        return str_repeat("    ", $this->iTabs + $iSuperTabs);
    }




}