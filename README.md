# ACF

Helper functions for working with the Advanced Custom Fields plugin.

## Requirements

- PHP 5.4+ 
- Composer 

## Getting Started

The easiest way to install this package is by using running the following command from your terminal:

```bash
composer require moxie-lean/acf --save
```

Or by adding the following lines on your `composer.json` file

```json
"require": {
  "moxie-lean/acf": "dev-master"
}
```

This will download the files from the [packagelist site](https://packagist.org/packages/moxie-lean/acf) 
and set you up with the latest version located on master branch of the repository. 

After that you can include the `autoload.php` file in order to be able to autoload 
the class during the object creation. 

```php
include '/vendor/autoload.php';
```

## Filters

### `ln_acf_apply_default_transforms_{field_key}`

This filter allows you to overwrite the default transforms applied to
the field. Where `{field_key}` is replaced by the key of your field
for example: `ln_acf_apply_default_transforms_field_56f293e024b74`.   

The filter has the following parameters:  

1. `$target_id`: The `id` of the post or page that where the field
   belongs.
2. `$field_obj`: array of data containing all field settings   

## `ln_acf_field_{field_key}`.

This filter allow you to overwrite the default value returned for a
particular field. The `{field_key}` is replaced by the key of your field
for example: `ln_acf_field_field_56f293e024b74`.  

The filter has the following parameters:  

1. `$value`: The value of the field or the default value to be returned.
2. `$target_id`: The `id` of the post or page that where the field
   belongs.
3. `$field_obj`: array of data containing all field settings   

### `ln_acf_image_size`

This filter is applied to images before are returned and can be used to
return a specifc size of the image.  

The filter has the following parameters. 

1. `$size`: By default the value is false and the `$attachment_id` is
   returned instead, here you can specify the size of the image you want to use.
2. `$field`: Is the field that has the image here you have all the
   information of that particular field such as: `key`, `name` and so
on,
3. `$sub_field`: This is always false for fields that are not repeter
   fields otherwise contains the fields that are childs.

### `ln_acf_repeater_as_array`.
