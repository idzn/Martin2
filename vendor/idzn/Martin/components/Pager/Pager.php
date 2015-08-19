<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Pager;

use application\custom\Controller;
use Martin\traits\UrlMethodsTrait;

class Pager
{
    use UrlMethodsTrait;
    /**
     * @var Runtime
     */
    private $config;
    private $originalUrl;
    private $limit;
    private $count;
    private $page;

    public function __construct($config = null)
    {
        $this->config = $config;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

    public function setPage($page = 1)
    {
        if ($page === null ||
            !is_numeric($page)  ||
            $page < 0
        )
            $this->page = 1;
        else
            $this->page = $page;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getPage()
    {
        return $this->page;
    }

    private function makeLink($param)
    {
        return $this->originalUrl . ((strpos($this->originalUrl, '?') === false) ? '?page=' : '&page=') . $param;
    }

    public function renderPagination()
    {
        $pagesCount = round($this->count / $this->limit);
        ob_start();
        echo '<ul class="pagination">';
        echo '<li><a href="' . $this->makeLink(1) . '">&laquo;</a></li>';
        for ($i = 1; $i <= $pagesCount; $i++) {
            echo '<li' . (($this->page == $i) ? ' class="active"' : '') . '><a href="' . $this->makeLink($i) . '">' . $i . '</a></li>';
        }
        echo '<li><a href="' . $this->makeLink($pagesCount) . '">&raquo;</a></li>';
        echo '</ul>';
        echo ob_get_clean();
    }
}