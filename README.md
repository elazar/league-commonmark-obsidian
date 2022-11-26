# League Commonmark Obsidian Extension

[![PHP Version Support](https://img.shields.io/static/v1?label=php&message=%3E=%208.0.2&color=blueviolet)](https://packagist.org/packages/elazar/flystream)
[![Packagist Version](https://img.shields.io/static/v1?label=packagist&message=1.0.0&color=blueviolet)](https://packagist.org/packages/elazar/flystream)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Buy Me a Cofee](https://img.shields.io/badge/buy%20me%20a%20coffee-donate-brightgreen.svg)](https://ko-fi.com/elazar)

An extension for [`league/commonmark`](https://commonmark.thephpleague.com/) to render elements specific to [Obsidian](https://obsidian.md).

Released under the [MIT License](LICENSE).

## Requirements

- PHP 8.0.2+

## Installation

Use [Composer](https://getcomposer.org/).

If you want to install this library [globally](https://getcomposer.org/doc/03-cli.md#global) to use the conversion script it provides:

```sh
composer global require elazar/league-commonmark-obsidian
```

If you want to install this library locally for use in your own codebase:

```sh
composer require elazar/league-commonmark-obsidian
```

## Usage

There are three ways to use this library.

### Conversion Script

The conversion script requires only a single command to run, but offers a very minimal conversion with no options for customization. It is mainly intended to provide a minimal example of using the converter (see the next section), but can be invoked like so:

```sh
composer global exec obsidian-to-html /path/to/vault /path/to/vault/Attachments /path/to/build
```

- `/path/to/vault` is the path to the root directory of the Obsidian vault to convert to HTML
- `/path/to/vault/Attachments` is the path to the subdirectory of the Obsidian vault that contains attachments
- `/path/to/build` is the path to the directory to receive the converted HTML

### Extension

If you want to use the extension in your own code, you can do so as follows.

```php
$extension = new Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianExtension(
    vaultPath: '/path/to/Vault',
    attachmentsPath: '/path/to/Vault/Attachments',
);

$environment = new League\CommonMark\Environment\Environment;
$environment->addExtension(new League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension);
$environment->addExtension($extension);

$converter = new League\CommonMark\MarkdownConverter($environment);

// Set the absolute path of the file being converted so that
// links can be resolved relative to that file
$extension->setFromPath('/path/to/Vault/Folder/File.md');

echo $converter->convert('[[Internal Link]]');
// Assuming that "Internal Link.md" is contained in the root
// directory of the vault, the above line outputs:
// <a href="../Internal Link.html">Internal Link</a>

echo $converter->convert('![[Attachment.pdf]]');
// Assuming that "Attachment.pdf" is contained in the "Attachments"
// subdirectory within the vault directory, the above line
// outputs: <img src="../Attachments/Attachment.pdf" />
```

### Converter

The converter is a single class that can be used in your own conversion script or other code. It requires writing a bit of PHP code to use it, but offers more options for customization than the stock conversion script, such as including more [extensions](https://commonmark.thephpleague.com/2.3/customization/extensions/) in the [environment](https://commonmark.thephpleague.com/2.3/customization/environment/) configuration or formatting the converted HTML.

Below is an example that adds the [Strikethrough extension](https://commonmark.thephpleague.com/2.3/extensions/strikethrough/) and a custom formatter that wraps the converted HTML in additional markup.

```php
$vaultPath = '/path/to/vault';
$attachmentsPath = $vaultPath. '/Attachments';
$buildPath = __DIR__ . '/build';

$formatter = new class implements Elazar\LeagueCommonMarkObsidian\Formatter\FormatterInterface {
    public function format(string $html, string $markdownFilePath): string {
        $title = str_replace('.md', '', basename($markdownFilePath));
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>$title</title>
</head>
<body>
<main>
$html
</main>
</body>
</html>
HTML;
    }
};

$extension = new Elazar\LeagueCommonMarkObsidian\LeagueCommonMarkObsidianExtension(
    $vaultPath,
    $attachmentsPath,
);

$environment = new League\CommonMark\Environment\Environment([]);
$environment->addExtension(new League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension);
$environment->addExtension(new League\CommonMark\Extension\Strikethrough\StrikethroughExtension);
$environment->addExtension($extension);

$converter = new Elazar\LeagueCommonMarkObsidian\Converter;
$converter->convert($vaultPath, $attachmentsPath, $buildPath, $environment, $formatter);
```

## Internals

In some instances, it can be helpful to be familiar with the internals of this library.

Here are some example use cases:

1. If you want to contribute to this library, it's helpful to know what parts of the codebase you may need to modify to implement a given feature or fix a given bug.
2. If you find a bug in this library and want to work around it, it may be possible to do so by overriding specific components of the library.
3. If Obsidian adds support for a new attachment file type and you want to add support for it to this library before it's added to the library's core, you may be able to do so by adding to the embed renderers it uses.

### Extension

The normal use case for [`LeagueCommonMarkObsidianExtension`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/LeagueCommonMarkObsidianExtension.php) is for it to receive a vault path and attachments path. In lieu of these, it can receive other dependencies detailed in subsequent sections of this document.

The main function of the extension is to add two inline parsers to the environment: [`InternalLinkParser`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Parser/InternalLinkParser.php) for [internal links](https://help.obsidian.md/How+to/Internal+link) and [`EmbedParser`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Parser/EmbedParser.php) for [embeds](https://help.obsidian.md/How+to/Format+your+notes).

The internal link parser renders a natively supported link element. The embed parser uses its own renderers to render custom inline HTML elements, as at least some of these are not natively supported by CommonMark.

### Embed Renderer

The [`EmbedRendererInterface`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Renderer/EmbedRendererInterface.php) `$embedRenderer` parameter of [`LeagueCommonMarkObsidianExtension`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/LeagueCommonMarkObsidianExtension.php) is used to render [embeds](https://help.obsidian.md/How+to/Format+your+notes).

The default used for `$embedRenderer` is an instance of [`EmbedRenderer`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Renderer/EmbedRenderer.php), which extends [`CompositeEmbedRenderer`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Renderer/CompositeEmbedRenderer.php) and composes all other renderers included in this library. It attempts to use each of these renderers in turn to render a given embed.

### Attachment Link Resolver

The [`LinkResolverInterface`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Resolver/LinkResolverInterface.php) `$attachmentLinkResolver` parameter of [`LeagueCommonMarkObsidianExtension`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/LeagueCommonMarkObsidianExtension.php) is used to resolve links for [embedded](https://help.obsidian.md/How+to/Embed+files#Embed+attachments) [attachments](https://help.obsidian.md/How+to/Manage+attachments).

The default used for `$attachmentLinkResolver` is an instance of [`AttachmentLinkResolver`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Resolver/AttachmentLinkResolver.php), which uses the vault and attachment paths to resolve attachment links and embeds relative to the Markdown file being rendered as HTML.

### Internal Link Resolver

The [`LinkResolverInterface`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Resolver/LinkResolverInterface.php) `$internalLinkResolver` parameter of [`LeagueCommonMarkObsidianExtension`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/LeagueCommonMarkObsidianExtension.php) is used to resolve [internal links](https://help.obsidian.md/How+to/Internal+link).

The default used for `$internalLinkResolver` is an instance of [`InternalLinkResolver`](https://github.com/elazar/league-commonmark-obsidian/blob/master/src/Resolver/InternalLinkResolver.php), which uses the vault path to resolve internal links relative to the Markdown file being rendered as HTML.

## Contributing

Regardless of how you want to contribute, please start by [filing an issue](https://github.com/elazar/league-commonmark-obsidian).

### Issues

Please prefix issue titles with one of the following:

- **Bug Report** (if you find behavior you believe to be a bug)
- **Feature Request** (if you would like to suggest an addition or change to the library)
- **Help Request** (for everything you aren't sure about)

Doing this allows for discussion with maintainers to troubleshoot problems, confirm bugs, or determine how best to make suggested features work.

In cases where you wish to contribute code, this discussion may help to clarify your implementation approach and reduce potential rework required on your part to get your contribution merged.

Help requests are closed once they are resolved or if they are inactive for 30 days. Other issues are left open until either a related PR is merged or a formal decision is made by the maintainers that no further action will be taken.

### Pull Requests

Once consensus is reached on a bug or feature implementation approach, file a PR using a branch with a name prefixed with either `bug/` or `feature/` for a bug report or feature request respectively.

Please be sure to reference the original issue you filed in your PR description to provide context for the changes.
