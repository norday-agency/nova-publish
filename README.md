# Nova publish

<!-- Header & Preview Image -->
<h1 align="center">
  <img src=".github/readme-hero.png">
</h1>

<!-- Shields -->

[![Run tests](https://github.com/norday-agency/nova-publish/actions/workflows/run-tests.yaml/badge.svg)](https://github.com/norday-agency/nova-publish/actions/workflows/run-tests.yaml) [![Code style](https://github.com/norday-agency/nova-publish/actions/workflows/code-style.yaml/badge.svg)](https://github.com/norday-agency/nova-publish/actions/workflows/code-style.yaml)

<!-- Description -->

> Adds a publish button to Nova to trigger a GitHub workflow which runs you static site generator

### Developed with ❤️ by [GRRR](https://grrr.nl)

- GRRR is a [B Corp](https://grrr.nl/en/b-corp/)
- GRRR has a [tech blog](https://grrr.tech/)
- GRRR is [hiring](https://grrr.nl/en/jobs/)
- [@GRRRTech](https://twitter.com/grrrtech) tweets

## Requirements

[Return To Top](#nova-publish)

- PHP 8.2, 8.3, 8.4
- Nova 5

## Installation

[Return To Top](#nova-publish)

Add the repository to `composer.json`

```JSON
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/norday-agency/nova-publish"
    }
]
```

Add install the package

```shell script
composer require grrr-amsterdam/nova-publish
```

Load the tool by adding it to `NovaServiceProvider.php`

```php
use Publish\Publish;

public function tools()
{
    return [new Publish()];
}
```

Publish configuration

```shell
php artisan vendor:publish --provider="Publish\ToolServiceProvider"
```

Configure [GitHub credentials](#github-credentials), set the name of the workflow file and configure an application version.

=======

## Local development

Run `yarn run dev` to watch for changes in the `resources/js` directory.

Use the local checkout in a project that uses this plugin. [The Composer documentation explains how to do this.](https://getcomposer.org/doc/05-repositories.md#path)

To run the tests you need a Nova License and a GitHub App with access to your repository. It will use the workflow `test-workflow.yml` to do integration tests.

Create `/.env` file with the following content:

```dotenv
NOVA_PUBLISH_PRIVATE_KEY="your GitHub App private key"
NOVA_PUBLISH_APPLICATION_ID="your GitHub App ID"
NOVA_PUBLISH_OWNER="your GitHub owner"
NOVA_PUBLISH_REPOSITORY="your GitHub repository"
NOVA_PUBLISH_WORKFLOW="workflow-file.yml"
```

## i18n

To add a language or change an existing translation, please read the [Laravel documentation about overriding package language files](https://laravel.com/docs/10.x/localization#overriding-package-language-files).

## GitHub credentials

You need a GitHub App to use this tool. The application must have access to the repository where the workflow is located.

[About creating GitHub Apps](https://docs.github.com/en/apps/creating-github-apps/about-creating-github-apps/about-creating-github-apps).

Use the application ID and private key in `config/publish.php`.

## Contribute

You need a Nova license to run the tests.

## Release new version

- Run `yarn run prod` to build the assets, and commit the changes
- Add the new version to `CHANGELOG.md`
