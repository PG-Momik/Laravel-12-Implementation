<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\WebzPostResponseDTO;
use App\Services\WebzApiService;
use Exception;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Throwable;

class WebzApiServiceUnitTest extends TestCase
{
    protected WebzApiService $service;

    /**
     * @throws ReflectionException
     */
    public function testSanitizeQueryParams(): void
    {
        $queryParams = [
            'q'          => '   test  ',
            'sort'       => ' relevance ',
            'order'      => ' asc ',
            'sentiment'  => ' negative ',
            'highlight'  => ' false ',
            'size'       => ' 10 ',
            'random_key' => 'random_value'
        ];

        $expectedParams = [
            'q'         => 'test',
            'sort'      => 'relevance',
            'order'     => 'asc',
            'sentiment' => 'negative',
            'highlight' => 'false',
            'size'      => '10',

        ];

        $sanitizedParams = $this->invokePrivateMethod('sanitizeQueryParams', [$queryParams]);

        $this->assertEquals($expectedParams, $sanitizedParams);
        $this->assertArrayNotHasKey('random_key', $sanitizedParams);
    }


    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function testPrepareNextUrl(): void
    {
        $expectedNextUrl = 'https://api.webz.io/newsApiLite?token=test-token&ts=1744350915425&q=random&sort=relevancy&order=desc&from=10&ns=40.811604&ni=81533ca53452eb2ec66e9d27c5d4a293df3bb5ff&sentiment=negative&highlight=true&size=10';
        $initialResponse = $this->getResponseJson();

        $responseDto = new WebzPostResponseDTO($initialResponse);
        $preparedNextUrl = $this->invokePrivateMethod('prepareNextUrl', [$responseDto]);

        $this->assertEquals($expectedNextUrl, $preparedNextUrl);
    }

    /**
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testHandleFailCaseThrows(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('API request failed with status: 500');

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('status')->willReturn(500);
        $mockResponse->method('body')->willReturn('Internal Server Error');

        $this->invokePrivateMethod('handleFailCase', [$mockResponse]);
    }


    /**
     * @throws ReflectionException
     */
    public function testShouldContinueFetchingMore(): void
    {
        $totalPostsAvailable = 10;
        $totalPostsRetrieved = 5;
        $this->assertTrue($this->invokePrivateMethod('shouldContinueFetchingMore', [$totalPostsAvailable, $totalPostsRetrieved]));
    }

    /**
     * @throws ReflectionException
     */
    public function testShouldNotContinueFetchingMore()
    {
        $totalPostsAvailable = 10;
        $totalPostsRetrieved = 11;
        $this->assertFalse($this->invokePrivateMethod('shouldContinueFetchingMore', [$totalPostsAvailable, $totalPostsRetrieved]));
    }

    /**
     * @throws ReflectionException
     */
    public function testShouldNotContinueFetchingMoreWhenCountSame()
    {
        $totalPostsAvailable = 10;
        $totalPostsRetrieved = 10;
        $this->assertFalse($this->invokePrivateMethod('shouldContinueFetchingMore', [$totalPostsAvailable, $totalPostsRetrieved]));
    }


    /**
     * @throws ReflectionException
     */
    public function testShouldWriteToDb(): void
    {
        $writeBatch = $this->generateWriteBatch(200);
        $this->assertTrue($this->invokePrivateMethod('shouldWriteToDb', [$writeBatch]));
    }

    /**
     * @throws ReflectionException
     */
    public function testShouldNotWriteToDb()
    {
        $writeBatch = $this->generateWriteBatch(199);
        $this->assertFalse($this->invokePrivateMethod('shouldWriteToDb', [$writeBatch]));
    }

    /**
     * Test that actual fetch and write process does not crash.
     *
     * @throws Throwable
     */
    public function testFetchPostsDoesNotCrash(): void
    {
        $mockedResponse = $this->getResponseJson();

        Http::fake([
            'https://api.webz.io/newsApiLite*' => Http::response($mockedResponse, 200)
        ]);

        $queryParams = [
            'q' => 'test query',
            'sort' => 'relevance',
            'order' => 'desc',
            'sentiment' => 'positive',
            'highlight' => 'true',
            'size' => '10'
        ];

        $this->service->fetchPosts($queryParams);

        $this->assertTrue(true);
    }

    /**
     * Returns valid test response json for testing.
     *
     * @throws Exception
     */
    protected function getResponseJson()
    {
        $initialResponse = file_get_contents(public_path('responseJson.json'));
        if($initialResponse){
            return json_decode($initialResponse, true);
        }

        throw new Exception();
    }


    /**
     * Return a non-empty array of size : $arraySize
     *
     * @param int $arraySize
     *
     * @return array
     */
    protected function generateWriteBatch(int $arraySize): array
    {
        return array_fill(0, $arraySize, 'val');
    }

    /**
     * Method to invoke private methods.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    protected function invokePrivateMethod(string $method, array $args): mixed
    {
        $ref       = new ReflectionClass($this->service);
        $refMethod = $ref->getMethod($method);

        return $refMethod->invokeArgs($this->service, $args);
    }

    /**
     * Test setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        $this->service = app(WebzApiService::class);
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    protected function createApplication(): Application
    {
        $app = require __DIR__ . '../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Teardown
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_error_handler();
        restore_exception_handler();
    }
}
