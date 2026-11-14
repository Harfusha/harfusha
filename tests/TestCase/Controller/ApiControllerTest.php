<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/** Requires real CakePHP middleware and routing; never substituted by offline tests. */
final class ApiControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function testReadOnlyCatalogueHasTypedContractAndRedaction(): void
    {
        $this->get('/api/v1/projects?lang=en');
        $this->assertResponseOk();
        $this->assertResponseContains('"language":"en"');
        $this->assertResponseContains('"slug":"local-ai-hub"');
        $this->assertResponseContains('"repository":null');
    }

    public function testSingleCaseStudyUnknownSlugAndLanguage(): void
    {
        $this->get('/api/v1/projects/local-ai-hub?lang=cs');
        $this->assertResponseOk();
        $this->assertResponseContains('"slug":"local-ai-hub"');
        $this->get('/api/v1/projects/this-project-does-not-exist');
        $this->assertResponseCode(404);
        $this->get('/api/v1/projects?lang=fr');
        $this->assertResponseCode(400);
    }

    public function testWriteMethodsAreNotExposed(): void
    {
        $this->post('/api/v1/projects');
        // A non-routed write endpoint should be absent (404), not an internal server failure.
        $this->assertResponseCode(404);
    }
}
