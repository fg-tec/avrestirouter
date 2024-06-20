<?php

use GuzzleHttp\Psr7\Response;

/**
 * Class TestController
 *
 * Example controller for handling test routes.
 */
class TestController
{
    /**
     * Handles the creation of a test resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return new Response(200, [], "<p>This is the testController create method.</p>");
    }
}
