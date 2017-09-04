<?php




add_action('wp_dashboard_setup', array('My_Dashboard_Widget','init') ,1);

class My_Dashboard_Widget {

    /**
     * The id of this widget.
     */
    const wid = 'my_host_info_wg';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {
        //Register widget settings...
        self::update_dashboard_widget_options(
            self::wid,                                  //The  widget id
            array(                                      //Associative array of options & default values
                'example_number' => 42,
            ),
            true                                        //Add only (will not update existing options)
        );

        //Register the widget...
        add_meta_box(
            self::wid,                                  //A unique slug/ID
            __( 'اطلاعات هاست', 'nouveau' ),//Visible name for the widget
            array('My_Dashboard_Widget','widget'),      //Callback for the main widget content
            //array('My_Dashboard_Widget','config')       //Optional callback for widget configuration content
            get_current_screen(),
            'normal',
            'high'
        );
    }

    /**
     * Load the widget code
     */
    public static function widget()
    {
        $url        = get_home_url();
        $urlArray   = parse_url($url);
        $domain     = str_replace('www.','',$urlArray['host']);
        
        $input = array(
                'apikey'  => SP_SITE_APIKEY,
                'site_id' => SP_SITE_ID,
                'domain'  => $domain,
            );
        $output = fetchDataFromServer('/host/info',$input);
        if($output && $output['status'])
        {
               
            $disk_usage     = $output['data']['disk'];
            $disk_all       = $output['data']['disk_all'];
            $disk_precent   = round((intval($disk_usage)/intval($disk_all))*100);
            if($disk_precent < 50)
                $disk_color = '#2ecc71';
            elseif ($disk_precent > 75)
                $disk_color = '#e74c3c';
            else
                $disk_color = '#f39c12';
               
            $bw_usage     = $output['data']['bandwidth'];
            $bw_all       = $output['data']['bandwidth_all'];
            $bw_precent   = round((intval($bw_usage)/intval($bw_all))*100);
            if($bw_precent < 50)
                $bw_color = '#2ecc71';
            elseif ($bw_precent > 75)
                $bw_color = '#e74c3c';
            else
                $bw_color = '#f39c12';
            
            
            
        
        ?>
        <table style="width:100%">
            <tr>
                <td <?php if ($disk_precent > 75) echo('style="color:#e74c3c"') ?>> میزان حجم هاست </td>
                <td style="text-align:center;width:70%">
                    <div style="min-width:100px;width:100%;  height:20px;border:1px solid #555;position:relative;border-radius:1px">
                        <div style="background: <?php echo($disk_color)  ?>;position : absolute;top:0px;left:0px;height:20px;width:<?php echo($disk_precent); ?>%;"></div>
                        <span style="position:absolute;z-index:10;color:#444;left:38%;top:1px;">  <?php echo($disk_usage.'/'.$disk_all); ?> مگابایت </span> 
                    </div> 
                </td>
            </tr>
            <tr>
                <td <?php if ($bw_precent > 75) echo('style="color:#e74c3c"') ?> > میزان  پهنای باند </td>
                <td style="text-align:center;width:70%">
                    <div style="min-width:100px;width:100%;  height:20px;border:1px solid #555;position:relative;border-radius:1px">
                        <div style="background: <?php echo($bw_color)  ?>;position : absolute;top:0px;left:0px;height:20px;width:<?php echo(($bw_precent==0)?1:$bw_precent); ?>%;"></div>
                        <span style="position:absolute;z-index:10;color:#444;left:38%;top:1px;">  <?php echo($bw_usage.'/'.$bw_all); ?> مگابایت </span> 
                    </div> 
                </td>
            </tr>

        </table>

        <?php
            
        }else{
            echo('<h1 style="color:red" >'.'خطا در برقراری ارتباط با سرور '.'<h1>');
            print_r($output);
        }
    }

    /**
     * Load widget config code.
     *
     * This is what will display when an admin clicks
     */
    public static function config() {
        //require_once( 'widget-config.php' );
    }

    /**
     * Gets the options for a widget of the specified name.
     *
     * @param string $widget_id Optional. If provided, will only get options for the specified widget.
     * @return array An associative array containing the widget's options and values. False if no opts.
     */
    public static function get_dashboard_widget_options( $widget_id='' )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        //If no widget is specified, return everything
        if ( empty( $widget_id ) )
            return $opts;

        //If we request a widget and it exists, return it
        if ( isset( $opts[$widget_id] ) )
            return $opts[$widget_id];

        //Something went wrong...
        return false;
    }

    /**
     * Gets one specific option for the specified widget.
     * @param $widget_id
     * @param $option
     * @param null $default
     *
     * @return string
     */
    public static function get_dashboard_widget_option( $widget_id, $option, $default=NULL ) {

        $opts = self::get_dashboard_widget_options($widget_id);

        //If widget opts dont exist, return false
        if ( ! $opts )
            return false;

        //Otherwise fetch the option or use default
        if ( isset( $opts[$option] ) && ! empty($opts[$option]) )
            return $opts[$option];
        else
            return ( isset($default) ) ? $default : false;

    }

    /**
     * Saves an array of options for a single dashboard widget to the database.
     * Can also be used to define default values for a widget.
     *
     * @param string $widget_id The name of the widget being updated
     * @param array $args An associative array of options being saved.
     * @param bool $add_only If true, options will not be added if widget options already exist
     */
    public static function update_dashboard_widget_options( $widget_id , $args=array(), $add_only=false )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        //Get just our widget's options, or set empty array
        $w_opts = ( isset( $opts[$widget_id] ) ) ? $opts[$widget_id] : array();

        if ( $add_only ) {
            //Flesh out any missing options (existing ones overwrite new ones)
            $opts[$widget_id] = array_merge($args,$w_opts);
        }
        else {
            //Merge new options with existing ones, and add it back to the widgets array
            $opts[$widget_id] = array_merge($w_opts,$args);
        }

        //Save the entire widgets array back to the db
        return update_option('dashboard_widget_options', $opts);
    }

}






?>