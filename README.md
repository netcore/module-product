## Shop management module
This module was made for easy shop integration.

## Features
- Variable products
- Cart
- Wishlist
- Parameter/attribute management
- Multi-currency

## Pre-installation
This module is part of Netcore CMS ecosystem and is only functional in a project that has following packages installed:

1. https://github.com/netcore/netcore
2. https://github.com/netcore/module-admin
3. https://github.com/netcore/module-translate

### Installation
1. Require this package using composer
```bash
    composer require netcore/module-product
```

2. Publish assets/configuration/migrations
```bash
    php artisan module:publish Product
    php artisan module:publish-config Product
    php artisan module:publish-migration Product
```

3. Before seeding data, configure category group that will be used for products
```
    Open config file and edit 'used_category_group'
```

4. Run the migrations and seeder
```bash
    php artisan migrate
    php artisan module:seed Product
```

### Configuration
- Configuration file is available at config/netcore/module-product.php

### TODO - improve readme..
