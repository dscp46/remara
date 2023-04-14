# RemAra
Central Repeater Management for the Auvergne-Rhône-Alpes Ham Radio Repeater network.

## Changelog


## Roadmap

### P1
- [x] Multilanguage support
- [x] Authentication/Authorization against MQTT's database
- [x] Repeater list (read, create, edit)
- [x] Ability to control repeaters
- [x] Privilege separation
- [x] API to fetch the userdb (consumed by svxrdb-ara, an out-of-tree variant of svxrdb)

### P2
- [ ] ACLs based on MQTT Groups/Roles
- [ ] MQTT Dynamic security manager GUI
- [ ] Sysop management

### Later, required for v1
- [ ] Fix the multiple toast bug
- [ ] Improve the repeater list layout to be more mobile-friendly
- [ ] User management
- [ ] Repeater group management
- [ ] Built-in User-friendly Installer
- [ ] Built-in application configuration editor
- [ ] Generic Database connector instead of MySQLi

### Later
- [ ] Improve and separate the toast and modal dialog logic from the Repeater module (front end)
- [ ] Replace mosquitto-php with php-mqtt client (easier for platform support and updates).

## Installing

### Prerequisites
* Apache+PHP stack (developped with a PHP 8.2, untested on older versions).
* MySQLi
* Mosquitto PHP module [Installing Mosquitto for PHP](https://github.com/mgdm/Mosquitto-PHP)
* Redis (for app and session cache)
