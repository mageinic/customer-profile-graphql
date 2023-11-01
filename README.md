# Customer Profile GraphQL

**Customer Profile GraphQL is a part of MageINIC Customer Profile extension that adds GraphQL features.** This extension extends Customer Profile definitions.

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageinic/customerprofilegraphql

php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento maintenance:disable
php bin/magento cache:flush
```

**Note:**
Magento 2 Customer Profile GraphQL requires installing [MageINIC Customer Profile](https://github.com/mageinic/Customer-Profile) in your Magento installation.

**Or Install via composer [Recommend]**
```
composer require mageinic/customerprofile
```

## 2. How to use

- To view the queries that the **MageINIC Customer Profile GraphQL** extension supports, you can check `Customer Profile GraphQl User Guide.pdf` Or run `CustomerProfileGraphQl.postman_collection.json` in Postman.

## 3. Get Support

- Feel free to [contact us](https://www.mageinic.com/contact.html) if you have any further questions.
- Like this project, Give us a **Star**
