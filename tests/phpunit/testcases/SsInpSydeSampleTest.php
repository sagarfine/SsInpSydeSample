<?php
declare(strict_types=1);
namespace SsInpSydeSampleTest;

require dirname(__DIR__, 3).'\SsClassInpSyde.php';
use inc\SsInpSyde\SsInpSydeTest;
use Brain\Monkey\Actions;
use Mockery;
use ReflectionClass;
use ReflectionException;
use SsClassInpSyde\SsClassInpSyde;
use stdClass;

class SsInpSydeSampleTest extends SsInpSydeTest
{
    private object $instance;

    public function __construct()
    {
        parent::__construct(null, [], '');
        $this->instance = Mockery::mock(SsClassInpSyde::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $reflectionClass = new ReflectionClass(SsClassInpSyde::class);
        $reflectionProperty= new stdClass();
        try {
            $reflectionProperty = $reflectionClass->getProperty('ssApiEndPoint');
        } catch (ReflectionException $exp) {
        }
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->instance, 'https://jsonplaceholder.typicode.com/users');
    }

    public function testSsFnInitialization()
    {
        $objOne = clone $this->instance;
        $objOne->expects('get_option')
            ->once()
            ->with('ssApiEndPoint')
            ->andReturn('https://jsonplaceholder.typicode.com/users');
        $expected='https://jsonplaceholder.typicode.com/users';
        $actual=$objOne->get_option('ssApiEndPoint');
        $this->assertEquals($expected, $actual);

        $objTwo= clone $this->instance;
        $objTwo->expects('get_option')
            ->once()
            ->with('ssCustomSlug')
            ->andReturn('ssinpsyde');
        $actual=$objTwo->get_option('ssCustomSlug');
        $this->assertIsString($actual);

        $objThree= clone $this->instance;
        $objThree->expects('get_option')
            ->once()
            ->with('ssCacheExpiry')
            ->andReturn(43200);
        $actual=$objThree->get_option('ssCacheExpiry');
        $this->assertIsInt($actual);
    }

    public function testSsHooks()
    {
        Actions\expectAdded('init');
        $this->assertFalse(has_action('init', [SsClassInpSyde::class, 'ssFnRewriteRules']));
        Actions\expectAdded('plugins_loaded');
        $this->assertFalse(has_action('plugins_loaded', [SsClassInpSyde::class, 'fnLoadTextDomain']));
        Actions\expectAdded('admin_menu');
        $this->assertFalse(has_action('admin_menu', [SsClassInpSyde::class, 'fnAddSettingsMenu']));
        Actions\expectAdded('template_redirect');
        $this->assertFalse(has_action('template_redirect', [SsClassInpSyde::class, 'fnTemplateRedirect']));
        Actions\expectAdded('admin_init');
        $this->assertFalse(has_action('admin_init', [SsClassInpSyde::class, 'fnAdminInitActions']));
        Actions\expectAdded('wp_ajax_ssFnGetUserPosts');
        $this->assertFalse(
            has_action('wp_ajax_ssFnGetUserPosts', [SsClassInpSyde::class, 'ssFnGetUserDetailsById'])
        );
        Actions\expectAdded('wp_ajax_nopriv_ssFnGetUserPosts');
        $this->assertFalse(
            has_action('wp_ajax_nopriv_ssFnGetUserPosts', [SsClassInpSyde::class, 'ssFnGetUserDetailsById'])
        );
        $this->assertFalse(has_filter('query_vars', [SsClassInpSyde::class, 'fnSsRewriteFilterRequest' ]));
        (new SsClassInpSyde())->fnCallHooks();
    }

    public function testSsFnGetEndpoint()
    {
        $objOne = clone $this->instance;

        $objOne->expects('ssFnSendRequest')
            ->once()
            ->with('ssArrUsersDetails')
            ->andReturn(['https://jsonplaceholder.typicode.com/users']);
        $actual=$objOne->ssFnSendRequest('ssArrUsersDetails');
        $this->assertIsArray($actual);
    }
}
