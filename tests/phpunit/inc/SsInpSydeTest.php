<?php
declare(strict_types=1);
namespace inc\SsInpSyde;

use Mockery;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;

class SsInpSydeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp():void
    {
        parent::setUp();
        Monkey\setUp();
        // A few common passthrough
        // 1. WordPress i18n functions
        Monkey\Functions\when('esc_html__')->returnArg(1);
        Monkey\Functions\when('__')->returnArg(1);
        Monkey\Functions\when('_e')->returnArg(1);
        Monkey\Functions\when('_n')->returnArg(1);
    }

    protected function tearDown():void
    {
        Monkey\tearDown();
        parent::tearDown();
        Mockery::close();
    }
}
