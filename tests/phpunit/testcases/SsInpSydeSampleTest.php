<?php
namespace SsInpSydeSampleTest;

require 'D:\wamp64\www\innoquestbi\wp-content\plugins\SsInpSydeSample\SsClassInpSyde.php';
use inc\SsInpSyde\SsInpSydeTest;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use SsClassInpSyde\SsClassInpSyde;

class SsInpSydeSampleTest extends SsInpSydeTest
{
    private object $instance;

    public function __construct()
    {
        parent::__construct(null, [], '');
        $this->instance = \Mockery::mock(SsClassInpSyde::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
    }

    public function testSsFnInitialization()
    {
        $this->instance->expects('get_option')
            ->once()
            ->with('ssApiEndPoint')
            ->andReturn('https://jsonplaceholder.typicode.com/users');
        //$expected='https://jsonplaceholder.typicode.com/users';
        $actual=$this->instance->get_option('ssApiEndPoint');

        $obj= \Mockery::mock(SsClassInpSyde::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $obj->expects('get_option')
        ->once()
        ->with('ssCustomSlug')
        ->andReturn('ssinpsyde');
        $actual=$obj->get_option('ssCustomSlug');
    }

    public function testSsHooks()
    {
        (new SsClassInpSyde())->fnCallHooks();
        self::assertFalse(has_action('admin_menu', 'SsClassInpSyde->fnAddSettingsMenu()', 20));
    }
}
