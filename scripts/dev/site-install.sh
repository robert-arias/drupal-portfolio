#!/usr/bin/env bash
# Generate a new ramdom uuid the first time you create a project.
# You could use the `uuidgen` bash command to get new one!!.
SITE_UUID="6625c107-297e-4e5c-b731-0a6112b5c462"
lando drush cc drush
echo "Installing the site..."
lando drush si minimal --account-pass=admin --site-name="Drupal Personal Project" -y --locale=en
echo "Setting the site uuid..."
lando drush config:set system.site uuid "$SITE_UUID" -y
if [ -f ./config/sync/core.extension.yml ]; then lando drush cim -y; lando drush cim -y; lando drush cr; fi

# Change mep by your own theme folder.
if [ -f ./themes/custom/THEME_NAME/package.json ]; then
  cd ./themes/custom/THEME_NAME
  if [ ! -d ./node_modules ]; then npm install; fi
  npm run build
fi

echo "Cleaning cache..."
lando drush cr
