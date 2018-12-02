<?php
/**
 * This page will be auto loaded and contain all constant to be used in full site.
 *
 * @author Anirban Saha
 */

 define('ADMIN_PATH', 'apex-site-admin');
 define('FEED_URL', 'http://services.eoddsmaker.net/demo/feeds/V2.0/dictionaries.ashx?l=1&u=m.mortensen78&p=m.mortensen78&');
 define('MARKET_FEED_URL', 'http://services.eoddsmaker.net/demo/feeds/V2.0/markets.ashx?l=1&u=m.mortensen78&p=m.mortensen78&');
 define('LIVE_FEED_URL', 'http://apexsports.asia:4000/api/getlivefeed');
define('PAGING', 10);
define('TRANSACTION_PAGE_LIMIT', 10);
if(env('APP_ENV') === 'production') {
    define('OPERATOR_ID', 7777);
    define('SITE_PASS_KEY', 'apex-site-key-7777');
    define('CONFLUX_URL', 'http://conflux.asia/');
    define('CONFLUX_API_URL', 'http://conflux.asia/rest/apex/api/');
} else {
    define('OPERATOR_ID', 4444);
    define('SITE_PASS_KEY', 'apex-site-dev-key-4444');
    define('CONFLUX_URL', 'http://conflux.undertesting.com/');
    define('CONFLUX_API_URL', 'http://conflux.undertesting.com/rest/apex/api/');
}