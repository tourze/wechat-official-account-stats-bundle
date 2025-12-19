<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\UserCumulateCrudController;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

/**
 * @internal
 */
#[CoversClass(UserCumulateCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserCumulateCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<UserCumulate>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(UserCumulateCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'date' => ['统计日期'];
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
        $controller = new UserCumulateCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(UserCumulate::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new UserCumulateCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new UserCumulateCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
