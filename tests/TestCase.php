<?php

namespace Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Publish\ToolServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $loadEnvironmentVariables = true;

    protected function defineEnvironment($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . "/..");
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        tap($app["config"], function (Repository $config) {
            $config->set(
                "publish.application_id",
                env("NOVA_PUBLISH_APPLICATION_ID", 1137646)
            );
            $config->set(
                "publish.private_key",
                env("NOVA_PUBLISH_PRIVATE_KEY", "dummy-key")
            );
            $config->set(
                "publish.owner",
                env("NOVA_PUBLISH_OWNER", "norday-agency")
            );
            $config->set(
                "publish.repository",
                env("NOVA_PUBLISH_REPOSITORY", "nova-publish")
            );
            $config->set("publish.workflow", "test-workflow.yml");
        });
    }

    protected function getPackageProviders($app)
    {
        return [ToolServiceProvider::class];
    }
}
