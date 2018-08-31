# cakephp-mailchimp
[![LICENSE](https://img.shields.io/github/license/smartsolutionsitaly/cakephp-mailchimp.svg)](LICENSE)
[![packagist](https://img.shields.io/badge/packagist-smartsolutionsitaly%2Fcakephp-mailchimp-brightgreen.svg)](https://packagist.org/packages/smartsolutionsitaly/cakephp-mailchimp)
[![issues](https://img.shields.io/github/issues/smartsolutionsitaly/cakephp-mailchimp.svg)](https://github.com/smartsolutionsitaly/cakephp-mailchimp/issues)
[![CakePHP](https://img.shields.io/badge/CakePHP-3.5%2B-brightgreen.svg)](https://github.com/cakephp/cakephp)

[MailChimp](https://mailchimp.com/) connector for [CakePHP](https://github.com/cakephp/cakephp)

## Installation

You can install _cakephp-mailchimp_ into your project using [Composer](https://getcomposer.org).

``` bash
$ composer require smartsolutionsitaly/cakephp-mailchimp
```

## Configuration

Put the MailChimp key and list in your application configuration file (usually config/app.php).

``` php
    'MailChimp' => [
        'key' => 'API-KEY',
        'lists' => [
            'list1' => 'LIST-ID',
            'list2' => 'LIST-ID'
        ]
    ];
```

## License
Licensed under The MIT License
For full copyright and license information, please see the [LICENSE](LICENSE)
Redistributions of files must retain the above copyright notice.

## Copyright
Copyright (c) 2018 Smart Solutions S.r.l. (https://smartsolutions.it)
