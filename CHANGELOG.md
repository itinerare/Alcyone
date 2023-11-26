<!--- BEGIN HEADER -->
# Changelog

All notable changes to this project will be documented in this file.
<!--- END HEADER -->

## [1.4.0](https://github.com/itinerare/Alcyone/compare/v1.3.0...v1.4.0) (2023-11-26)

### Features


##### Images

* Use imagick for share image conversion ([b93363](https://github.com/itinerare/Alcyone/commit/b9336373875adbf912dd2a49f1c08d31690ec949))


---

## [1.3.0](https://github.com/itinerare/Alcyone/compare/v1.2.0...v1.3.0) (2023-11-19)

### Features


##### Images

* Use ImageMagick for larger images ([41d131](https://github.com/itinerare/Alcyone/commit/41d131a47842c7ca265d2211a0208e201cc3d938))

### Bug Fixes


##### Reports

* Use admin URL in report notif mail ([27ca6c](https://github.com/itinerare/Alcyone/commit/27ca6cd9dd7367d3e3bcaf1ce90713884e9bb22e))


---

## [1.2.0](https://github.com/itinerare/Alcyone/compare/v1.1.1...v1.2.0) (2023-11-12)

### Features


##### Reports

* Add opt-in email notifications for new reports, tests ([81d28e](https://github.com/itinerare/Alcyone/commit/81d28eac8fece0b2e582ca81287a8ab039342580))

##### Users

* Add admin notification preference, tests ([3ce088](https://github.com/itinerare/Alcyone/commit/3ce088750f4754479c3b13c81e02a08715353cc8))

### Bug Fixes


##### Admin

* Fix rank page verbiage ([48f972](https://github.com/itinerare/Alcyone/commit/48f97269cbb6ac0a8e91e7338b111d4a7b8972ad))

##### Tests

* Tweak report notif test conditions ([f4a793](https://github.com/itinerare/Alcyone/commit/f4a793aa73d7e22fdfb8974896d9f85798dbd8ca))


---

## [1.1.1](https://github.com/itinerare/Alcyone/compare/v1.1.0...v1.1.1) (2023-11-05)

### Bug Fixes

* Fix errant form field styling ([2f8796](https://github.com/itinerare/Alcyone/commit/2f8796b9115db2d86428c5d6e17f8c2941edc6ac))

##### Users

* Fix 2FA challenge display, view JS ([74c7bd](https://github.com/itinerare/Alcyone/commit/74c7bdad098797fdaa808aeec7dfec607766c323))
* Make 2FA QR code more readable ([888608](https://github.com/itinerare/Alcyone/commit/888608a35266a35762311164beffbedb5da48071))


---

## [1.1.0](https://github.com/itinerare/Alcyone/compare/v1.0.1...v1.1.0) (2023-11-03)

### Features

* Add tinyMCE to site page editing ([c40e3f](https://github.com/itinerare/Alcyone/commit/c40e3f514d3293965fa33c1bcc13623744031495))


---

## [1.0.1](https://github.com/itinerare/Alcyone/compare/v1.0.0...v1.0.1) (2023-11-03)

### Bug Fixes


##### Images

* Fix pagination ([4ce8a7](https://github.com/itinerare/Alcyone/commit/4ce8a72e4341d32b171741ceeeb6c244dc533837))


---

## [1.0.0](https://github.com/itinerare/Alcyone/compare/25a3f8a22ef8d6d09bf8db6f9cf52ef3fc09d5c8...v1.0.0) (2023-11-03)

### Features

* Add theme support ([6564a9](https://github.com/itinerare/Alcyone/commit/6564a926a798389aa6b9b1ada3ecea7b3c4bbb5e))
* Add url display widget, add to image info ([b3115a](https://github.com/itinerare/Alcyone/commit/b3115a5aeaa26a69875a98fac9d5e9869c09eeb0))
* Enable model safeties ([7e84f0](https://github.com/itinerare/Alcyone/commit/7e84f0787ecc03ce1a56847577895a447f702d91))
* Set up dropbox storage ([f0acbc](https://github.com/itinerare/Alcyone/commit/f0acbc15882406a797b3ed35bea1068f67ba691c))

##### Admin

* Add invitation, rank, user admin panels ([93d3c1](https://github.com/itinerare/Alcyone/commit/93d3c1e256c0296ded6efa23835da67a9c06b035))

##### Commands

* Add setup command, site pages command ([7e9fed](https://github.com/itinerare/Alcyone/commit/7e9fed87458cea9c9aa390210c2a8012561fe49c))

##### Images

* Add uploads, view, deletion ([2ca522](https://github.com/itinerare/Alcyone/commit/2ca522082db2a7ea02e89a6a448c61fa4a78743c))
* Soft delete images, add notification ([8f79f4](https://github.com/itinerare/Alcyone/commit/8f79f46ea84ab6d45f2e172862e998e1704e302a))
* Visual polish for image url display ([a6f533](https://github.com/itinerare/Alcyone/commit/a6f5337945a55f4333e175ff4999fe74e028235f))

##### Pages

* Add site pages, admin panel ([48b103](https://github.com/itinerare/Alcyone/commit/48b103983884786562230756180baad5fd5374f4))

##### Reports

* Add admin reports queue, model, table, admin index ([1c6b17](https://github.com/itinerare/Alcyone/commit/1c6b17ca999f2c697e3d3fa59b07ceaee95e10b5))
* Add report creation, processing ([301da6](https://github.com/itinerare/Alcyone/commit/301da6ccfe2882fdf09d6febdfb7bcf28b92f210))

##### Tests

* Add admin invitation code tests ([b6d695](https://github.com/itinerare/Alcyone/commit/b6d695f48d274cb378fb5c9c85f82e668d0672fe))
* Add admin rank tests ([d4fbfd](https://github.com/itinerare/Alcyone/commit/d4fbfd41178b00c367590ffa936fca613386cfd3))
* Add admin report tests ([4321e7](https://github.com/itinerare/Alcyone/commit/4321e7a0da88c950dc783f51074491856ea10518))
* Add admin user edit tests ([12921f](https://github.com/itinerare/Alcyone/commit/12921fd21cf7767bdc379c4b2e19c4c34a70851f))
* Add auth tests ([b9a860](https://github.com/itinerare/Alcyone/commit/b9a860359a26c7731fe8acf22b013c8bf371907d))
* Add email contents tests ([786cf7](https://github.com/itinerare/Alcyone/commit/786cf7905abec221f9cb4e18c2778ae03d9b578a))
* Add image upload tests ([a7c67f](https://github.com/itinerare/Alcyone/commit/a7c67fe05a78c64e0042c4dedaed45df2a4f6112))
* Add report checks to image deletion tests ([e81ef8](https://github.com/itinerare/Alcyone/commit/e81ef844a1125d2b9bd63e51e644646a670e1a5c))
* Add report view/create tests ([528aa0](https://github.com/itinerare/Alcyone/commit/528aa04c45baa67051ab11fba00664d5464a3036))
* Add site page tests ([f4f598](https://github.com/itinerare/Alcyone/commit/f4f598f0c282f070b25609f07e2302118c63b112))
* Add user function tests ([e5058d](https://github.com/itinerare/Alcyone/commit/e5058de1fdb8c58433e76132de7c20213d3c0086))
* Add user notification tests, factory ([64abd1](https://github.com/itinerare/Alcyone/commit/64abd1ce9178824d079874750136232ab805f74e))
* Basic test setup, access tests ([57967f](https://github.com/itinerare/Alcyone/commit/57967f770f950ca26675e11a52815b92e8fc09c0))
* Check for image URLs in image modal test ([060172](https://github.com/itinerare/Alcyone/commit/060172f2d28897c789c67a42efeb6ee21099774c))
* More robust report view tests ([52a04c](https://github.com/itinerare/Alcyone/commit/52a04ccfeb4a46800ac3cda8d8095731cf63087a))

##### Users

* Set up basic auth, account settings, notifications ([b2be7b](https://github.com/itinerare/Alcyone/commit/b2be7b616b862fb7812624b3e3bf80eb9211140a))

### Bug Fixes

* Add header padding on mobile layout ([68a9b3](https://github.com/itinerare/Alcyone/commit/68a9b3e46288e272a2bbeee062d2426cd2f50eae))
* Flash validation errors ([d69f8f](https://github.com/itinerare/Alcyone/commit/d69f8fd26abd4f0d174f1c423f09b094d7511ee8))
* Middleware issues ([511efb](https://github.com/itinerare/Alcyone/commit/511efb0b1749f7a55110bd3b987d3dd6044793a0))
* Mobile navbar toggle ([dc1fc4](https://github.com/itinerare/Alcyone/commit/dc1fc4e83c949b85bb21a8e41ba1fe42041648db))

##### Admin

* Show correct page title ([6bed37](https://github.com/itinerare/Alcyone/commit/6bed373ad43181cd4268177a8951cdbe5badd8d9))

##### Images

* Fix imageupload user relation class reference ([c7aca8](https://github.com/itinerare/Alcyone/commit/c7aca848b283551663c2d8bdfa2677d5871a9e2f))
* Fix report action check ([f42d3a](https://github.com/itinerare/Alcyone/commit/f42d3acd4053d4890905c3caa04a639468d41260))
* Prevent image view/deletion by users other than uploader ([a1f634](https://github.com/itinerare/Alcyone/commit/a1f634d2e039e84e8055755e4e05271a1a2c9b1d))

##### Invitations

* Adjust pagination ([4439e5](https://github.com/itinerare/Alcyone/commit/4439e59f8ab6c3ce5a4da82794898a22c11ff2ed))

##### Reports

* Cancel reports for an image if it is deleted by the uploader ([321316](https://github.com/itinerare/Alcyone/commit/321316d6c73891c12efbe7399d05e6a9c9f1c231))
* Commit admin routes ([d070df](https://github.com/itinerare/Alcyone/commit/d070dfee3e845ae02d8d592a2cdd8b4cde4eaee7))
* Don't show deleted images ([b2926d](https://github.com/itinerare/Alcyone/commit/b2926d713be9f2ac1af5cfa46bda0b132caf086d))
* Fix image deletion check ([b90747](https://github.com/itinerare/Alcyone/commit/b90747d0b1b5ac01d9791c35c50d32cda76eada0))
* Fix queue sort dropdown styling ([63702a](https://github.com/itinerare/Alcyone/commit/63702aa8a8c48f26a281235e104631366e09a642))
* Only allow pending reports to be processed ([3bbd74](https://github.com/itinerare/Alcyone/commit/3bbd745649c7c9e98ce0df7241e8d8f3e28536ac))

##### Tests

* Fix image soft deletion check ([4e29b3](https://github.com/itinerare/Alcyone/commit/4e29b3308153314e4784ef445afb0b23a8279424))
* Only create files for report view if image is not deleted ([00a478](https://github.com/itinerare/Alcyone/commit/00a478bd2508e4567ae20e966a10a2e0c42ed67c))

##### Users

* Add ban reason, time to table ([fb2a7e](https://github.com/itinerare/Alcyone/commit/fb2a7ee32ac9a1755e3b36b2d007a6269ddf01f2))
* Add is_banned to users table ([8e180b](https://github.com/itinerare/Alcyone/commit/8e180b3d0b1497c0c81050744b08b19410b976f8))
* Define default values in model ([150cf3](https://github.com/itinerare/Alcyone/commit/150cf397b08ca13dd4c00cedf33dc6167103ffc7))
* Fix admin user search, dropdown styling ([6dd2e7](https://github.com/itinerare/Alcyone/commit/6dd2e7b1283b2045a3bfbc712085ba3d75666c17))
* Fix notification grouping ([b2b486](https://github.com/itinerare/Alcyone/commit/b2b486d14d31b9e19a5dbcc1dd4f4353ba98c684))
* Fix theme choice validation ([1abc37](https://github.com/itinerare/Alcyone/commit/1abc3737c485d2f13cda48a2a76edcd937b91eda))
* Link to adminUrl in displayName ([a6082a](https://github.com/itinerare/Alcyone/commit/a6082aed2f5fd151c46eee8d1201b074426d5c1b))
* Load notifs page scripts as module ([829521](https://github.com/itinerare/Alcyone/commit/829521c96af098ccfa565979ec2f53d77bf98f7c))


---

