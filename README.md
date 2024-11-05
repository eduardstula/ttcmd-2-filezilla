# ttcmd-2-filezilla

This application is a simple command line tool that convert Total Commander FTP connection settings
to FileZilla Sites XML format.

## Requirements
- PHP 8.2 (CLI) or higher
- XML extension enabled

## Before you start
- Copy your `wcx_ftp.ini` file from Total Commander to this directory.
- The `wcx_ftp.ini` file is typically located in the %APPDATA%\GHISLER directory.

## Usage
Runt the following command in the terminal:
```shell
php index.php
```
## Output
The output is a FileZilla Sites XML file named `filezilla.xml`.

## Import to FileZilla
- Open FileZilla.
- Go to `File` -> `Import` -> `Sites`.
- Select the `filezilla.xml` file.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Thanks
- [PELock](https://github.com/PELock) for the password decoder.