<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class TokenFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = service('uri');
        $token = $uri->getSegment(3); // Assuming the token is in the 3rd segment

        if (!$token) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Fetch the token from the database
        $db = \Config\Database::connect();
        $query = $db->table('users') // Adjust table name
            ->where('reset_password_token', $token)
            ->get();

        if ($query->getNumRows() == 0) {
            // Token not found in database
            throw PageNotFoundException::forPageNotFound();
        }

        // Token is valid, you can retrieve additional data if needed
    }


    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
