<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\UserSummaryCrudController;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;

/**
 * @internal
 */
#[CoversClass(UserSummaryCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserSummaryCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<UserSummary>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(UserSummaryCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'date' => ['统计日期'];
        yield 'newUser' => ['新增用户数'];
        yield 'cancelUser' => ['取消关注用户数'];
        yield 'cumulateUser' => ['累计用户数'];
    }

    public static function provideNewPageFields(): iterable
    {
        // 此控制器禁用了NEW操作，但需要提供虚拟数据避免空数据提供器错误
        yield 'dummy' => ['dummy'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 此控制器禁用了EDIT操作，但需要提供虚拟数据避免空数据提供器错误
        yield 'dummy' => ['dummy'];
    }

    public function testControllerConfiguration(): void
    {
        $controller = new UserSummaryCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(UserSummary::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new UserSummaryCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new UserSummaryCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
