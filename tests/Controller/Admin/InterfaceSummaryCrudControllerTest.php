<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\InterfaceSummaryCrudController;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

/**
 * @internal
 */
#[CoversClass(InterfaceSummaryCrudController::class)]
#[RunTestsInSeparateProcesses]
final class InterfaceSummaryCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<InterfaceSummary>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @phpstan-ignore-next-line */
        return self::getService(InterfaceSummaryCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '被动回复次数' => ['被动回复次数'];
        yield '失败次数' => ['失败次数'];
        yield '总耗时' => ['总耗时'];
        yield '最大耗时' => ['最大耗时'];
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

    public function testGetEntityFqcn(): void
    {
        $controller = new InterfaceSummaryCrudController();
        $this->assertSame(
            InterfaceSummary::class,
            $controller::getEntityFqcn()
        );
    }

    public function testControllerConfiguration(): void
    {
        $controller = new InterfaceSummaryCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(InterfaceSummary::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new InterfaceSummaryCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new InterfaceSummaryCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
