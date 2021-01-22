<?php

//CheckIpMiddleware doesn't exists anymore, but these tests can help in creating tests for custom middlewares.

//namespace Different\Dwfw\Tests\Unit\Middlewares;
//
//use Different\Dwfw\app\Http\Middleware\CheckIpMiddleware;
//use App\Models\User;
//use Different\Dwfw\app\Models\Log;
//use Different\Dwfw\Tests\TestCase;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Http;
//
//class CheckIpTest extends TestCase
//{

//    protected function createRequest(
//        $method = 'GET',
//        $content = '',
//        $uri = '/test',
//        $server = ['CONTENT_TYPE' => 'application/json'],
//        $parameters = [],
//        $cookies = [],
//        $files = []
//    ) {
//        $request = new \Illuminate\Http\Request;
//        return $request->createFromBase(
//            \Symfony\Component\HttpFoundation\Request::create(
//                $uri,
//                $method,
//                $parameters,
//                $cookies,
//                $files,
//                $server,
//                $content
//            )
//        );
//    }
//
//    /** @test */
//    function config_file_exists()
//    {
//        self::assertIsArray(config('checkIp.allow_list'));
//    }
//
//    /** @test */
//    function allowed_when_no_entries()
//    {
//        $middleware = new CheckIpMiddleware();
//        $request = new Request;
//        $response = $middleware->handle($request, function () { });
//        $this->assertNotEquals($response ? $response->getStatusCode() : null, 403);
//    }
//
//    /** @test */
//    function allowed_when_not_on_allow_list()
//    {
//        config(['checkIp.allow_list' => ['127.0.0.1']]);
//        $middleware = new CheckIpMiddleware();
//        $request = $this->createRequest();
//        $response = $middleware->handle($request, function () { });
//        $this->assertNotEquals($response ? $response->getStatusCode() :  null, 403);
//    }
//
//    /** @test */
//    function blocked_when_not_on_allow_list()
//    {
//        config(['checkIp.allow_list' => []]);
//        $middleware = new CheckIpMiddleware();
//        $request = $this->createRequest();
//        $response = $middleware->handle($request, function () { });
//        $this->assertEquals($response->getStatusCode(), 403);
//    }
//
//    /** @test */
//    function blocked_when_on_block_list()
//    {
//        config(['checkIp.block_list' => ['127.0.0.1']]);
//        $middleware = new CheckIpMiddleware();
//        $request = $this->createRequest();
//        $response = $middleware->handle($request, function () { });
//        $this->assertEquals($response->getStatusCode(), 403);
//    }
//
//}
