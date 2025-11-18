<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\InterfaceSummaryHourCrudController;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;

/**
 * @internal
 */
#[CoversClass(InterfaceSummaryHourCrudController::class)]
#[RunTestsInSeparateProcesses]
final class InterfaceSummaryHourCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<InterfaceSummaryHour>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @phpstan-ignore-next-line */
        return self::getService(InterfaceSummaryHourCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'date' => ['统计日期'];
        yield 'refHour' => ['小时'];
        yield 'callbackCount' => ['被动回复次数'];
        yield 'failCount' => ['失败次数'];
        yield 'totalTimeCost' => ['总耗时'];
        yield 'maxTimeCost' => ['最大耗时'];
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
        $controller = new InterfaceSummaryHourCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(InterfaceSummaryHour::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new InterfaceSummaryHourCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new InterfaceSummaryHourCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
