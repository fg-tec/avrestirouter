<?php

namespace Tests\Routing;

use AvrestiRouter\Routing\AvrestiRouter;
use AvrestiRouter\Routing\Route;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AvrestiRouterTest extends TestCase
{
    protected AvrestiRouter $router;

    protected function setUp(): void
    {
        $this->router = new AvrestiRouter();
    }

    public function testRouteWithoutNameAndWithoutGroup()
    {
        $route = $this->router->get('/test', fn() => new Response(200, [], 'Test'));
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('/test', $route->getUri());
        $this->assertNull($route->getName());
        $this->assertNull($route->getGroup());

        $request = new ServerRequest('GET', '/test');
        $response = $this->router->resolve($request);
        $this->assertEquals('Test', (string) $response->getBody());
    }

    public function testRouteWithNameAndWithoutGroup()
    {
        $route = $this->router->get('/about', fn() => new Response(200, [], 'About'))->name('about');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('/about', $route->getUri());
        $this->assertEquals('about', $route->getName());
        $this->assertNull($route->getGroup());

        $request = new ServerRequest('GET', '/about');
        $response = $this->router->resolve($request);
        $this->assertEquals('About', (string) $response->getBody());

        $generatedUrl = $this->router->generateUrl('about');
        $this->assertEquals('/about', $generatedUrl);
    }

    public function testRoutesGroupedAndNamed()
    {
        $this->router->group(['group' => 'auth'], function (AvrestiRouter $router) {
            $router->get('/dashboard', fn() => new Response(200, [], 'Dashboard'))->name('dashboard');
            $router->get('/settings', fn() => new Response(200, [], 'Settings'))->name('settings');
        });

        $request = new ServerRequest('GET', '/dashboard');
        $response = $this->router->resolve($request);
        $this->assertEquals('Dashboard', (string) $response->getBody());
        $this->assertEquals('dashboard', $this->router->getCurrentRoute()->getName());
        $this->assertEquals('auth', $this->router->getCurrentRoute()->getGroup());

        $request = new ServerRequest('GET', '/settings');
        $response = $this->router->resolve($request);
        $this->assertEquals('Settings', (string) $response->getBody());
        $this->assertEquals('settings', $this->router->getCurrentRoute()->getName());
        $this->assertEquals('auth', $this->router->getCurrentRoute()->getGroup());
    }

    public function testRouteWithParams()
    {
        $this->router->get('/profile/{id}', fn($id) => new Response(200, [], "Profile ID: $id"))->name('profile.show');
        $this->router->get('/post/{slug}', fn($slug) => new Response(200, [], "Post Slug: $slug"))->name('post.show');

        $request = new ServerRequest('GET', '/profile/123');
        $response = $this->router->resolve($request);
        $this->assertEquals('Profile ID: 123', (string) $response->getBody());
        $this->assertEquals('profile.show', $this->router->getCurrentRoute()->getName());
        $this->assertEquals(['123'], $this->router->getCurrentRoute()->getParams());

        $request = new ServerRequest('GET', '/post/my-first-post');
        $response = $this->router->resolve($request);
        $this->assertEquals('Post Slug: my-first-post', (string) $response->getBody());
        $this->assertEquals('post.show', $this->router->getCurrentRoute()->getName());
        $this->assertEquals(['my-first-post'], $this->router->getCurrentRoute()->getParams());

        $generatedUrl = $this->router->generateUrl('profile.show', ['id' => 123]);
        $this->assertEquals('/profile/123', $generatedUrl);

        $generatedUrl = $this->router->generateUrl('post.show', ['slug' => 'my-first-post']);
        $this->assertEquals('/post/my-first-post', $generatedUrl);
    }
}
