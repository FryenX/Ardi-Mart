<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class UuidFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = service('uri');
        $segments = $uri->getSegments(); // Get all segments of the URI

        // Regular expression for UUID validation
        $uuidRegex = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/';

        // Iterate through segments to find a valid UUID
        foreach ($segments as $segment) {
            if (preg_match($uuidRegex, $segment)) {
                return; // If a valid UUID is found, exit the method
            }
        }

        // If no valid UUID is found, return an HTTP 404 error
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after logic is needed
    }
}
