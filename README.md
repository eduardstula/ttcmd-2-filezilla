# ttcmd-2-filezilla 📂

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![GitHub issues](https://img.shields.io/github/issues/eduardstula/ttcmd-2-filezilla)]()
[![GitHub stars](https://img.shields.io/github/stars/eduardstula/ttcmd-2-filezilla)]()
[![GitHub forks](https://img.shields.io/github/forks/eduardstula/ttcmd-2-filezilla)]()
[![GitHub watchers](https://img.shields.io/github/watchers/eduardstula/ttcmd-2-filezilla)]()

> Finally! You can easily migrate your FTP connection settings from Total Commander to FileZilla.

This application is a simple command line tool that convert Total Commander FTP connection settings
to FileZilla Sites XML format. 

## 🤔 Why world needs this?
Total Commander is the best file manager for Windows. No doubt about that.
But when you need migrate from Windows to Linux, you will need easy way to transfer your FTP connection settings to new FTP client.


## 💿 Requirements
- PHP 8.2 (CLI) or higher
- XML extension enabled

## 🏃 Before you start
- Copy your `wcx_ftp.ini` file from Total Commander to this directory.
- The `wcx_ftp.ini` file is typically located in the %APPDATA%\GHISLER directory.

## 🚀 Usage
Runt the following command in the terminal:
```shell
php index.php
```
## ☄️ Output
The output is a FileZilla Sites XML file named `filezilla.xml`.

## 💾 Import to FileZilla
- Open FileZilla.
- Go to `File` -> `Import` -> `Sites`.
- Select the `filezilla.xml` file.

## 😶 Disclaimer
- This application is provided as is without any warranty. Use at your own risk.
- This application is not affiliated with Total Commander or FileZilla.
- Use this application only for personal use to transfer your FTP connection settings from Total Commander to FileZilla.
- Do not use this application illegally.

## 🫂 Contributing

If you want to contribute to this project, you can create a pull request with your changes. I will be happy to review and merge them.

## 📒 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Thanks
- [PELock](https://github.com/PELock) for the password decoder.