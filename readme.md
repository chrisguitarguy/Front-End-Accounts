# Frontend Accounts

Frontend Accounts brings login, register, forgot password, reset password and
account edit screen to the front end of your WordPress website.

In other words, you can build membership-based sites without needing to send
users to `wp-login.php` or your admin area.

Additionally, the plugin also introduces a new role with limited capabilities
called "Unprivleged User." Users with this role can't access the admin area.

## Requirements

* PHP 5.3+
* The technical skills to do your theme's integration

## Theme Integration

Unlike many plugins, this is not a drop in solution. It takes some work on your
part to get the front end working. There are some examples in the `themes`
directory, which get registred as a new theme directory via
`register_theme_directory`.

Essentially, you'll need to create an `account.php` file in your theme's (or
child theme's) folder. This file should be just like any other template, except
you'll replace the loop with a call to `the_account`.

    <?php
    /**
     * Template for Account Pages
     */

    !defined('ABSPATH') && exit;

    get_header('account');

    ?>
    <div class="row">

        <div class="span8">
            <?php the_account(); /* where the magic happens */ ?>
        </div>

        <?php get_sidebar('account'); ?>

    </div>

    <?php
    get_footer('account');

## Modifying Sections

Front end accounts uses a set of PHP classes to automate form creation, display
and validation. Every built in account section provides a way to alter forms via
an action called `frontend_accounts_alter_{section}_form`, like
`frontend_accounts_alter_account_form`.

The Form api is simple: the action will recieve a
`Chrisguitarguy\FrontEndAccounts\Form\FormInterface` object with provides the
methods `addField` and `removeField` which work much as you'd expect.

Don't want your users to be able to edit their account description?

    <?php
    add_action('frontend_accounts_alter_account_form', function($form) {
        $form->removeField('description');
    });

Want to add a field for phone number?

    <?php
    use Chrisguitarguy\FrontEndAccounts\Form;

    add_action('frontend_accounts_alter_account_form', function($form) {
        $form->addField('phone_number', array(
            'type'          => 'text', // this is the default
            'validators'    => array(
                // make sure the field is not empty
                new Form\Validator\NotEmpty(__('Please enter a phone number', 'your_texdomain')),
            ),
            'required'      => true, // HTML5, client side validation
        ));
    });

And, of course you'll need to save it.

    <?php
    add_action(
        'frontend_accounts_account_post_save_user',
        function($user, $formdata, $account_obj) {
            // this fires AFTER wp_update_user tries to save the user

            // $user is the WP_User object

            // $formdata is the validated data from the form

            // $account_obj is the that called do_action, it provides some
            // helpful methods: addError and removeError for showing errors
            // to users.

            // remember the validator above? phone_number should always be set.
            if (isset($formdata['phone_number'])) {
                update_user_meta($user->ID, 'yourprefix_phone_number', $formdata['phone_number']);
            } else {
                // show an error!
                $account_obj->addError('no_phone', __('Please provide a phone number.', 'your_textdomain'));
            }
        },
        10,
        4
    );

## Adding your Own Account Sections

Account page URL structure looks like this: `/account/{action}/{additional}`.

Where `{action}` determines which page to show (or none) based on a whitelist,
and `{additional}` is an optional piece of URL uses for this like indicating
success messages or for things like password reset keys.

**NOTE:** Frontend Accounts should work with non-pretty permalinks as well, but has
not been thoroughly tested

For our example: we're going to add an imaginary page called favorites.

To add your own section, you need to "whitelist" it's `{action}` by hooking into
`frontend_accounts_registered_sections`.

    <?php
    add_filter('frontend_accounts_registered_sections', function($sections) {
        $sections[] = 'favorites';
        return $sections;
    });

**NOTE:** you probably want your section `{action}`'s to be URL friendly. Be
sure to do that. Frontend accounts does *not* do it for you.

Once you whitelist the section, the plugin creates several subactions that
provide you with more specific places to hook in and display your content:

* `frontend_accounts_save_{action}` -- Fired on POST requests to the section.
The callback will receive the post data (`$_POST`) as the first argument and
anything in the `{additional}` field as the second.
* `frontend_accounts_init_{action}` -- Fired on template redirect before the
section gets sent to the client's screen. Comes after the `save` action.
* `frontend_accounts_content_{action}` -- Fired to display the actual content.

We only really need `frontend_accounts_content_favorites` for our purposes here.

    <?php
    add_action('frontend_accounts_content_favorites', function() {
        // some the user's favorites here!
    });

## A Note on Templating

Remember when I said that you needed just an `account.php` file in your theme?

That's not 100% true. You do need at least that file. But Frontend Accounts will
also look for more section-specific files.  Our favorites example, for instance,
would look for.

* `account-favorites.php`
* `account.php`

All the core sections do that same. Each template much include a call to
`the_account` to make things happen, but you have the flexibility to change many
things abount the styling/layout of each account page via different templates.

See the FAQ section below for a list of all core-provided sectons.

## Replace Default Sections

Frontend Accounts uses the API above to provide the default sections. As such,
you can remove entire sections and replace them with your own.

To see out that works, see the `Chrisguitarguy\FrontEndAccounts\SectionBase`
class along with its subclasses.

Everyone section class "cascades" down from a plugins loaded action, so you can
turn off the entire thing easily. Or you can remove parts of it.

    <?php
    use Chrisguitarguy\FrontEndAccounts\Account;

    add_action('plugins_loaded', function() {
        // remove the entire account edit section
        remove_action('plugins_loaded', array(Account::instance(), '_setup'));
    }, 9); // somewhere before priority 10

    // if you want to replace only parts, you'll need to hook in a bit later.
    add_action('plugins_loaded', function() {
        // remove the cotent
        remove_action('frontend_accounts_content_edit', array(Account::instance(), 'content'));

        // remove the save callback
        remove_action('frontend_accounts_save_edit', array(Account::instance(), 'save'));

        // remove the init callback
        remove_action('frontend_accounts_save_init', array(Account::instance(), 'initSection'));
    }, 11); // somewhere after priority 10

## FAQ

### Will this be on WordPress.org?

No. WordPress itself only requires PHP 5.2.4, and I don't really want to deal
with a bunch of folks wondering why this plugin won't work for them because of
that.

### Can you add X feature?

Maybe. You should try adding it yourself, outside of the "core" of this plugin
first, however. If it's difficult, let me know what would make it easier.

Right now this plugin is just about as big as it needs to be. It provides all
the core "account" related functionality without much else.

### What account sections does this plugin provide?

**Login**

Action: `login`

Class: `Chrisguitarguy\FrontEndAccounts\Login`

**Forgot Password**

Action: `forgot_password`

Class: `Chrisguitarguy\FrontEndAccounts\ForgotPassword`

**Register**

Only turned on when registration is enabled.

Action: `register`

Class: `Chrisguitarguy\FrontEndAccounts\Register`

**Reset Password**

Where users go when they request a password on the forgot password page.

Action: `reset_password`

Class: `Chrisguitarguy\FrontEndAccounts\ResetPassword`

**Edit**

Action: `edit`

Class: `Chrisguitarguy\FrontEndAccounts\Account`

## License

MIT. See `LICENSE` for more information.
