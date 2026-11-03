<?php

namespace App\Controllers\Admin;

class Help extends BaseController
{

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function index()
    {

        return $this->render('help/index', [
            'title'   => 'Ayuda',
        ]);
    }
}
