c5_reload_on_attribute_change
=============================

Reloads page after attributes are saved, so users see immediate feedback on page types that use attributes for layout/design options.

## Requirements
Probably only works with Concrete5.5+

## Installation
Drop the `reload_on_attribute_change.php` file into your site's top-level `elements` directory.

## Usage
Add this to the top of any page type templates you want this functionality on:

    Loader::element('reload_on_attribute_change');

(A good place to add it is under the `include('elements/header.php');` line.)