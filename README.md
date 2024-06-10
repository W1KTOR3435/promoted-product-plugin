# Promoted Product Plugin

## Description

The Promoted Product Plugin is a custom WordPress plugin that allows you to feature a promoted product on every page of your WooCommerce site. This plugin adds custom fields to the product editor to set a product as promoted and specify a custom title and expiration date. The promoted product is displayed prominently on your site's header.

## Features

- Add a product as promoted with a custom title.
- Set an expiration date for the promotion.
- Display the promoted product on every page of the site.
- Automatically handle promotion expiration.

## Installation

1. Download the plugin files and place them in the `wp-content/plugins/promoted-product-plugin` directory.
2. Navigate to the WordPress admin dashboard.
3. Go to Plugins > Installed Plugins.
4. Activate the Promoted Product Plugin.

## Usage

### Setting Up a Promoted Product

1. Navigate to WooCommerce > Products and edit a product.
2. In the product editor, under the "General" tab, you will see the following fields:
   - **Promote this product**: Check this box to promote the product.
   - **Promoted Product Title**: Enter a custom title to display instead of the product title.
   - **Set Expiration Date**: Check this box to set an expiration date for the promotion.
   - **Expiration Date**: Select the expiration date and time.
3. Save the product.

### Plugin Settings

1. Navigate to WooCommerce > Settings > Products.
2. Under the "Promoted Product" tab, you will find the following settings:
   - **Title**: Enter a title for the promoted product section (e.g., "FLASH SALE:").
   - **Background Color**: Select a background color for the promoted product section.
   - **Text Color**: Select a text color for the promoted product section.
   - **Active Promoted Product**: Displays the currently promoted product and a link to edit it.

### Displaying the Promoted Product

The promoted product is automatically displayed in a full-width div at the bottom of the site's header. The display format is:
[Promoted title from backend]: [product title | custom title]

## Technical Details

### Code Structure

- **Plugin.php**: Initializes the plugin.
- **Settings.php**: Handles the plugin settings in WooCommerce.
- **ProductEditor.php**: Adds custom fields to the product editor and handles saving and expiration logic.
- **Frontend.php**: Displays the promoted product on the frontend.

### Scheduling Expiration

The plugin uses WordPress's scheduling functions to check and handle promotion expiration. The expiration date is checked automatically, and the promotion is removed once the expiration date is reached.

## Development

### Autoloading

The plugin uses Composer for autoloading classes. Ensure you run `composer install` to set up the autoloader. 

### WordPress Coding Standards

The plugin follows WordPress coding standards. All functions and methods are properly documented, and security best practices are followed. `composer phpcs` and `composer phpcbf` scripts are added to help maintain wordpress coding standards.

## Author

Wiktor Kowalczyk