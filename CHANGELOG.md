# Changelog

## [v0.3.2](https://github.com/runapi-ai/suno-php/releases/tag/v0.3.2) - 2026-09-16

### Added
- Add persona, voice, style-expansion, timestamped-lyrics, audio-export, music-visualization, and music-from-sample resources with typed resource handles and resource provenance billing.

### Changed
- Add voice_id to the text-to-music request shape for canonical Voice consumption.

### Deprecated
- Deprecate generate-persona, generate-voice, check-voice, boost-style, get-timestamped-lyrics, convert-audio, visualize-music, and add-samples.
  Replacement: Migrate to personas, voices, style-expansions, timestamped-lyrics, audio-exports, music-visualizations, and music-from-sample; the replacement resources preserve resumable RunAPI handles.


## [v0.3.1](https://github.com/runapi-ai/suno-php/releases/tag/v0.3.1) - 2026-08-14

### Fixed
- Require replace-section windows to be at least 10 seconds without applying the removed 60-second maximum.


## [v0.3.0](https://github.com/runapi-ai/suno-php/releases/tag/v0.3.0) - 2026-08-10

### Added
- Add music inspiration from one to four caller-supplied audio URLs.


## [v0.2.1](https://github.com/runapi-ai/suno-php/releases/tag/v0.2.1) - 2026-08-06

### Added
- Add Composer resources for stitching audio, remastering audio, and adding samples from a selected time range.


## [v0.2.0](https://github.com/runapi-ai/suno-php/releases/tag/v0.2.0) - 2026-07-21

### Added
- Add lyrics generation queries and lyric blending with typed request and response models.


## [v0.1.2](https://github.com/runapi-ai/suno-php/releases/tag/v0.1.2) - 2026-07-20

### Added
- Add advanced stem separation parameters, validation, and typed completed response models.


## [v0.1.1](https://github.com/runapi-ai/suno-php/releases/tag/v0.1.1) - 2026-07-08

### Changed
- Refresh Suno replace-section input validation for current RunAPI inputs.

## [v0.1.0](https://github.com/runapi-ai/suno-php/releases/tag/v0.1.0) - 2026-06-25

### Added
- Publish the first RunAPI PHP Composer package release for `runapi-ai/suno`.
- Include typed PHP client resources, package README, Apache-2.0 license, and Composer CI.
