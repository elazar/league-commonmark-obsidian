# 2.1.0 - 2023-03-06

- Fixed a bug where internal links to anchors on the same page were not parsed correctly ([#3](https://github.com/elazar/league-commonmark-obsidian/pull/3))

# 2.0.0 - 2023-01-15

This release fixes a bug where passing a custom value for the `$environment`
parameter to `Converter->convert()` could cause multiple instances of
`LeagueCommonMarkObsidianExtension` to be added to `$environment`, which could
result in internal links and attachments not being resolved correctly.

- **Backward Compatibility Break**: Remove `Converter->configureEnvironment()`
  and move its logic into `Converter->getExtension()`
- **Backward Compatibility Break**: Add `$extension` parameter to
  `Converter->getExtension()`

# 1.0.0 - 2022-11-26

- Initial release
