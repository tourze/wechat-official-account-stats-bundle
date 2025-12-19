<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\SettlementIncomeDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;

/**
 * @internal
 */
#[CoversClass(SettlementIncomeDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SettlementIncomeDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<SettlementIncomeData>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(SettlementIncomeDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'date' => ['统计日期'];
        yield 'body' => ['主体名称'];
        yield 'month' => ['收入月份'];
        yield 'zone' => ['日期区间'];
        yield 'order' => ['结算周期'];
        yield 'settStatus' => ['结算状态'];
        yield 'revenueAll' => ['累计收入'];
        yield 'penaltyAll' => ['扣除金额'];
        yield 'settledRevenueAll' => ['已结算金额'];
        yield 'settledRevenue' => ['区间内结算收入'];
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
        $controller = new SettlementIncomeDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(SettlementIncomeData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new SettlementIncomeDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new SettlementIncomeDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
