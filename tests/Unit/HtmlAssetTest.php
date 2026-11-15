<?php
declare(strict_types=1);

namespace App\Test\Unit;

use App\Support\Html;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HtmlAssetTest extends TestCase
{
    public function testAssetUrlsTrackTheExactCurrentFileContents(): void
    {
        foreach (['assets/css/app.css', 'assets/js/app.js', 'assets/js/lab-core.js'] as $asset) {
            $root = defined('WWW_ROOT') ? WWW_ROOT : dirname(__DIR__, 2) . '/webroot/';
            $hash = hash_file('sha256', $root . $asset);
            self::assertIsString($hash);
            self::assertSame('/' . $asset . '?v=' . substr($hash, 0, 12), Html::versionedAsset($asset));
        }
    }

    public function testAssetPathsCannotEscapeThePublicAllowlist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Html::versionedAsset('../../.env');
    }
}
