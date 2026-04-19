<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RedirectResponse;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'sipena'];
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $timezone = app_setting('app_timezone', config('App')->appTimezone ?? 'Asia/Jakarta');
        if (is_string($timezone) && $timezone !== '') {
            try {
                date_default_timezone_set($timezone);
            } catch (\Throwable $e) {
                // Keep default timezone from App config when setting value is invalid.
            }
        }

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    protected function enforceRoles(array $allowedRoles): ?RedirectResponse
    {
        $role = strtolower(trim((string) session('role')));
        if ($role === '' || ! in_array($role, $allowedRoles, true)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return null;
    }
}
