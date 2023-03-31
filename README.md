# Starter Site

Welcome to the Starter Site repository! This is a minimal Drupal 10 installation that you can use as a starting point for your own projects.

## Features

This starter site comes with the following features:

- Minimal configuration: The site is designed to be as lightweight as possible, so it includes only the basic configuration required to get up and running.
- Easy installation: You can install the site quickly and easily using Lando and Drush. Just follow the instructions below.
- Customizable: Once you've installed the site, you can customize it to your heart's content. Add new modules, themes, and content types to create the site you want.

## How to Install

To install the Starter Site, follow these steps:

1. Clone the repository to your local machine.
2. Install Lando if you haven't already done so.
3. Navigate to the root of the repository and run ``lando start`` to start the development environment.
4. Run `lando drush si --existing-config -y` to install Drupal using existing configuration.
5. Once the installation is complete, navigate to the site in your browser and start customizing!

## Customization

The Starter Site is designed to be as flexible as possible, so you can customize it to your heart's content. Here are some ideas for how to get started:

- Add new modules: Drupal has a huge ecosystem of modules that can add new features and functionality to your site. Visit the [Drupal module repository](https://www.drupal.org/project/project_module) to browse available modules.
- Install a new theme: Drupal comes with a few built-in themes, but there are also many third-party themes available that you can use to change the look and feel of your site. Visit the Drupal theme repository to browse available themes.
- Create new content types: Drupal's flexible content model allows you to create custom content types with their own fields and settings. Use the Drupal UI or code to create new content types that fit your needs.

## Based On

The Starter Site is based on the [Sous Drupal Project](https://github.com/fourkitchens/sous-drupal-project), a Drupal distribution designed for building editorial and content-driven sites. We've stripped away some of the extra features to create a more lightweight starting point, but you may find it helpful to reference Sous if you need to add more advanced functionality to your site.
