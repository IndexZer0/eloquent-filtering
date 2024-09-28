# Changelog

All notable changes to `eloquent-filtering` will be documented in this file.

## 2.1.0 - 2024-09-28
### Added
- Support defining pivot filters on Custom Intermediate Table Models.

## 2.0.0 - 2024-09-11
### Added
- [Docs](https://docs.eloquentfiltering.com)
- `FilterType` Enum
- Required Filters
- Pivot Filters
- Morph Filters
- Validation Rules
- Filter Modifiers
- Exceptions
  - InvalidFiltersPayloadException
  - InvalidModelFqcnException
  - UnsupportedModifierException
  - RequiredFilterException
### Changed
- Qualifying Columns for all core field filters
- Custom Filters Structure
### Removed
- Some dedicated filter classes were removed in favour of new modifiers feature.
  - `$like:start`
  - `$like:end`
  - `$notLike:start`
  - `$notLike:end`
- `Filter::all()`
- `Target::relationAlias()`
- `Types::except()`
- Config
  - `default_allowed_filter_list`

## 1.0.0 - 2024-06-15
- Initial Release
