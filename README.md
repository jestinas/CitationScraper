# Local Citation Scraper





## Installation

Scraper requires [PHP](https://php.net) 7.1 or 7.2. This particular version supports Laravel 5.5 or 5.6 only.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):


Via Composer

``` bash
$ composer require sturt/citationscraper
```

Once installed, if you are not using automatic package discovery, then you need to register the `Sturt\CitationScraper\CitationServiceProvider::class` service provider in your `config/app.php`.

## Configuration

Scraper supports optional configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/citationscraper.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.



## Usage

```
use SB;

Scraper using Controller
        $data = [
            'data_mondovo_name'      =>      'Mondovo',
            'data_mondovo_address'      =>     '10685-B Hazelhurst Dr Houston, TX',
            'data_mondovo_phone'      =>      '7135748451',
            'data_mondovo_zipcode'      =>      '77043',
            'module_id'                 =>      'google'
        ];
        return SB::ScrapeYext($data);

```


## End Points Default

```

/api/v1/providers - List all providers
/api/v1/research_data - List all providers

```
