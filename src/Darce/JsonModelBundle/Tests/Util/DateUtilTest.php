<?php declare(strict_types=1);

namespace App\Darce\JsonModelBundle\Tests\Util;

use App\Darce\JsonModelBundle\Utils\DateUtil;
use DateTime;
use PHPUnit\Framework\TestCase;

class DateUtilTest extends TestCase
{
    public function testSimpleFormatIsRecognizedAsDate(): void
    {
        $datetime = DateUtil::recognizeString('2019-07-25 15:00:00');
        $this->assertInstanceOf(DateTime::class, $datetime);
    }

    public function testStringIsNotADate(): void
    {
        $datetime = DateUtil::recognizeString('foo bar baz');
        $this->assertNull($datetime);
    }

    public function testFormatWithTIsRecognizedAsDate(): void
    {
        $datetime = DateUtil::recognizeString('2015-01-19T16:47:30-05:00');
        $this->assertInstanceOf(DateTime::class, $datetime);
    }

    public function testStringNumberIsNotADate(): void
    {
        $datetime = DateUtil::recognizeString('1.0');
        $this->assertNull($datetime);
    }

    public function testCharIsNotADate(): void
    {
        $datetime = DateUtil::recognizeString('S');
        $this->assertNull($datetime);
    }
}