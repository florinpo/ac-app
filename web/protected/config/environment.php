<?php



// You need to specify the path to CORE FOLDER CORRECTLY
define('CORE_FOLDER', dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'ac-source'.DIRECTORY_SEPARATOR.'core');
define('CMS_FOLDER', CORE_FOLDER . DIRECTORY_SEPARATOR . 'cms');
define('CMS_WIDGETS', CMS_FOLDER . DIRECTORY_SEPARATOR . 'widgets');
define('COMMON_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'common');

define('FRONT_SITE_URL', 'http://localhost/ac/web/');
define('IMAGES_URL', 'http://localhost/ac/images');
define('IMAGES_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'images');

define('FRONTEND_CLEAR_CACHE_KEY', 'ads2312rGmuf6d2xxzNEsSQzi');
define('USER_SALT', 'sdad12313f6d2xxzNEsSQzi');



//StripSlashes all GET, POST, COOKIE

if (get_magic_quotes_gpc()) {

    function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }

    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
}

/**
 * This class helps you to config your Yii application
 * environment.
 * Any comments please post a message in the forum
 * Enjoy it!
 *
 * @name Environment
 * @author Fernando Torres | Marciano Studio
 * @version 1.0
 */
class Environment {

    const DEVELOPMENT = 100;
    const TEST = 200;
    const STAGE = 300;
    const PRODUCTION = 400;

    private $_mode = 0;
    private $_debug;
    private $_trace_level;
    private $_config;

    /**
     * Main configuration
     * This is the general configuration that uses all environments
     */
    private function _main() {
        return array(
            // Project Name                    
            'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
            'id' => 'backend',
            'name' => 'New Application',
            'sourceLanguage' => 'en_us',
            'language' => 'en_us',
            'defaultController' => 'site',
            // Preloading 'log' component
            'preload' => array('log'),
            // autoloading model and component classes
            'import' => array(
                //Import from Application folder
                'application.models.*',
                'application.components.*',
                //Import from Common folder
                'common.components.*',
                'common.storages.*',
                //Import from CMS
                'cms.components.*',
                'cms.components.user.*',
                'cms.components.user.YiiPassword.*',
                'cms.components.validators.*',
                'cms.extensions.*',
                'cms.extensions.behaviors.*',
                'cms.extensions.DGSphinxSearch.*',
                'cms.extensions.PcMaxmindGeoIp.*',
                'cms.extensions.gapi.*',
                'cms.extensions.chosen.Chosen',
                'cmswidgets.*',
                //Import from CMS Models
                'cms.models.user.*',
                'cms.models.company.*',
                'cms.models.privatemessage.*',
                'cms.models.mailbox.*',
                'cms.models.notification.*',
                'cms.models.product_sale.*',
                'cms.models.location.*',
                'cms.models.membership.*',
                'cms.models.object.*',
                'cms.models.resource.*',
                'cms.models.page.*',
                'cms.models.translate.*',
                //Import MODULES
                //Import Rights Module                
                'cms.modules.rights.components.*',
                'cms.modules.rights.components.dataproviders.*',
                'cms.modules.rights.models.*',
                'cms.modules.rights.RightsModule',
            ),
            'modules' => array(
                'contentViewsCounter' => array(
                    'class' => 'cms.modules.PcViewsCounter.PcViewsCounterModule',
                ),
                'rights' => array(
                    'class' => 'cms.modules.rights.RightsModule',
                    'install' => false, // Enables the installer.
                    'appLayout' => 'application.views.layouts.main',
                    'superuserName' => 'Admin',
                ),
                'error' => array(
                    'class' => 'cms.modules.error.ErrorModule',
                ),
                'cache' => array(
                    'class' => 'cms.modules.cache.CacheModule',
                    'password' => '123456',
                ),
            ),
            // Application components
            'components' => array(
                'cache' => array(
                    'class' => 'CApcCache',
                ),
                'cache2' => array(
                    'class' => 'system.caching.CDbCache',
                    'connectionID' => 'db',
                ),
                //User Componenets
                'user' => array(
                    'class' => 'cms.components.user.GxcUser',
                    // enable cookie-based authentication                        
                    'allowAutoLogin' => true,
                    'autoRenewCookie' => true,
                    'loginUrl' => array('page/render', 'slug' => 'sign-in'),
                    'stateKeyPrefix' => 'gxc_front_u_', //Should Change for Different Apps
                ),
                //Auth Manager
                'authManager' => array(
                    'class' => 'cms.modules.rights.components.RDbAuthManager',
                    'defaultRoles' => array()
                ),
                // Error Handler
                'errorHandler' => array(
                    'errorAction' => 'error',
                ),
                // URLs in path-format
                'urlManager' => array(
                    'urlFormat' => 'path',
                    'showScriptName' => false,
                    'urlSuffix' => '/',
                    'rules' => array(
                        '<slug:[\w\-]+>' => 'page/render',
                        '<controller:\w+>/<id:\d+>' => '<controller>/view',
                        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                    ),
                ),
                'session' => array(
                    'class' => 'CDbHttpSession',
                    'connectionID' => 'db',
                    'autoCreateSessionTable' => false,
                    'sessionTableName' => 'gxc_session',
                    'sessionName' => 'gxc_session_id_frontend', //Should Change for Different Apps
                    'timeout' => 86400,
                ),
                //Use the Settings Extension and Store value in Database
                'settings' => array(
                    'class' => 'cms.extensions.settings.CmsSettings',
                    'cacheId' => 'global_website_settings',
                    'cacheTime' => 84000,
                ),
                'request' => array(
                    'class' => 'cms.components.HttpRequest',
                    'enableCsrfValidation' => false,
                    'enableCookieValidation' => true,
                    'noCsrfValidationRoutes' => array(
                        'site/cachingclear', 'company/uploadlogo', 'productsale/upload', 'site/deletecache',
                        'site/uploadavatar', 'message/upload'
                    )
                ),
                'search' => array(
                    'class' => 'cms.extensions.DGSphinxSearch.DGSphinxSearch',
                    'server' => '127.0.0.1',
                    'port' => 9312,
                    'maxQueryTime' => 3000,
                    'enableProfiling' => 0,
                    'enableResultTrace' => 0,
                ),
                'messages' => array(
                    'class' => 'cms.components.PhpMessageSource',
                    'cachingDuration' => 86400,
                )
            ),
            // Application-level parameters
            'params' => array(
                'environment' => $this->_mode,
                'ga_email' => 'florinpojum29@gmail.com',
                'ga_password' => 'Libisor2984',
                'ga_profile_id' => '80316775',
                'lastModifications' => array(
                    'gxc_mailbox_conversation' => 0,
                    'gxc_mailbox_message' => 0
                ),
            )
        );
    }

    /**
     * Development configuration
     * Usage:
     * - Local website
     * - Local DB
     * - Show all details on each error.
     * - Gii module enabled
     */
    private function _development() {

        // Set Time Zone
        date_default_timezone_set('Europe/Rome');

        define('SITE_PATH', 'http://localhost/ac/web/');
        define('RESOURCE_URL', 'http://localhost/ac/resources');
        define('RESOURCES_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'resources');

        // Define EMAIL INFORMATION
        define('ADMIN_EMAIL', 'admin@localhost.com');

        // Define Related to Upload File Size
        define('UPLOAD_MAX_SIZE', 10485760); //10mb
        define('UPLOAD_MIN_SIZE', 1); // 1 byte

        return array(
            // Modules
            'modules' => array(
                'gii' => array(
                    'class' => 'system.gii.GiiModule',
                    'password' => '123456',
                    'ipFilters' => array('127.0.0.1', '::1'),
                    'newFileMode' => 0666,
                    'newDirMode' => 0777,
                    'generatorPaths' => array('cms.gii',),
                ),
            ),
            // Application components
            'components' => array(
                // Database
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=gxc2',
                    'schemaCachingDuration' => 3600,
                    'emulatePrepare' => true,
                    'username' => 'florin',
                    'password' => '000000',
                    'charset' => 'utf8',
                    'tablePrefix' => 'gxc_',
                    'enableProfiling' => true,
                    'enableParamLogging' => true,
                    'queryCacheID' => 'cache'
                ),
                // Application Log
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array('class' => 'cms.extensions.yii-debug-toolbar.YiiDebugToolbarRoute'),
                        // Save log messages on file
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning,trace, info',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Test configuration
     * Usage:
     * - Local website
     * - Local DB
     * - Standard production error pages (404,500, etc.)
     * @var array
     */
    private function _test() {

        // Set Time Zone
        date_default_timezone_set('Europe/Rome');

        define('SITE_PATH', 'http://localhost/ac/web/');
        define('RESOURCE_URL', 'http://localhost/ac/resources');
        define('RESOURCES_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'resources');

        // Define EMAIL INFORMATION
        define('ADMIN_EMAIL', 'admin@localhost.com');


        return array(
            // Application components
            'components' => array(
                // Database
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=gxc2',
                    'schemaCachingDuration' => 3600,
                    'emulatePrepare' => true,
                    'username' => 'florin',
                    'password' => '000000',
                    'charset' => 'utf8',
                    'tablePrefix' => 'gxc_'
                ),
                // Fixture Manager for testing
                'fixture' => array(
                    'class' => 'system.test.CDbFixtureManager',
                ),
                // Application Log
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning,trace, info',
                        ),
                        // Show log messages on web pages
                        array(
                            'class' => 'CWebLogRoute',
                            'levels' => 'error, warning',
                        ),
                        // Show PhpQuickProfiler
                        array(
                            'class' => 'cms.extensions.pqp.PQPLogRoute',
                            'categories' => 'application.*, exception.*, system.*',
                            'levels' => 'error, warning, info',
                        )
                    ),
                ),
            ),
        );
    }

    /**
     * Stage configuration
     * Usage:
     * - Online website
     * - Production DB
     * - All details on error
     */
    private function _stage() {

        // Set Time Zone
        date_default_timezone_set('Europe/Rome');

        define('SITE_PATH', 'http://localhost/ac/web/');
        define('RESOURCE_URL', 'http://localhost/ac/resources');
        define('RESOURCES_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'resources');

        // Define EMAIL INFORMATION
        define('ADMIN_EMAIL', 'admin@localhost.com');


        return array(
            // Application components
            'components' => array(
                // Database
                // Database
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=gxc2',
                    'schemaCachingDuration' => 3600,
                    'emulatePrepare' => true,
                    'username' => 'florin',
                    'password' => '123456',
                    'charset' => 'utf8',
                    'tablePrefix' => 'gxc_'
                ),
                // Application Log
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning, trace, info',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Production configuration
     * Usage:
     * - online website
     * - Production DB
     * - Standard production error pages (404,500, etc.)
     */
    private function _production() {

        // Set Time Zone
        date_default_timezone_set('Europe/Rome');

        define('SITE_PATH', 'http://localhost/ac/web/');
        define('RESOURCE_URL', 'http://localhost/ac/resources');
        define('RESOURCES_FOLDER', dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'resources');

        // Define EMAIL INFORMATION
        define('ADMIN_EMAIL', 'admin@localhost.com');

        // Define Related to Upload File Size
        define('UPLOAD_MAX_SIZE', 10485760); //10mb
        define('UPLOAD_MIN_SIZE', 1); // 1 byte


        return array(
            // Application components
            'components' => array(
                // Database
                'db' => array(
                    'connectionString' => 'mysql:host=localhost;dbname=gxc2',
                    'schemaCachingDuration' => 3600,
                    'emulatePrepare' => true,
                    'username' => 'florin',
                    'password' => '000000',
                    'charset' => 'utf8',
                    'tablePrefix' => 'gxc_'
                ),
                // Application Log
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning',
                        ),
                    // Send errors via email to the system admin
//                        array(
//                            'class' => 'CEmailLogRoute',
//                            'levels' => 'error, warning',
//                            'emails' => ADMIN_EMAIL,
//                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns the debug mode
     * @return Bool
     */
    public function getDebug() {
        return $this->_debug;
    }

    /**
     * Returns the trace level for YII_TRACE_LEVEL
     * @return int
     */
    public function getTraceLevel() {
        return $this->_trace_level;
    }

    /**
     * Returns the configuration array depending on the mode
     * you choose
     * @return array
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * Initilizes the Environment class with the given mode
     * @param constant $mode
     */
    function __construct($mode) {
        $this->_mode = $mode;
        $this->setConfig();
    }

    /**
     * Sets the configuration for the choosen environment
     * @param constant $mode
     */
    private function setConfig() {
        switch ($this->_mode) {
            case self::DEVELOPMENT:
                $this->_config = array_merge_recursive($this->_main(), $this->_development());
                $this->_debug = TRUE;
                $this->_trace_level = 3;
                break;
            case self::TEST:
                $this->_config = array_merge_recursive($this->_main(), $this->_test());
                $this->_debug = FALSE;
                $this->_trace_level = 0;
                break;
            case self::STAGE:
                $this->_config = array_merge_recursive($this->_main(), $this->_stage());
                $this->_debug = TRUE;
                $this->_trace_level = 0;
                break;
            case self::PRODUCTION:
                $this->_config = array_merge_recursive($this->_main(), $this->_production());
                $this->_debug = FALSE;
                $this->_trace_level = 0;
                break;
            default:
                $this->_config = $this->_main();
                $this->_debug = TRUE;
                $this->_trace_level = 0;
                break;
        }
    }

}

// END Environment Class