# ACF
Helper functions for working with the Advanced Custom Fields plugin.

## Getting Started

The easiest way to install this package is by using composer from your terminal:

```bash
composer require moxie-leean/acf --save
```

Or by adding the following lines on your `composer.json` file

```json
"require": {
  "moxie-leean/acf": "dev-master"
}
```

This will download the files from the [packagelist site](https://packagist.org/packages/moxie-leean/acf) 
and set you up with the latest version located on master branch of the repository. 

After that you can include the `autoload.php` file in order to
be able to autoload the class during the object creation.

```php
include '/vendor/autoload.php';
```

## Usage

The module provides a class with a number of helper functions.

Except for ```is_active``` all functions return ```null``` if the ACF plugin is not active, otherwise they will return the value of the field.

For repeater fields they will return an empty array if there are no items.


### \Leean\Acf::is_active()
Returns true if the ACf plugin is installed and active. False if not.


### \Leean\Acf::get_post_field( $field, $post_id )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $post_id: The id of the post, or the current post in the loop if left out.


### \Leean\Acf::get_comment_field( $field, $comment )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $comment: The id of the comment or the WP_Comment object.


### \Leean\Acf::get_attachment_field( $field, $attachment_id )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $attachment_id: The id of the attachment.


### \Leean\Acf::function get_taxonomy_field( $field, $taxonomy_term )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $taxonomy_term: The id of the taxonomy term or the WP_Term object.


### \Leean\Acf::get_user_field( $field, $user_id )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $comment: The id of the user.


### \Leean\Acf::get_widget_field( $field, $widget_id )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
- $widget_id: The id of the widget.


### \Leean\Acf::get_option_field( $field )

- $field: The ACF field key or name. Note that ACF recommend always using the key.
