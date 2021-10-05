# Jobletter-Bundle

The contao jobletter bundle adds the ability to notify users about new job offers.

The way it interacts with Contao is modeled after the newsletter bundle.

## Features

- Compatible with Contao 4.9 and higher versions (PHP 8 Support)
- Two new Frontend-modules to subscribe and unsubscribe from notifications
- Possibility to send messages job by job or all at once
- Overview about all subscribers

## Installation

**Via Composer**
```shell
$ composer require dreibein/contao-jobletter-bundle
```

## Dependencies

- PHP: `>=7.4`
- Contao: `^4.9`
- Dreibein/ContaoJobpostingBundle: `^1.0`

## Besonderheiten

Es wird bei der Registrierung ein eigener OptIn-Token-Service verwendet, anstatt der von Contao.
Dadurch hat man die Möglichkeit die E-Mail-Adresse des Seite-Admins an das E-Mail-Objekt weiterzugeben.
Würde dies nicht gemacht werden, greift der Fallback aus der E-Mail-Klasse, die immer den Administrator aus den Einstellungen verwenden.
