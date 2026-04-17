<?php

namespace Tests;

use Illuminate\Support\Facades\Cache;
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

        // Seed the cache so the access token flow (JWT + installations) is skipped entirely.
        Cache::put("nova-publish-github-access-token", "fake-token");

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
        Cache::put("nova-publish-github-access-token", "fake-token");

        Http::fake([
            "api.github.com/repos/norday-agency/nova-publish/actions/workflows/test-workflow.yml/runs" => Http::response(
                [
                    "workflow_runs" => [
                        [
                            "conclusion" => "success",
                            "status" => "completed",
                            "created_at" => "2025-01-01T00:00:00Z",
                            "updated_at" => "2025-01-01T00:01:00Z",
                        ],
                    ],
                ]
            ),
        ]);

        /** @var PublishManager $manager */
        $manager = app(PublishManager::class);

        $lastRun = $manager->getLastRun();

        $this->assertInstanceOf(Run::class, $lastRun);
    }

    public function testStartPublish(): void
    {
        Cache::put("nova-publish-github-access-token", "fake-token");

        Http::fake([
            "api.github.com/repos/norday-agency/nova-publish/actions/workflows/test-workflow.yml/runs" => Http::response(
                [
                    "workflow_runs" => [
                        [
                            "conclusion" => "success",
                            "status" => "completed",
                            "created_at" => "2025-01-01T00:00:00Z",
                            "updated_at" => "2025-01-01T00:01:00Z",
                        ],
                    ],
                ]
            ),
            "api.github.com/repos/norday-agency/nova-publish/actions/workflows/test-workflow.yml/dispatches" => Http::response(
                [],
                204
            ),
        ]);

        /** @var PublishManager $manager */
        $manager = app(PublishManager::class);

        $manager->publish("main");

        Http::assertSent(function ($request) {
            return str_contains($request->url(), "dispatches") &&
                $request["ref"] === "main";
        });
    }
}
