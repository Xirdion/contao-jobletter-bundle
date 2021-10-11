# Jobletter-Bundle

The contao jobletter bundle adds the ability to notify users about new job offers.

The way it interacts with Contao is modeled after the newsletter bundle.

## Features

- Compatible with Contao 4.9 and higher (PHP 8 Support)
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

## Aufbau

### Job-Archiv
Das Job-Archiv wird um Einstellungen für den Mail-Versand erweitert.
Unter anderem kann hier der Text für den Job-Letter definiert werden.
Dafür stehen folgende Tokens zur Verfügung:
- `##email##` (E-Mail-Adresse des Empfängers)
- `##archive##` (Name des aktuellen Job-Archivs)
- `##categories##` (Liste der abonnierten Kategorien)
- `##job##` (Name des Jobs)
- `##job_link##` (Link zur Detail-Seite des Jobs)
- `##unsubscribe_link##` (Link zur Abmelde-Seite des Job-Letters)

In der Listenansicht hat man die Möglichkeit die Abonnenten des Job-Letters für dieses Archiv einsehen.
Auch deren aktuellen Status (Anmeldung bestätigt oder nicht) kann man dort erkennen.

### Job
Auf der Listenansicht der Jobs hat man die Möglichkeit für alle Jobs, die einen aktiven Abonnent haben, auf einmal den Job-Letter zu versenden.
Des Weiteren kann man pro Job individuell die E-Mail versenden.

## An- und Abmeldung
Bei der An- und Abmeldung hat man jeweils die Möglichkeit bestimmte Job-Kategorien auszuwählen.
So kann man die Zuordnung für den Versand von Nachrichten noch detaillierter definieren.

## Besonderheiten

- Es wird bei der Registrierung ein eigener OptIn-Token-Service verwendet, anstatt der von Contao.
Dadurch hat man die Möglichkeit die E-Mail-Adresse des Seite-Admins an das E-Mail-Objekt weiterzugeben.
Würde dies nicht gemacht werden, greift der Fallback aus der E-Mail-Klasse, die immer den Administrator aus den Einstellungen verwenden.


- Beim Versand des Job-Letters wird überprüft, wann der Benutzer das letzte mal zum aktuellen Job benachrichtigt wurde.
Ist dies bereits über **30 Tage** her, dann wird dieser erneut benachrichtigt.