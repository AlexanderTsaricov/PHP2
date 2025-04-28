<?php

use Predis\Client;
use App\src\Cache\Redis;
use PHPUnit\Framework\Attributes\TestWith;

require_once 'vendor/autoload.php';

class RedisTest extends PHPUnit\Framework\TestCase {
    public function testGetWithResponseData() {
        $expectedData = 'test data';
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
        ->with($testKey)
        ->once()
        ->andReturn($expectedData);

        $redis = new Redis($mockClient);
        $result = $redis->get($testKey);

        $this->assertEquals($expectedData, $result);

    }

    public function testGetWithFalseresponse() {
        $expectedData = true;
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('get')
        ->with($testKey)
        ->once()
        ->andReturn(null);

        $redis = new Redis($mockClient);
        $result = $redis->get($testKey, $expectedData);

        $this->assertTrue($result);
    }

    public function testSetWithoutTtl() {
        $testValue = 'value';
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('set')
        ->with($testKey, $testValue)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->set($testKey, $testValue);

        $this->assertTrue($result);
    }

    public function testSetWithTtl() {
        $testValue = 'value';
        $testKey = 'key';
        $testTtl = 3600;

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('set')
        ->with($testKey, $testValue, 'EX', $testTtl)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->set($testKey, $testValue, $testTtl);

        $this->assertTrue($result);
    }

    public function testDelete() {
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('del')
        ->with($testKey)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->delete($testKey);

        $this->assertTrue($result);
    }

    public function testClear() {
        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('flushall')
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->clear();

        $this->assertTrue($result);
    }

    public function testGetMultipleWithResopnseData() {
        $expectedData = ['testData1', 'testData2'];
        $testKeys = ['key1', 'key2'];

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('mget')
        ->with($testKeys)
        ->once()
        ->andReturn($expectedData);

        $redis = new Redis($mockClient);
        $result = $redis->getMultiple($testKeys);

        $this->assertEquals($expectedData, $result);
    }

    #[TestWith([[null, null]])]
    #[TestWith([[null, 'value']])]
    #[TestWith([['value', null]])]
    public function testGetMultipleWithFalseResponseAndArrays($expectedData) {
        $testKeys = ['key1', 'key2'];

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('mget')
        ->with($testKeys)
        ->once()
        ->andReturn($expectedData);

        $redis = new Redis($mockClient);
        $result = $redis->getMultiple($testKeys);

        $this->assertEquals($expectedData, $result);
    }

    public function testSetMultipleWithoutTtlWithTrueSend() {
        $testValues = ['value', 'value2'];
        $testKeys = ['key', 'key2'];
        $testData = [
            $testKeys[0] => $testValues[0],
            $testKeys[1] => $testValues[1],
        ];

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('multi')
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[0], $testValues[0])
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[1], $testValues[1])
        ->once();
        $mockClient->shouldReceive('exec')
        ->once()
        ->andReturn([true, true]);


        $redis = new Redis($mockClient);
        $result = $redis->setMultiple($testData);

        $this->assertTrue($result);
    }

    public function testSetMultipleWithTtlWithTrueSend() {
        $testValues = ['value', 'value2'];
        $testKeys = ['key', 'key2'];
        $testData = [
            $testKeys[0] => $testValues[0],
            $testKeys[1] => $testValues[1],
        ];
        $testTtl = new \DateInterval('PT3600S');

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('multi')
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[0], $testValues[0])
        ->once();
        $mockClient->shouldReceive('expire')
        ->with($testKeys[0], 3600)
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[1], $testValues[1])
        ->once();
        $mockClient->shouldReceive('expire')
        ->with($testKeys[1], 3600)
        ->once();
        $mockClient->shouldReceive('exec')
        ->once()
        ->andReturn([true, true]);


        $redis = new Redis($mockClient);
        $result = $redis->setMultiple($testData, $testTtl);

        $this->assertTrue($result);
    }

    #[TestWith([[false, false]])]
    #[TestWith([[false, true]])]
    #[TestWith([[true, false]])]
    public function testSetMultipleWithoutTtlWithFalseSend($resultSend) {
        $testValues = ['value', 'value2'];
        $testKeys = ['key', 'key2'];
        $testData = [
            $testKeys[0] => $testValues[0],
            $testKeys[1] => $testValues[1],
        ];

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('multi')
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[0], $testValues[0])
        ->once();
        $mockClient->shouldReceive('set')
        ->with($testKeys[1], $testValues[1])
        ->once();
        $mockClient->shouldReceive('exec')
        ->once()
        ->andReturn($resultSend);


        $redis = new Redis($mockClient);
        $result = $redis->setMultiple($testData);

        $this->assertFalse($result);
    }

    public function testDeleteMultiple() {
        $testKeys = ['key', 'key1'];

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('del')
        ->with($testKeys)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->deleteMultiple($testKeys);

        $this->assertTrue($result);

    }

    public function testHasTrueResponse() {
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('exists')
        ->with($testKey)
        ->andReturn(1)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->has($testKey);

        $this->assertTrue($result);
    }

    public function testHasFalseResponse() {
        $testKey = 'key';

        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('exists')
        ->with($testKey)
        ->andReturn(0)
        ->once();

        $redis = new Redis($mockClient);
        $result = $redis->has($testKey);

        $this->assertFalse($result);
    }
}