<?php

namespace Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use PHPUnit\Framework\TestCase;

class LicenseProductsViewTest extends TestCase
{
    public function test_license_product_loop_does_not_replace_the_home_featured_array(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2).'/resources/views/pages/index.blade.php');
        $this->assertStringNotContainsString('$featured = $licenseItem', $source);
        $this->assertStringNotContainsString('$featuredHome', $source);

        $compiled = (new BladeCompiler(new Filesystem(), sys_get_temp_dir()))->compileString($source);

        $this->assertStringNotContainsString('$featured = $licenseItem', $compiled);
        $this->assertStringContainsString(
            "make('partials.product-card', ['featured' => \$licenseItem]",
            $compiled
        );
    }
}
