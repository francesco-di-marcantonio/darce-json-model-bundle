<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Tests\Util;

use App\Darce\JsonModelBundle\Utils\JsonUtil;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JsonUtilTest extends TestCase
{
    public function testJsonFileIsRead(): void
    {
        $json = JsonUtil::obtainJsonFromFile(__DIR__ . '/../data/valid.json');
        $this->assertIsString($json);
    }

    public function testJsonFileNotFound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        JsonUtil::obtainJsonFromFile(__DIR__ . '/../data/not_existing.json');
    }

    public function testJsonIsValid(): void
    {
        $json = JsonUtil::obtainJsonFromFile(__DIR__ . '/../data/valid.json');
        JsonUtil::isValidJson($json);
        $this->assertIsString($json);
    }

    public function testJsonIsNotValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $json = JsonUtil::obtainJsonFromFile(__DIR__ . '/../data/invalid.json');
        JsonUtil::isValidJson($json);
    }
}