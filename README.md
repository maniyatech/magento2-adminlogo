# ManiyaTech AdminLogo module for Magento 2

Customize your Magento 2 admin panel branding. Upload a custom login page logo and menu bar logo, and define a custom logo title. Supports PNG, JPG, SVG , GIF formats.

## How to install ManiyaTech_AdminLogo module

### Composer Installation

Run the following command in Magento 2 root directory to install ManiyaTech_AdminLogo module via composer.

#### Install

```
composer require maniyatech/magento2-adminlogo
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

#### Update

```
composer update maniyatech/magento2-adminlogo
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

Run below command if your store is in the production mode:

```
php bin/magento setup:di:compile
```

### Manual Installation

If you prefer to install this module manually, kindly follow the steps described below - 

- Download the latest version [here](https://github.com/maniyatech/magento2-adminlogo/archive/refs/heads/main.zip) 
- Create a folder path like this `app/code/ManiyaTech/AdminLogo` and extract the `main.zip` file into it.
- Navigate to Magento root directory and execute the below commands.

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```
