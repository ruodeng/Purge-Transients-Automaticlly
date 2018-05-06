<?php
/**
 * Created by PhpStorm.
 * User: ruo
 * Date: 2018/5/6
 * Time: 12:59
 */
class purge_transients_automaticlly{
    public static function instance() {
        new self();
    }

    public function __construct() {

        # add admin page and rewrite defaults
        if(is_admin()) {
            add_action('admin_menu',
                [
                    __CLASS__,
                    'admin_menu',
                ]
            );
            add_action('admin_init',
                [
                    __CLASS__,
                    'register_settings',
                ]
            );
        }

        add_action(
            'template_redirect',
            [
                __CLASS__,
                'redirect_hook',
            ]
        );
    }
    public  static function get_options() {
        return wp_parse_args(
            get_option('purge_transients_automaticlly'),
            [
                'enabled'=>0,
                'scheduled'            => 'daily',
            ]

        );
    }
    public static function admin_menu()
    {
        add_options_page(
            'Purge Transients Automaticlly',
            'Purge Transients Automaticlly',
            'manage_options',
            'purge-transients-automaticlly',
            [
                __CLASS__,
                'settings_page',
            ]
        );

    }
    public static function register_settings(){
        register_setting(
            'purge_transients_automaticlly',
            'purge_transients_automaticlly'
        );
    }
    public static function settings_page(){
        $options = self::get_options();
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

      
        ?>
        <div class="wrap">

            <h2>Scheduled</h2>
            <p>Corn job schedule to purge the transients.</p>
            <p>Current the purge action scheduled <strong><?php echo $options['scheduled']; ?></strong>.</p>
            <form method="post" action="options.php">
                <?php settings_fields( 'purge_transients_automaticlly' ); ?>
                <?php do_settings_sections( 'purge_transients_automaticlly' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th>Enable:</th>
                        <td>
                            <input type='checkbox' name='purge_transients_automaticlly[enabled]' value='1' <?php echo  $options['enabled']==1?'checked':''; ?> >
                        </td>
                    </tr>
                    <tr valign="top">
                        <th>Scheduled:</th>
                        <td>
                            <select name="purge_transients_automaticlly[scheduled]">
                                <option value="hourly" <?php selected( $options['scheduled'], 'hourly' ); ?>>Hourly</option>
                                <option value="daily" <?php selected( $options['scheduled'], 'daily' ); ?>>Daily</option>
<!--                                <option value="weekly" --><?php //selected( $options['scheduled'], 'weekly' ); ?><!-->Weekly</option>-->
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php



    }
}
