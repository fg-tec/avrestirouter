<?php

use GuzzleHttp\Psr7\Response;

class TestController
{
    public function create(): Response
    {
        return new Response(200, [], "<p>This is the testController create method.</p>");
    }

}