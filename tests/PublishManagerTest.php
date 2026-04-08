<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use Publish\PublishManager;
use Publish\Run;

class PublishManagerTest extends TestCase
{
    public function testLastRunThrowsException(): void
    {
        $this->expectExceptionMessage(
            "Workflow test-workflow.yml hasn't run yet. Run it once manually via GitHub to kickstart nova-publish."
        );

        // Fake the test-workflow.yml workflow without any runs.
        Http::fake([
            "api.github.com/repos/norday-agency/nova-publish/actions/workflows/test-workflow.yml/runs" => Http::response(
                ["workflow_runs" => []]
            ),
        ]);

        /** @var PublishManager $manager */
        $manager = app(PublishManager::class);
        $manager->getLastRun();
    }

    public function testGetLastRun(): void
    {
        /** @var PublishManager $manager */
        $manager = app(PublishManager::class);

        $lastRun = $manager->getLastRun();

        $this->assertInstanceOf(Run::class, $lastRun);
    }

    public function testStartPublish(): void
    {
        /** @var PublishManager $manager */
        $manager = app(PublishManager::class);

        $manager->publish("main");

        // Wait for the workflow run to become available in the API.
        sleep(1);

        $newRun = $manager->getLastRun();

        // Just check for the right result, because tests are running in parallel it's hard to predict the exact outcome.
        $this->assertInstanceOf(Run::class, $newRun);
    }
}
