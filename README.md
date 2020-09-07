# Hogarth Drupal Assessment Project

## Installation

To install the project please follow the following instructions  
1. Clone the repo  
2. Navigate to the root directory of the newly cloned project  
3. Install the Back-End dependencies by running: "composer install"  
4. Set up VHost routing to the directory where the project is installed  
5. Configure *settings.local.php* file in the default directory for a single site installation. The minimum configuration required is DB and trusted hosts.  

---

## Development mode
To enable development mode add the following code to *settings.local.php* file:

``` php
if (file_exists($app_root . '/' . $site_path . '/settings.dev.php')) {
    include $app_root . '/' . $site_path . '/settings.dev.php';
}
```
In *settings.dev.php* the *development.services.yml* file is called to override the default Drupal settings.  
