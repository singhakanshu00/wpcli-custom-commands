# PMC Plugin

This plugin facilitates backend functionality within WordPress and includes custom CLI commands.

## Description

PMC Plugin is designed to handle backend processes, such as creating custom CLI commands and managing post categories.

## Installation

1. **Setup a WordPress VIP Site**
   To set up a WordPress VIP site, follow the guidelines provided [here](https://docs.wpvip.com/local-development/third-party-app/).

2. **Use the CLI Command**
   Execute the provided CLI command to set all post categories to 'pmc' (parent)->'rollingstone' (child).
   This command also counts all images within post content and adds a meta called `_pmc_image_counts` to each post.
   - CLI Reference: [VIP CLI Documentation](https://docs.wpvip.com/vip-cli/)

## Usage

1. Custom Commands:

- `wp category set_post_categories --post-type=post` : Set post categories to pmc(parent)->rollingstone(child) and count images in post content.
