<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatOfficialAccountStatsBundle\Controller\Admin\MessageSendWeekDataCrudController;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;

/**
 * @internal
 */
#[CoversClass(MessageSendWeekDataCrudController::class)]
#[RunTestsInSeparateProcesses]
final class MessageSendWeekDataCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<MessageSendWeekData>
     */
    protected function getControllerService(): AbstractCrudController
    {
        /** @phpstan-ignore-next-line */
        return self::getService(MessageSendWeekDataCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield '统计日期' => ['统计日期'];
        yield '消息类型' => ['消息类型'];
        yield '发送用户数' => ['发送用户数'];
        yield '发送消息总数' => ['发送消息总数'];
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
        $controller = new MessageSendWeekDataCrudController();

        // 测试字段配置
        $fields = $controller->configureFields('index');
        $this->assertIsIterable($fields);

        // 测试实体类配置
        $this->assertSame(MessageSendWeekData::class, $controller::getEntityFqcn());
    }

    public function testConfigureFields(): void
    {
        $controller = new MessageSendWeekDataCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureCrud(): void
    {
        $controller = new MessageSendWeekDataCrudController();
        $crud = $controller->configureCrud(Crud::new());

        $this->assertNotNull($crud);
    }
}
