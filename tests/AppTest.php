<?php

declare(strict_types=1);

namespace Lsv\TimeHarvestCliTest;

use Lsv\TimeHarvestCli\App;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class AppTest extends TestCase
{
    /**
     * @test
     */
    public function canGetApp(): void
    {
        $app = App::get();
        $commands = $app->all();

        $finder = new Finder();
        $files = $finder->files()->in(__DIR__.'/../src/Console')->name('*Command.php')->notName('AbstractCommand.php');

        self::assertCount(count($commands) - 9, $files);
    }

    /**
     * @test
     */
    public function canGetVersion(): void
    {
        $app = App::get();
        self::assertSame('dev-master', $app->getVersion());
    }
}
