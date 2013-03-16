<?php
/**
 * Front End Accounts
 *
 * @category    WordPress
 * @package     FrontEndAcounts
 * @since       0.1
 * @author      Christopher Davis <http://christopherdavis.me>
 * @copyright   2013 Christopher Davis
 * @license     http://opensource.org/licenses/MIT MIT
 */

namespace Chrisguitargy\FrontEndAccounts;

!defined('ABSPATH') && exit;

/**
 * A simple, almost PSR-0 Compliant autoloader. Strips out the $prefix and
 * loads classes from a directory without having to deal with directory
 * nesting 3 levels deep.
 *
 * @since   0.1
 */
class Autoloader
{
    /**
     * The namespace prefix to match with this autoloader.
     *
     * @since   0.1
     * @access  private
     * @var     string
     */
    private $prefix;

    /**
     * The directory in which our autoloader will look for classes.
     *
     * @since   0.1
     * @access  private
     * @var     string
     */
    private $dir;

    /**
     * Container for the instance of the autoloader used in
     * `spl_autoload_register`
     *
     * @since   0.1
     * @access  private
     * @var     Chrisguitarguy\FrontEndAccounts\Autoloader
     * @static
     */
    private static $ins = null;

    /**
     * Get an instance of the autoloader. Used with `spl_autoload_register`
     *
     * @since   0.1
     * @access  public
     * @return  Chrisguitarguy\FrontEnd\Accounts\Autoloader
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$ins)) {
            self::$ins = new self(__NAMESPACE__, __DIR__);
        }

        return self::$ins;
    }

    /**
     * Register the autoloader.
     *
     * @since   0.1
     * @access  public
     * @return  void
     * @static
     */
    public static function register()
    {
        spl_autoload_register(self::instance());
    }

    /**
     * Unregister the autoloader.
     *
     * @since   0.1
     * @access  public
     * @return  void
     * @static
     */
    public static function unregister()
    {
        spl_autoload_unregister(self::instance());
    }

    /**
     * Constructor. Set the namespace prefix and directory.
     *
     * @since   0.1
     * @access  public
     * @param   string $ns The namespace prefix
     * @param   string $dir The directory
     * @return  void
     */
    public function __construct($ns, $dir)
    {
        $this->prefix = $ns;
        $this->dir = $dir;
    }

    /**
     * The actual autoload callback.
     *
     * @since   0.1
     * @access  public
     * @param   string $cls The fully qualified class name
     * @return  boolean
     */
    public function __invoke($cls)
    {
        if ($cls = $this->resolveName($cls)) {
            $path = $this->dir . $cls . '.php';

            if (file_exists($path)) {
                require_once $path;
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize and strip the prefix from the class name. Replace backslashes
     * and underscores with directory separators.
     *
     * @since   0.1
     * @access  private
     * @param   string $cls A fully qualified class name
     * @return  boolean|string False if the class isn't in the correct namespace.
     *          A normalized class name otherwise.
     */
    private function resolveName($cls)
    {
        $cls = ltrim($cls, '\\');

        // if we don't have a class in this namespace don't bother.
        if (0 !== strpos($cls, $this->prefix)) {
            return false;
        }

        return str_replace(
            array('\\', '_'),
            DIRECTORY_SEPARATOR,
            str_replace($this->prefix, '', $cls)
        );
    }
}
