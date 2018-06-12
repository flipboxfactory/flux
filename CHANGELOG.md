Changelog
=========
## 1.0.0-rc.4 - 2018-05-11
### Changed
- Updated dependencies

## 1.0.0-rc.3 - 2018-04-16
### Added
- `TransformHelper::eventName` to assist in assembling multi-part event names.
- `RegisterTransformerEvent` for event based transformer management. 
 
### Changed
- A class is no longer required on the Transformer record.
 
## 1.0.0-rc.2 - 2018-03-27
### Added
- `TransformFilter::$matchCallback` callable to handle custom logic in determining of a transformer should be applied.
- `TransformFilter::$transformEmpty` to easily set whether empty response data should be sent to the transformer.  
 Defaults to `false`.

## 1.0.0-rc.1 - 2018-03-27
### Changed
- Icons

## 1.0.0-rc - 2018-03-20
Initial release.
