<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'html', 'menu', 'i18n', 'thumb'];
    protected $data    = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->data['base_url'] = base_url();
        $this->data['segment']  = service('uri')->getSegment(2) ?? 'dashboard';
    }

    protected function render(string $view, array $data = [])
    {
        $data = array_merge($this->data, $data);
        return view('admin/' . $view, $data);
    }
}