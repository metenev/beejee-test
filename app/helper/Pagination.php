<?php

namespace BeeJeeTest\Helper;

use BeeJeeTest\Core\Request;

class Pagination {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function uri($page, $full = false)
    {
        $result = $this->request->uri;

        if ($full)
        {
            $result = "{$this->scheme}://{$this->host}/" . $result;
        }

        $params = $this->request->get();

        $params['page'] = $page + 1;

        $result .= '?' . http_build_query($params);

        return $result;
    }

}
