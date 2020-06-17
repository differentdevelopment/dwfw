
# Change Log
All notable changes to this project will be documented in this file.
 
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.9.8] - 2020-06-17
### Changed
- file retrieve changed to stream

## [0.9.7] - 2020-06-16
### Fixed
- typo fixes

## [0.9.6] - 2020-06-15
### Added
- DatabaseSeeder publish

### Fixed
- double routing on user profile image

## [0.9.5] - 2020-06-12
### Changed
- moved user model functions to trait

## [0.9.3] - 2020-06-10
### Added
- image orientate method

### Changed
- LoggableAdmin accepts array|object|string as data
- LogsCrudController updated for prettier show page
- updated observers getData method

### Fixed
- determining partner_id on file storing

## [0.9.1] - 2020-06-05
### Fixed
- Observer data, implementing fixes

## [0.9.0] - 2020-06-05
### Added
- Introduced automatized logging via observers

## [0.8.1] - 2020-06-03
### Changed
- TestCase, CreatesApplications

## [0.8.0] - 2020-06-02
### Added
- introduced tests

## [0.7.0] - 2020-05-29
### Added
- installer

## [0.6.0] - 2020-05-29
### Added
- profile image handling

### Changed
- Moved methods from BaseCrudController to traits

## [0.5.0] - 2020-05-29
### Added
- UsersCrud
- PartnersCrud
- translations

## [0.4.0] - 2020-05-29
### Added
- CheckIfAdmin moved to package
- auth/permission config updates

## [0.3.1] - 2020-05-25
### Fixed
- image resizing problem

## [0.3.0] - 2020-05-25
File handling, Timezones functionality
### Added
- routing for files
- Files controller
- TimeZones controller

## [0.2.1] - 2020-05-22
### Fixed
- model loading errors

## [0.2.0] - 2020-05-22
Separated models/traits, added middleware for timezone handling
### Fixed
- migration sequence
- seeder errors

## [0.1.2] - 2020-05-21
### Fixed
- namespaces

## [0.1.1] - 2020-05-21
### Fixed
- autoload Differen\Dwfw namespace

## [0.1.0] - 2020-05-21
Initial release
### Added
- DwfwServiceProvider
- Base Models, Migrations, Seeder
- [BackPack](https://backpackforlaravel.com/) configs
