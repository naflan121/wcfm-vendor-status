# WooCommerce Vendor Status Plugin

This WooCommerce plugin displays the online status of vendors on the shop page and vendor store page. It adds a status indicator (Online/Offline) for each product and vendor.

## Features

- Display online/offline status of vendors on the product listing page.
- Display online/offline status of vendors on the vendor store page.
- Automatically updates vendor online status every 15 minutes.
- Custom shortcode to display vendor status anywhere on the site.

## Installation

1. Download the plugin files and upload them to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Display Vendor Status on Product Listing Page

The plugin automatically hooks into the WooCommerce product loop and adds the vendor status indicator before the product title.

### Display Vendor Status on Vendor Store Page

Use the `[vendor_status]` shortcode to display the vendor status on the vendor store page.

### Shortcode

To display the vendor status on any page or post, use the shortcode:

```plaintext
[vendor_status]
