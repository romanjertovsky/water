<?php


class Paginator extends FormCommon
{


    private $iPages;
    private $iActivePage;


    public function __construct($iRowsCount, $iActivePage = 1)
    {
        $this->iPages = ceil($iRowsCount / PAGE_SIZE);
        $this->iActivePage = $iActivePage;
    }


    public function makePaginator()
    {

        if($this->iPages < 2) {
            return '';
        }

        $sPaginator  = $this->tab(0) . "<small>Страница:</small><br>\n";
        $sPaginator .= $this->tab(0) . "<nav aria-label=\"Pages\">\n";
        $sPaginator .= $this->tab(1) . "<ul class=\"pagination\">\n";
        $sModName = Core::getController()->getSModName();

        for($i = 1; $i < $this->iPages+1; $i++) {
            $sPaginator .= $this->tab(2) .
                "<li class=\"page-item" .
                ($i == $this->iActivePage?
                    ' active'
                    :
                    '') .
                "\"><a class=\"page-link\" href=\"/$sModName/$i/\">$i</a></li>\n";
        }

        $sPaginator .= $this->tab(1) . "</ul>\n";
        $sPaginator .= $this->tab(0) . "</nav>\n";

        return $sPaginator;

    }


}


