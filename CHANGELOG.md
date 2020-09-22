
# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.10.17] - 2020-09-22
### added
- Status for logs

### changed
- Log entity_type + entity_id -> polymorph
- Upgrade automatically publishes version at the end of the process

### fixed
- User preview error if no view file declared for it.
- Backpack navbar fix removed

## [0.10.16] - 2020-09-11
### added
- Addded BackPack navbar fix for navbar colors

## [0.10.15] - 2020-09-07
### added
- Trait DwfwTestCase

### changed
- backpackforlaravel translations fine-tune
- Upgrade command progress bar customization

## [0.10.14] - 2020-09-02
### added
- Filters for Log crud
- Hungarian translations for backpackforlaravel package
- BaseModel
- BaseDwfwApiFormRequest
- Disable debugbar middleware
- Ip restriction middleware
- Upgrade command (php artisan dwfw:upgrade)

### changed
- Breaking change: New abstract method in BaseCrudController

## [0.10.13] - 2020-08-27
### fixed
- Dwfwuser Trait file route added missing parameter

## [0.10.12] - 2020-08-26
### fixed
- file path

## [0.10.11] - 2020-08-24
### fixed
- file storing with partner/storage_path
### added
- tests for file storing
- partnerfactory

## [0.10.10] - 2020-08-14
### added
- added Guard for file routes
### changed
- File handling methods - breaking change: file and file-b64 routes now have 1 more required parameter,
and disk name should be included like {disk}/file/{file)

## [0.10.9] - 2020-07-29
### added
- Included HU localization files for Laravel

## [0.10.8] - 2020-07-29
### changed
- if getUserModelByRoute function set, it determines the model for user in logs crud
- changed usercrud abort route to method for artisan optimize

## [0.10.7] - 2020-07-21
### Updated
- localizeArray -> added namespace option for lang keys

## [0.10.6] - 2020-07-21
### Updated
- Composer dependencies
- Install process

## [0.10.5] - 2020-07-17
### changed
- BaseCrudController@checkForColumnId searches for crud_title accessor 
### fixed
- logs:clear route

## [0.10.4] - 2020-07-16
### changed
- migrations dropColumn changed to one operation

## [0.10.3] - 2020-07-10
### added
- log clearer artisan command

## [0.10.2] - 2020-06-26
### changed
- changed uploaded file getclientmimetype to getmimetype

## [0.10.1] - 2020-06-25
### fixed
- Removed content-length header from file retrieve response

## [0.10.0] - 2020-06-18
### added
- Base64 image store

## [0.9.10] - 2020-06-18
### fixed
- User observer registering

## [0.9.9] - 2020-06-18
### changed
- composer updates

### fixed
- User model dd

## [0.9.8] - 2020-06-17
### changed
- file retrieve changed to stream

## [0.9.7] - 2020-06-16
### fixed
- typo fixes

## [0.9.6] - 2020-06-15
### added
- DatabaseSeeder publish

### fixed
- double routing on user profile image

## [0.9.5] - 2020-06-12
### changed
- moved user model functions to trait

## [0.9.3] - 2020-06-10
### added
- image orientate method

### changed
- LoggableAdmin accepts array|object|string as data
- LogsCrudController updated for prettier show page
- updated observers getData method

### fixed
- determining partner_id on file storing

## [0.9.1] - 2020-06-05
### fixed
- Observer data, implementing fixes

## [0.9.0] - 2020-06-05
### added
- Introduced automatized logging via observers

## [0.8.1] - 2020-06-03
### changed
- TestCase, CreatesApplications

## [0.8.0] - 2020-06-02
### added
- introduced tests

## [0.7.0] - 2020-05-29
### added
- installer

## [0.6.0] - 2020-05-29
### added
- profile image handling

### changed
- Moved methods from BaseCrudController to traits

## [0.5.0] - 2020-05-29
### added
- UsersCrud
- PartnersCrud
- translations

## [0.4.0] - 2020-05-29
### added
- CheckIfAdmin moved to package
- auth/permission config updates

## [0.3.1] - 2020-05-25
### fixed
- image resizing problem

## [0.3.0] - 2020-05-25
File handling, Timezones functionality
### added
- routing for files
- Files controller
- TimeZones controller

## [0.2.1] - 2020-05-22
### fixed
- model loading errors

## [0.2.0] - 2020-05-22
Separated models/traits, added middleware for timezone handling
### fixed
- migration sequence
- seeder errors

## [0.1.2] - 2020-05-21
### fixed
- namespaces

## [0.1.1] - 2020-05-21
### fixed
- autoload Differen\Dwfw namespace

## [0.1.0] - 2020-05-21
Initial release
### added
- DwfwServiceProvider
- Base Models, Migrations, Seeder
- [BackPack](https://backpackforlaravel.com/) configs
