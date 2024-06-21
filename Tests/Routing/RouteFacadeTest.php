<?php

namespace Tests\Routing;

use AvrestiRouter\Routing\AvrestiRouter;
use AvrestiRouter\Routing\Facade\Route;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouteFacadeTest extends TestCase
{
    protected AvrestiRouter $router;

    protected function setUp(): void
    {
        $this->router = new AvrestiRouter();
        Route::setRouter($this->router);
    }

    public function testRouteWithoutNameAndWithoutGroup()
    {
        $route = Route::get('/test', fn() => new Response(200, [], 'Test'));
        $this->assertInstanceOf(\AvrestiRouter\Routing\Route::class, $route);

        $request = new ServerRequest('GET', '/test');
        $response = Route::resolve($request);
        $this->assertEquals('Test', (string) $response->getBody());
    }

    public function testRouteWithNameAndWithoutGroup()
    {
        $route = Route::get('/about', fn() => new Response(200, [], 'About'))->name('about');
        $this->assertInstanceOf(\AvrestiRouter\Routing\Route::class, $route);
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('/about', $route->getUri());
        $this->assertEquals('about', $route->getName());
        $this->assertNull($route->getGroup());

        $request = new ServerRequest('GET', '/about');
        $response = Route::resolve($request);
        $this->assertEquals('About', (string) $response->getBody());

        $generatedUrl = Route::generateUrl('about');
        $this->assertEquals('/about', $generatedUrl);
    }

    public function testRoutesGroupedAndNamed()
    {
        Route::group(['group' => 'auth'], function () {
            Route::get('/dashboard', fn() => new Response(200, [], 'Dashboard'))->name('dashboard');
            Route::get('/settings', fn() => new Response(200, [], 'Settings'))->name('settings');
        });

        $request = new ServerRequest('GET', '/dashboard');
        $response = Route::resolve($request);
        $this->assertEquals('Dashboard', (string) $response->getBody());
        $this->assertEquals('dashboard', Route::getCurrentRoute()->getName());
        $this->assertEquals('auth', Route::getCurrentRoute()->getGroup());

        $request = new ServerRequest('GET', '/settings');
        $response = Route::resolve($request);
        $this->assertEquals('Settings', (string) $response->getBody());
        $this->assertEquals('settings', Route::getCurrentRoute()->getName());
        $this->assertEquals('auth', Route::getCurrentRoute()->getGroup());
    }

    public function testRouteWithParams()
    {
        Route::get('/profile/{id}', fn($id) => new Response(200, [], "Profile ID: $id"))->name('profile.show');
        Route::get('/post/{slug}', fn($slug) => new Response(200, [], "Post Slug: $slug"))->name('post.show');

        $request = new ServerRequest('GET', '/profile/123');
        $response = Route::resolve($request);
        $this->assertEquals('Profile ID: 123', (string) $response->getBody());
        $this->assertEquals('profile.show', Route::getCurrentRoute()->getName());
        $this->assertEquals(['123'], Route::getCurrentRoute()->getParams());

        $request = new ServerRequest('GET', '/post/my-first-post');
        $response = Route::resolve($request);
        $this->assertEquals('Post Slug: my-first-post', (string) $response->getBody());
        $this->assertEquals('post.show', Route::getCurrentRoute()->getName());
        $this->assertEquals(['my-first-post'], Route::getCurrentRoute()->getParams());

        $generatedUrl = Route::generateUrl('profile.show', ['id' => 123]);
        $this->assertEquals('/profile/123', $generatedUrl);

        $generatedUrl = Route::generateUrl('post.show', ['slug' => 'my-first-post']);
        $this->assertEquals('/post/my-first-post', $generatedUrl);
    }
}
