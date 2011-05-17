/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */

After copy directory app to your magento run this commands under Ubuntu:

MAGENTO=magento # your magento directory

# Set some permissions
chmod 700 -R /var/www/$MAGENTO/
chown -R www-data:www-data /var/www/$MAGENTO

# Clear cache
rm -rf /var/www/$MAGENTO/var/cache/*
